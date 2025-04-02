<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class LoginPageController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        // Récupérer les erreurs de connexion (si il y en a)
        $error = $authenticationUtils->getLastAuthenticationError();

        // Récupérer le dernier email (nom d'utilisateur) saisi
        $lastUsername = $authenticationUtils->getLastUsername();

        // Si l'utilisateur est déjà connecté, redirigez vers la page d'accueil
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');  // Remplacez 'app_home' par votre route d'accueil
        }

        // Retourner la vue avec les informations nécessaires : erreur, email et autres
        return $this->render('login_page/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }
}
