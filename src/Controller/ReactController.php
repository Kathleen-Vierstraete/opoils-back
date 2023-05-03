<?php 

// Dans App\Controller\ReactController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ReactController extends AbstractController

{
    /**
     * @Route("/{reactRouting}", name="app_react", priority="-1", defaults={"reactRouting": null}, requirements={"reactRouting"=".+"})
     */
    public function index()
    {
        return $this->render('react/index.html.twig');
    }

}