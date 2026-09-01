<?php

namespace App\DataFixtures;

use App\Entity\Activite;
use App\Entity\Exercice;
use App\Entity\Seance;
use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Jeu de donnees de demonstration.
 *
 * Charge dans les environnements de developpement et de test uniquement.
 * Les mots de passe respectent la politique de l'application, de sorte que
 * la demonstration exerce la vraie configuration de securite.
 *
 * Ces comptes ne doivent jamais etre charges en production : ce sont des
 * identifiants publics, connus de quiconque lit le depot.
 */
class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // --- Exercices de coherence cardiaque -------------------------------
        // Les trois rythmes imposes par le cahier des charges.
        $exercices = [];
        $definitions = [
            ['7-4-8', 7, 4, 8, 'Inspiration 7 s, apnee 4 s, expiration 8 s. Rythme apaisant, adapte a l\'endormissement.'],
            ['5-5', 5, 0, 5, 'Inspiration 5 s, expiration 5 s. Rythme de reference de la coherence cardiaque.'],
            ['4-6', 4, 0, 6, 'Inspiration 4 s, expiration 6 s. Expiration allongee, favorise la detente.'],
        ];

        foreach ($definitions as [$nom, $inspiration, $apnee, $expiration, $description]) {
            $exercice = (new Exercice())
                ->setNom($nom)
                ->setDureeInspiration($inspiration)
                ->setDureeApnee($apnee)
                ->setDureeExpiration($expiration)
                ->setDescription($description);

            $manager->persist($exercice);
            $exercices[$nom] = $exercice;
        }

        // --- Activites de detente -------------------------------------------
        $activites = [];
        $catalogue = [
            ['Yoga doux', 'Seance de yoga accessible a tous, centree sur la respiration et les etirements.', 30, 'https://www.youtube.com/watch?v=OG5Iu-SLno4'],
            ['Meditation guidee', 'Dix minutes de meditation guidee pour relacher les tensions.', 10, 'https://www.youtube.com/watch?v=inpok4MKVLM'],
            ['Marche consciente', 'Marche lente en pleine conscience, en exterieur de preference.', 20, 'https://www.youtube.com/watch?v=BjkPuChM8-4'],
            ['Etirements du soir', 'Sequence d\'etirements pour preparer le corps au sommeil.', 15, 'https://www.youtube.com/watch?v=6E5NKZ2Bw_A'],
        ];

        foreach ($catalogue as [$nom, $description, $duree, $url]) {
            $activite = (new Activite())
                ->setNom($nom)
                ->setDescription($description)
                ->setDuree($duree)
                ->setUrlMedia($url);

            $manager->persist($activite);
            $activites[] = $activite;
        }

        // Une activite peut proposer un exercice de respiration associe.
        $activites[0]->addExercice($exercices['5-5']);
        $activites[3]->addExercice($exercices['7-4-8']);

        // --- Seance de demonstration ----------------------------------------
        $seance = (new Seance())->setDateRealisation(new \DateTime());
        $seance->addExercice($exercices['5-5']);
        $manager->persist($seance);

        // --- Comptes ---------------------------------------------------------
        $comptes = [
            ['Kibout', 'Akram', 'admin@cesizen.fr', ['ROLE_ADMIN'], 'Admin1!Cesizen'],
            ['Dupont', 'Marie', 'marie@cesizen.fr', ['ROLE_USER'], 'User1!Cesizen'],
        ];

        foreach ($comptes as [$nom, $prenom, $email, $roles, $motDePasse]) {
            $utilisateur = (new Utilisateur())
                ->setNom($nom)
                ->setPrenom($prenom)
                ->setEmail($email)
                ->setRoles($roles)
                ->setDateInscription(new \DateTime())
                ->setSeance($seance);

            $utilisateur->setPassword(
                $this->passwordHasher->hashPassword($utilisateur, $motDePasse)
            );

            $utilisateur->addActivite($activites[0]);
            $manager->persist($utilisateur);
        }

        $manager->flush();
    }
}
