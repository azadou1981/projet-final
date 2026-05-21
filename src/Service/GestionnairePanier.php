<?php

namespace App\Service;

use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class GestionnairePanier
{
    private \Symfony\Component\HttpFoundation\Session\SessionInterface $session;
    private \App\Repository\ProduitRepository $produitRepository;

    public function __construct(RequestStack $requestStack, ProduitRepository $produitRepository)
    {
        $this->session = $requestStack->getSession();
        $this->produitRepository = $produitRepository;
    }

    /**
     * Ajoute un produit au panier (ou augmente la quantité)
     */
    public function ajouter(int $produitId, int $quantite = 1): void
    {
        $panier = $this->session->get('panier', []);

        if (isset($panier[$produitId])) {
            $panier[$produitId] += $quantite;
        } else {
            $panier[$produitId] = $quantite;
        }

        $this->session->set('panier', $panier);
    }

    /**
     * Supprime un produit du panier
     */
    public function supprimer(int $produitId): void
    {
        $panier = $this->session->get('panier', []);
        unset($panier[$produitId]);
        $this->session->set('panier', $panier);
    }

    /**
     * Récupère le panier complet avec les objets Produit
     * Retourne : [{ produit: Produit, quantite: int, total: float }]
     */
    public function obtenirPanier(): array
    {
        $panier = $this->session->get('panier', []);
        $panierComplet = [];

        foreach ($panier as $produitId => $quantite) {
            $produit = $this->produitRepository->find($produitId);
            if ($produit) {
                $panierComplet[] = [
                    'produit' => $produit,
                    'quantite' => $quantite,
                    'total' => $produit->getPrix() * $quantite,
                ];
            }
        }

        return $panierComplet;
    }

    /**
     * Calcule le total du panier
     */
    public function getTotal(): float
    {
        $total = 0;
        foreach ($this->obtenirPanier() as $item) {
            $total += $item['total'];
        }
        return $total;
    }

    /**
     * Vide le panier
     */
    public function vider(): void
    {
        $this->session->remove('panier');
    }

    /**
     * Retourne le nombre total d'articles dans le panier
     */
    public function getNombreArticles(): int
    {
        return array_sum($this->session->get('panier', []));
    }
}
