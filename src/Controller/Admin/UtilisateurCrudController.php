<?php
// src/Controller/Admin/UtilisateurCrudController.php

namespace App\Controller\Admin;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Administration des comptes utilisateurs.
 *
 * Le mot de passe etait auparavant expose en champ texte simple : le hachage
 * s'affichait en clair dans le formulaire, et toute valeur saisie etait
 * enregistree telle quelle, sans hachage. Il est desormais traite comme un
 * champ en ecriture seule, hache avant persistance.
 */
class UtilisateurCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly LoggerInterface $securiteLogger,
        private readonly Security $security,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Utilisateur::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('nom');
        yield TextField::new('prenom');
        yield EmailField::new('email');

        // Uniquement sur les pages de creation et de modification : le mot de
        // passe n'apparait jamais en liste ni en detail.
        if (in_array($pageName, [Crud::PAGE_NEW, Crud::PAGE_EDIT], true)) {
            yield TextField::new('plainPassword')
                ->setLabel('Mot de passe')
                ->setFormType(RepeatedType::class)
                ->setFormTypeOptions([
                    'type' => PasswordType::class,
                    'first_options' => ['label' => 'Mot de passe'],
                    'second_options' => ['label' => 'Confirmer le mot de passe'],
                    'invalid_message' => 'Les deux mots de passe ne correspondent pas.',
                    // En modification, un champ vide signifie « ne pas changer ».
                    'required' => $pageName === Crud::PAGE_NEW,
                    'mapped' => true,
                ])
                ->setHelp('Laisser vide en modification pour conserver le mot de passe actuel.')
                ->onlyOnForms();
        }

        yield ChoiceField::new('roles')
            ->setChoices([
                'Utilisateur' => 'ROLE_USER',
                'Administrateur' => 'ROLE_ADMIN',
            ])
            ->allowMultipleChoices()
            ->renderExpanded();

        yield DateTimeField::new('dateInscription')
            ->setFormTypeOptions(['disabled' => true])
            ->hideOnForm();
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Utilisateur) {
            $this->hacherMotDePasse($entityInstance);
            $this->journaliser('Compte cree depuis le back-office', $entityInstance);
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Utilisateur) {
            $this->hacherMotDePasse($entityInstance);
            $this->journaliser('Compte modifie depuis le back-office', $entityInstance);
        }

        parent::updateEntity($entityManager, $entityInstance);
    }

    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Utilisateur) {
            $this->journaliser('Compte supprime depuis le back-office', $entityInstance);
        }

        parent::deleteEntity($entityManager, $entityInstance);
    }

    /**
     * Hache le mot de passe saisi, puis efface immediatement sa version en
     * clair. Un champ laisse vide en modification conserve l'ancien hachage.
     */
    private function hacherMotDePasse(Utilisateur $utilisateur): void
    {
        $enClair = $utilisateur->getPlainPassword();

        if ($enClair === null || $enClair === '') {
            return;
        }

        $utilisateur->setPassword($this->passwordHasher->hashPassword($utilisateur, $enClair));
        $utilisateur->eraseCredentials();
    }

    /**
     * Toute action d'administration sur un compte est tracee : qui a agi, sur
     * quel compte, et quand. C'est la piste d'audit exigee en cas d'incident.
     */
    private function journaliser(string $message, Utilisateur $cible): void
    {
        $this->securiteLogger->info($message, [
            'cible' => $cible->getUserIdentifier(),
            'administrateur' => $this->security->getUser()?->getUserIdentifier() ?? 'inconnu',
        ]);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'Gestion des utilisateurs')
            ->setPageTitle(Crud::PAGE_NEW, 'Ajouter un utilisateur')
            ->setPageTitle(Crud::PAGE_EDIT, 'Modifier un utilisateur')
            ->setEntityLabelInSingular('Utilisateur')
            ->setEntityLabelInPlural('Utilisateurs')
            ->setDefaultSort(['dateInscription' => 'DESC']);
    }
}
