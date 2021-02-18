<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210216143734 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE contact_groupe (contact_id INT NOT NULL, groupe_id INT NOT NULL, INDEX IDX_942BBBA0E7A1254A (contact_id), INDEX IDX_942BBBA07A45358C (groupe_id), PRIMARY KEY(contact_id, groupe_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE contact_groupe ADD CONSTRAINT FK_942BBBA0E7A1254A FOREIGN KEY (contact_id) REFERENCES contact (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE contact_groupe ADD CONSTRAINT FK_942BBBA07A45358C FOREIGN KEY (groupe_id) REFERENCES groupe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE groupe ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE groupe ADD CONSTRAINT FK_4B98C21A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_4B98C21A76ED395 ON groupe (user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE contact_groupe');
        $this->addSql('ALTER TABLE groupe DROP FOREIGN KEY FK_4B98C21A76ED395');
        $this->addSql('DROP INDEX IDX_4B98C21A76ED395 ON groupe');
        $this->addSql('ALTER TABLE groupe DROP user_id');
    }
}
