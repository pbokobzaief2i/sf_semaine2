<?php

namespace App\Controller;

use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    public function listHtml(BookRepository $repository): Response
    {
        $books = $repository->findAll();
        return $this->render('book/index.html.twig', [
            'books' => $books,
        ]);
    }
}
