[Docs index](README.md) · [Managing banners →](managing-banners.md)

# Installation

## 1. Require the plugin

```bash
composer require odiseoteam/sylius-banner-plugin
```

## 2. Add the slider

Several banners in the same place are rendered as a [Swiper.js](https://github.com/nolimits4web/swiper) slider:

```bash
yarn add swiper@^11.2.7
```

## 3. Import the assets

Import the assets of the plugin in the entrypoint of your shop, and build them:

```js
// assets/shop/entrypoint.js
import '../../vendor/odiseoteam/sylius-banner-plugin/assets/shop/entrypoint';
```

```bash
yarn encore dev # or: yarn encore production
```

## 4. Import the configuration

```yaml
# config/packages/_sylius.yaml
imports:
    # ...
    - { resource: "@OdiseoSyliusBannerPlugin/config/config.yaml" }
```

## 5. Add the routes

```yaml
# config/routes.yaml
odiseo_sylius_banner_admin:
    resource: "@OdiseoSyliusBannerPlugin/config/admin_routing.yaml"
    prefix: '/%sylius_admin.path_name%'

odiseo_sylius_banner_shop:
    resource: "@OdiseoSyliusBannerPlugin/config/shop_routing.yaml"
    prefix: /{_locale}/banners
    requirements:
        _locale: ^[A-Za-z]{2,4}(_([A-Za-z]{4}|[0-9]{3}))?(_([A-Za-z]{2}|[0-9]{3}))?$
```

## 6. Run the migrations

```bash
bin/console doctrine:migrations:migrate
bin/console cache:clear
```

The plugin registers its own migrations, for MySQL and PostgreSQL, so there is nothing to
configure. If your application does not use `sylius-labs/doctrine-migrations-extra-bundle`,
register them yourself:

```yaml
# config/packages/doctrine_migrations.yaml
doctrine_migrations:
    migrations_paths:
        # ...
        'Odiseo\SyliusBannerPlugin\Migrations': '%kernel.project_dir%/vendor/odiseoteam/sylius-banner-plugin/src/Migrations'
```

## Done

There is a new **Banners** section under **Catalog** in the admin panel. Create a banner there and
it shows up on the homepage: see [Managing banners](managing-banners.md).

## Example banners

The default fixtures of Sylius get four banners for the homepage and one for each of its main
taxons:

```bash
bin/console sylius:fixtures:load
```

> [!WARNING]
> This reloads every fixture of the suite and replaces the data of the store. Use it on a
> development database only.

---

[Docs index](README.md) · [Managing banners →](managing-banners.md)
