<?php

namespace App\Controller\Api;

use App\Entity\Dog;
use App\Entity\Hobby;
use App\Repository\HobbyRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

class ApiHobbyController extends AbstractController
{
    /**
     * Returns in JSON data the list of all hobbies
     * 
     * @Route("/api/hobbies", name="app_api_hobby")
     */
    public function index(HobbyRepository $hobbyRepository): Response
    {
        $hobbiesList = $hobbyRepository->findAll();
        
        return $this->json(
                            $hobbiesList,
                            Response::HTTP_OK, [], 
                            ['groups' => 'get_hobbies_collection']
                        );

    }


    /** 
     * return in JSON the list of all hobbies of a given dog
     *      
     * @Route("/api/dog/{id<\d+>}/hobbies", name="app_api_get_hobbies_by_dog", methods={"GET"})
     */
    public function getHobbiesByDog(Dog $dog = null)
    {

        if(!$dog){
            return $this->json(
                ['error' => 'Chien non trouvé'], 
                 Response::HTTP_NOT_FOUND,
            );
        }

        // using the method getHobbies in the dog Entity thanks to the relation many to one
        $hobbiesByDog = $dog->getHobbies();

        $data = [
            "dog" => $dog,
            "hobbies" => $hobbiesByDog
        ];
        return $this->json(
            $data,
            Response::HTTP_OK,
            [],
            [
                'groups' => [
                    // the group of dog
                    'get_dogs_collection',
                    // the group of hobbies
                    'get_hobbies_collection'
                ]
            ]);
    }

    /**
     * Creation of a hobby for a given dog via API
     * 
     * @Route("/api/secure/dogs/{id<\d+>}/hobby", name="api_hobbies_post", methods={"POST"})
     */
    public function createItem(Request $request, SerializerInterface $serializer, ManagerRegistry $doctrine, ValidatorInterface $validator, Dog $dog = null)
    {

        if(!$dog){
            return $this->json(
                ['error' => 'Chien non trouvé'], 
                 Response::HTTP_NOT_FOUND,
            );
        }

        // we get the JSON
        $jsonContent = $request->getContent();

        //managing errors
        try {
           
            //we deserialize (convert) the JSON into a Hobby entity
            $hobby = $serializer->deserialize($jsonContent, Hobby::class, 'json');
        }
        catch(NotEncodableValueException $e) {
            return $this->json(
                ["error" => 'JSON invalide'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        //we validate the gotten Hobby entity
        $errors = $validator->validate($hobby);

        if(count($errors) > 0){
            return $this->json(
                $errors, 
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }
        

        // Saving the entity
        $hobby->setDog($dog);
        $entityManager = $doctrine->getManager();
        $entityManager->persist($hobby);
        $entityManager->flush();

        // returning the answer

        return $this->json(
            // the created hobby
            $hobby,
            // The status code 201 : CREATED
            Response::HTTP_CREATED,
            [
                // Location = /api/hobbies(for redirection to all hobbies url)
                'Location' => $this->generateUrl('app_api_hobby',)
            ],
            ['groups' => 'get_item']
        );
    }

// -------------------------------------

    /**
     * Updating a dog via API put
     * @Route("/api/secure/dogs/{dog_id<\d+>}/hobby/{id<\d+>}", name="api_hobbies_update_item", methods={"PUT"})
     * @ParamConverter("dog", options={"mapping": {"dog_id": "id"}})
     */
    public function updateItem(ManagerRegistry $doctrine, Request $request, SerializerInterface $serializer, ValidatorInterface $validatorInterface, Hobby $hobby = null, Dog $dog = null)
    {

        if (!$dog) {
            return $this->json([
                'error' => "Chien non trouvé",
                response::HTTP_NOT_FOUND
            ]);
        } 
        
        else if (!$hobby) {
            return $this->json([
                'error' => "Hobby non trouvé",
                response::HTTP_NOT_FOUND
            ]);
        }
        
        else {
            // get the json
            $jsonContent = $request->getContent();

            try {
                // deserialize le json into post entity
                $hobby = $serializer->deserialize($jsonContent, Hobby::class, 'json', ['object_to_populate' => $hobby]);
            } catch (NotEncodableValueException $e) {
                return $this->json(
                    ["error" => "JSON INVALIDE"],
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            //we validate the gotten Hobby entity
            $errors = $validatorInterface->validate($hobby);

            if (count($errors) > 0) {
                return $this->json(
                    $errors,
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $entityManager = $doctrine->getManager();
            $entityManager->persist($hobby);
            $entityManager->flush();

            return $this->json(
                $hobby,
                //The status code 204 : UPDATED
                204,
                [
                    
                ],
                ['groups' => 'get_item']
            );
        }
    }

    
}
