<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190520144150 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE action_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE type_action_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE action (id INT NOT NULL, fk_type_id INT NOT NULL, fk_acte_id INT NOT NULL, fk_utilisateur_id INT NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_47CC8C923563B1BF ON action (fk_type_id)');
        $this->addSql('CREATE INDEX IDX_47CC8C92574885EB ON action (fk_acte_id)');
        $this->addSql('CREATE INDEX IDX_47CC8C928E8608A6 ON action (fk_utilisateur_id)');
        $this->addSql('CREATE TABLE type_action (id INT NOT NULL, libelle VARCHAR(63) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_641BE7AAA4D60759 ON type_action (libelle)');
        $this->addSql('ALTER TABLE action ADD CONSTRAINT FK_47CC8C923563B1BF FOREIGN KEY (fk_type_id) REFERENCES type_action (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE action ADD CONSTRAINT FK_47CC8C92574885EB FOREIGN KEY (fk_acte_id) REFERENCES acte (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE action ADD CONSTRAINT FK_47CC8C928E8608A6 FOREIGN KEY (fk_utilisateur_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE acte DROP CONSTRAINT fk_9ec413261a54add1');
        $this->addSql('DROP INDEX idx_9ec413261a54add1');
        $this->addSql('ALTER TABLE acte DROP fk_createur_id');
        $this->addSql('ALTER TABLE acte DROP date_ajout');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE action DROP CONSTRAINT FK_47CC8C923563B1BF');
        $this->addSql('DROP SEQUENCE action_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE type_action_id_seq CASCADE');
        $this->addSql('DROP TABLE action');
        $this->addSql('DROP TABLE type_action');
        $this->addSql('ALTER TABLE acte ADD fk_createur_id INT NOT NULL');
        $this->addSql('ALTER TABLE acte ADD date_ajout TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE acte ADD CONSTRAINT fk_9ec413261a54add1 FOREIGN KEY (fk_createur_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_9ec413261a54add1 ON acte (fk_createur_id)');
    }
}
