<?php

namespace App\Controller\Api;

use App\Entity\Dog;
use App\Entity\Picture;
use App\Repository\PictureRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

class ApiPictureController extends AbstractController
{
    /**
     * Returns in JSON data the list of all pictures
     * 
     * @Route("/api/pictures", name="app_api_picture")
     */
    public function index(PictureRepository $pictureRepository): Response
    {
        $picturesList = $pictureRepository->findAll();

        return $this->json(
            $picturesList,
            Response::HTTP_OK,
            [],
            ['groups' => 'get_pictures_collection']
        );
    }


    /** 
     * return in JSON the list of all pictures of a given dog
     *      
     * @Route("/api/dog/{id<\d+>}/pictures", name="app_api_get_pictures_by_dog", methods={"GET"})
     */
    public function getPicturesByDog(Dog $dog = null)
    {

        if (!$dog) {
            return $this->json(
                ['error' => 'Chien non trouvé'],
                Response::HTTP_NOT_FOUND,
            );
        }


        // using the method getPictures in the dog Entity thanks to the relation many to one
        $picturesByDog = $dog->getPictures();

        $data = [
            "dog" => $dog,
            "pictures" => $picturesByDog
        ];
        return $this->json(
            $data,
            Response::HTTP_OK,
            [],
            [
                'groups' => [
                    // the group of dog
                    'get_dogs_collection',
                    // the group of pictures
                    'get_pictures_collection'
                ]
            ]
        );
    }

// ------------------------------------    

    /**
     * Creation of a pictures for a given dog via API
     * 
     * @Route("/api/secure/dogs/{id<\d+>}/pictures", name="api_pictures_post", methods={"POST"})
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
           
            //we deserialize (convert) the JSON into a Picture entity
            $picture = $serializer->deserialize($jsonContent, Picture::class, 'json');
        }
        catch(NotEncodableValueException $e) {
            return $this->json(
                ["error" => 'JSON invalide'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        //we validate the gotten Picture entity
        $errors = $validator->validate($picture);

        if(count($errors) > 0){
            return $this->json(
                $errors, 
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }
        

        // Saving the entity
        $picture->setDog($dog);
        $entityManager = $doctrine->getManager();
        $entityManager->persist($picture);
        $entityManager->flush();

        // returning the answer

        return $this->json(
            // the created picture
            $picture,
            // The status code 201 : CREATED
            Response::HTTP_CREATED,
            [
                // Location = /api/pictures(for redirection to all pictures url)
                'Location' => $this->generateUrl('app_api_picture',)
            ],
            ['groups' => 'get_item']
        );
    }    

// ------------------------------------------


    /**
     * Updating a dog via API put
     * @Route("/api/secure/dogs/{dog_id<\d+>}/picture/{id<\d+>}", name="api_pictures_update_item", methods={"PUT"})
     * @ParamConverter("dog", options={"mapping": {"dog_id": "id"}})
     */
    public function updateItem(ManagerRegistry $doctrine, Request $request, SerializerInterface $serializer, ValidatorInterface $validatorInterface, Picture $picture = null, Dog $dog = null)
    {

        if (!$dog) {
            return $this->json([
                'error' => "Chien non trouvé",
                response::HTTP_NOT_FOUND
            ]);
        } 
        
        else if (!$picture) {
            return $this->json([
                'error' => "Photo non trouvée",
                response::HTTP_NOT_FOUND
            ]);
        }
        
        else {
            // get the json
            $jsonContent = $request->getContent();

            try {
                // deserialize le json into post entity
                $picture = $serializer->deserialize($jsonContent, Picture::class, 'json', ['object_to_populate' => $picture]);
            } catch (NotEncodableValueException $e) {
                return $this->json(
                    ["error" => "JSON INVALIDE"],
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            //we validate the gotten Picture entity
            $errors = $validatorInterface->validate($picture);

            if (count($errors) > 0) {
                return $this->json(
                    $errors,
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $entityManager = $doctrine->getManager();
            $entityManager->persist($picture);
            $entityManager->flush();

            return $this->json(
                $picture,
                //The status code 204 : UPDATED
                204,
                [
                    
                ],
                ['groups' => 'get_item']
            );
        }
    }

}
