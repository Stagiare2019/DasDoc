<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190612090826 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE acte_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE action_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE etat_acte_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE famille_matiere_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE matiere_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE motcle_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE nature_acte_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE piece_jointe_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE service_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE type_action_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE utilisateur_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE acte (id INT NOT NULL, fk_etat_id INT NOT NULL, fk_nature_id INT NOT NULL, fk_matiere_id INT NOT NULL, fk_service_id INT DEFAULT NULL, nom_pdf VARCHAR(255) NOT NULL, numero VARCHAR(63) NOT NULL, objet VARCHAR(255) NOT NULL, date_decision DATE NOT NULL, date_effectivite_debut DATE DEFAULT NULL, date_effectivite_fin DATE DEFAULT NULL, description TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9EC41326BC3AFBDF ON acte (nom_pdf)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9EC41326F55AE19E ON acte (numero)');
        $this->addSql('CREATE INDEX IDX_9EC41326FD71BBD3 ON acte (fk_etat_id)');
        $this->addSql('CREATE INDEX IDX_9EC413265A92F9DB ON acte (fk_nature_id)');
        $this->addSql('CREATE INDEX IDX_9EC4132640218CB ON acte (fk_matiere_id)');
        $this->addSql('CREATE INDEX IDX_9EC413261D326375 ON acte (fk_service_id)');
        $this->addSql('CREATE TABLE action (id INT NOT NULL, fk_type_id INT NOT NULL, fk_acte_id INT DEFAULT NULL, fk_utilisateur_id INT NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_47CC8C923563B1BF ON action (fk_type_id)');
        $this->addSql('CREATE INDEX IDX_47CC8C92574885EB ON action (fk_acte_id)');
        $this->addSql('CREATE INDEX IDX_47CC8C928E8608A6 ON action (fk_utilisateur_id)');
        $this->addSql('CREATE TABLE etat_acte (id INT NOT NULL, libelle VARCHAR(63) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6056D389A4D60759 ON etat_acte (libelle)');
        $this->addSql('CREATE TABLE famille_matiere (id INT NOT NULL, libelle VARCHAR(63) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5C7E5DA4A4D60759 ON famille_matiere (libelle)');
        $this->addSql('CREATE TABLE matiere (id INT NOT NULL, fk_famille_id INT NOT NULL, libelle VARCHAR(127) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9014574AA4D60759 ON matiere (libelle)');
        $this->addSql('CREATE INDEX IDX_9014574A67C9B117 ON matiere (fk_famille_id)');
        $this->addSql('CREATE TABLE motcle (id INT NOT NULL, libelle VARCHAR(63) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4496AE75A4D60759 ON motcle (libelle)');
        $this->addSql('CREATE TABLE motcle_acte (motcle_id INT NOT NULL, acte_id INT NOT NULL, PRIMARY KEY(motcle_id, acte_id))');
        $this->addSql('CREATE INDEX IDX_32EC2CF71D93C8D9 ON motcle_acte (motcle_id)');
        $this->addSql('CREATE INDEX IDX_32EC2CF7A767B8C7 ON motcle_acte (acte_id)');
        $this->addSql('CREATE TABLE nature_acte (id INT NOT NULL, libelle VARCHAR(63) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_38F2A57AA4D60759 ON nature_acte (libelle)');
        $this->addSql('CREATE TABLE piece_jointe (id INT NOT NULL, fk_acte_id INT NOT NULL, nom_pdf VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AB5111D4BC3AFBDF ON piece_jointe (nom_pdf)');
        $this->addSql('CREATE INDEX IDX_AB5111D4574885EB ON piece_jointe (fk_acte_id)');
        $this->addSql('CREATE TABLE service (id INT NOT NULL, libelle VARCHAR(63) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E19D9AD2A4D60759 ON service (libelle)');
        $this->addSql('CREATE TABLE type_action (id INT NOT NULL, libelle VARCHAR(63) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_641BE7AAA4D60759 ON type_action (libelle)');
        $this->addSql('CREATE TABLE utilisateur (id INT NOT NULL, email VARCHAR(127) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1D1C63B3E7927C74 ON utilisateur (email)');
        $this->addSql('ALTER TABLE acte ADD CONSTRAINT FK_9EC41326FD71BBD3 FOREIGN KEY (fk_etat_id) REFERENCES etat_acte (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE acte ADD CONSTRAINT FK_9EC413265A92F9DB FOREIGN KEY (fk_nature_id) REFERENCES nature_acte (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE acte ADD CONSTRAINT FK_9EC4132640218CB FOREIGN KEY (fk_matiere_id) REFERENCES matiere (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE acte ADD CONSTRAINT FK_9EC413261D326375 FOREIGN KEY (fk_service_id) REFERENCES service (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE action ADD CONSTRAINT FK_47CC8C923563B1BF FOREIGN KEY (fk_type_id) REFERENCES type_action (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE action ADD CONSTRAINT FK_47CC8C92574885EB FOREIGN KEY (fk_acte_id) REFERENCES acte (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE action ADD CONSTRAINT FK_47CC8C928E8608A6 FOREIGN KEY (fk_utilisateur_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE matiere ADD CONSTRAINT FK_9014574A67C9B117 FOREIGN KEY (fk_famille_id) REFERENCES famille_matiere (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE motcle_acte ADD CONSTRAINT FK_32EC2CF71D93C8D9 FOREIGN KEY (motcle_id) REFERENCES motcle (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE motcle_acte ADD CONSTRAINT FK_32EC2CF7A767B8C7 FOREIGN KEY (acte_id) REFERENCES acte (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE piece_jointe ADD CONSTRAINT FK_AB5111D4574885EB FOREIGN KEY (fk_acte_id) REFERENCES acte (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE action DROP CONSTRAINT FK_47CC8C92574885EB');
        $this->addSql('ALTER TABLE motcle_acte DROP CONSTRAINT FK_32EC2CF7A767B8C7');
        $this->addSql('ALTER TABLE piece_jointe DROP CONSTRAINT FK_AB5111D4574885EB');
        $this->addSql('ALTER TABLE acte DROP CONSTRAINT FK_9EC41326FD71BBD3');
        $this->addSql('ALTER TABLE matiere DROP CONSTRAINT FK_9014574A67C9B117');
        $this->addSql('ALTER TABLE acte DROP CONSTRAINT FK_9EC4132640218CB');
        $this->addSql('ALTER TABLE motcle_acte DROP CONSTRAINT FK_32EC2CF71D93C8D9');
        $this->addSql('ALTER TABLE acte DROP CONSTRAINT FK_9EC413265A92F9DB');
        $this->addSql('ALTER TABLE acte DROP CONSTRAINT FK_9EC413261D326375');
        $this->addSql('ALTER TABLE action DROP CONSTRAINT FK_47CC8C923563B1BF');
        $this->addSql('ALTER TABLE action DROP CONSTRAINT FK_47CC8C928E8608A6');
        $this->addSql('DROP SEQUENCE acte_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE action_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE etat_acte_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE famille_matiere_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE matiere_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE motcle_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE nature_acte_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE piece_jointe_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE service_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE type_action_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE utilisateur_id_seq CASCADE');
        $this->addSql('DROP TABLE acte');
        $this->addSql('DROP TABLE action');
        $this->addSql('DROP TABLE etat_acte');
        $this->addSql('DROP TABLE famille_matiere');
        $this->addSql('DROP TABLE matiere');
        $this->addSql('DROP TABLE motcle');
        $this->addSql('DROP TABLE motcle_acte');
        $this->addSql('DROP TABLE nature_acte');
        $this->addSql('DROP TABLE piece_jointe');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE type_action');
        $this->addSql('DROP TABLE utilisateur');
    }
}
