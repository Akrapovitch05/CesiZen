<?php
// src/Controller/RegistrationController.php

namespace App\Controller;

use App\Entity\Diagnostic;
use App\Entity\Seance;
use App\Entity\TrackerEmotion;
use App\Entity\Utilisateur;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
    #[Route('/inscription', name: 'app_registration')]
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new Utilisateur();
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encoder le mot de passe avec UserPasswordHasherInterface
            $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);
            // Ajouter la date d'inscription (par défaut, date du jour)
            $user->setDateInscription(new \DateTime());
            // Ajouter un rôle par défaut (ROLE_USER)
            $user->setRoles(['ROLE_USER']);
            $defaultTracker = $entityManager->getRepository(TrackerEmotion::class)->find(1); // ID d’un tracker par défaut
            $user->setTracker($defaultTracker);
            $defaultSeance = $entityManager->getRepository(Seance::class)->find(1);
            if (!$defaultSeance) {
                throw new \Exception("Aucune séance par défaut trouvée ! Ajoutez-en une dans la base.");
            }
            $user->setSeance($defaultSeance);
            $defaultDiagnostic = $entityManager->getRepository(Diagnostic::class)->find(1);
            if (!$defaultDiagnostic) {
                throw new \Exception("Aucun diagnostic par défaut trouvé ! Ajoutez-en un dans la base.");
            }
            $user->setDiagnostic($defaultDiagnostic);



            // Sauvegarder l'utilisateur
            $entityManager->persist($user);
            $entityManager->flush();

            // Rediriger l'utilisateur vers la page d'accueil ou une autre page
            return $this->redirectToRoute('app_home');
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
