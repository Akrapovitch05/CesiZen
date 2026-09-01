<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration de reference du schema CESIZen.
 *
 * Les migrations precedentes produisaient un schema incompatible avec les
 * entites (colonnes mdp et est_admin la ou le code attend password et roles,
 * cles etrangeres inexistantes) : sur une base fraiche, l'authentification
 * etait impossible. Cette migration unique repart du mapping Doctrine, seule
 * source de verite du code, et garantit un deploiement reproductible.
 *
 * Les tables de jointure sont nommees explicitement plutot que Asso_5 et
 * asso_7 : une table comportant une majuscule fonctionne sous Windows, ou
 * MySQL ignore la casse, mais casse sous Linux, ou elle est significative.
 */
final class Version20260901093711 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Schema de reference : comptes utilisateurs, activites de detente, exercices de respiration et seances.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE activite (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, description VARCHAR(255) NOT NULL, duree INT NOT NULL, url_media VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE activite_exercice (activite_id INT NOT NULL, exercice_id INT NOT NULL, INDEX IDX_798016259B0F88B1 (activite_id), INDEX IDX_7980162589D40298 (exercice_id), PRIMARY KEY(activite_id, exercice_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE exercice (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, duree_inspiration INT NOT NULL, duree_apnee INT NOT NULL, duree_expiration INT NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE seance (id INT AUTO_INCREMENT NOT NULL, date_realisation DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE seance_exercice (seance_id INT NOT NULL, exercice_id INT NOT NULL, INDEX IDX_8A34735E3797A94 (seance_id), INDEX IDX_8A3473589D40298 (exercice_id), PRIMARY KEY(seance_id, exercice_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, id_seance INT DEFAULT NULL, nom VARCHAR(50) NOT NULL, prenom VARCHAR(50) NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, date_inscription DATE NOT NULL, roles JSON NOT NULL, UNIQUE INDEX UNIQ_1D1C63B3E7927C74 (email), INDEX IDX_1D1C63B3F94A48E3 (id_seance), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateur_activite (utilisateur_id INT NOT NULL, activite_id INT NOT NULL, INDEX IDX_A60EAC8AFB88E14F (utilisateur_id), INDEX IDX_A60EAC8A9B0F88B1 (activite_id), PRIMARY KEY(utilisateur_id, activite_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE activite_exercice ADD CONSTRAINT FK_798016259B0F88B1 FOREIGN KEY (activite_id) REFERENCES activite (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE activite_exercice ADD CONSTRAINT FK_7980162589D40298 FOREIGN KEY (exercice_id) REFERENCES exercice (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE seance_exercice ADD CONSTRAINT FK_8A34735E3797A94 FOREIGN KEY (seance_id) REFERENCES seance (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE seance_exercice ADD CONSTRAINT FK_8A3473589D40298 FOREIGN KEY (exercice_id) REFERENCES exercice (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3F94A48E3 FOREIGN KEY (id_seance) REFERENCES seance (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE utilisateur_activite ADD CONSTRAINT FK_A60EAC8AFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE utilisateur_activite ADD CONSTRAINT FK_A60EAC8A9B0F88B1 FOREIGN KEY (activite_id) REFERENCES activite (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE activite_exercice DROP FOREIGN KEY FK_798016259B0F88B1');
        $this->addSql('ALTER TABLE activite_exercice DROP FOREIGN KEY FK_7980162589D40298');
        $this->addSql('ALTER TABLE seance_exercice DROP FOREIGN KEY FK_8A34735E3797A94');
        $this->addSql('ALTER TABLE seance_exercice DROP FOREIGN KEY FK_8A3473589D40298');
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B3F94A48E3');
        $this->addSql('ALTER TABLE utilisateur_activite DROP FOREIGN KEY FK_A60EAC8AFB88E14F');
        $this->addSql('ALTER TABLE utilisateur_activite DROP FOREIGN KEY FK_A60EAC8A9B0F88B1');
        $this->addSql('DROP TABLE activite');
        $this->addSql('DROP TABLE activite_exercice');
        $this->addSql('DROP TABLE exercice');
        $this->addSql('DROP TABLE seance');
        $this->addSql('DROP TABLE seance_exercice');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP TABLE utilisateur_activite');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
