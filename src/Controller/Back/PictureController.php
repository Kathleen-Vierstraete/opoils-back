<?php

namespace App\Controller\Back;

use App\Entity\Dog;
use App\Entity\Picture;
use App\Form\PictureType;
use App\Repository\PictureRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("/back/picture")
 */
class PictureController extends AbstractController
{
    /**
     * route to display all pictures
     * 
     * @Route("/", name="app_back_picture_index", methods={"GET"})
     */
    public function index(PictureRepository $pictureRepository): Response
    {
        //returning a render as a 'twig view'
        return $this->render('back/picture/index.html.twig', [
            //finding all pictures
            'pictures' => $pictureRepository->findAll(),
        ]);
    }

// ---------------- END OF METHOD ---------------------   

    /**
     *Route to display the pictures of a dog in particular
     * 
     * @Route("/dog/{id<\d+>}", name="app_back_picture_indexpictures", methods={"GET"})
     */
    public function indexPictures(PictureRepository $pictureRepository, Dog $dog): Response
    {
        //returning a render as a 'twig view'
        return $this->render('back/picture/index.html.twig', [
            //finding the pictures for a given dog
            'pictures' => $pictureRepository->findByDog($dog),
            //associating the dog to the pictures
            'dog' => $dog,
        ]);
    }

// ---------------- END OF METHOD ---------------------       

    /**
     * Route to create a new picture entity
     * 
     * @Route("/new/dog/{id<\d+>}", name="app_back_picture_new", methods={"GET", "POST"})
     */
    public function new(Request $request, PictureRepository $pictureRepository, Dog $dog, SluggerInterface $slugger): Response
    {
        //creating the entity via the form 
        $picture = new Picture();
        $form = $this->createForm(PictureType::class, $picture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //we associate the current dog
            $picture->setDog($dog);

    // ---------------- MANAGING THE FILE UPLOAD ---------------------

            /** @var UploadedFile $pictureFile */
            $pictureFile = $form->get('picture')->getData();

            // this condition is needed because the 'picture' field is not required
            // so the jpeg file must be processed only when a file is uploaded
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
            $picture->setPicture($newFilename);
            }

        // ... persist the $picture variable or any other work

    // ---------------- END OF MANAGING THE FILE UPLOAD ---------------------

            //saving the entity
            $pictureRepository->add($picture, true);

            //redirection route
            return $this->redirectToRoute('app_back_picture_indexpictures', ['id' => $dog->getId()], Response::HTTP_SEE_OTHER);
        }

        //returning a render as a 'twig form'
        return $this->renderForm('back/picture/new.html.twig', [
            'picture' => $picture,
            'form' => $form,
            'dog' => $dog,
        ]);
    }

// ---------------- END OF METHOD ---------------------   

    /**
     * Route to display a given hobby
     * 
     * @Route("/{id}/show/dog/{dog_id<\d+>}", name="app_back_picture_show", methods={"GET"})
     * @ParamConverter("dog", options={"mapping": {"dog_id": "id"}})
     */
    public function show(Picture $picture, Dog $dog): Response
    {
        // returning a render as a 'twig view'
        return $this->render('back/picture/show.html.twig', [
            'picture' => $picture,
            'dog' => $dog,
        ]);
    }

// ---------------- END OF METHOD ---------------------      

    /**
     * Route to edit a pciture entity
     * 
     * @Route("/{id}/edit/dog/{dog_id<\d+>}", name="app_back_picture_edit", methods={"GET", "POST"})
     * @ParamConverter("dog", options={"mapping": {"dog_id": "id"}})
     */
    public function edit(Request $request, Picture $picture, PictureRepository $pictureRepository, Dog $dog, SluggerInterface $slugger): Response
    {
        //retrieving the entity via the form 
        $form = $this->createForm(PictureType::class, $picture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

    // ---------------- MANAGING THE FILE UPLOAD ---------------------

            /** @var UploadedFile $pictureFile */
            $pictureFile = $form->get('picture')->getData();

            // this condition is needed because the 'picture' field is not required
            // so the jpeg file must be processed only when a file is uploaded
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
            $picture->setPicture($newFilename);
            }

        // ... persist the $picture variable or any other work

    // ---------------- END OF MANAGING THE FILE UPLOAD ---------------------

            //saving the entity
            $pictureRepository->add($picture, true);

            //redirection route
            return $this->redirectToRoute('app_back_picture_indexpictures', ['id' => $dog->getId()], Response::HTTP_SEE_OTHER);
        }

        // returning a render as a 'twig form'
        return $this->renderForm('back/picture/edit.html.twig', [
            'picture' => $picture,
            'form' => $form,
            'dog' => $dog,
        ]);
    }

// ---------------- END OF METHOD ---------------------      

    /**
     * Route to delete a picture entity
     * 
     * @Route("/{id}/dog/{dog_id<\d+>}", name="app_back_picture_delete", methods={"POST"})
     * @ParamConverter("dog", options={"mapping": {"dog_id": "id"}})
     */
    public function delete(Request $request, Picture $picture, PictureRepository $pictureRepository, Dog $dog): Response
    {
        //making sure to get the token
        if ($this->isCsrfTokenValid('delete'.$picture->getId(), $request->request->get('_token'))) {
            //deleting the entity
            $pictureRepository->remove($picture, true);
        }

        //redirection route
        return $this->redirectToRoute('app_back_picture_indexpictures', ['id' => $dog->getId()], Response::HTTP_SEE_OTHER);
    }

// ---------------- END OF METHOD ---------------------     
    
}
