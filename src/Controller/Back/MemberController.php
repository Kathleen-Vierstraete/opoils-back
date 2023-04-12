<?php

namespace App\Controller\Back;

use App\Entity\Member;
use App\Form\MemberType;
use App\Repository\MemberRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @Route("/back/member")
 */
class MemberController extends AbstractController
{
    /**
     * @Route("/", name="app_back_member_index", methods={"GET"})
     */
    public function index(MemberRepository $memberRepository): Response
    {
        return $this->render('back/member/index.html.twig', [
            'members' => $memberRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_back_member_new", methods={"GET", "POST"})
     */
    public function new(Request $request, MemberRepository $memberRepository, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $member = new Member();
        $form = $this->createForm(MemberType::class, $member);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //hashing the password beforehead
            $hashedPassword = $userPasswordHasher->hashPassword($member , $member->getPassword());

            //defining the member's password with the hashed password
            $member->setPassword($hashedPassword);

            $memberRepository->add($member, true);

            return $this->redirectToRoute('app_back_member_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/member/new.html.twig', [
            'member' => $member,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_back_member_show", methods={"GET"})
     */
    public function show(Member $member): Response
    {
        return $this->render('back/member/show.html.twig', [
            'member' => $member,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_back_member_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Member $member, MemberRepository $memberRepository, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $form = $this->createForm(MemberType::class, $member);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //hashing the password beforehead
            $hashedPassword = $userPasswordHasher->hashPassword($member , $member->getPassword());

            //defining the member's password with the hashed password
            $member->setPassword($hashedPassword);

            $memberRepository->add($member, true);

            return $this->redirectToRoute('app_back_member_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/member/edit.html.twig', [
            'member' => $member,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_back_member_delete", methods={"POST"})
     */
    public function delete(Request $request, Member $member, MemberRepository $memberRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$member->getId(), $request->request->get('_token'))) {
            $memberRepository->remove($member, true);
        }

        return $this->redirectToRoute('app_back_member_index', [], Response::HTTP_SEE_OTHER);
    }
}
