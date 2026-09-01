<?php

namespace App\Factory;

use App\Entity\Exercice;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Exercice>
 */
final class ExerciceFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Exercice::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'nom' => self::faker()->unique()->word(),
            'dureeInspiration' => self::faker()->numberBetween(3, 10),
            'dureeApnee' => self::faker()->numberBetween(0, 5),
            'dureeExpiration' => self::faker()->numberBetween(3, 10),
            'description' => self::faker()->sentence(),
        ];
    }
}
