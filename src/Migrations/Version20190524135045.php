<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190524135045 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE acte ALTER date_decision TYPE DATE');
        $this->addSql('ALTER TABLE acte ALTER date_decision DROP DEFAULT');
        $this->addSql('ALTER TABLE acte ALTER date_effectivite_debut TYPE DATE');
        $this->addSql('ALTER TABLE acte ALTER date_effectivite_debut DROP DEFAULT');
        $this->addSql('ALTER TABLE acte ALTER date_effectivite_fin TYPE DATE');
        $this->addSql('ALTER TABLE acte ALTER date_effectivite_fin DROP DEFAULT');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE acte ALTER date_decision TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE acte ALTER date_decision DROP DEFAULT');
        $this->addSql('ALTER TABLE acte ALTER date_effectivite_debut TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE acte ALTER date_effectivite_debut DROP DEFAULT');
        $this->addSql('ALTER TABLE acte ALTER date_effectivite_fin TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE acte ALTER date_effectivite_fin DROP DEFAULT');
    }
}
