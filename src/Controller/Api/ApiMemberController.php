<?php

namespace App\Controller\Api;

use App\Entity\Member;
use App\Repository\MemberRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

class ApiMemberController extends AbstractController
{
    /**
     * Returns in JSON data the list of all members
     * 
     * @Route("/api/members", name="app_api_members")
     */
    public function getCollection(MemberRepository $memberRepository): Response
    {
        $membersList = $memberRepository->findAll();

        return $this->json(
            $membersList,
            Response::HTTP_OK,
            [],
            ['groups' => 'get_members_collection']
        );
    }

    /**
     * JSON request to get one given member
     *
     * @Route("/api/members/{id<\d+>}", name="api_members_get_item", methods={"GET"})
     * 
     */
    public function getMemberItem(Member $member = null)
    {


        if (!$member) {
            return $this->json(
                ['error' => 'Membre non trouvé'],
                Response::HTTP_NOT_FOUND,
            );
        }

        return $this->json(
            $member,
            200,
            [],
            ['groups' => 'get_member_item']
        );
    }

    /**
     * JSON request to get the connected member
     * 
     * @Route("/api/member", name="app_api_member", methods={"GET"})
     * 
     */
    public function getConnectedMember()
    {
        /** @var \App\Entity\Member $member*/
        $member = $this->getUser();
        //dd($member);

/*         $data  = [];

        $memberData = [];



        $memberDogs = $member->getDogs();
        // dd($memberDogs);

        $dogs = [];

        foreach ($memberDogs as $memberDog) {
            $id = $memberDog->getId();
            $name = $memberDog->getName();
            $age = $memberDog->getAge();
            $race = $memberDog->getRace();
            $size = $memberDog->getSize();
            $personality = $memberDog->getPersonality();
            $presentation = $memberDog->getPresentation();
            $slug = $memberDog->getSlug();

            $dogPictures = $memberDog->getPictures();

            $picturesArray = [];

            foreach ($dogPictures as $dogPicture) {
                $id = $dogPicture->getId();
                $picture = $dogPicture->getPicture();


                $picturesArray[] = [
                    "id" => $id,
                    "picture" => $picture,
                ];
            }

            $dogHobbies = $memberDog->getHobbies();
            $hobbiesArray = [];

            foreach ($dogHobbies as $dogHobby) {
                $id = $dogHobby->getId();
                $hobby = $dogHobby->getHobby();


                $hobbiesArray[] = [
                    "id" => $id,
                    "hobby" => $hobby,
                ];
            }

            $dogs[] = [
                "id" => $id,
                "name" => $name,
                "age" => $age,
                "race" => $race,
                "size" => $size,
                "personality" => $personality,
                "presentation" => $presentation,
                "slug" => $slug,
                "picturesArray" => $picturesArray,
                "hobbiesArray" => $hobbiesArray
            ];
        }; */

        $lastname = $member->getLastname();
        $firstname = $member->getFirstname();
        $username = $member->getUsername();
        $postalCode = $member->getPostalCode();
        $email = $member->getEmail();
        $slug = $member->getSlug();
        $memberPicture = $member->getPicture();
        

        $memberData[] = [
            "lastname" => $lastname,
            "firstname" => $firstname,
            "username" => $username,
            "postalCode" => $postalCode,
            "email" => $email,
            "slug" => $slug,
            "memberPicture" => $memberPicture,
        ]; 

        $data = [
            "memberData" => $memberData,
            "dogs" => $member->getDogs(),
        ];

        return $this->json(
            $data,
            Response::HTTP_OK,
            [],
            ['groups' => 'get_connected_member']
        );
    }

    /**
     * Creation of a member via API
     * 
     * @Route("/api/secure/members", name="api_members_post", methods={"GET","POST"})
     */
    public function createItem(Request $request, SerializerInterface $serializer, ManagerRegistry $doctrine, ValidatorInterface $validator, SluggerInterface $slugger, UserPasswordHasherInterface $userPasswordHasher): JsonResponse
    {
        // we get the JSON
        $jsonContent = $request->getContent();



        //managing errors
        try {
            //we deserialize (convert) the JSON into a Member entity
            $member = $serializer->deserialize($jsonContent, Member::class, 'json');            
        } catch (NotEncodableValueException $e) {
            return $this->json(
                ["error" => 'JSON invalide'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        //we validate the gotten Member entity
        $errors = $validator->validate($member);

        if (count($errors) > 0) {
            return $this->json(
                $errors,
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        // password safety
        if (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[!@$!%*?&€()+])[A-Za-z\d!@$!%*?&€*()+]{8,}$/', $member->getPassword())) {
            return new JsonResponse([
            'error' => 'Le mot de passe doit contenir au moins 8 caractères, dont une majuscule, un chiffre et un caractère spécial.'
            ], 
            JsonResponse::HTTP_BAD_REQUEST);
       }

       // password hashing
       $hashedPassword = $userPasswordHasher->hashPassword($member, $member->getPassword());
       // save the hashed password 
       $member->setPassword($hashedPassword);

        $member->setSlug($slugger->slug($member->getUsername())->lower());


        // Saving the entity
        $entityManager = $doctrine->getManager();
        $entityManager->persist($member);
        $entityManager->flush();

        // returning the answer

        return $this->json(
            // the created member
            $member,
            //The status code 201 : CREATED
            Response::HTTP_CREATED,
            [
                // Location = /api/members (for redirection to all members url)
                'Location' => $this->generateUrl('app_api_members',)
            ],
            ['groups' => 'get_item']
        );
    }




    /**
     * Updating a member via API put
     * @Route("/api/secure/members/{id<\d+>}", name="api_member_update_item", methods={"PUT"})
     */
    public function updateItem(ManagerRegistry $doctrine, Request $request, SerializerInterface $serializer, ValidatorInterface $validatorInterface, SluggerInterface $slugger, Member $member = null)
    {

        if (!$member) {
            return $this->json([
                'error' => "Membre non trouvé",
                response::HTTP_NOT_FOUND
            ]);
        } else {
            // get the json
            $jsonContent = $request->getContent();

            try {
                // deserialize le json into post entity
                $member = $serializer->deserialize($jsonContent, Member::class, 'json', ['object_to_populate' => $member]);
            } catch (NotEncodableValueException $e) {
                return $this->json(
                    ["error" => "JSON INVALIDE"],
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $member->setSlug($slugger->slug($member->getUsername())->lower());            

            //we validate the gotten Member entity
            $errors = $validatorInterface->validate($member);

            if (count($errors) > 0) {
                return $this->json(
                    $errors,
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $entityManager = $doctrine->getManager();
            $entityManager->persist($member);
            $entityManager->flush();

            return $this->json(
                $member,
                //The status code 204 : UPDATED
                204,
                [
                    // Location = /api/members (for redirection to all members url)
                    'Location' => $this->generateUrl('app_api_members',)
                ],
                ['groups' => 'get_item']
            );
        }
    }

    //------------------------------------    

    /**
     * Deleting a given member
     * @Route("/api/secure/members/{id<\d+>}", name="api_member_delete_item", methods={"DELETE"})
     */
    public function deleteItem(Member $member = null, ManagerRegistry $doctrine)
    {
        if (!$member) {
            return $this->json(
                ['error' => 'Membre non trouvé'],
                Response::HTTP_NOT_FOUND,
            );
        }

        $entityManager = $doctrine->getManager();
        $entityManager->remove($member);
        $entityManager->flush();

        return new Response(null, 204);
    }
}
