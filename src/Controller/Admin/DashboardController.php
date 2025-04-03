<?php

namespace App\Controller\Admin;

use App\Entity\Activite;
use App\Entity\Diagnostic;
use App\Entity\Emotion;
use App\Entity\Exercice;
use App\Entity\Seance;
use App\Entity\Utilisateur;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        return $this->render('admin/my_dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('CesiZen - Admin');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToRoute('Retourner au site', 'fa fa-home', 'app_home');
        yield MenuItem::linkToDashboard('Tableau de bord', 'fa fa-home');
        yield MenuItem::linkToCrud('Utilisateurs', 'fa fa-users', Utilisateur::class);
        yield MenuItem::linkToCrud('Activités', 'fa fa-running', Activite::class);
        yield MenuItem::linkToCrud('Exercices', 'fa fa-dumbbell', Exercice::class);

        yield MenuItem::linkToCrud('Séances', 'fa fa-calendar', Seance::class);


    }

    // Corrected configureUserMenu method
    public function configureUserMenu(UserInterface $user): UserMenu
    {
        // Creating a UserMenu instance to customize the user menu
        return UserMenu::new()
            // Utilisation de addMenuItems() pour configurer le menu utilisateur
            ->addMenuItems([
                MenuItem::linkToLogout('Déconnexion', 'fa fa-sign-out')
            ]);
    }
}
