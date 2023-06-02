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
     * Route to display all dogs
     * 
     * @Route("/", name="app_back_member_index", methods={"GET"})
     */
    public function index(MemberRepository $memberRepository): Response
    {
        //returning a render as a 'twig view'
        return $this->render('back/member/index.html.twig', [
            //finding all members
            'members' => $memberRepository->findAll(),
        ]);
    }

// ---------------- END OF METHOD ---------------------

    /**
     * Route to create a new member
     * 
     * @Route("/new", name="app_back_member_new", methods={"GET", "POST"})
     */
    public function new(Request $request, SluggerInterface $slugger, MemberRepository $memberRepository, UserPasswordHasherInterface $userPasswordHasher ): Response
    {
        //creating the entity via the form 
        $member = new Member();
        $form = $this->createForm(MemberType::class, $member);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

    // ---------------- MANAGING THE PASSWORD HASH ---------------------

            //hashing the password beforehead
            $hashedPassword = $userPasswordHasher->hashPassword($member , $member->getPassword());

            //defining the member's password with the hashed password
            $member->setPassword($hashedPassword);

    // ---------------- END OF PASSWORD HASH MANAGEMENT ---------------------

    // ---------------- MANAGING THE FILE UPLOAD ---------------------

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

    // ---------------- END OF MANAGING THE FILE UPLOAD ---------------------

            //setting the slug thanks to the username
            $member->setSlug($slugger->slug($member->getUsername())->lower());

            //saving the entity
            $memberRepository->add($member, true);

            //redirection route 
            return $this->redirectToRoute('app_back_member_index', [], Response::HTTP_SEE_OTHER);
        }

        // returning a render as a 'twig form'
        return $this->renderForm('back/member/new.html.twig', [
            'member' => $member,
            'form' => $form,
        ]);
    }

// ---------------- END OF METHOD ---------------------    

    /**
     * Route to display a given member
     * 
     * @Route("/{id}", name="app_back_member_show", methods={"GET"})
     */
    public function show(Member $member): Response
    {
        // returning a render as a 'twig view'
        return $this->render('back/member/show.html.twig', [
            'member' => $member,
        ]);
    }

// ---------------- END OF METHOD ---------------------   

    /**
     * Route to edit a member entity
     * 
     * @Route("/{id}/edit", name="app_back_member_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Member $member, SluggerInterface $slugger, MemberRepository $memberRepository, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        //retrieving the entity via the form 
        $form = $this->createForm(MemberType::class, $member);
        //dd($form);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //retrieving the password
            $newPassword = $form->get('password')->getData();

            //dd($newPassword);

            //if there is a password, we hash it and put it in $member
            if ($newPassword !== null) {
                $hashedPassword = $userPasswordHasher->hashPassword($member, $newPassword);
                $member->setPassword($hashedPassword);
            }

    // ---------------- MANAGING THE FILE UPLOAD ---------------------

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

    // ---------------- END OF MANAGING THE FILE UPLOAD ---------------------           

            //setting the slug thanks to the member's username
            $member->setSlug($slugger->slug($member->getUsername())->lower());

            //saving the entity
            $memberRepository->add($member, true);

            //redirection route
            return $this->redirectToRoute('app_back_member_index', [], Response::HTTP_SEE_OTHER);
        }

        // returning a render as a 'twig form'
        return $this->renderForm('back/member/edit.html.twig', [
            'member' => $member,
            'form' => $form,
        ]);
    }

// ---------------- END OF METHOD ---------------------   

    /**
     * Route to delete a given member
     * 
     * @Route("/{id}", name="app_back_member_delete", methods={"POST"})
     */
    public function delete(Request $request, Member $member, MemberRepository $memberRepository): Response
    {
        //making sure to get the token
        if ($this->isCsrfTokenValid('delete'.$member->getId(), $request->request->get('_token'))) {

            //deleting the entity            
            $memberRepository->remove($member, true);
        }

        //redirection route
        return $this->redirectToRoute('app_back_member_index', [], Response::HTTP_SEE_OTHER);
    }

// ---------------- END OF METHOD ---------------------   
 
}