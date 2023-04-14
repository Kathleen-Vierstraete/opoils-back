<?php

namespace App\Controller\Api;

use App\Repository\DogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiDogController extends AbstractController
{
    /**
     * Returns in JSON data the list of all dogs
     * 
     * @Route("/api/dogs", name="app_api_dog")
     */
    public function index(DogRepository $dogRepository): Response
    {
        $dogsList = $dogRepository->findAll();
        
        return $this->json(
                            $dogsList,
                            Response::HTTP_OK, [], 
                            ['groups' => 'get_dogs_collection']
                        );

    }
}

