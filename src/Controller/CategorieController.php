<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class CategorieController extends AbstractController
{
    /**
     * @Route("/categorie", name="categorie")
     */
    public function index(Request $request, TranslatorInterface $trans): Response
    {
        $em = $this->getDoctrine()->getManager(); // Récupération de doctrine

        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request); // analyse la requete HTTP
        if($form->isSubmitted() && $form->isValid()){
            $em->persist($categorie); // prépare la sauvegarde
            $em->flush(); // execute la sauvegarde

            $this->addFlash(
                'success',
                $trans->trans('categorie.ajoutee')
            );
        }

        $categories = $em->getRepository(Categorie::class)->findAll();
        
        $categorieParDate = $em->getRepository(Categorie::class)->findByDate('2020-11-04');

        return $this->render('categorie/index.html.twig', [
            'categories' => $categories,
            'ajout' => $form->createView(),
            'categorieParDate' => $categorieParDate
        ]);
    }

    /**
     * @Route("/categorie/{id}", name="show")
     */
    public function show(Categorie $categorie = null, Request $request){ // converti automatiquement l'id en une catégorie
        if($categorie == null){ // On n'a pas trouvé de categorie correspondant à l'id
            $this->addFlash(
                'erreur',
                'La catégorie est introuvable'
            );
            return $this->redirectToRoute('categorie');
        }

        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($categorie);
            $em->flush();

            $this->addFlash(
                'success',
                'Catégorie mise à jour'
            );
        }

        return $this->render('categorie/show.html.twig', [
            'categorie' => $categorie,
            'maj' => $form->createView()
        ]);
    }

    /**
     * @Route("/categorie/delete/{id}", name="delete")
     */
    public function delete(Categorie $categorie = null){
        if($categorie == null){
            $this->addFlash(
                'erreur',
                'Catégorie introuvable'
            );
            return $this->redirectToRoute('categorie');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($categorie);
        $em->flush();

        $this->addFlash(
            'success',
            'Catégorie supprimée'
        );

        return $this->redirectToRoute('categorie');

    }
}
