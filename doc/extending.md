[← API](api.md) · [Docs index](README.md) · [Development →](development.md)

# Extending

## The admin form

Every card and every field of the form is its own Twig hook, so each can be moved, replaced or
removed without copying a template. The hooks exist for both `create` and `update`:

| Hook | Hookables |
| ---- | --------- |
| `odiseo_banner.admin.banner.create.content.form#left` | `translations` |
| `odiseo_banner.admin.banner.create.content.form#right` | `general`, `visibility` |
| `odiseo_banner.admin.banner.create.content.form.general` | `code`, `enabled`, `position` |
| `odiseo_banner.admin.banner.create.content.form.visibility` | `channels`, `taxons` |
| `odiseo_banner.admin.banner.create.content.form.translations` | `images`, `texts`, `link` |
| `odiseo_banner.admin.banner.create.content.form.translations.images` | `image`, `mobile_image` |
| `odiseo_banner.admin.banner.create.content.form.translations.texts` | `main_text`, `secondary_text` |
| `odiseo_banner.admin.banner.create.content.form.translations.link` | `url`, `button_text` |

For instance, to hide the secondary text and add a field of your own to the general card:

```yaml
# config/packages/sylius_twig_hooks.yaml
sylius_twig_hooks:
    hooks:
        'odiseo_banner.admin.banner.create.content.form.translations.texts':
            secondary_text:
                enabled: false

        'odiseo_banner.admin.banner.create.content.form.general':
            campaign:
                template: 'admin/banner/form/campaign.html.twig'
                priority: -100
```

Repeat it under `odiseo_banner.admin.banner.update...` for the edit form. Inside a field template,
the form is in `hookable_metadata.context.form`.

## The entities

`Banner` and `BannerTranslation` are Sylius resources, declared as mapped superclasses. Extend them
as any other Sylius resource:

```yaml
# config/packages/_sylius.yaml
sylius_resource:
    resources:
        odiseo_banner.banner:
            classes:
                model: App\Entity\Banner
```

Add the new fields to the form with a [form extension](https://symfony.com/doc/current/form/create_form_type_extension.html)
of `Odiseo\SyliusBannerPlugin\Form\Type\BannerType`, and render them with a hook as above.

## The shop

See [Displaying banners](displaying-banners.md) for the components, the hooks they are placed
with and the templates to override.

## Services

| Service | What it does |
| ------- | ------------ |
| `odiseo_banner.repository.banner` | Finds the enabled banners of a channel, without taxons or by taxon |
| `odiseo_banner.provider.taxon_banners` | The banners of a taxon, inherited from its parents |
| `odiseo_banner.uploader.banner_image` | Stores the uploaded images and removes the replaced ones |
| `odiseo_banner.storage.banner_image` | The Flysystem storage of the images |

---

[← API](api.md) · [Docs index](README.md) · [Development →](development.md)
