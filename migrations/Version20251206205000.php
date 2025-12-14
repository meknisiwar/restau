<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251206205000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create rsvp table for event registrations';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE rsvp (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, event_id INT NOT NULL, guests INT NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX uniq_user_event (user_id, event_id), INDEX IDX_9E3B0B3A76ED395 (user_id), INDEX IDX_9E3B0B3A2B36786 (event_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE rsvp ADD CONSTRAINT FK_9E3B0B3A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE rsvp ADD CONSTRAINT FK_9E3B0B3A2B36786 FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE rsvp DROP FOREIGN KEY FK_9E3B0B3A76ED395');
        $this->addSql('ALTER TABLE rsvp DROP FOREIGN KEY FK_9E3B0B3A2B36786');
        $this->addSql('DROP TABLE rsvp');
    }
}
