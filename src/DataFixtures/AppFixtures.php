<?php

namespace App\DataFixtures;

use App\Entity\TrackerEmotion;
use App\Factory\ActiviteFactory;
use App\Factory\DiagnosticFactory;
use App\Factory\EmotionFactory;
use App\Factory\ExerciceFactory;
use App\Factory\FavoriActiviteFactory;
use App\Factory\SeanceFactory;
use App\Factory\TrackerEmotionFactory;
use App\Factory\UtilisateurFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        ActiviteFactory::createMany(50);
        DiagnosticFactory::createMany(50);
        EmotionFactory::createMany(50);
        ExerciceFactory::createMany(50);
        FavoriActiviteFactory::createMany(50);
        SeanceFactory::createMany(50);
        TrackerEmotionFactory::createMany(50);
        UtilisateurFactory::createMany(50);
        UtilisateurFactory::createOne([
            'email' =>'user@cesizen.com',
            'roles' => ['ROLE_USER']
        ]);
        UtilisateurFactory::createOne([
            'email' =>'admin@cesizen.com',
            'roles' => ['ROLE_ADMIN']
        ]);
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
