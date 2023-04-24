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
