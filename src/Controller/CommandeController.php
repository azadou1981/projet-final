<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Utilisateur;
use App\Service\GestionnairePanier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CommandeController extends AbstractController
{
    #[Route('/commande/confirmer', name: 'app_commande_confirmer')]
    public function confirmer(GestionnairePanier $gestionnairePanier, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $panier = $gestionnairePanier->obtenirPanier();

        if (empty($panier)) {
            return $this->redirectToRoute('app_boutique');
        }

        /** @var Utilisateur $utilisateur */
        $utilisateur = $this->getUser();

        $commande = new Commande();
        $commande->setUtilisateur($utilisateur);
        $commande->setTotal($gestionnairePanier->getTotal());

        foreach ($panier as $item) {
            $commande->addProduit($item['produit']);
        }

        $em->persist($commande);
        $em->flush();

        $gestionnairePanier->vider();
        $this->addFlash('success', 'Commande confirmée ! Merci !');

        return $this->redirectToRoute('app_commande_succes', ['id' => $commande->getId()]);
    }

    #[Route('/commande/{id}/succes', name: 'app_commande_succes')]
    public function succes(Commande $commande): Response
    {
        /** @var Utilisateur $utilisateur */
        $utilisateur = $this->getUser();

        $commandeUtilisateur = $commande->getUtilisateur();
        if (!$commandeUtilisateur || $commandeUtilisateur->getId() !== $utilisateur->getId()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('commande/succes.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('/profil/commandes', name: 'app_profil_commandes')]
    public function mesCommandes(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var Utilisateur $utilisateur */
        $utilisateur = $this->getUser();
        $commandes = $utilisateur->getCommandes();

        return $this->render('commande/mes_commandes.html.twig', [
            'commandes' => $commandes,
        ]);
    }
}
