<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Utilisateur;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires de l'entite Utilisateur.
 *
 * Cette classe porte la logique de contrôle d'acces : les roles qu'elle
 * renvoie determinent ce que l'utilisateur peut faire. Elle est testee en
 * isolation complete, sans base de donnees ni conteneur.
 */
final class UtilisateurTest extends TestCase
{
    public function testUnCompteSansRoleExpliciteObtientRoleUser(): void
    {
        $utilisateur = new Utilisateur();

        // Un compte sans role ne doit jamais se retrouver sans autorisation :
        // Symfony attend que tout utilisateur authentifie porte ROLE_USER.
        self::assertSame(['ROLE_USER'], $utilisateur->getRoles());
    }

    public function testUnAdministrateurConserveRoleUser(): void
    {
        $utilisateur = (new Utilisateur())->setRoles(['ROLE_ADMIN']);

        self::assertContains('ROLE_ADMIN', $utilisateur->getRoles());
        self::assertContains('ROLE_USER', $utilisateur->getRoles());
    }

    public function testLesRolesNeSontJamaisDupliques(): void
    {
        // ROLE_USER est ajoute automatiquement : le declarer explicitement ne
        // doit pas produire de doublon, qui fausserait les comparaisons.
        $utilisateur = (new Utilisateur())->setRoles(['ROLE_USER', 'ROLE_USER', 'ROLE_ADMIN']);

        $roles = $utilisateur->getRoles();

        self::assertSame(array_unique($roles), $roles);
        self::assertCount(2, $roles);
    }

    public function testPlusieursRolesSontConserves(): void
    {
        // L'ancien champ etait une chaine ne pouvant porter qu'un seul role :
        // ce test verrouille la correction.
        $utilisateur = (new Utilisateur())->setRoles(['ROLE_ADMIN', 'ROLE_MODERATEUR']);

        self::assertContains('ROLE_ADMIN', $utilisateur->getRoles());
        self::assertContains('ROLE_MODERATEUR', $utilisateur->getRoles());
    }

    public function testIsAdminRefleteLeRoleAdministrateur(): void
    {
        self::assertFalse((new Utilisateur())->isAdmin());
        self::assertFalse((new Utilisateur())->setRoles(['ROLE_USER'])->isAdmin());
        self::assertTrue((new Utilisateur())->setRoles(['ROLE_ADMIN'])->isAdmin());
    }

    public function testLIdentifiantDAuthentificationEstLAdresseElectronique(): void
    {
        $utilisateur = (new Utilisateur())->setEmail('marie@cesizen.fr');

        self::assertSame('marie@cesizen.fr', $utilisateur->getUserIdentifier());
    }

    public function testEffacerLesIdentifiantsSupprimeLeMotDePasseEnClair(): void
    {
        $utilisateur = (new Utilisateur())->setPlainPassword('MotDePasse1!Test');

        $utilisateur->eraseCredentials();

        // Un mot de passe en clair qui survivrait a l'authentification pourrait
        // etre serialise en session ou apparaitre dans une trace d'erreur.
        self::assertNull($utilisateur->getPlainPassword());
    }

    public function testEffacerLesIdentifiantsNAlterePasLeMotDePasseHache(): void
    {
        $hache = '$2y$13$abcdefghijklmnopqrstuv';
        $utilisateur = (new Utilisateur())
            ->setPassword($hache)
            ->setPlainPassword('MotDePasse1!Test');

        $utilisateur->eraseCredentials();

        self::assertSame($hache, $utilisateur->getPassword());
    }

    public function testLaDateDInscriptionEstInitialiseeALaCreation(): void
    {
        $utilisateur = new Utilisateur();

        self::assertInstanceOf(\DateTimeInterface::class, $utilisateur->getDateInscription());
    }

    public function testUneActiviteNEstJamaisAssocieeDeuxFois(): void
    {
        $utilisateur = new Utilisateur();
        $activite = new \App\Entity\Activite();

        $utilisateur->addActivite($activite);
        $utilisateur->addActivite($activite);

        self::assertCount(1, $utilisateur->getActivites());
    }

    public function testUneActivitePeutEtreRetiree(): void
    {
        $utilisateur = new Utilisateur();
        $activite = new \App\Entity\Activite();

        $utilisateur->addActivite($activite);
        $utilisateur->removeActivite($activite);

        self::assertCount(0, $utilisateur->getActivites());
    }
}
