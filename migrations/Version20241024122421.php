<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241024122421 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE evenement_musical (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, date_de_debut DATETIME NOT NULL, date_de_fin DATETIME NOT NULL, prix DOUBLE PRECISION NOT NULL, adresse LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE partie_concert (id INT AUTO_INCREMENT NOT NULL, nom_artiste VARCHAR(255) NOT NULL, artiste_principal TINYINT(1) NOT NULL, date_de_debut DATETIME NOT NULL, date_de_fin DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE scene (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, nombre_max_participants INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE evenement_musical');
        $this->addSql('DROP TABLE partie_concert');
        $this->addSql('DROP TABLE scene');
    }
}
