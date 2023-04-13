<?php

namespace App\Controller\Api;

use App\Repository\MemberRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
                            Response::HTTP_OK, [], 
                            ['groups' => 'get_members_collection']
                        );

    }
}
