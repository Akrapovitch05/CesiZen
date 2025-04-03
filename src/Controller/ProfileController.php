<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(Security $security): Response
    {
        // Vérifier si l'utilisateur est connecté
        $user = $security->getUser();

        // Si l'utilisateur n'est pas connecté, on le redirige vers la page de connexion
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Si l'utilisateur est connecté, on affiche son profil
        return $this->render('profile/profile.html.twig', [
            'user' => $user,
        ]);
    }
}