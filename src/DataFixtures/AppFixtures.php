<?php

namespace App\DataFixtures;

use App\Entity\Exercice;
use App\Entity\Seance;
use App\Entity\Utilisateur;
use App\Entity\Activite;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\Factory;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Création des exercices de respiration
        $exercices = [
            ['748', 7, 4, 8, 'Exercice de cohérence cardiaque avec rythme 7-4-8.'],
            ['55', 5, 0, 5, 'Exercice de cohérence cardiaque avec rythme 5-5.'],
            ['46', 4, 0, 6, 'Exercice de cohérence cardiaque avec rythme 4-6.']
        ];

        $exerciceEntities = [];
        foreach ($exercices as [$nom, $insp, $apnee, $exp, $desc]) {
            $exercice = new Exercice();
            $exercice->setNom($nom)
                ->setDureeInspiration($insp)
                ->setDureeApnee($apnee)
                ->setDureeExpiration($exp)
                ->setDescription($desc);
            $manager->persist($exercice);
            $exerciceEntities[] = $exercice;
        }

        // Création des séances
        $seance1 = new Seance();
        $seance1->setDateRealisation(new \DateTime('now'));
        $manager->persist($seance1);

        // Création des utilisateurs
        $user1 = new Utilisateur();
        $user1->setNom('kibout')
            ->setPrenom('akram')
            ->setEmail('akram@cesizen.com')
            ->setPassword($this->passwordHasher->hashPassword($user1, 'password'))
            ->setDateInscription(new \DateTime('now'))
            ->setRoles(['ROLE_ADMIN'])
            ->setSeance($seance1);
        $manager->persist($user1);

        // Création des activités
        $activite1 = new Activite();
        $activite1->setNom('Yoga')
            ->setDescription('Séance de relaxation.')
            ->setDuree(30)
            ->setUrlMedia('https://www.youtube.com/watch?v=OG5Iu-SLno4&ab_channel=YogaavecJo%C3%ABlle');
        $manager->persist($activite1);


        // Associations
        $user1->getActivites()->add($activite1);
        $seance1->getExercices()->add($exerciceEntities[0]);
        $activite1->getExercices()->add($exerciceEntities[1]);

        $manager->flush();
    }
}
