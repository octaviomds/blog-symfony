<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    #[Route('/', name: 'blog_index')]
    public function index(EntityManagerInterface $em): Response
    {
        $articles = $em->getRepository(Article::class)
            ->findBy([], ['datePublication' => 'DESC']);

        return $this->render('blog/index.html.twig', [
            'articles' => $articles
        ]);
    }

    #[Route('/}', name: 'blog_show')]
    public function show(Article $article): Response
    {
        return $this->render('blog/show.html.twig', [
            'article' => $article
        ]);
    }

    #[Route('/', name: 'blog_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($article);
            $em->flush();

            $this->addFlash('success', 'Article publié avec succès !');
            return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
        }

        return $this->render('blog/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/', name: 'blog_admin')]
    public function admin(EntityManagerInterface $em): Response
    {
        $articles = $em->getRepository(Article::class)
            ->findBy([], ['datePublication' => 'DESC']);

        return $this->render('blog/admin.html.twig', [
            'articles' => $articles
        ]);
    }

    #[Route('/', name: 'blog_delete', methods: ['POST'])]
    public function delete(Article $article, EntityManagerInterface $em): Response
    {
        $em->remove($article);
        $em->flush();

        $this->addFlash('success', 'Article supprimé avec succès !');
        return $this->redirectToRoute('blog_admin');
    }
}
