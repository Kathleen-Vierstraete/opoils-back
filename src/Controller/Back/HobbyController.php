<?php

namespace App\Controller\Back;

use App\Entity\Dog;
use App\Entity\Hobby;
use App\Form\HobbyType;
use App\Repository\HobbyRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("/back/hobby")
 */
class HobbyController extends AbstractController
{
    /**
     * @Route("/", name="app_back_hobby_index", methods={"GET"})
     */
    public function index(HobbyRepository $hobbyRepository): Response
    {
        return $this->render('back/hobby/index.html.twig', [
            'hobbies' => $hobbyRepository->findAll(),
            // 'dog' => $dog,
        ]);
    }

    /**
     * the hobbies of a dog in particular
     * 
     * @Route("/dog/{id<\d+>}", name="app_back_hobby_indexhobbies", methods={"GET"})
     */
    public function indexHobbies(HobbyRepository $hobbyRepository, Dog $dog): Response
    {
        return $this->render('back/hobby/index.html.twig', [
            'hobbies' => $hobbyRepository->findByDog($dog),

            'dog' => $dog,
        ]);
    }

    /**
     * @Route("/new/dog/{id<\d+>}", name="app_back_hobby_new", methods={"GET", "POST"})
     */
    public function new(Request $request, HobbyRepository $hobbyRepository, Dog $dog): Response
    {
        $hobby = new Hobby();
        $form = $this->createForm(HobbyType::class, $hobby);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //on asssocie la saison à la série courante (dans la route)
            $hobby->setDog($dog);

            $hobbyRepository->add($hobby, true);

            return $this->redirectToRoute('app_back_hobby_indexhobbies', ['id' => $dog->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/hobby/new.html.twig', [
            'hobby' => $hobby,
            'form' => $form,
            'dog' => $dog
        ]);
    }

    /**
     * @Route("/{id}/show/dog/{dog_id<\d+>}", name="app_back_hobby_show", methods={"GET"})
     * @ParamConverter("dog", options={"mapping": {"dog_id": "id"}})
     */
    public function show(Hobby $hobby, Dog $dog): Response
    {
        return $this->render('back/hobby/show.html.twig', [
            'hobby' => $hobby,
            'dog' => $dog,
        ]);
    }

    /**
     * @Route("/{id}/edit/dog/{dog_id<\d+>}", name="app_back_hobby_edit", methods={"GET", "POST"})
     * @ParamConverter("dog", options={"mapping": {"dog_id": "id"}})
     */
    public function edit(Request $request, Hobby $hobby, HobbyRepository $hobbyRepository, Dog $dog): Response
    {
        $form = $this->createForm(HobbyType::class, $hobby);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hobbyRepository->add($hobby, true);

            return $this->redirectToRoute('app_back_hobby_indexhobbies', ['id' => $dog->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/hobby/edit.html.twig', [
            'hobby' => $hobby,
            'form' => $form,
            'dog'=> $dog,
        ]);
    }

    /**
     * @Route("/{id}", name="app_back_hobby_delete", methods={"POST"})
     */
    public function delete(Request $request, Hobby $hobby, HobbyRepository $hobbyRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$hobby->getId(), $request->request->get('_token'))) {
            $hobbyRepository->remove($hobby, true);
        }

        return $this->redirectToRoute('app_back_hobby_index', [], Response::HTTP_SEE_OTHER);
    }
}

// TENTATIVE DE REDIRECTION DE LA ROUTE DE SUPPRESSION

    /**
     * @Route("/dog/{id<\d+>}", name="app_back_hobby_delete", methods={"POST"})
     * @ParamConverter("dog", options={"mapping": {"dog_id": "id"}})
     */
/*     public function delete(Request $request, Hobby $hobby, HobbyRepository $hobbyRepository, Dog $dog): Response
    {
        if ($this->isCsrfTokenValid('delete'.$hobby->getId(), $request->request->get('_token'))) {
            $hobbyRepository->remove($hobby, true);
        }

        return $this->redirectToRoute('app_back_hobby_indexhobbies', ['id' => $dog->getId()], Response::HTTP_SEE_OTHER);
    } */
