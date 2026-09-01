<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests fonctionnels des pages publiques et du contrôle d'accès.
 *
 * Ces tests constituent le socle de non-régression : ils garantissent
 * qu'aucune livraison ne casse l'accès aux pages ouvertes au public, ni
 * n'ouvre par erreur une page réservée.
 */
final class PagesPubliquesTest extends WebTestCase
{
    /**
     * Pages accessibles sans authentification (visiteur anonyme).
     *
     * @return array<string, array{string}>
     */
    public static function pagesPubliques(): array
    {
        return [
            'accueil' => ['/'],
            'connexion' => ['/login'],
            'inscription' => ['/inscription'],
            'catalogue des activites' => ['/activites'],
            'liste des exercices' => ['/exercices'],
        ];
    }

    /**
     * @dataProvider pagesPubliques
     */
    public function testUnePagePubliqueRepondSansAuthentification(string $url): void
    {
        $client = static::createClient();
        $client->request('GET', $url);

        self::assertResponseIsSuccessful(
            sprintf('La page publique %s doit rester accessible a un visiteur anonyme.', $url)
        );
    }

    /**
     * Pages réservées : un visiteur anonyme doit être redirigé, jamais servi.
     *
     * @return array<string, array{string}>
     */
    public static function pagesProtegees(): array
    {
        return [
            'back-office' => ['/admin'],
            'profil utilisateur' => ['/profile'],
        ];
    }

    /**
     * @dataProvider pagesProtegees
     */
    public function testUnePageProtegeeRefuseUnVisiteurAnonyme(string $url): void
    {
        $client = static::createClient();
        $client->request('GET', $url);

        // Le contrôle d'accès doit rediriger vers la connexion. Un code 200
        // signifierait que la page a été servie sans authentification.
        self::assertResponseRedirects(
            expectedCode: 302,
            message: sprintf('La page %s ne doit pas etre servie a un visiteur anonyme.', $url)
        );
    }

    /**
     * Une URL inexistante doit produire un 404, et non une erreur serveur :
     * une 500 exposerait une trace technique exploitable par un attaquant.
     */
    public function testUneUrlInexistanteRenvoieUne404(): void
    {
        $client = static::createClient();
        $client->request('GET', '/cette-page-n-existe-pas');

        self::assertResponseStatusCodeSame(404);
    }
}
