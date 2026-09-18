# Changelog

All notable changes to this project are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).
Releases previous to 2.1.0 are only listed on the
[GitHub releases page](https://github.com/odiseoteam/SyliusBannerPlugin/releases).

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

[2.1.0]: https://github.com/odiseoteam/SyliusBannerPlugin/compare/v2.0.0...v2.1.0
