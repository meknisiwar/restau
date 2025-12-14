<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251206170000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add Coupon entity and loyaltyPoints to User';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE coupon (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(50) NOT NULL, type VARCHAR(20) NOT NULL, amount NUMERIC(10, 2) DEFAULT NULL, usage_limit INT NOT NULL, used_count INT NOT NULL, expires_at DATETIME DEFAULT NULL, active TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_COUPON_CODE (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE user ADD loyalty_points INT NOT NULL DEFAULT 0');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE coupon');
        $this->addSql('ALTER TABLE user DROP loyalty_points');
    }
}
