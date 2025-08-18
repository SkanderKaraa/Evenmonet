<?php

namespace App\Controller\Admin;

use App\Entity\Annonce;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;




class AnnonceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Annonce::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('titre'),
            TextEditorField::new('description'),
            TextField::new('ville'),
            NumberField::new('prix'),       // <-- Utilise NumberField pour les nombres décimaux
            BooleanField::new('isApproved', 'Approuvé ?'),
            AssociationField::new('user')->onlyOnIndex(),
        ];
    }
}
