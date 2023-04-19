<?php

namespace App\Controller\Api;

use App\Repository\HobbyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
}
