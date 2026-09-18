<p align="center">
    <a href="https://odiseo.io/en?utm_source=github&utm_medium=readme&utm_campaign=sylius-banner-plugin" target="_blank" title="Odiseo">
        <img src="https://github.com/odiseoteam/SyliusBannerPlugin/blob/master/sylius-banner-plugin.png" alt="Sylius Banner Plugin" />
    </a>
    <br />
    <a href="https://packagist.org/packages/odiseoteam/sylius-banner-plugin" title="License" target="_blank">
        <img src="https://img.shields.io/packagist/l/odiseoteam/sylius-banner-plugin.svg" />
    </a>
    <a href="https://packagist.org/packages/odiseoteam/sylius-banner-plugin" title="Version" target="_blank">
        <img src="https://img.shields.io/packagist/v/odiseoteam/sylius-banner-plugin.svg" />
    </a>
    <a href="https://github.com/odiseoteam/SyliusBannerPlugin/actions" title="Build Status" target="_blank">
        <img src="https://img.shields.io/github/actions/workflow/status/odiseoteam/SyliusBannerPlugin/build.yml" />
    </a>
    <a href="https://scrutinizer-ci.com/g/odiseoteam/SyliusBannerPlugin/" title="Scrutinizer" target="_blank">
        <img src="https://img.shields.io/scrutinizer/g/odiseoteam/SyliusBannerPlugin.svg" />
    </a>
    <a href="https://packagist.org/packages/odiseoteam/sylius-banner-plugin" title="Total Downloads" target="_blank">
        <img src="https://poser.pugx.org/odiseoteam/sylius-banner-plugin/downloads" />
    </a>
    <a href="https://sylius-devs.slack.com" title="Slack" target="_blank">
        <img src="https://img.shields.io/badge/community%20chat-slack-FF1493.svg" />
    </a>
</p>
<p align="center"><a href="https://sylius.com/partners/odiseo/" target="_blank"><img src="https://github.com/odiseoteam/SyliusBannerPlugin/blob/master/badge-partner-by-sylius.png" width="140"></a></p>

## Description

This is a Sylius Plugin that add banners to your store. The banners are fully customizable by the admin.

Features:

* Templates: Show all images or by taxon.

* Sliders: We are providing the integration with [Swiper.js](https://github.com/nolimits4web/swiper). 

Supported versions:

| Package       | Version |
|---------------|---------|
| PHP           | ^8.2    |
| sylius/sylius | ^2.0    |

## Screenshots

<img src="https://github.com/odiseoteam/SyliusBannerPlugin/blob/master/screenshot_1.png" alt="Banners admin" width="650">
<img src="https://github.com/odiseoteam/SyliusBannerPlugin/blob/master/screenshot_2.png" alt="Banners admin edit" width="650">
<img src="https://github.com/odiseoteam/SyliusBannerPlugin/blob/master/screenshot_3.png" alt="Banners shop homepage" width="650">

## Demo

Want a live walkthrough of this plugin? [Get in touch](https://odiseo.io/en/contact-us?utm_source=github&utm_medium=readme&utm_campaign=sylius-banner-plugin) — or browse all our Sylius plugins at [odiseo.io](https://odiseo.io/en/products/sylius-plugins?utm_source=github&utm_medium=readme&utm_campaign=sylius-banner-plugin).

## Configuration

### Images

The banner images are rendered through LiipImagine. By default the plugin serves them as WebP,
with quality 80 for the desktop filter set and 75 for the mobile one. You can change it from your
application:

```yml
# config/packages/odiseo_sylius_banner.yaml
odiseo_sylius_banner:
    images:
        format: webp # null keeps the format of the uploaded file
        quality: 80
        mobile_quality: 75
```

> **Upgrading from 2.0**: previously no format or quality was declared, so the images were served
> in their original format with quality 100. Set `format: null` and `quality: 100` to keep the
> old behaviour. The uploaded file is always stored as it is, only the rendered image changes.

### Order

Banners have a `position` field. They are rendered from the lowest position to the highest one,
using the id as a tie-breaker, and the position is editable from the admin panel. The admin grid
is sorted by position as well.

## Documentation

- [Installation](doc/installation.md)

## Credits

This plugin is maintained by [Odiseo](https://odiseo.io/en?utm_source=github&utm_medium=readme&utm_campaign=sylius-banner-plugin). Want us to help you with this plugin or any Sylius project? [Get in touch](https://odiseo.io/en/contact-us?utm_source=github&utm_medium=readme&utm_campaign=sylius-banner-plugin).
