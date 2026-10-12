<?php

declare(strict_types=1);

namespace Odiseo\SyliusBannerPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Sylius\Bundle\CoreBundle\Doctrine\Migrations\AbstractPostgreSQLMigration;

/** PostgreSQL twin of Version20260918120000. */
final class Version20260918120001 extends AbstractPostgreSQLMigration
{
    public function getDescription(): string
    {
        return 'Adds the banner position, so the order can be set from the admin panel.';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf(
            $schema->hasTable('odiseo_banner') && $schema->getTable('odiseo_banner')->hasColumn('position'),
            'The position column is already there.',
        );

        $this->addSql('ALTER TABLE odiseo_banner ADD position INT DEFAULT 0 NOT NULL');

        // Keeps the current order of existing installations.
        $this->addSql('UPDATE odiseo_banner SET position = id');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE odiseo_banner DROP COLUMN position');
    }
}
