[← Displaying banners](displaying-banners.md) · [Docs index](README.md) · [API →](api.md)

# Configuration

Nothing here is required: the plugin works with its defaults.

## Images

The banner images are rendered through LiipImagine. By default they are served as WebP, with
quality 80 for the image and 75 for the mobile one:

```yaml
# config/packages/odiseo_sylius_banner.yaml
odiseo_sylius_banner:
    images:
        format: webp # null keeps the format of the uploaded file
        quality: 80
        mobile_quality: 75
```

The uploaded file is always stored as it is; only the rendered image changes.

The plugin registers two filter sets, `odiseo_banner_image` (up to 1920 × 600) and
`odiseo_banner_mobile_image` (up to 768 × 1000). A filter set with the same name defined by your
application takes precedence.

> [!NOTE]
> **Upgrading from 2.0**: no format or quality was declared before, so the images were served in
> their original format with quality 100. Set `format: null` and `quality: 100` to keep that.

## Storage

The uploaded images are stored through Flysystem, in `public/media/banner-image` by default. To
store them somewhere else, override the storage:

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

LiipImagine reads the images from that same directory. If you move the storage, point the
`odiseo_banner_image` loader of `liip_imagine` at the new place as well.

## Fixtures

The `banner` fixture takes these options for each banner:

| Option | Default |
| ------ | ------- |
| `code` | Random |
| `position` | `0` |
| `channels` | Every channel |
| `taxons` | None: the banner goes on the homepage |
| `image` | An example image of the plugin |
| `mobile_image` | An example image of the plugin. `~` for none |
| `url` | Random. `~` for none |
| `main_text`, `secondary_text`, `button_text` | None |

The same content is used for every locale of the store.

```yaml
# config/packages/sylius_fixtures.yaml
sylius_fixtures:
    suites:
        default:
            fixtures:
                banner:
                    options:
                        custom:
                            -
                                code: 'summer_sale'
                                position: 1
                                taxons: ['t_shirts']
                                image: '%kernel.project_dir%/fixtures/summer_sale.jpg'
                                mobile_image: ~
                                url: 'https://example.com/sale'
                                main_text: 'Summer sale'
                                button_text: 'Shop now'
```

A taxon that does not exist in the store is left out, and the banner is created without it.

---

[← Displaying banners](displaying-banners.md) · [Docs index](README.md) · [API →](api.md)
