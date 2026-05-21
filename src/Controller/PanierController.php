<?php

namespace App\Controller;

use App\Service\GestionnairePanier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PanierController extends AbstractController
{
    #[Route('/panier', name: 'app_panier')]
    public function index(GestionnairePanier $gestionnairePanier): Response
    {
        return $this->render('panier/index.html.twig', [
            'panier' => $gestionnairePanier->obtenirPanier(),
            'total' => $gestionnairePanier->getTotal(),
        ]);
    }

    #[Route('/panier/ajouter/{id}', name: 'app_panier_ajouter')]
    public function ajouter(int $id, GestionnairePanier $gestionnairePanier): Response
    {
        $gestionnairePanier->ajouter($id);
        $this->addFlash('success', 'Produit ajouté au panier !');
        return $this->redirectToRoute('app_panier');
    }

    #[Route('/panier/supprimer/{id}', name: 'app_panier_supprimer')]
    public function supprimer(int $id, GestionnairePanier $gestionnairePanier): Response
    {
        $gestionnairePanier->supprimer($id);
        return $this->redirectToRoute('app_panier');
    }

    #[Route('/panier/commander', name: 'app_panier_commander')]
    public function commander(GestionnairePanier $gestionnairePanier): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render('panier/commander.html.twig', [
            'panier' => $gestionnairePanier->obtenirPanier(),
            'total' => $gestionnairePanier->getTotal(),
        ]);
    }
}
