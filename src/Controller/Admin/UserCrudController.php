<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;


class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('fullName', 'Nom complet'),
            TextField::new('username', 'Nom d’utilisateur'),
            EmailField::new('email'),
            ChoiceField::new('roles')
                ->setLabel('Rôles')
                ->setChoices([
                    'Client' => 'ROLE_CLIENT',
                    'Administrateur' => 'ROLE_ADMIN',
                ])
                ->allowMultipleChoices()
                ->renderExpanded(false), 
        ];
    }
}
