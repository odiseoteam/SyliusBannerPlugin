## Usage

For the administration you will have the Banner menu.
Feel free to modify the plugin templates like you want.

You can choose your js library by configuration:

```yml
# config/packages/odiseo_sylius_banner.yaml
odiseo_sylius_banner:
    slider: #swiper, glide, sliderpro. If you don't want any use ~
```

### Partial routes

To render banner images you can do something like this:

```twig
{{ render(url('odiseo_sylius_banner_plugin_shop_partial_banner', {'template': '@OdiseoSyliusBannerPlugin/Shop/Banner/_banner.html.twig'})) }}
``` 
   
And to render banner images by taxon:

```twig
{{ render(url('odiseo_sylius_banner_plugin_shop_partial_banner_by_taxon', {'taxon': taxon.slug, 'template': '@OdiseoSyliusBannerPlugin/Shop/Banner/_banner.html.twig'})) }}
```

### Form validation group

For forms use the validation group named `odiseo`
