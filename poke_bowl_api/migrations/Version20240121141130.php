<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240121141130 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE utilisateur (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, login VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL, adresse_email VARCHAR(255) NOT NULL, nom_photo_profil CLOB DEFAULT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1D1C63B3AA08CB10 ON utilisateur (login)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1D1C63B388D20D42 ON utilisateur (adresse_email)');
        $this->addSql('ALTER TABLE recette ADD COLUMN recommande BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE recette ADD COLUMN date DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('CREATE TEMPORARY TABLE __temp__recette AS SELECT id, nom FROM recette');
        $this->addSql('DROP TABLE recette');
        $this->addSql('CREATE TABLE recette (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, nom VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO recette (id, nom) SELECT id, nom FROM __temp__recette');
        $this->addSql('DROP TABLE __temp__recette');
    }
}
