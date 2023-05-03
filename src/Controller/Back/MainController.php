<?php

namespace App\Controller\Back;

use App\Entity\Member;
use App\Repository\DogRepository;
use App\Repository\MemberRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    /**
     * Route to display the home page for the backoffice 
     * 
     * @Route("/back/", name="app_back")
     */
    public function index(): Response
    { 
        //getting the connected member
        /** @var \App\Entity\Member $member*/
        $member = $this->getUser();

        //returning a render as a 'twig view'
        return $this->render('back/main/backoffice.html.twig', [
            'member' => $member,
        
        ]);
    }

// ---------------- END OF METHOD ---------------------   

    /**
     * Route to display the main page for the backoffice
     * 
     * @Route("/back/main", name="app_main_back")
     */
    public function main(MemberRepository $memberRepository, DogRepository $dogRepository): Response
    {
        //returning a render as a 'twig view'
        return $this->render('back/main/index.html.twig', [
            //finding all members
            'members' => $memberRepository->findAll(),

            //finding all dogs
            'dogs' => $dogRepository->findAll(),
        ]);
    }

// ---------------- END OF METHOD ---------------------   

}
