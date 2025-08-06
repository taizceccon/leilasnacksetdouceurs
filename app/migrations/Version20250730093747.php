<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250730093747 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE `order` ADD stripe_session_id VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE order_item ADD unit_price INT NOT NULL, DROP item, DROP subtotal, CHANGE product_id product_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX fulltext_index ON product
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE `order` DROP stripe_session_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE order_item ADD item VARCHAR(255) NOT NULL, ADD subtotal DOUBLE PRECISION NOT NULL, DROP unit_price, CHANGE product_id product_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE FULLTEXT INDEX fulltext_index ON product (titre, description)
        SQL);
    }
}
