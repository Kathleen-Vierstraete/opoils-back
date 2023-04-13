``` PHP

<?php

namespace App\Controller\Api;

use App\Entity\Genre;
use App\Repository\GenreRepository;
use App\Repository\MovieRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GenreController extends AbstractController
{
    /**
     * @Route("/api/genres", name="api_genres_get", methods={"GET"})
     */
    public function getCollection(GenreRepository $genreRepository): Response
    {
        $genresList = $genreRepository->findAll();

        return $this->json($genresList, Response::HTTP_OK, [], ['groups' => 'get_genres_collection']);
    }

    /**
     * @Route("/api/genres/{id<\d+>}/movies", name="api_genres_get_movies", methods={"GET"})
     */
    public function getItemAndMovies(Genre $genre = null, MovieRepository $movieRepository): Response
    {
        // 404 ?
        if ($genre === null) {
            return $this->json(['error' => 'Genre non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        $moviesList = $genre->getMovies();
        //$moviesList = $movieRepository->findBy(['genres' => $genre]);

        // Tableau PHP à convertir en JSON
        $data = [
            'genre' => $genre,
            'movies' => $moviesList,
        ];

        return $this->json(
            $data,
            Response::HTTP_OK,
            [],
            [
                'groups' => [
                    // Le groupe des films
                    'get_movies_collection',
                    // Le groupe des genres
                    'get_genres_collection'
                ]
            ]);
    }
}
