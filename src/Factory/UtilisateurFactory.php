<?php

namespace App\Factory;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityRepository;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @extends PersistentProxyObjectFactory<Utilisateur>
 */
final class UtilisateurFactory extends PersistentProxyObjectFactory
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public static function class(): string
    {
        return Utilisateur::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'dateInscription' => self::faker()->dateTime(),
            'diagnostic' => DiagnosticFactory::new(),
            'email' => self::faker()->email(),
            'password' => $this->passwordHasher->hashPassword(new Utilisateur(), 'password'), // Hachage du mot de passe
            'nom' => self::faker()->word(),
            'prenom' => self::faker()->word(),
            'seance' => SeanceFactory::new(),
            'tracker' => TrackerEmotionFactory::new(),
            'roles' => self::faker()->randomElement([['ROLE_USER'], ['ROLE_ADMIN'], []]), // Anonyme, User ou Admin
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Utilisateur $utilisateur): void {})
            ;
    }
}
