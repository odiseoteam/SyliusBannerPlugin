<?php

declare(strict_types=1);

namespace Odiseo\SyliusBannerPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Sylius\Bundle\CoreBundle\Doctrine\Migrations\AbstractPostgreSQLMigration;

/** PostgreSQL twin of Version20250521005123. */
final class Version20250521005124 extends AbstractPostgreSQLMigration
{
    public function getDescription(): string
    {
        return 'Creates the banner tables.';
    }

    public function up(Schema $schema): void
    {
        // Before 2.2 there was no PostgreSQL migration: the tables may have been created by hand.
        $this->skipIf($schema->hasTable('odiseo_banner'), 'The banner tables are already there.');

        $this->addSql('CREATE SEQUENCE odiseo_banner_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE odiseo_banner_translation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE odiseo_banner (id INT NOT NULL, code VARCHAR(255) NOT NULL, enabled BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2FB97E5E77153098 ON odiseo_banner (code)');
        $this->addSql('CREATE TABLE odiseo_banners_channels (banner_id INT NOT NULL, channel_id INT NOT NULL, PRIMARY KEY(banner_id, channel_id))');
        $this->addSql('CREATE INDEX IDX_AAD9CFD0684EC833 ON odiseo_banners_channels (banner_id)');
        $this->addSql('CREATE INDEX IDX_AAD9CFD072F5A1AA ON odiseo_banners_channels (channel_id)');
        $this->addSql('CREATE TABLE odiseo_banners_taxons (banner_id INT NOT NULL, taxon_id INT NOT NULL, PRIMARY KEY(banner_id, taxon_id))');
        $this->addSql('CREATE INDEX IDX_FB5C1345684EC833 ON odiseo_banners_taxons (banner_id)');
        $this->addSql('CREATE INDEX IDX_FB5C1345DE13F470 ON odiseo_banners_taxons (taxon_id)');
        $this->addSql('CREATE TABLE odiseo_banner_translation (id INT NOT NULL, translatable_id INT NOT NULL, image_name VARCHAR(255) NOT NULL, mobile_image_name VARCHAR(255) DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, main_text VARCHAR(255) DEFAULT NULL, secondary_text VARCHAR(255) DEFAULT NULL, button_text VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, locale VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_BC61EAF72C2AC5D3 ON odiseo_banner_translation (translatable_id)');
        $this->addSql('CREATE UNIQUE INDEX odiseo_banner_translation_uniq_trans ON odiseo_banner_translation (translatable_id, locale)');
        $this->addSql('ALTER TABLE odiseo_banners_channels ADD CONSTRAINT FK_AAD9CFD0684EC833 FOREIGN KEY (banner_id) REFERENCES odiseo_banner (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE odiseo_banners_channels ADD CONSTRAINT FK_AAD9CFD072F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id)');
        $this->addSql('ALTER TABLE odiseo_banners_taxons ADD CONSTRAINT FK_FB5C1345684EC833 FOREIGN KEY (banner_id) REFERENCES odiseo_banner (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE odiseo_banners_taxons ADD CONSTRAINT FK_FB5C1345DE13F470 FOREIGN KEY (taxon_id) REFERENCES sylius_taxon (id)');
        $this->addSql('ALTER TABLE odiseo_banner_translation ADD CONSTRAINT FK_BC61EAF72C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES odiseo_banner (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE odiseo_banners_channels');
        $this->addSql('DROP TABLE odiseo_banners_taxons');
        $this->addSql('DROP TABLE odiseo_banner_translation');
        $this->addSql('DROP TABLE odiseo_banner');
        $this->addSql('DROP SEQUENCE odiseo_banner_id_seq');
        $this->addSql('DROP SEQUENCE odiseo_banner_translation_id_seq');
    }
}
