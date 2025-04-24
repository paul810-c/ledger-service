<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250418125627 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE ledger_balances (currency VARCHAR(255) NOT NULL, ledger_id UUID NOT NULL, balance_in_cents BIGINT NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(ledger_id, currency))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_10B7E1F5A7B913DD ON ledger_balances (ledger_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN ledger_balances.ledger_id IS '(DC2Type:ledger_id)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN ledger_balances.updated_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE ledgers (id UUID NOT NULL, name VARCHAR(255) NOT NULL, currency VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN ledgers.id IS '(DC2Type:ledger_id)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN ledgers.created_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE transactions (id UUID NOT NULL, ledger_id UUID NOT NULL, type VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, amount_in_cents BIGINT NOT NULL, currency VARCHAR(3) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_EAA81A4CA7B913DD ON transactions (ledger_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN transactions.id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN transactions.ledger_id IS '(DC2Type:ledger_id)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN transactions.created_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE ledger_balances ADD CONSTRAINT FK_10B7E1F5A7B913DD FOREIGN KEY (ledger_id) REFERENCES ledgers (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE transactions ADD CONSTRAINT FK_EAA81A4CA7B913DD FOREIGN KEY (ledger_id) REFERENCES ledgers (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE ledger_balances DROP CONSTRAINT FK_10B7E1F5A7B913DD
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE transactions DROP CONSTRAINT FK_EAA81A4CA7B913DD
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE ledger_balances
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE ledgers
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE transactions
        SQL);
    }
}
