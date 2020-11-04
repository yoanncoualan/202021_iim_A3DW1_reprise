<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/article")
 */
class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="article")
     */
    public function index(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();

        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->persist($article);
            $em->flush();

            $this->addFlash('success', 'Article ajouté');
        }

        $articles = $em->getRepository(Article::class)->findAll();

        return $this->render('article/index.html.twig', [
            'articles' => $articles,
            'ajout' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}", name="show_article")
     */
    public function show(Article $article = null, Request $request){
        if($article == null){
            $this->addFlash('erreur', 'Article introuvable');
            return $this->redirectToRoute('article');
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            $this->addFlash('success', 'Article modifié');
        }

        return $this->render('article/show.html.twig', [
            'article' => $article,
            'maj' => $form->createView()
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete_article")
     */
    public function delete(Article $article = null){
        if($article == null){
            $this->addFlash('erreur', 'Article introuvable');
            return $this->redirectToRoute('article');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($article);
        $em->flush();

        $this->addFlash('success', 'Article supprimé');
        return $this->redirectToRoute('article');
    }
}
