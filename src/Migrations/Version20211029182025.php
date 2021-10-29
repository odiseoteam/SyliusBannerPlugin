<?php

declare(strict_types=1);

namespace Odiseo\SyliusBannerPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211029182025 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE odiseo_banner (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_2FB97E5E77153098 (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE odiseo_banners_channels (banner_id INT NOT NULL, channel_id INT NOT NULL, INDEX IDX_AAD9CFD0684EC833 (banner_id), INDEX IDX_AAD9CFD072F5A1AA (channel_id), PRIMARY KEY(banner_id, channel_id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE odiseo_banners_taxons (banner_id INT NOT NULL, taxon_id INT NOT NULL, INDEX IDX_FB5C1345684EC833 (banner_id), INDEX IDX_FB5C1345DE13F470 (taxon_id), PRIMARY KEY(banner_id, taxon_id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE odiseo_banner_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, image_name VARCHAR(255) NOT NULL, mobile_image_name VARCHAR(255) NOT NULL, url VARCHAR(255) DEFAULT NULL, mainText VARCHAR(255) DEFAULT NULL, secondaryText VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_BC61EAF72C2AC5D3 (translatable_id), UNIQUE INDEX odiseo_banner_translation_uniq_trans (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE odiseo_banners_channels ADD CONSTRAINT FK_AAD9CFD0684EC833 FOREIGN KEY (banner_id) REFERENCES odiseo_banner (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE odiseo_banners_channels ADD CONSTRAINT FK_AAD9CFD072F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id)');
        $this->addSql('ALTER TABLE odiseo_banners_taxons ADD CONSTRAINT FK_FB5C1345684EC833 FOREIGN KEY (banner_id) REFERENCES odiseo_banner (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE odiseo_banners_taxons ADD CONSTRAINT FK_FB5C1345DE13F470 FOREIGN KEY (taxon_id) REFERENCES sylius_taxon (id)');
        $this->addSql('ALTER TABLE odiseo_banner_translation ADD CONSTRAINT FK_BC61EAF72C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES odiseo_banner (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE odiseo_banners_channels DROP FOREIGN KEY FK_AAD9CFD0684EC833');
        $this->addSql('ALTER TABLE odiseo_banners_taxons DROP FOREIGN KEY FK_FB5C1345684EC833');
        $this->addSql('ALTER TABLE odiseo_banner_translation DROP FOREIGN KEY FK_BC61EAF72C2AC5D3');
        $this->addSql('DROP TABLE odiseo_banner');
        $this->addSql('DROP TABLE odiseo_banners_channels');
        $this->addSql('DROP TABLE odiseo_banners_taxons');
        $this->addSql('DROP TABLE odiseo_banner_translation');
    }
}
