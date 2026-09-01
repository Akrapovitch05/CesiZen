<?php
// src/Controller/RegistrationController.php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\RegistrationType;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/inscription', name: 'app_registration', methods: ['GET', 'POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        LoggerInterface $securiteLogger,
        RateLimiterFactory $inscriptionLimiter,
    ): Response {
        $utilisateur = new Utilisateur();
        $form = $this->createForm(RegistrationType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Limite la creation de comptes en masse depuis une meme adresse,
            // qui servirait a saturer la base ou a tester des adresses volees.
            $limite = $inscriptionLimiter->create($request->getClientIp());
            if (!$limite->consume()->isAccepted()) {
                $securiteLogger->warning('Inscription : limite de debit atteinte', [
                    'ip' => $request->getClientIp(),
                ]);

                $this->addFlash('error', 'Trop de tentatives d\'inscription. Réessayez dans quelques minutes.');

                return $this->render('registration/register.html.twig', [
                    'form' => $form->createView(),
                ]);
            }

            $utilisateur
                ->setPassword($passwordHasher->hashPassword($utilisateur, (string) $utilisateur->getPlainPassword()))
                ->setDateInscription(new \DateTime())
                // Un compte cree publiquement n'obtient jamais autre chose que
                // ROLE_USER : l'elevation de privileges passe par le back-office.
                ->setRoles(['ROLE_USER']);

            // Le mot de passe en clair ne doit pas survivre au hachage.
            $utilisateur->eraseCredentials();

            try {
                $entityManager->persist($utilisateur);
                $entityManager->flush();
            } catch (UniqueConstraintViolationException) {
                // Deux inscriptions simultanees sur la meme adresse peuvent
                // passer la validation applicative ; la contrainte en base
                // reste le dernier rempart.
                $this->addFlash('error', 'Un compte existe déjà avec cette adresse électronique.');

                return $this->render('registration/register.html.twig', [
                    'form' => $form->createView(),
                ]);
            }

            $securiteLogger->info('Compte cree', [
                'utilisateur' => $utilisateur->getUserIdentifier(),
                'ip' => $request->getClientIp(),
            ]);

            $this->addFlash('success', 'Votre compte a bien été créé. Vous pouvez vous connecter.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
