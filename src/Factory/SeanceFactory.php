<?php

namespace App\Factory;

use App\Entity\Seance;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Seance>
 */
final class SeanceFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Seance::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'dateRealisation' => self::faker()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
