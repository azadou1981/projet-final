<?php

namespace App\Controller\Admin;

use App\Entity\Commande;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AsCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;

#[AsCrudController(dashboard: DashboardController::class)]
class CommandeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Commande::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('utilisateur', 'Client'),
            NumberField::new('total', 'Total (€)')->setNumDecimals(2),
            ChoiceField::new('statut', 'Statut')->setChoices([
                'En attente' => 'en_attente',
                'Payée' => 'payee',
                'Expédiée' => 'expediee',
                'Livrée' => 'livree',
            ]),
            AssociationField::new('produits', 'Produits')->hideOnIndex(),
            DateTimeField::new('dateCreation', 'Date')->hideOnForm(),
        ];
    }
}
