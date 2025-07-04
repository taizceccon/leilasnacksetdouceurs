<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250703145654 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Étape 1 : mettre une valeur par défaut dans les lignes existantes qui sont NULL
        $this->addSql("UPDATE order_item SET item = 'Unnamed Item' WHERE item IS NULL");

        // Étape 2 : appliquer la contrainte NOT NULL
        $this->addSql("ALTER TABLE order_item CHANGE item item VARCHAR(255) NOT NULL");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE order_item CHANGE item item VARCHAR(255) DEFAULT NULL
        SQL);
    }
}
