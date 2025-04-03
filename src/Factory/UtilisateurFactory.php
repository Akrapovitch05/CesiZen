<?php

namespace App\Factory;

use App\Entity\Utilisateur;
use Zenstruck\Foundry\ModelFactory;

/** @extends ModelFactory<Utilisateur> */
final class UtilisateurFactory extends ModelFactory
{
    protected function getDefaults(): array
    {
        return [
            'nom' => self::faker()->lastName(),
            'prenom' => self::faker()->firstName(),
            'email' => self::faker()->unique()->email(),
            'password' => 'password',
            'dateInscription' => self::faker()->dateTimeBetween('-2 years', 'now'),
            'roles' => 'ROLE_USER',
        ];
    }
}
