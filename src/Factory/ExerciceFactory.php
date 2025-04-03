<?php

namespace App\Factory;

use App\Entity\Exercice;
use Zenstruck\Foundry\ModelFactory;

/** @extends ModelFactory<Exercice> */
final class ExerciceFactory extends ModelFactory
{
    protected function getDefaults(): array
    {
        return [
            'nom' => self::faker()->word(),
            'dureeInspiration' => self::faker()->numberBetween(3, 10),
            'dureeApnee' => self::faker()->numberBetween(0, 5),
            'dureeExpiration' => self::faker()->numberBetween(3, 10),
            'description' => self::faker()->sentence(),
        ];
    }
}
