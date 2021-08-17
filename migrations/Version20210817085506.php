<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210817085506 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CAB0FA336');
        $this->addSql('DROP INDEX IDX_9474526CAB0FA336 ON comment');
        $this->addSql('ALTER TABLE comment CHANGE answer_to_id replay_to_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CB04726D1 FOREIGN KEY (replay_to_id) REFERENCES comment (id)');
        $this->addSql('CREATE INDEX IDX_9474526CB04726D1 ON comment (replay_to_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CB04726D1');
        $this->addSql('DROP INDEX IDX_9474526CB04726D1 ON comment');
        $this->addSql('ALTER TABLE comment CHANGE replay_to_id answer_to_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CAB0FA336 FOREIGN KEY (answer_to_id) REFERENCES comment (id)');
        $this->addSql('CREATE INDEX IDX_9474526CAB0FA336 ON comment (answer_to_id)');
    }
}
