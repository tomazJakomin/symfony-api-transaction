<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210805145855 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer_bonus_transactions DROP FOREIGN KEY FK_56E0C293B171EB6C');
        $this->addSql('DROP INDEX IDX_56E0C293B171EB6C ON customer_bonus_transactions');
        $this->addSql('ALTER TABLE customer_bonus_transactions CHANGE customer_id_id customer_id INT NOT NULL');
        $this->addSql('ALTER TABLE customer_bonus_transactions ADD CONSTRAINT FK_56E0C2939395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('CREATE INDEX IDX_56E0C2939395C3F3 ON customer_bonus_transactions (customer_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer_bonus_transactions DROP FOREIGN KEY FK_56E0C2939395C3F3');
        $this->addSql('DROP INDEX IDX_56E0C2939395C3F3 ON customer_bonus_transactions');
        $this->addSql('ALTER TABLE customer_bonus_transactions CHANGE customer_id customer_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE customer_bonus_transactions ADD CONSTRAINT FK_56E0C293B171EB6C FOREIGN KEY (customer_id_id) REFERENCES customer (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_56E0C293B171EB6C ON customer_bonus_transactions (customer_id_id)');
    }
}
