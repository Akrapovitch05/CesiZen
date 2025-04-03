<?php

namespace App\Factory;

use App\Entity\Seance;
use Zenstruck\Foundry\ModelFactory;

/** @extends ModelFactory<Seance> */
final class SeanceFactory extends ModelFactory
{
    protected function getDefaults(): array
    {
        return [
            'dateRealisation' => self::faker()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
