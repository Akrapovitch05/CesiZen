<?php

namespace App\Factory;

use App\Entity\Activite;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Activite>
 */
final class ActiviteFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Activite::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'nom' => self::faker()->unique()->words(2, true),
            'description' => self::faker()->text(200),
            'duree' => self::faker()->numberBetween(5, 60),
            'urlMedia' => self::faker()->url(),
        ];
    }
}
