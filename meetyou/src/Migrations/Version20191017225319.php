<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191017225319 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('create index users_name_first_name_last_index on users (name_first, name_last);');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('drop index users_name_first_name_last_index on users;');
    }
}
