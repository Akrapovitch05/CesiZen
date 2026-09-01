<?php

namespace App\Factory;

use App\Entity\Utilisateur;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * Fabrique de comptes utilisateurs pour les tests et les jeux de donnees.
 *
 * @extends PersistentProxyObjectFactory<Utilisateur>
 */
final class UtilisateurFactory extends PersistentProxyObjectFactory
{
    /**
     * Mot de passe en clair partage par tous les comptes generes, pour
     * pouvoir s'authentifier dans les tests fonctionnels. Il respecte la
     * politique de l'application (12 caracteres, casse mixte, chiffre,
     * caractere special) afin que les tests valident la vraie politique.
     */
    public const MOT_DE_PASSE = 'MotDePasse1!Test';

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    public static function class(): string
    {
        return Utilisateur::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'nom' => self::faker()->lastName(),
            'prenom' => self::faker()->firstName(),
            'email' => self::faker()->unique()->safeEmail(),
            'dateInscription' => \DateTime::createFromInterface(
                self::faker()->dateTimeBetween('-2 years', 'now')
            ),
            'roles' => ['ROLE_USER'],
        ];
    }

    /**
     * Cree un compte administrateur.
     */
    public function administrateur(): static
    {
        return $this->with(['roles' => ['ROLE_ADMIN']]);
    }

    protected function initialize(): static
    {
        return $this->afterInstantiate(function (Utilisateur $utilisateur): void {
            // Le mot de passe est hache avec l'algorithme reellement configure,
            // pour que les tests d'authentification exercent le vrai mecanisme.
            $utilisateur->setPassword(
                $this->passwordHasher->hashPassword($utilisateur, self::MOT_DE_PASSE)
            );
        });
    }
}
