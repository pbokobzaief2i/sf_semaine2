<?php

namespace App\Controller;

use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class BookController extends AbstractController
{
    #[Route('/book-html', name: 'app_book_html')]
    public function listHtml(BookRepository $repository): Response
    {
        // récupération des livres en base
        $books = $repository->findAll();

        // rendu du template et renvoi de la réponse
        return $this->render('book/index.html.twig', [
            'books' => $books,
        ]);
    }

    #[Route('/book', name: 'list_book')]
    public function list(BookRepository $repository, SerializerInterface $serializer)
    {
        // récupération des livres en base
        $books = $repository->findAll();

        // transformation des objets en json via le serializer
        // cf schéma super important pour la compréhension:
        // https://symfony.com/doc/current/components/serializer.html 
        $serializedBooks = $serializer->serialize($books, 'json');        
        
        // on veut maintenant renvoyer cette chaine de caractères json
        $response = new Response($serializedBooks);
        // faut préciser le content-type dans les headers http de la réponse
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
