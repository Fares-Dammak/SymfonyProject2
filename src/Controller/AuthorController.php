<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthorController extends AbstractController
{
    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }

    #[Route('/author/{name}', name: 'author')]
    public function showAuth(string $name): Response
    {
        return $this->render('author/index.html.twig', [
            'name' => $name,
        ]);
    }

    #[Route('/authorTable', name: 'authorTable')]
    public function listAuthors(): Response
    {
        $authors = array(
            array('id' => 1, 'picture' => 'images/victor_hugo.jpg', 'username' => 'Victor Hugo', 'email' => 'victor@gmail.com', 'nbBooks' => 100),
            array('id' => 3, 'picture' => 'images/taha.jpeg', 'username' => 'Taha', 'email' => 'taha@gmail.com', 'nbBooks' => 200),
            array('id' => 2, 'picture' => 'images/shikspear.jpeg', 'username' => 'Shakespeare', 'email' => 'shakespeare@gmail.com', 'nbBooks' => 300),
        );

        return $this->render('author/list.html.twig', [
            'authors' => $authors,
        ]);
    }

    #[Route('/author/details/{id}', name: 'author_details')]
    public function authorDetails(int $id): Response
    {
        $authors = [
            ['id' => 1, 'picture' => '/images/victor_hugo.jpg', 'username' => 'Victor Hugo', 'email' => 'victor.hugo@gmail.com', 'nb_books' => 100],
            ['id' => 2, 'picture' => '/images/shikspear.jpg', 'username' => 'Shakespeare', 'email' => 'william.shakespeare@gmail.com', 'nb_books' => 200],
            ['id' => 3, 'picture' => '/images/taha.jpeg', 'username' => 'Taha Hussein', 'email' => 'taha.hussein@gmail.com', 'nb_books' => 300],
        ];

        $author = array_filter($authors, fn($a) => $a['id'] === $id);

        if (empty($author)) {
            throw $this->createNotFoundException('Author not found');
        }

        $author = array_shift($author);

        return $this->render('author/details.html.twig', ['author' => $author]);
    }

    #[Route('/read', name: 'read')]
    public function read(AuthorRepository $rep): Response
    {
        $list = $rep->findAll();
        return $this->render('author/affiche.html.twig', [
            'authors' => $list,
        ]);
    }

    #[Route('/AddStatic', name: 'AddStatic')]
    public function AddStatic(EntityManagerInterface $em): Response
    {
        $author1 = new Author();
        $author1->setUsername("nour");
        $author1->setEmail("badraaaaNouNouuu");
        $em->persist($author1);
        $em->flush();

        return $this->redirectToRoute('read');
    }

    #[Route('/Add', name: 'Add')]
    public function Add(Request $req, EntityManagerInterface $em): Response
    {
        $author1 = new Author();
        $form = $this->createForm(AuthorType::class, $author1);
        $form->add('send', SubmitType::class);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($author1);
            $em->flush();
            return $this->redirectToRoute('read');
        }

        return $this->renderForm('author/ajout.html.twig', ["f" => $form]);
    }

    #[Route('/edit/{id}', name: 'edit')]
    public function edit(Request $req, EntityManagerInterface $em, AuthorRepository $rep, $id): Response
    {
        $author1 = $rep->find($id);

        if (!$author1) {
            $this->addFlash('error', 'Author not found.');
            return $this->redirectToRoute('read');
        }

        $form = $this->createForm(AuthorType::class, $author1);
        $form->add('edit', SubmitType::class);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($author1);
            $em->flush();
            return $this->redirectToRoute('read');
        }

        return $this->renderForm('author/edit.html.twig', ["f" => $form]);
    }

    #[Route('/remove/{id}', name: 'remove')]
    public function remove($id, EntityManagerInterface $em, AuthorRepository $rep): Response
    {
        $author1 = $rep->find($id);

        if (!$author1) {
            $this->addFlash('error', 'Author not found.');
            return $this->redirectToRoute('read');
        }

        $em->remove($author1);
        $em->flush();
        $this->addFlash('success', 'Author deleted.');
        return $this->redirectToRoute('read');
    }
}
