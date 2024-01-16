<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloController extends AbstractController
{
    #[Route('/bonjour', name: 'app_hello')]
    public function index(): Response
    {
        $monJson = '{"prenom" :"pierre", "nom": "bokobza"}';
        $response = new Response($monJson);

        $response->headers->set('Content-Type', 'application/json');

        return $response;

        // return $this->render('hello/index.html.twig', [
        //     'controller_name' => 'HelloController',
        // ]);
    }
}
