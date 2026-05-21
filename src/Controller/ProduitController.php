<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProduitController extends AbstractController
{
    #[Route('/boutique', name: 'app_boutique')]
    public function boutique(ProduitRepository $produitRepository): Response
    {
        $produits = $produitRepository->findBy([], ['dateCreation' => 'DESC']);

        return $this->render('produit/boutique.html.twig', [
            'produits' => $produits,
        ]);
    }

    #[Route('/produit/{id}', name: 'app_produit_voir')]
    public function voir(Produit $produit): Response
    {
        return $this->render('produit/voir.html.twig', [
            'produit' => $produit,
        ]);
    }
}
