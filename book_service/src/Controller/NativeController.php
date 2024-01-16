<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class NativeController
{
    #[Route('/au-revoir')]
    public function simpleText(): Response
    {
        return new Response("au revoir");
    }

    #[Route('/au-revoir-en-json')]
    public function simpleReponseJson(): Response
    {
        // On veut renvoyer ce contenu en précisant que c'est du json
        $monJson = '{"message" :"au revoir"}';
        
        // On aurait pu faire comme ça à la main 
        $responseSimple = new Response($monJson);
        $responseSimple = new Response();
        $responseSimple->setContent($monJson);
        $responseSimple->headers->set('Content-Type', 'application/json');
        // return $responseSimple
        
        // mais on va plutôt renvoyer une réponse JsonResponse
        $responseJson = new JsonResponse($monJson);
        return $responseSimple;
    }

    #[Route('/ancienne-url-de-au-revoir')]
        public function simpleRedirection(): Response
    {
        // On aurait pu faire à la main comme ça
        $response = new Response();
        $response->setStatusCode(Response::HTTP_FOUND);
        $response->headers->set('Location', 'http://google.com');
        // return $response

        // mais on va plutôt faire mieux, en utilisant symfony
        $response = new RedirectResponse('/au-revoir');
        return $response;
    }
}
