## Installation

1. Run `composer require odiseoteam/sylius-banner-plugin` --no-scripts`

2. Enable the plugin in bundles.php

```php
<?php
// config/bundles.php

return [
    // ...
    Vich\UploaderBundle\VichUploaderBundle::class => ['all' => true],
    Odiseo\SyliusBannerPlugin\OdiseoSyliusBannerPlugin::class => ['all' => true],
];
```

3. Import the plugin configurations

```yml
# config/packages/_sylius.yaml
imports:
    ...

    - { resource: "@OdiseoSyliusBannerPlugin/Resources/config/config.yaml" }
```

4. Add the shop and admin routes

```yml
# config/routes.yaml
odiseo_sylius_banner_plugin_admin:
    resource: "@OdiseoSyliusBannerPlugin/Resources/config/routing/admin.yaml"
    prefix: /admin

odiseo_sylius_banner_plugin_shop:
    resource: "@OdiseoSyliusBannerPlugin/Resources/config/routing/shop.yaml"
    prefix: /{_locale}/banners
    requirements:
        _locale: ^[A-Za-z]{2,4}(_([A-Za-z]{4}|[0-9]{3}))?(_([A-Za-z]{2}|[0-9]{3}))?$
```

5. Create banner folder: run `mkdir public/media/banner-image -p` and insert a .gitkeep file in that folder

6. Finish the installation updating the database schema and installing assets

```
php bin/console doctrine:schema:update --force
php bin/console sylius:theme:assets:install
php bin/console cache:clear
```
