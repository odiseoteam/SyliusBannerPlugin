## Installation

1. Run `composer require odiseoteam/sylius-banner-plugin`

2. Run `yarn add swiper@^11.2.7`

3. Run `yarn encore dev` or `yarn encore production`

4. Import the plugin configurations

```yml
# config/packages/_sylius.yaml
imports:
    # ...
    - { resource: "@OdiseoSyliusBannerPlugin/config/config.yaml" }
```

5. Add the shop and admin routes

```yml
# config/routes.yaml
odiseo_sylius_banner_admin:
    resource: "@OdiseoSyliusBannerPlugin/config/admin_routing.yaml"
    prefix: /admin

odiseo_sylius_banner_shop:
    resource: "@OdiseoSyliusBannerPlugin/config/shop_routing.yaml"
    prefix: /{_locale}/banners
    requirements:
        _locale: ^[A-Za-z]{2,4}(_([A-Za-z]{4}|[0-9]{3}))?(_([A-Za-z]{2}|[0-9]{3}))?$
```

6. Finish the installation updating by runing migrations

```
php bin/console doctrine:migrations:migrate
php bin/console cache:clear
```
Optional, run fixtures to load one banner: 

```
php bin/console sylius:fixtures:load -n
```

7. Installation complete! Now you will see the new section under "Catalog" menu on the admin panel. Also you will see the banner on the homepage.
