<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250331084433 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE activite (id INT AUTO_INCREMENT NOT NULL, favori_id INT NOT NULL, nom VARCHAR(50) NOT NULL, description VARCHAR(255) NOT NULL, duree INT NOT NULL, url_media VARCHAR(50) NOT NULL, INDEX IDX_B8755515FF17033F (favori_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE diagnostic (id INT AUTO_INCREMENT NOT NULL, score_stress INT NOT NULL, date_realisation DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE emotion (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, niveau1 VARCHAR(50) NOT NULL, niveau2 VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE exemplename (id INT AUTO_INCREMENT NOT NULL, propertynametest VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE exercice (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, duree_inspiration INT NOT NULL, duree_apnee INT NOT NULL, duree_expiration INT NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE favori_activite (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE seance (id INT AUTO_INCREMENT NOT NULL, date_realisation DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE tracker_emotion (id INT AUTO_INCREMENT NOT NULL, date_enregistrement DATETIME NOT NULL, niveau_emotion INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, tracker_id INT NOT NULL, seance_id INT NOT NULL, diagnostic_id INT NOT NULL, nom VARCHAR(50) NOT NULL, prenom VARCHAR(50) NOT NULL, email VARCHAR(50) NOT NULL, mdp VARCHAR(255) NOT NULL, date_inscription DATE NOT NULL, UNIQUE INDEX UNIQ_1D1C63B3E7927C74 (email), INDEX IDX_1D1C63B3FB5230B (tracker_id), INDEX IDX_1D1C63B3E3797A94 (seance_id), INDEX IDX_1D1C63B3224CCA91 (diagnostic_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', available_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', delivered_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE activite ADD CONSTRAINT FK_B8755515FF17033F FOREIGN KEY (favori_id) REFERENCES favori_activite (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3FB5230B FOREIGN KEY (tracker_id) REFERENCES tracker_emotion (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3E3797A94 FOREIGN KEY (seance_id) REFERENCES seance (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3224CCA91 FOREIGN KEY (diagnostic_id) REFERENCES diagnostic (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE activite DROP FOREIGN KEY FK_B8755515FF17033F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B3FB5230B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B3E3797A94
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B3224CCA91
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE activite
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE diagnostic
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE emotion
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE exemplename
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE exercice
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE favori_activite
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE seance
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE tracker_emotion
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE utilisateur
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
    }
}
