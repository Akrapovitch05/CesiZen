<?php
// src/Controller/Admin/UtilisateurCrudController.php

namespace App\Controller\Admin;

use App\Entity\Utilisateur;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Fields;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;

class UtilisateurCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Utilisateur::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('nom'),
            TextField::new('prenom'),
            EmailField::new('email'),
            ChoiceField::new('roles')
                ->setChoices([
                    'Utilisateur' => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN'
                ])
                ->allowMultipleChoices(),
            AssociationField::new('seance')
                ->setLabel('Séance')
                ->formatValue(fn($value) => $value ? $value->getDateRealisation()->format('Y-m-d') : 'Aucune séance'),

            AssociationField::new('diagnostic')
                ->setLabel('Diagnostic')
                ->formatValue(fn($value) => $value ? $value->getId() : 'Aucun diagnostic'), // Affiche l'ID du diagnostic
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'Gestion des utilisateurs')
            ->setPageTitle(Crud::PAGE_NEW, 'Ajouter un utilisateur')
            ->setPageTitle(Crud::PAGE_EDIT, 'Modifier un utilisateur');
    }
}
