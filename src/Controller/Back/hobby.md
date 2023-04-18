``` PHP

/**
 * @Route("/back/hobby")
 */
class HobbyController extends AbstractController
{
    /**
     * @Route("/", name="app_back_hobby_index", methods={"GET"})
     */
    public function index(HobbyRepository $hobbyRepository, Dog $dog): Response
    {
        return $this->render('back/hobby/index.html.twig', [
            'hobbies' => $hobbyRepository->findAll(),
            'dog' => $dog,
        ]);
    }




    /**
 * @Route("/back/hobby")
 */
class HobbyController extends AbstractController
{
    /**
     * @Route("/", name="app_back_hobby_listhobbies", methods={"GET"})
     */
    public function listHobbies(HobbyRepository $hobbyRepository, Dog $dog, Member $memberRepository): Response
    {
        return $this->render('back/hobby/listhobbies.html.twig', [
            'hobbies' => $hobbyRepository->findAll(),
            'dog' => $dog,
            'member' => $member,
        ]);
    }



    <?php

namespace App\Controller\Back;

use App\Entity\Dog;
use App\Entity\Hobby;
use App\Entity\Member;
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

// TENTATIVE DE ROUTE HOBBIESLIST

    /**
     * @Route("/", name="app_back_hobby_list", methods={"GET"})
     */
    public function listHobbies(HobbyRepository $hobbyRepository, Dog $dog, Member $member): Response
    {
        $hobbies = $hobbyRepository->findAll();
        dd($hobbies);

        return $this->render('back/hobby/list.html.twig', [
            'hobbies' => $hobbyRepository->findAll(),
            'dog' => $dog,
            'member' => $member,
        ]);
    }
    
// FIN DE TENTATIVE DE ROUTE HOBBIESLIST 



    // /**
    //  * @Route("/", name="app_back_hobby_index", methods={"GET"})
    //  */
    public function index(HobbyRepository $hobbyRepository, Dog $dog): Response
    {
         return $this->render('back/hobby/index.html.twig', [
             'hobbies' => $hobbyRepository->findAll(),
             'dog' => $dog,
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



{% extends 'back/base_back.html.twig' %}

{% block title %}Listes des chiens{% endblock %}

{% block body %}
    <h1>Listes des chiens</h1><br>

    <table class="table table-hover ">
        <thead class="table-warning">
            <tr>
                <th>Id</th>
                <th>Nom</th>
                <th>Age</th>
                <th>Race</th>
                <th>Présentation</th>
                <th>Propriétaire</th>
                <th>Hobbys</th>
                <th>Photo</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        {% for dog in dogs %}
            <tr>
                <td>{{ dog.id }}</td>
                <td>{{ dog.name }}</td>
                <td>{{ dog.age }}</td>
                <td>{{ dog.race }}</td>
                <td>{{ dog.presentation }}</td>
                <td>{{ dog.member.pseudo }}</td>
                <td>
                {% for hobby in dog.hobbies %}
                    <ul>
                        <li> {{ hobby }}</li>
                    </ul>
                {% endfor %}
                </td>                

                <td>{# {{ dog.picture.picture }} #}</td>
                <td>
                    <a class="btn btn-sm btn-info" href="{{ path('app_back_dog_show', {'id': dog.id}) }}">Voir</a>
                    <a class="btn btn-sm btn-secondary" href="{{ path('app_back_dog_edit', {'id': dog.id}) }}">Editer</a>
                    <a class="btn btn-sm btn-success" href="{{ path('app_back_hobby_indexhobbies', {'id': dog.id}) }}">Hobbies</a>
                    {{ include('back/dog/_delete_form.html.twig') }}
                </td>
            </tr>
        {% else %}
            <tr>
                <td colspan="6">Pas de chiens enregistrés</td>
            </tr>
        {% endfor %}
        </tbody>
    </table>

    <a class="btn btn-warning" href="{{ path('app_back_dog_new') }}">Ajouter un nouveau chien</a>
{% endblock %}
