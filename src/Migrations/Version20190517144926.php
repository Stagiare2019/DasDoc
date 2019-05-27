<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190517144926 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE UNIQUE INDEX UNIQ_9EC41326BC3AFBDF ON acte (nom_pdf)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9EC41326F55AE19E ON acte (numero)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6056D389A4D60759 ON etat_acte (libelle)');
        $this->addSql('ALTER TABLE famille_matiere RENAME COLUMN abbreviation TO abreviation');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5C7E5DA4A4D60759 ON famille_matiere (libelle)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5C7E5DA486B470F8 ON famille_matiere (abreviation)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9014574AA4D60759 ON matiere (libelle)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9014574A86B470F8 ON matiere (abreviation)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4496AE75A4D60759 ON motcle (libelle)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_38F2A57AA4D60759 ON nature_acte (libelle)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_38F2A57A86B470F8 ON nature_acte (abreviation)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AB5111D4BC3AFBDF ON piece_jointe (nom_pdf)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E19D9AD2A4D60759 ON service (libelle)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E19D9AD286B470F8 ON service (abreviation)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6741C54DA4D60759 ON type_motcle (libelle)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX UNIQ_9EC41326BC3AFBDF');
        $this->addSql('DROP INDEX UNIQ_9EC41326F55AE19E');
        $this->addSql('DROP INDEX UNIQ_AB5111D4BC3AFBDF');
        $this->addSql('DROP INDEX UNIQ_6056D389A4D60759');
        $this->addSql('DROP INDEX UNIQ_38F2A57AA4D60759');
        $this->addSql('DROP INDEX UNIQ_38F2A57A86B470F8');
        $this->addSql('DROP INDEX UNIQ_E19D9AD2A4D60759');
        $this->addSql('DROP INDEX UNIQ_E19D9AD286B470F8');
        $this->addSql('DROP INDEX UNIQ_5C7E5DA4A4D60759');
        $this->addSql('DROP INDEX UNIQ_5C7E5DA486B470F8');
        $this->addSql('ALTER TABLE famille_matiere RENAME COLUMN abreviation TO abbreviation');
        $this->addSql('DROP INDEX UNIQ_9014574AA4D60759');
        $this->addSql('DROP INDEX UNIQ_9014574A86B470F8');
        $this->addSql('DROP INDEX UNIQ_6741C54DA4D60759');
        $this->addSql('DROP INDEX UNIQ_4496AE75A4D60759');
    }
}
