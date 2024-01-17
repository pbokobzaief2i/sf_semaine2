<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Transport\Serialization\Serializer;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
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

    #[Route('/book', methods: ['GET'], name: 'list_book')]
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

    #[Route('/book/{id}', methods: ['GET'], name: 'find', requirements: ['id' => '\d+'])]
    public function getOne(
        BookRepository $repository,
        int $id
    ): Response {
        // Récupération d'un seul  livre par id, via la méthode dédiée du repository
        $book = $repository->find($id);

        if ($book === null) {
            return new Response(null, 404);
        }

        return $this->redirectToRoute('find_with_title', [
            'id' => $book->getId(),
            'title' => $book->getTitle(),
        ]);
    }

    /**
     * Cette route est optionnelle, elle est pour comprendre
     * le fonctionnement de symfony
     * Pas forcément à faire en exercice 
     */
    #[Route('/book/{title}-{id}', name: 'find_with_title')]
    public function getOneWithOne(
        BookRepository $repository,
        NormalizerInterface $normalizer,
        int $id
    ): Response {
        // Récupération d'un seul  livre par id, via la méthode dédiée du repository
        $book = $repository->find($id);

        if ($book === null) {
            return new Response(null, 404);
        }

        $bookArray = $normalizer->normalize($book);
        return new JsonResponse($bookArray);
    }

    #[Route('/book/{id}', methods: ['DELETE'], name: 'delete')]
    public function delete(
        BookRepository $repository,
        EntityManagerInterface $entityManager,
        $id
    ) {
        $book = $repository->find($id);

        if ($book === null) {
            return new JsonResponse([
                'status' => 'error',
                'reason' => 'book not found'
            ], 404);
        }

        // C'est l'entity manager qui a pour rôle de modifier la base
        // soit des enregistrements avec persist, 
        // soit des suppressions comme ici avec delete
        $entityManager->remove($book);

        // Le flush doit être appelé une fois, il effectue 
        // toutes les opérations précédemment demandées
        $entityManager->flush();

        $responseContents = [
            'status' => 'success',
            'message' => "Le livre qui s'appelait " . $book->getTitle() . " et qui avait l'id " . $book->getId() . " a été supprimé",
        ];
        return new JsonResponse($responseContents);
    }

    #[Route('/book', methods: ['POST'], name: 'create')]
    public function create(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager)
    {
        $json = $request->getContent();
        $book = $serializer->deserialize($json, Book::class, 'json');
        $entityManager->persist($book);
        $entityManager->flush();

        return new JsonResponse($serializer->serialize($book, 'json'));
    }

    #[Route('/book/{id}', methods: ['PUT'], name: 'create')]
    public function update(
        Request $request,
        $id,
        BookRepository $repository,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager
    ) {
        // on vérifie que ce livre existait déjà en base, et on le récupère
        $existingBook = $repository->find($id);
        if ($existingBook === null) {
            return new JsonResponse([
                'status' => 'error',
                'reason' => 'book not found'
            ], 404);
        }

        // Le contenu du livre à modifier, contenu dans le body de la requete HTTP
        $json = $request->getContent();

        // On récupère le livre décrit dans le json posté
        $newBook = $serializer->deserialize($json, Book::class, 'json');

        // on vérifie que le json envoyé décrit bien un id qui est le MEME que celui dans l'URL
        // Si c'est pas le cas, ce n'est pas cohérent car 
        // l'url dit "met à jour le livre 5" mais la donnée postée dit "je suis le livre 6"
        if($newBook->getId() !== $id) {
            return new JsonResponse([
                'message' => 'les ids ne correspondent pas', 
            ], Response::HTTP_BAD_REQUEST);
        }

        // On enregistre 
        $entityManager->persist($newBook);
        $entityManager->flush();

        return new JsonResponse($serializer->serialize($existingBook, 'json'));
    }
}
