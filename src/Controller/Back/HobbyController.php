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
     * route to display all hobbies
     * 
     * @Route("/", name="app_back_hobby_index", methods={"GET"})
     */
    public function index(HobbyRepository $hobbyRepository): Response
    {
        //returning a render as a 'twig view'
        return $this->render('back/hobby/index.html.twig', [

            //finding all hobbies
            'hobbies' => $hobbyRepository->findAll(),
            // 'dog' => $dog,
        ]);
    }

// ---------------- END OF METHOD ---------------------   

    /**
     *Route to display the hobbies of a dog in particular
     * 
     * @Route("/dog/{id<\d+>}", name="app_back_hobby_indexhobbies", methods={"GET"})
     */
    public function indexHobbies(HobbyRepository $hobbyRepository, Dog $dog): Response
    {
        //returning a render as a 'twig view'
        return $this->render('back/hobby/index.html.twig', [
            //finding the hobbies for a given dog
            'hobbies' => $hobbyRepository->findByDog($dog),
            //associating the dog to the hobbies
            'dog' => $dog,
        ]);
    }

// ---------------- END OF METHOD ---------------------   

    /**
     * Route to create a new hobby entity
     * 
     * @Route("/new/dog/{id<\d+>}", name="app_back_hobby_new", methods={"GET", "POST"})
     */
    public function new(Request $request, HobbyRepository $hobbyRepository, Dog $dog): Response
    {
        
        //creating the entity via the form 
        $hobby = new Hobby();
        $form = $this->createForm(HobbyType::class, $hobby);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //we associate the hobby to the current dog (in the route)
            $hobby->setDog($dog);

            //saving the entity
            $hobbyRepository->add($hobby, true);

            //redirection route
            return $this->redirectToRoute('app_back_hobby_indexhobbies', ['id' => $dog->getId()], Response::HTTP_SEE_OTHER);
        }

        //returning a render as a 'twig form'
        return $this->renderForm('back/hobby/new.html.twig', [
            'hobby' => $hobby,
            'form' => $form,
            'dog' => $dog
        ]);
    }

// ---------------- END OF METHOD ---------------------   

    /**
     * Route to display a given hobby
     * 
     * @Route("/{id}/show/dog/{dog_id<\d+>}", name="app_back_hobby_show", methods={"GET"})
     * @ParamConverter("dog", options={"mapping": {"dog_id": "id"}})
     */
    public function show(Hobby $hobby, Dog $dog): Response
    {
        // returning a render as a 'twig view'
        return $this->render('back/hobby/show.html.twig', [
            'hobby' => $hobby,
            'dog' => $dog,
        ]);
    }

// ---------------- END OF METHOD ---------------------   

    /**
     * Route to edit a hobby entity
     * 
     * @Route("/{id}/edit/dog/{dog_id<\d+>}", name="app_back_hobby_edit", methods={"GET", "POST"})
     * @ParamConverter("dog", options={"mapping": {"dog_id": "id"}})
     */
    public function edit(Request $request, Hobby $hobby, HobbyRepository $hobbyRepository, Dog $dog): Response
    {

        //retrieving the entity via the form 
        $form = $this->createForm(HobbyType::class, $hobby);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //saving the entity
            $hobbyRepository->add($hobby, true);

            //redirection route
            return $this->redirectToRoute('app_back_hobby_indexhobbies', ['id' => $dog->getId()], Response::HTTP_SEE_OTHER);
        }

        // returning a render as a 'twig form'
        return $this->renderForm('back/hobby/edit.html.twig', [
            'hobby' => $hobby,
            'form' => $form,
            'dog'=> $dog,
        ]);
    }

// ---------------- END OF METHOD ---------------------  

    /**
     * Route to delete a hobby entity
     * 
     * @Route("/{id}/dog/{dog_id<\d+>}", name="app_back_hobby_delete", methods={"POST"})
     * @ParamConverter("dog", options={"mapping": {"dog_id": "id"}})
     */
    public function delete(Request $request, Hobby $hobby, HobbyRepository $hobbyRepository, Dog $dog): Response
    {
        //making sure to get the token
        if ($this->isCsrfTokenValid('delete'.$hobby->getId(), $request->request->get('_token'))) {

            //deleting the entity
            $hobbyRepository->remove($hobby, true);
        }

        //redirection route
        return $this->redirectToRoute('app_back_hobby_indexhobbies', ['id' => $dog->getId()], Response::HTTP_SEE_OTHER);
    }

// ---------------- END OF METHOD ---------------------      

}


