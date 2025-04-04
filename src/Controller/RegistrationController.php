<?php
// src/Controller/RegistrationController.php

namespace App\Controller;

use ApiPlatform\Metadata\ApiResource;
use App\Entity\Seance;
use App\Entity\Utilisateur;
use App\Entity\Activite;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
#[ApiResource]
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

            // Assigner une séance par défaut à l'utilisateur (si elle existe)
            $defaultSeance = $entityManager->getRepository(Seance::class)->find(1);
            if (!$defaultSeance) {
                throw new \Exception("Aucune séance par défaut trouvée ! Ajoutez-en une dans la base.");
            }
            $user->setSeance($defaultSeance);

            // Assigner des activités par défaut à l'utilisateur
            $activite1 = $entityManager->getRepository(Activite::class)->find(1); // ID d'une activité par défaut
            if ($activite1) {
                $user->addActivite($activite1);
            } else {
                throw new \Exception("Aucune activité par défaut trouvée ! Ajoutez-en une dans la base.");
            }

            // Sauvegarder l'utilisateur
            $entityManager->persist($user);
            $entityManager->flush();

            // Rediriger l'utilisateur vers la page d'accueil ou une autre page
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
