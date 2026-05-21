<?php

namespace App\DataFixtures;

use App\Entity\Commande;
use App\Entity\Produit;
use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // ========== 10 PRODUITS ==========
        $produitsData = [
            ['nom' => 'Bague Or 18K',        'prix' => 89.99,  'stock' => 10, 'desc' => 'Bague en or 18 carats, élégante et intemporelle.'],
            ['nom' => 'Collier Argent 925',   'prix' => 129.99, 'stock' => 5,  'desc' => 'Collier en argent sterling 925 avec pendentif minimaliste.'],
            ['nom' => 'Bracelet Cuir',        'prix' => 49.99,  'stock' => 15, 'desc' => 'Bracelet en cuir véritable, fait main.'],
            ['nom' => 'Boucles Perles',       'prix' => 59.99,  'stock' => 8,  'desc' => "Boucles d'oreilles ornées de perles d'eau douce naturelles."],
            ['nom' => 'Montre Classique',     'prix' => 199.99, 'stock' => 3,  'desc' => 'Montre analogique avec bracelet en cuir italien.'],
            ['nom' => 'Pendentif Cœur',       'prix' => 39.99,  'stock' => 20, 'desc' => 'Pendentif cœur en argent, idéal pour offrir.'],
            ['nom' => 'Bague Diamant',        'prix' => 299.99, 'stock' => 2,  'desc' => 'Bague sertie de diamants certifiés.'],
            ['nom' => 'Chaîne Or',            'prix' => 159.99, 'stock' => 7,  'desc' => 'Chaîne fine en or jaune, maille forçat.'],
            ['nom' => 'Collier Perles',       'prix' => 89.99,  'stock' => 12, 'desc' => 'Collier de perles blanches classiques.'],
            ['nom' => 'Bracelet Argent',      'prix' => 69.99,  'stock' => 10, 'desc' => 'Bracelet gourmette en argent massif.'],
        ];

        $produits = [];
        foreach ($produitsData as $data) {
            $produit = new Produit();
            $produit->setNom($data['nom']);
            $produit->setDescription($data['desc']);
            $produit->setPrix($data['prix']);
            $produit->setStock($data['stock']);
            $produit->setImage(null);
            $manager->persist($produit);
            $produits[] = $produit;
        }

        // ========== 2 UTILISATEURS DE TEST ==========
        $admin = new Utilisateur();
        $admin->setEmail('admin@example.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPrenom('Admin');
        $admin->setNom('Crystal');
        $admin->setAdresse('1 Rue du Bijou, 75000 Paris');
        $admin->setTelephone('0123456789');
        $admin->setMotDePasse(
            $this->passwordHasher->hashPassword($admin, 'admin123')
        );
        $manager->persist($admin);

        $client = new Utilisateur();
        $client->setEmail('client@example.com');
        $client->setRoles(['ROLE_USER']);
        $client->setPrenom('Marie');
        $client->setNom('Dupont');
        $client->setAdresse('25 Avenue des Champs, 69000 Lyon');
        $client->setTelephone('0612345678');
        $client->setMotDePasse(
            $this->passwordHasher->hashPassword($client, 'client123')
        );
        $manager->persist($client);

        // ========== 3 COMMANDES DE DÉMONSTRATION ==========
        // Commande 1 : admin, en_attente, 2 produits
        $commande1 = new Commande();
        $commande1->setUtilisateur($admin);
        $commande1->setStatut('en_attente');
        $commande1->setTotal($produits[0]->getPrix() + $produits[1]->getPrix());
        $commande1->addProduit($produits[0]);
        $commande1->addProduit($produits[1]);
        $manager->persist($commande1);

        // Commande 2 : client, payee, 3 produits
        $commande2 = new Commande();
        $commande2->setUtilisateur($client);
        $commande2->setStatut('payee');
        $commande2->setTotal(
            $produits[2]->getPrix() + $produits[3]->getPrix() + $produits[4]->getPrix()
        );
        $commande2->addProduit($produits[2]);
        $commande2->addProduit($produits[3]);
        $commande2->addProduit($produits[4]);
        $manager->persist($commande2);

        // Commande 3 : client, livree, 1 produit
        $commande3 = new Commande();
        $commande3->setUtilisateur($client);
        $commande3->setStatut('livree');
        $commande3->setTotal($produits[5]->getPrix());
        $commande3->addProduit($produits[5]);
        $manager->persist($commande3);

        $manager->flush();
    }
}
