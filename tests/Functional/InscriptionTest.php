<?php

namespace App\Tests\Functional;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Tests fonctionnels du parcours d'inscription.
 *
 * Verrouillent deux corrections majeures :
 * - l'inscription echouait systematiquement en erreur 500 sur une base
 *   fraiche, le controleur exigeant des donnees de reference codees en dur ;
 * - aucune contrainte ne s'appliquait au mot de passe.
 */
final class InscriptionTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);

        // Chaque test part d'une base sans comptes, pour rester independant
        // de l'ordre d'execution.
        $this->entityManager->createQuery('DELETE FROM App\Entity\Utilisateur u')->execute();
    }

    public function testLaPageDInscriptionEstAccessibleAuVisiteurAnonyme(): void
    {
        $this->client->request('GET', '/inscription');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('form');
    }

    /**
     * Test de non-regression du defaut le plus grave rencontre : sur une base
     * correctement migree, aucun compte ne pouvait etre cree.
     */
    public function testUneInscriptionValideCreeUnCompte(): void
    {
        $this->soumettreInscription('nouveau@cesizen.fr', 'MotDePasse1!Valide');

        $utilisateur = $this->trouverParEmail('nouveau@cesizen.fr');

        self::assertNotNull($utilisateur, 'Le compte doit etre cree en base.');
        self::assertResponseRedirects('/login');
    }

    public function testUnCompteCreeNObtientQueRoleUser(): void
    {
        $this->soumettreInscription('simple@cesizen.fr', 'MotDePasse1!Valide');

        $utilisateur = $this->trouverParEmail('simple@cesizen.fr');

        // Une inscription publique ne doit jamais pouvoir conferer de
        // privileges d'administration.
        self::assertNotNull($utilisateur);
        self::assertSame(['ROLE_USER'], $utilisateur->getRoles());
    }

    public function testLeMotDePasseEstStockeHache(): void
    {
        $motDePasse = 'MotDePasse1!Valide';
        $this->soumettreInscription('hache@cesizen.fr', $motDePasse);

        $utilisateur = $this->trouverParEmail('hache@cesizen.fr');

        self::assertNotNull($utilisateur);
        self::assertNotSame($motDePasse, $utilisateur->getPassword(), 'Le mot de passe ne doit jamais etre stocke en clair.');
        self::assertTrue(
            static::getContainer()->get(UserPasswordHasherInterface::class)
                ->isPasswordValid($utilisateur, $motDePasse),
            'Le hachage doit correspondre au mot de passe saisi.'
        );
    }

    /**
     * @dataProvider motsDePasseInvalides
     */
    public function testUnMotDePasseFaibleEstRefuse(string $motDePasse, string $raison): void
    {
        $this->soumettreInscription('faible@cesizen.fr', $motDePasse);

        self::assertNull(
            $this->trouverParEmail('faible@cesizen.fr'),
            sprintf('Un mot de passe %s ne doit pas permettre la creation d\'un compte.', $raison)
        );
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function motsDePasseInvalides(): array
    {
        return [
            'trop court' => ['Ab1!', 'trop court'],
            'sans majuscule' => ['motdepasse1!aa', 'sans majuscule'],
            'sans minuscule' => ['MOTDEPASSE1!AA', 'sans minuscule'],
            'sans chiffre' => ['MotDePasse!!aa', 'sans chiffre'],
            'sans caractere special' => ['MotDePasse1aaa', 'sans caractere special'],
        ];
    }

    public function testUneAdresseDejaUtiliseeEstRefusee(): void
    {
        $this->soumettreInscription('doublon@cesizen.fr', 'MotDePasse1!Valide');
        self::assertNotNull($this->trouverParEmail('doublon@cesizen.fr'));

        $this->soumettreInscription('doublon@cesizen.fr', 'AutreMotDePasse2!');

        self::assertCount(
            1,
            $this->entityManager->getRepository(Utilisateur::class)->findBy(['email' => 'doublon@cesizen.fr']),
            'Deux comptes ne peuvent pas partager la meme adresse electronique.'
        );
    }

    public function testUneConfirmationDifferenteEstRefusee(): void
    {
        $crawler = $this->client->request('GET', '/inscription');
        $formulaire = $crawler->selectButton('S\'inscrire')->form([
            'registration[nom]' => 'Martin',
            'registration[prenom]' => 'Luc',
            'registration[email]' => 'confirmation@cesizen.fr',
            'registration[plainPassword][first]' => 'MotDePasse1!Valide',
            'registration[plainPassword][second]' => 'MotDePasse2!Different',
        ]);
        $this->client->submit($formulaire);

        self::assertNull($this->trouverParEmail('confirmation@cesizen.fr'));
    }

    /**
     * Le formulaire doit porter un jeton CSRF : sans lui, un site tiers
     * pourrait creer des comptes a l'insu du visiteur.
     */
    public function testLeFormulaireEstProtegeContreLaFalsificationDeRequete(): void
    {
        $crawler = $this->client->request('GET', '/inscription');

        self::assertGreaterThan(
            0,
            $crawler->filter('input[name="registration[_token]"]')->count(),
            'Le formulaire d\'inscription doit contenir un jeton CSRF.'
        );
    }

    private function soumettreInscription(string $email, string $motDePasse): void
    {
        $crawler = $this->client->request('GET', '/inscription');
        $formulaire = $crawler->selectButton('S\'inscrire')->form([
            'registration[nom]' => 'Martin',
            'registration[prenom]' => 'Luc',
            'registration[email]' => $email,
            'registration[plainPassword][first]' => $motDePasse,
            'registration[plainPassword][second]' => $motDePasse,
        ]);

        $this->client->submit($formulaire);
    }

    private function trouverParEmail(string $email): ?Utilisateur
    {
        $this->entityManager->clear();

        /** @var UtilisateurRepository $depot */
        $depot = $this->entityManager->getRepository(Utilisateur::class);

        return $depot->findOneBy(['email' => $email]);
    }
}
