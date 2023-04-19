<?php

namespace App\Controller\Api;

use App\Entity\Member;
use App\Repository\MemberRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
                            Response::HTTP_OK, [], 
                            ['groups' => 'get_members_collection']
                        );

    }

    /**
     * Creation of a member via API
     * 
     * @Route("/api/secure/members", name="api_members_post", methods={"POST"})
     */
    public function createItem(Request $request, SerializerInterface $serializer, ManagerRegistry $doctrine, ValidatorInterface $validator)
    {
        // we get the JSON
        $jsonContent = $request->getContent();


        //managing errors
        try {
            //we deserialize (convert) the JSON into a Member entity
            $member = $serializer->deserialize($jsonContent, Member::class, 'json');
        }
        catch(NotEncodableValueException $e) {
            return $this->json(
                ["error" => 'JSON invalide'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        //we validate the gotten Member entity
        $errors = $validator->validate($member);

        if(count($errors) > 0){
            return $this->json(
                $errors, 
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        // Saving the entity
        $entityManager = $doctrine->getManager();
        $entityManager->persist($member);
        $entityManager->flush();

        // returning the answer

        return $this->json(
            // the created member
            $member,
            // Le status code 201 : CREATED
            Response::HTTP_CREATED,
            [
                // Location = /api/members (for redirection to all members url)
                'Location' => $this->generateUrl('app_api_members',)
            ],
            ['groups' => 'get_item']
        );
    }
}
