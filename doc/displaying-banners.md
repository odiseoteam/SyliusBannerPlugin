[← Managing banners](managing-banners.md) · [Docs index](README.md) · [Configuration →](configuration.md)

# Displaying banners

## Where they are shown

| Banner | Shown on |
| ------ | -------- |
| Without taxons | The homepage |
| With taxons | The product list of each of those taxons, above the products |

In both cases only the enabled banners of the current channel are shown, ordered by position and
then by creation.

A taxon without banners of its own shows the ones of its closest parent that has any.

One banner is rendered as an image. Several are rendered as a slider.

| Homepage | Taxon |
| -------- | ----- |
| ![Banners on the homepage](images/shop-homepage.png) | ![The banner of a taxon](images/shop-taxon.png) |

## Rendering them somewhere else

Both places are Twig components, hooked into the shop by the plugin:

| Component | Renders | Hook |
| --------- | ------- | ---- |
| `odiseo_banner:shop:banners` | The banners without taxons | `sylius_shop.homepage.index` |
| `odiseo_banner:shop:taxon_banners` | The banners of the taxon in the URL | `sylius_shop.product.index.content` |

Use the first one anywhere in your templates. Pass it a `taxon` to render the banners of that
taxon, inherited from its parents as on the product list:

```twig
{{ component('odiseo_banner:shop:banners') }}

{{ component('odiseo_banner:shop:banners', {taxon: taxon}) }}
```

To move or remove a banner the plugin places, override its hook from your application:

```yaml
# config/packages/sylius_twig_hooks.yaml
sylius_twig_hooks:
    hooks:
        'sylius_shop.product.index.content':
            banner:
                enabled: false
```

## Templates

Override any of them from `templates/bundles/OdiseoSyliusBannerPlugin/`:

| Template | Renders |
| -------- | ------- |
| `shop/banner/banner.html.twig` | The wrapper of the homepage banners |
| `shop/banner/taxon_banner.html.twig` | The wrapper of the banners of a taxon |
| `shop/banner/content/index.html.twig` | One banner as an image, several as a slider |
| `shop/banner/content/image.html.twig` | A banner: images, texts and link |
| `shop/banner/content/slider.html.twig` | The slider |

## Styles and slider

The styles and the Swiper setup are in `assets/shop/` of the plugin, imported from your shop
entrypoint during the [installation](installation.md). Rebuild your assets after updating the
plugin to pick up their changes.

On the product list of a taxon the banner takes the whole width of the page and is at most 280 px
high.

## Legacy routes

The banners can also be rendered through a sub-request, as the plugin did before it had components:

| Route | Renders |
| ----- | ------- |
| `odiseo_banner_shop_partial_banner` | The banners without taxons |
| `odiseo_banner_shop_partial_banner_by_taxon` | The banners of the taxon with the slug in `taxon`, without inheritance |

```twig
{{ render(url('odiseo_banner_shop_partial_banner', {
    'template': '@OdiseoSyliusBannerPlugin/shop/banner/content/index.html.twig'
})) }}
```

Prefer the components: a sub-request rendered inside a Twig hook breaks the page in the `test`
environment of Symfony 7.4.

---

[← Managing banners](managing-banners.md) · [Docs index](README.md) · [Configuration →](configuration.md)
