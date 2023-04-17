<?php

namespace App\Controller\Back;

use App\Repository\DogRepository;
use App\Repository\MemberRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    /**
     * @Route("/back/", name="app_back")
     */
    public function index(MemberRepository $memberRepository, DogRepository $dogRepository): Response
    {
        return $this->render('back/main/index.html.twig', [
            'members' => $memberRepository->findAll(),
            'dogs' => $dogRepository->findAll(),
        ]);
    }
}
