<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230612095046 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add index to sylius_product_attribute_value table on `locale_code`';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE INDEX locale_code ON sylius_product_attribute_value (locale_code)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX locale_code ON sylius_product_attribute_value');
    }
}
