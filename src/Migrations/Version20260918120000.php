<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260918120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds the banner position, so the order can be set from the admin panel.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE odiseo_banner ADD position INT DEFAULT 0 NOT NULL
        SQL);

        // Keeps the current order of existing installations.
        $this->addSql(<<<'SQL'
            UPDATE odiseo_banner SET position = id
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE odiseo_banner DROP position
        SQL);
    }
}
