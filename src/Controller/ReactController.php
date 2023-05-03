<?php 

// Dans App\Controller\ReactController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReactController extends AbstractController
{
    private $pathPublicFolder;

    public function __construct(string  $publicFolderName)
    {
        $this->pathPublicFolder = __DIR__ . '/../../' . $publicFolderName;
    }

    /**
     * @Route("/{reactRouting}", name="app_react", priority="-1", defaults={"reactRouting": null}, requirements={"reactRouting"=".+"})
     */
    public function index()
    {
        // Get all js and css files generate by 'yarn build' 
        $fullPathJsFiles = glob($this->pathPublicFolder . '/js/*.js');
        $fullPathCssFiles = glob($this->pathPublicFolder . '/css/*.css');

        // Just take the name of the files
        $allJsFiles = [];
        $allCssFiles = [];
        foreach($fullPathJsFiles as $file){
            $allJsFiles[] = basename($file);
        }
        foreach($fullPathCssFiles as $file){
            $allCssFiles[] = basename($file);
        }

        return $this->render('react/index.html.twig', [
            'jsFiles' => $allJsFiles,
            'cssFiles' => $allCssFiles
        ]);
    }
}