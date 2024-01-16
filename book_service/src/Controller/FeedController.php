<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FeedController extends AbstractController
{
    #[Route('/feed', name: 'app_feed')]
    public function index(): Response
    {
        $templateParams = ['titreDeMaPage' => "Homepage des flux d'acutalité"];
        return $this->render('feed/feedhome.html.twig', $templateParams);
    }

    #[Route('/feed/{username}', name: 'app_user_feed')]
    public function userFeed(string $username)
    {
        return $this->render('feed/user.html.twig',  [
            'titreDeMaPage' => "Flux actu $username",
            'username' => $username
        ]);
    }
}
