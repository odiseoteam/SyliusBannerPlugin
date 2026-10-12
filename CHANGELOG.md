# Changelog

All notable changes to this project are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).
Releases previous to 2.1.0 are only listed on the
[GitHub releases page](https://github.com/odiseoteam/SyliusBannerPlugin/releases).

## [Unreleased]

### Added

- Support for Sylius 2.3.
- PostgreSQL migrations (`Version20250521005124` and `Version20260918120001`), twins of the MySQL
  ones. Each one only runs on its own platform.
- `odiseo_banner:shop:banners` Twig component, which renders the banners of the current channel.
  Pass it a `taxon` to render only the banners assigned to that taxon:
  `{{ component('odiseo_banner:shop:banners', {taxon: taxon}) }}`.
- Symfony Flex recipe, kept in `recipe/`.
- **The banners of a taxon are shown on its product list**, above the products, through the new
  `odiseo_banner:shop:taxon_banners` component. A taxon without banners of its own shows the ones of
  its closest parent that has any.
- The taxons of a banner can be set from the admin form again. The field had been left out because
  its form type has no widget in the Sylius 2 admin; it now uses the Sylius 2 autocomplete.
- The admin list shows where each banner is shown, and can be filtered by taxon.
- `BannerRepositoryInterface::findWithoutTaxonByChannel()`.
- `position` option on the banner fixture. A fixture banner with no channels is now assigned to
  every channel, since a banner without one is never shown.
- **An API for the admin and the shop**, on the API Platform version of each Sylius release (4.0 to
  5.0). The admin can list, filter, read, create, update and delete banners under
  `/api/v2/admin/banners`, and upload their images; the shop reads the enabled banners of its channel
  under `/api/v2/shop/banners`, with the URLs of the rendered images. See `doc/api.md`.
- Uploaded files have to be images, both in the admin panel and in the API.

### Changed

- **A banner assigned to taxons is no longer shown on the homepage**: it is shown only on the pages
  of those taxons. The homepage, and the `odiseo_banner_shop_partial_banner` route, show the banners
  that have no taxon.
- **The admin form is split into cards**: the content of each locale on the left, grouped into
  images, texts and link, and the general data and the visibility on the right. Every card and every
  field is its own Twig hook under `odiseo_banner.admin.banner.(create|update).content.form`, so they
  can be reordered or replaced one by one. The `form/sections*` templates and the
  `...content.form.sections` hooks are gone.
- The default fixtures also load a banner for each of the main taxons of the Sylius fixtures.
- The default fixtures load four banners, with their mobile images, instead of a random one. The
  example images are new and much lighter; `banner1.png` is gone.
- **Banner images are stored through Flysystem instead of Gaufrette**, which Sylius 2.3 no longer
  ships. The storage is `odiseo_banner.storage.banner_image` and points at the same directory,
  `%sylius_core.public_dir%/media/banner-image`, so the stored images stay where they are.
- `BannerImageUploader` receives a `League\Flysystem\FilesystemOperator`.
- The homepage banner is rendered by the `odiseo_banner:shop:banners` component instead of a
  sub-request to `odiseo_banner_shop_partial_banner`. The sub-request broke the page in the `test`
  environment of Symfony 7.4. Both partial routes are still available.
- An uploaded file that cannot be read no longer removes the stored image.
- The migrations extend the Sylius base classes, so the MySQL ones are skipped on other platforms.

### Removed

- `config/api_platform/`. Those files were written for API Platform 2 and were never loaded, so no
  endpoint goes away.
- The `odiseo_banner.filesystem.banner_image` service and the `odiseo_banner_image` adapter and
  filesystem of `knp_gaufrette`.

### Upgrading from 2.1

There is nothing to run and no file to move. Only applications that overrode the Gaufrette
filesystem, for instance to store the images somewhere else, have to move that configuration to
Flysystem:

```yaml
# config/packages/flysystem.yaml
flysystem:
    storages:
        odiseo_banner.storage.banner_image:
            adapter: 'local'
            options:
                directory: '%sylius_core.public_dir%/media/banner-image'
            directory_visibility: 'public'
```

## [2.1.1] - 2026-09-18

### Fixed

- Editing a banner no longer destroys the stored image. The uploader removed the stored file before
  checking whether a new one had been uploaded, so any edit that did not upload an image again —
  changing the link, fixing a text, editing another translation — deleted the original for good.
  The damage stayed invisible, because the name was kept in the database and LiipImagine went on
  serving its cached version; it only surfaced once that cache was regenerated. The stored file is
  now removed only when there is a replacement for it.
- The migrations are now found by the applications that install the plugin. They declared the
  `App\Migrations` namespace, which belongs to the project and, in a standard Symfony application,
  points at the project's own migrations directory, while the extension registered the plugin's
  directory under a third namespace, `DoctrineMigrations`. Nothing ever matched:
  `doctrine:migrations:status` reported no new migrations and the schema stayed out of sync, so
  installing the plugin meant generating the migration by hand with `doctrine:migrations:diff`.
  They now live in `Odiseo\SyliusBannerPlugin\Migrations`, which is what the extension registers.

### Upgrading from 2.1.0

There is nothing to run. Changing the namespace makes Doctrine see both migrations as new, so each
one checks the schema first and skips itself when the tables, or the `position` column, are already
there.

The rows recorded under the old namespace are left behind. Doctrine only warns about them, so they
can stay; to clean them up, in the migrations table (`sylius_migrations` in a Sylius application):

```sql
DELETE FROM sylius_migrations WHERE version LIKE 'App\\Migrations\\%';
```

## [2.1.0] - 2026-09-18

### Added

- Configuration for the rendered images under `odiseo_sylius_banner.images`: `format`, `quality`
  and `mobile_quality`. Use `format: null` to keep the format of the uploaded file.
- `position` field on banners, editable from the admin panel and shown as a sortable column in the grid.
- Migration `Version20260918120000`, which adds the column and backfills it with the banner id so
  existing installations keep their current order.

### Fixed

- Uploading a banner image no longer hangs. The uploader named the file after `uniqid()`, which
  starts with the timestamp in hexadecimal, and kept generating names until one contained no `ad`,
  so that ad blockers would not hide the image. Whenever that hexadecimal timestamp itself contains
  `ad` — an eighteen hour window recurring roughly every two hundred days, and the current one
  started on 2026-09-18 — no name could ever satisfy the condition: the loop spun forever on a CPU
  core, silently, without an error or a log entry. The substring is now rewritten instead of
  retried. This affected every image upload from the admin panel, and `sylius:fixtures:load`.

### Changed

- **Banner images are now served as WebP with quality 80 (75 for the mobile filter set).** Until now
  no format or quality was declared, so LiipImagine kept the original format at quality 100, which
  made the rendered banners much heavier than they needed to be. Set `format: null` and
  `quality: 100` to restore the previous output. Uploaded files are still stored untouched.
- The LiipImagine loader and filter sets are registered by the plugin extension instead of
  `config/config.yaml`. Filter sets defined by the application still take precedence over them.
- Enabled banners are returned ordered by `position` and then by `id`, instead of relying on the
  order given by the database.
- The admin grid is sorted by `position: asc` by default, instead of `createdAt: desc`.

[Unreleased]: https://github.com/odiseoteam/SyliusBannerPlugin/compare/v2.1.1...HEAD
[2.1.1]: https://github.com/odiseoteam/SyliusBannerPlugin/compare/v2.1.0...v2.1.1
[2.1.0]: https://github.com/odiseoteam/SyliusBannerPlugin/compare/v2.0.0...v2.1.0
