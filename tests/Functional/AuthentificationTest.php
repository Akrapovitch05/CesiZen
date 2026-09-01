<?php

namespace App\Tests\Functional;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Tests fonctionnels de l'authentification et du cloisonnement des roles.
 *
 * C'est le cœur du plan de securisation : ces tests verifient qu'un
 * utilisateur ne peut pas acceder a ce qui ne le concerne pas, sur une
 * application manipulant des donnees de sante mentale.
 */
final class AuthentificationTest extends WebTestCase
{
    private const MOT_DE_PASSE = 'MotDePasse1!Test';

    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->entityManager->createQuery('DELETE FROM App\Entity\Utilisateur u')->execute();
    }

    public function testUnUtilisateurPeutSeConnecterAvecSesIdentifiants(): void
    {
        $this->creerCompte('utilisateur@cesizen.fr', ['ROLE_USER']);

        $this->seConnecter('utilisateur@cesizen.fr', self::MOT_DE_PASSE);

        self::assertResponseRedirects();
        self::assertNotNull(
            static::getContainer()->get('security.token_storage')->getToken(),
            'Une session authentifiee doit exister apres connexion.'
        );
    }

    public function testUnMauvaisMotDePasseNAuthentifiePas(): void
    {
        $this->creerCompte('utilisateur@cesizen.fr', ['ROLE_USER']);

        $this->seConnecter('utilisateur@cesizen.fr', 'MauvaisMotDePasse1!');

        // L'echec renvoie vers la page de connexion, et le profil reste ferme.
        $this->client->request('GET', '/profile');
        self::assertResponseRedirects();
    }

    public function testUneAdresseInconnueNAuthentifiePas(): void
    {
        $this->seConnecter('inconnu@cesizen.fr', self::MOT_DE_PASSE);

        $this->client->request('GET', '/profile');
        self::assertResponseRedirects();
    }

    public function testLeFormulaireDeConnexionPorteUnJetonCsrf(): void
    {
        $crawler = $this->client->request('GET', '/login');

        // Sans jeton, un site tiers peut forcer la connexion d'un visiteur sur
        // un compte qu'il controle, puis observer ce que la victime y depose.
        self::assertGreaterThan(
            0,
            $crawler->filter('input[name="_csrf_token"]')->count(),
            'Le formulaire de connexion doit contenir un jeton CSRF.'
        );
    }

    public function testUnUtilisateurConnectePeutVoirSonProfil(): void
    {
        $utilisateur = $this->creerCompte('utilisateur@cesizen.fr', ['ROLE_USER']);

        $this->client->loginUser($utilisateur);
        $this->client->request('GET', '/profile');

        self::assertResponseIsSuccessful();
    }

    /**
     * Test de non-regression du cloisonnement : un utilisateur simple ne doit
     * en aucun cas atteindre le back-office.
     */
    public function testUnUtilisateurSimpleNAccedePasAuBackOffice(): void
    {
        $utilisateur = $this->creerCompte('utilisateur@cesizen.fr', ['ROLE_USER']);

        $this->client->loginUser($utilisateur);
        $this->client->request('GET', '/admin');

        self::assertResponseStatusCodeSame(403);
    }

    public function testUnAdministrateurAccedeAuBackOffice(): void
    {
        $administrateur = $this->creerCompte('admin@cesizen.fr', ['ROLE_ADMIN']);

        $this->client->loginUser($administrateur);
        $this->client->request('GET', '/admin');

        self::assertResponseIsSuccessful();
    }

    public function testUnAdministrateurConserveLAccesAuProfil(): void
    {
        // La hierarchie de roles doit donner a ROLE_ADMIN les droits de
        // ROLE_USER, sans quoi un administrateur perdrait l'acces au site.
        $administrateur = $this->creerCompte('admin@cesizen.fr', ['ROLE_ADMIN']);

        $this->client->loginUser($administrateur);
        $this->client->request('GET', '/profile');

        self::assertResponseIsSuccessful();
    }

    public function testUnVisiteurAnonymeEstRedirigeVersLaConnexion(): void
    {
        $this->client->request('GET', '/profile');

        self::assertResponseRedirects();
        self::assertStringContainsString(
            '/login',
            (string) $this->client->getResponse()->headers->get('Location')
        );
    }

    private function creerCompte(string $email, array $roles): Utilisateur
    {
        $utilisateur = (new Utilisateur())
            ->setNom('Dupont')
            ->setPrenom('Marie')
            ->setEmail($email)
            ->setRoles($roles)
            ->setDateInscription(new \DateTime());

        $utilisateur->setPassword(
            static::getContainer()->get(UserPasswordHasherInterface::class)
                ->hashPassword($utilisateur, self::MOT_DE_PASSE)
        );

        $this->entityManager->persist($utilisateur);
        $this->entityManager->flush();

        return $utilisateur;
    }

    private function seConnecter(string $email, string $motDePasse): void
    {
        $crawler = $this->client->request('GET', '/login');
        $formulaire = $crawler->selectButton('Se connecter')->form([
            '_username' => $email,
            '_password' => $motDePasse,
        ]);

        $this->client->submit($formulaire);
    }
}
