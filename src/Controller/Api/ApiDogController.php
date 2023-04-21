<?php

namespace App\Controller\Api;

use App\Entity\Dog;
use App\Entity\Member;
use App\Repository\DogRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

class ApiDogController extends AbstractController
{
    /**
     * Returns in JSON data the list of all dogs
     * 
     * @Route("/api/dogs", name="app_api_dogs")
     */
    public function index(DogRepository $dogRepository): Response
    {
        $dogsList = $dogRepository->findAll();

        return $this->json(
            $dogsList,
            Response::HTTP_OK,
            [],
            ['groups' => 'get_dogs_collection']
        );
    }

    /**
     * JSON request to get one given member
     *
     * @Route("/api/dogs/{id<\d+>}", name="api_dogs_get_item", methods={"GET"})
     * 
     */
    public function getDogItem(Dog $dog = null)
    {

        if (!$dog) {
            return $this->json(
                ['error' => 'Chien non trouvé'],
                Response::HTTP_NOT_FOUND,
            );
        }

        return $this->json(
            $dog,
            200,
            [],
            ['groups' => 'get_dog_item']
        );
    }


    /**
     * Creation of a dog via API
     * 
     * @Route("/api/secure/dogs", name="api_dogs_post", methods={"POST"})
     */
    public function createItem(Request $request, SerializerInterface $serializer, ManagerRegistry $doctrine, ValidatorInterface $validator)
    {
        /** @var \App\Entity\Member $member*/
        $member = $this->getUser();
        // dd($member);

        // we get the JSON
        $jsonContent = $request->getContent();

        //managing errors
        try {

            //we deserialize (convert) the JSON into a Dog entity
            $dog = $serializer->deserialize($jsonContent, Dog::class, 'json');
        } catch (NotEncodableValueException $e) {
            return $this->json(
                ["error" => 'JSON invalide'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        //we validate the gotten Dog entity
        $errors = $validator->validate($dog);

        if (count($errors) > 0) {
            return $this->json(
                $errors,
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }


        // Saving the entity
        $dog->setMember($member);
        $entityManager = $doctrine->getManager();
        $entityManager->persist($dog);
        $entityManager->flush();

        // returning the answer

        return $this->json(
            // the created dog
            $dog,
            //The status code 201 : CREATED
            Response::HTTP_CREATED,
            [
                // Location = /api/dogs (for redirection to all dogs url)
                'Location' => $this->generateUrl('app_api_dogs',)
            ],
            ['groups' => 'get_item']
        );
    }

    // -------------------------------------

    /**
     * Updating a dog via API put
     * @Route("/api/secure/dogs/{id<\d+>}", name="api_dog_update_item", methods={"PUT"})
     */
    public function updateItem(ManagerRegistry $doctrine, Request $request, SerializerInterface $serializer, ValidatorInterface $validatorInterface, Dog $dog = null)
    {

        /** @var \App\Entity\Member $member*/
        $member = $this->getUser();

        if (!$dog) {
            return $this->json([
                'error' => "Chien non trouvé",
                response::HTTP_NOT_FOUND
            ]);
        } else {
            // get the json
            $jsonContent = $request->getContent();

            try {
                // deserialize le json into post entity
                $dog = $serializer->deserialize($jsonContent, Dog::class, 'json', ['object_to_populate' => $dog]);
            } catch (NotEncodableValueException $e) {
                return $this->json(
                    ["error" => "JSON INVALIDE"],
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            //we validate the gotten Member entity
            $errors = $validatorInterface->validate($dog);

            if (count($errors) > 0) {
                return $this->json(
                    $errors,
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $entityManager = $doctrine->getManager();
            $entityManager->persist($dog);
            $entityManager->flush();

            return $this->json(
                $dog,
                //The status code 204 : UPDATED
                204,
                [
                    // Location = /api/dogs (for redirection to all dogs url)
                    'Location' => $this->generateUrl('app_api_dogs',)
                ],
                ['groups' => 'get_item']
            );
        }
    }

//------------------------------------    

     /**
     * Deleting a given dog
     * @Route("/api/secure/dogs/{id<\d+>}", name="api_dog_delete_item", methods={"DELETE"})
     */
    public function deleteItem (Dog $dog = null, ManagerRegistry $doctrine)
    {
        if (!$dog) {
            return $this->json(
                ['error' => 'Chien non trouvé'],
                Response::HTTP_NOT_FOUND,
                );
            }

            $entityManager = $doctrine->getManager();
            $entityManager->remove($dog);
            $entityManager->flush();
        
        return new Response(null, 204);
    }
}
