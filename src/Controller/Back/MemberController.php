<?php

namespace App\Controller\Back;

use App\Entity\Member;
use App\Form\MemberType;
use App\Repository\MemberRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
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
    public function new(Request $request, SluggerInterface $slugger, MemberRepository $memberRepository, UserPasswordHasherInterface $userPasswordHasher ): Response
    {
        $member = new Member();
        $form = $this->createForm(MemberType::class, $member);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

// GESTION PASSWORD

            //hashing the password beforehead
            $hashedPassword = $userPasswordHasher->hashPassword($member , $member->getPassword());

            //defining the member's password with the hashed password
            $member->setPassword($hashedPassword);

// GESTION FILES MEDIA

    /** @var UploadedFile $pictureFile */
    $pictureFile = $form->get('picture')->getData();

            // this condition is needed because the 'picture' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($pictureFile) {
                $originalFilename = pathinfo($pictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$pictureFile->guessExtension();    

                // Move the file to the directory where pictures are stored
                try {
                    $pictureFile->move(
                        $this->getParameter('pictures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                    throw $e;
                }

                // updates the 'pictureFilename' property to store the JPG file name
                // instead of its contents
                $member->setPicture($newFilename);
            }

            // ... persist the $member variable or any other work

// END FILES

            $member->setSlug($slugger->slug($member->getUsername())->lower());

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
    public function edit(Request $request, Member $member, SluggerInterface $slugger, MemberRepository $memberRepository, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $form = $this->createForm(MemberType::class, $member);
        //dd($form);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $newPassword = $form->get('password')->getData();

            //dd($newPassword);

            //if there is a password, we hash it and put it in $member
            if ($newPassword !== null) {
                $hashedPassword = $userPasswordHasher->hashPassword($member, $newPassword);
                $member->setPassword($hashedPassword);
            }

// GESTION FILES MEDIA

    /** @var UploadedFile $pictureFile */
    $pictureFile = $form->get('picture')->getData();

            // this condition is needed because the 'picture' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($pictureFile) {
                $originalFilename = pathinfo($pictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$pictureFile->guessExtension();    

                // Move the file to the directory where pictures are stored
                try {
                    $pictureFile->move(
                        $this->getParameter('pictures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                    throw $e;
                }

                // updates the 'pictureFilename' property to store the JPG file name
                // instead of its contents
                $member->setPicture($newFilename);
            }

            // ... persist the $member variable or any other work

// END FILES            

            $member->setSlug($slugger->slug($member->getUsername())->lower());

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