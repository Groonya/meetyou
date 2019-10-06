<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191003225603 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('create table users ( id VARCHAR(36) not null, email varchar(255) not null, password_hash varchar(255) not null, name_first varchar(50) not null, name_last varchar(50) not null, birthday date not null, gender VARCHAR(50) not null, interests text not null, city varchar(100) not null, constraint users_pk primary key (id) ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('create unique index users_email_uindex on users (email)');

    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE users');
    }
}
