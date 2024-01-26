<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240121144438 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__recette AS SELECT id, nom, recommande, date FROM recette');
        $this->addSql('DROP TABLE recette');
        $this->addSql('CREATE TABLE recette (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, auteur_id INTEGER NOT NULL, nom VARCHAR(255) NOT NULL, recommande BOOLEAN NOT NULL, date DATETIME NOT NULL, CONSTRAINT FK_49BB639060BB6FE6 FOREIGN KEY (auteur_id) REFERENCES utilisateur (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO recette (id, nom, recommande, date) SELECT id, nom, recommande, date FROM __temp__recette');
        $this->addSql('DROP TABLE __temp__recette');
        $this->addSql('CREATE INDEX IDX_49BB639060BB6FE6 ON recette (auteur_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__recette AS SELECT id, nom, recommande, date FROM recette');
        $this->addSql('DROP TABLE recette');
        $this->addSql('CREATE TABLE recette (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, recommande BOOLEAN NOT NULL, date DATETIME NOT NULL)');
        $this->addSql('INSERT INTO recette (id, nom, recommande, date) SELECT id, nom, recommande, date FROM __temp__recette');
        $this->addSql('DROP TABLE __temp__recette');
    }
}
