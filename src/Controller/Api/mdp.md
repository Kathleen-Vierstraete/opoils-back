``` PHP

/**
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param ManagerRegistry $doctrine
     * @return void
     * 
     * @Route("user" , name="user", methods={"GET","POST"})
     */
    public function createItemUser(Request $request,EntityManagerInterface $entityManager, SerializerInterface $serializer, UserPasswordHasherInterface $userPasswordHasher, UserRepository $userRepository): JsonResponse
    {
        
        $content =$request->getContent();
        if (empty($content)){
            return new JsonResponse(['error' => 'La requête doit contenir un corps JSON.'], JsonResponse::HTTP_BAD_REQUEST);
        }
        // translation of Json information and creation of a class
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');

         //check all parameters to look if they are not empty and if 'firstname' and '  lastname' are both string
        $missingFields = [];
            if (!$user->getFirstname() || !preg_match('/^[a-zA-Z]*$/',$user->getFirstname())) {
                $missingFields[] = 'firstname';
                return new JsonResponse(['error' => sprintf('Prénom invalide')], JsonResponse::HTTP_BAD_REQUEST);
            }
            if (!$user->getLastname() || !preg_match('/^[a-zA-Z]*$/',$user->getLastname())) {
                $missingFields[] = 'lastname';
                return new JsonResponse(['error' => sprintf('Nom invalide')], JsonResponse::HTTP_BAD_REQUEST);
            }
            if (!$user->getEmail()) {
                $missingFields[] = 'email';
            }
            if (!$user->getPassword()) {
                $missingFields[] = 'password';
            }
            
            // Add Rules for password 
            if (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+])[A-Za-z\d!@#$%^&*()_+]{8,}$/', $user->getPassword())) {
                 return new JsonResponse(['error' => 'Le mot de passe doit contenir au moins 8 caractères, dont une majuscule, un chiffre et un caractère spécial.'], JsonResponse::HTTP_BAD_REQUEST);
            }
            // password hash
            $hashedPassword = $userPasswordHasher->hashPassword($user, $user->getPassword());
            // save the password hash
            $user->setPassword($hashedPassword);


                // Look if the user already exist in database
            $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);
                if ($existingUser) {
                    return new JsonResponse(['error' => 'Email invalide.'], JsonResponse::HTTP_BAD_REQUEST);
                }


                // save use in the BDD
                $userRepository->add($user, true);
                // creation of the user in the database

                // Return Json of for the creation of the new user to the front
                return $this->json(
                    // information of the creation convert in Json
                    $user,
                    // the status code of create 201
                    Response::HTTP_CREATED,
                    // the header
                    [],
                    // element group for user
                    ['groups' => 'get_itemUser']
                );
            }