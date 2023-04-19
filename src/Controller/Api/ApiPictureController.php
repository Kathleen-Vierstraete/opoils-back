<?php

namespace App\Controller\Api;

use App\Repository\PictureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
                            Response::HTTP_OK, [], 
                            ['groups' => 'get_pictures_collection']
                        );

    }
}