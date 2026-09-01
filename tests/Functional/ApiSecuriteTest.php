<?php

namespace App\Tests\Functional;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Tests fonctionnels du controle d'acces de l'API.
 *
 * Verrouillent la vulnerabilite la plus grave identifiee : l'entite
 * Utilisateur portait un #[ApiResource] sans aucune regle d'acces, exposant
 * l'integralite des comptes a un visiteur anonyme sur une application
 * traitant des donnees de sante mentale.
 */
final class ApiSecuriteTest extends WebTestCase
{
    private const MOT_DE_PASSE = 'MotDePasse1!Test';
    private const ENTETES_JSONLD = ['HTTP_ACCEPT' => 'application/ld+json'];

    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->entityManager->createQuery('DELETE FROM App\Entity\Utilisateur u')->execute();
    }

    /**
     * @dataProvider ressourcesProtegees
     */
    public function testUneRessourceProtegeeRefuseUnVisiteurAnonyme(string $url): void
    {
        $this->client->request('GET', $url, [], [], self::ENTETES_JSONLD);

        // Le visiteur anonyme est redirige vers la connexion : dans tous les
        // cas, il ne doit jamais recevoir un 200 sur ces ressources.
        self::assertNotSame(
            200,
            $this->client->getResponse()->getStatusCode(),
            sprintf('%s ne doit jamais etre servie a un visiteur anonyme.', $url)
        );
    }

    /**
     * @dataProvider ressourcesProtegees
     */
    public function testUneRessourceProtegeeRefuseUnUtilisateurSimple(string $url): void
    {
        $this->client->loginUser($this->creerCompte('utilisateur@cesizen.fr', ['ROLE_USER']));
        $this->client->request('GET', $url, [], [], self::ENTETES_JSONLD);

        self::assertResponseStatusCodeSame(403);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function ressourcesProtegees(): array
    {
        return [
            'comptes utilisateurs' => ['/api/utilisateurs'],
            'seances' => ['/api/seances'],
        ];
    }

    /**
     * @dataProvider cataloguesPublics
     */
    public function testUnCataloguePublicResteLisibleAnonymement(string $url): void
    {
        $this->client->request('GET', $url, [], [], self::ENTETES_JSONLD);

        self::assertResponseIsSuccessful(
            sprintf('%s est un catalogue public : il doit rester accessible.', $url)
        );
    }

    /**
     * @return array<string, array{string}>
     */
    public static function cataloguesPublics(): array
    {
        return [
            'activites de detente' => ['/api/activites'],
            'exercices de respiration' => ['/api/exercices'],
        ];
    }

    public function testUnAdministrateurConsulteLesComptes(): void
    {
        $this->client->loginUser($this->creerCompte('admin@cesizen.fr', ['ROLE_ADMIN']));
        $this->client->request('GET', '/api/utilisateurs', [], [], self::ENTETES_JSONLD);

        self::assertResponseIsSuccessful();
    }

    /**
     * Meme pour un administrateur legitime, le hachage du mot de passe ne doit
     * jamais transiter : une fuite permettrait une attaque par dictionnaire
     * hors ligne sur l'ensemble des comptes.
     */
    public function testLApiNExposeJamaisLeMotDePasse(): void
    {
        $this->creerCompte('cible@cesizen.fr', ['ROLE_USER']);
        $this->client->loginUser($this->creerCompte('admin@cesizen.fr', ['ROLE_ADMIN']));

        $this->client->request('GET', '/api/utilisateurs', [], [], self::ENTETES_JSONLD);
        $contenu = (string) $this->client->getResponse()->getContent();

        self::assertResponseIsSuccessful();
        self::assertStringNotContainsString('password', $contenu);
        self::assertStringNotContainsString('$2y$', $contenu, 'Aucun hachage bcrypt ne doit apparaitre.');
    }

    public function testUnVisiteurAnonymeNePeutPasCreerUneActivite(): void
    {
        $this->client->request(
            'POST',
            '/api/activites',
            [],
            [],
            ['CONTENT_TYPE' => 'application/ld+json'],
            json_encode(['nom' => 'Intrusion', 'description' => 'test', 'duree' => 5, 'urlMedia' => 'https://exemple.fr'])
        );

        self::assertNotSame(
            201,
            $this->client->getResponse()->getStatusCode(),
            'L\'ecriture doit etre reservee aux administrateurs.'
        );
    }

    public function testUnUtilisateurSimpleNePeutPasCreerUneActivite(): void
    {
        $this->client->loginUser($this->creerCompte('utilisateur@cesizen.fr', ['ROLE_USER']));

        $this->client->request(
            'POST',
            '/api/activites',
            [],
            [],
            ['CONTENT_TYPE' => 'application/ld+json'],
            json_encode(['nom' => 'Intrusion', 'description' => 'test', 'duree' => 5, 'urlMedia' => 'https://exemple.fr'])
        );

        self::assertResponseStatusCodeSame(403);
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
}
