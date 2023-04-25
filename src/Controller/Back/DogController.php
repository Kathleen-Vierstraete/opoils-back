<?php

namespace App\Controller\Back;

use App\Entity\Dog;
use App\Entity\Hobby;
use App\Form\DogType;
use App\Repository\DogRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * @Route("/back/dog")
 */
class DogController extends AbstractController
{
    /**
     * @Route("/", name="app_back_dog_index", methods={"GET"})
     */
    public function index(DogRepository $dogRepository): Response
    {
        return $this->render('back/dog/index.html.twig', [
            'dogs' => $dogRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_back_dog_new", methods={"GET", "POST"})
     */
    public function new(Request $request, DogRepository $dogRepository, SluggerInterface $slugger): Response
    {
        $dog = new Dog();
        $form = $this->createForm(DogType::class, $dog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $dog->setSlug($slugger->slug($dog->getName())->lower());
            
// GESTION FILES MEDIA

    /** @var UploadedFile $pictureFile */
    $pictureFile = $form->get('main_picture')->getData();

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
                $dog->setMainPicture($newFilename);
            }

            // ... persist the $member variable or any other work

// END FILES
            
            $dogRepository->add($dog, true);          


            return $this->redirectToRoute('app_back_dog_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/dog/new.html.twig', [
            'dog' => $dog,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_back_dog_show", methods={"GET"})
     */
    public function show(Dog $dog): Response
    {
        return $this->render('back/dog/show.html.twig', [
            'dog' => $dog,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_back_dog_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Dog $dog, DogRepository $dogRepository, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(DogType::class, $dog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

// GESTION FILES MEDIA

    /** @var UploadedFile $pictureFile */
    $pictureFile = $form->get('main_picture')->getData();

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
                $dog->setMainPicture($newFilename);
            }

            // ... persist the $member variable or any other work

// END FILES
            
            $dog->setSlug($slugger->slug($dog->getName())->lower());
            
            $dogRepository->add($dog, true);

            return $this->redirectToRoute('app_back_dog_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/dog/edit.html.twig', [
            'dog' => $dog,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_back_dog_delete", methods={"POST"})
     */
    public function delete(Request $request, Dog $dog, DogRepository $dogRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$dog->getId(), $request->request->get('_token'))) {
            $dogRepository->remove($dog, true);
        }

        return $this->redirectToRoute('app_back_dog_index', [], Response::HTTP_SEE_OTHER);
    }
}
