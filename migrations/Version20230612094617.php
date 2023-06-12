<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230612094617 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create refresh token table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE bitbag_refresh_token (id INT AUTO_INCREMENT NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid DATETIME NOT NULL, remember_me TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_CD7BD0E9C74F2195 (refresh_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE bitbag_refresh_token');
    }
}
