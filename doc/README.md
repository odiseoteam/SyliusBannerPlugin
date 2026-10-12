# Documentation

Banners for the homepage and the taxons of a Sylius store.
Back to the [project README](../README.md).

## Getting it running

| | |
|---|---|
| [Installation](installation.md) | Six steps, from Composer to the migrations |
| [Changelog](../CHANGELOG.md) | What changed in each release, with the upgrade notes |

## Using it

| | |
|---|---|
| [Managing banners](managing-banners.md) | The list and the form of the admin panel |
| [Displaying banners](displaying-banners.md) | Where they are shown, the components and the templates |
| [API](api.md) | The admin and shop endpoints |

## Adapting it

| | |
|---|---|
| [Configuration](configuration.md) | Image format and quality, storage and fixtures |
| [Extending](extending.md) | The hooks of the admin form, the entities and the services |
| [Development](development.md) | Test application, test suites and the CI matrix |

## In one minute

A **banner** has an image, an optional mobile image, texts and a link, each of them per locale.

It is shown on the **channels** it is assigned to. Without **taxons** it goes on the homepage; with
taxons, on the product list of those taxons and of their children that have no banner of their own.

One banner is rendered as an image, several as a slider, ordered by **position**.
