[← Configuration](configuration.md) · [Docs index](README.md) · [Extending →](extending.md)

# API

The plugin exposes the banners through the Sylius API, on the API Platform version your Sylius
release ships (4.1 on Sylius 2.0, up to 5.0 on Sylius 2.3).

## Admin

Everything the admin panel does, with the administrator token of the Sylius API.

| Method | Path | What it does |
| ------ | ---- | ------------ |
| GET | `/api/v2/admin/banners` | Lists the banners by position |
| POST | `/api/v2/admin/banners` | Creates a banner, images included (`multipart/form-data`) |
| GET | `/api/v2/admin/banners/{code}` | Shows a banner with its translations |
| PUT | `/api/v2/admin/banners/{code}` | Updates the position, state, channels, taxons and texts (JSON) |
| POST | `/api/v2/admin/banners/{code}/images` | Replaces the images of a translation (`multipart/form-data`) |
| DELETE | `/api/v2/admin/banners/{code}` | Deletes a banner |

The list is filtered with `code` (partial match), `enabled` and `taxons.code`, and sorted with
`order[code]`, `order[position]` and `order[createdAt]`.

### Creating a banner

A banner needs an image, so it is created in a single multipart request:

```bash
curl -X POST https://example.com/api/v2/admin/banners \
    -H "Authorization: Bearer $TOKEN" \
    -F "code=summer_sale" \
    -F "position=1" \
    -F "channels[]=/api/v2/admin/channels/WEB" \
    -F "taxons[]=/api/v2/admin/taxons/t_shirts" \
    -F "translations[en_US][mainText]=Summer sale" \
    -F "translations[en_US][url]=https://example.com/sale" \
    -F "translations[en_US][image]=@banner.jpg" \
    -F "translations[en_US][mobileImage]=@banner-mobile.jpg"
```

| Field | Notes |
| ----- | ----- |
| `code` | Required, unique |
| `position` | Defaults to `0` |
| `enabled` | Defaults to `true` |
| `channels[]`, `taxons[]` | IRIs of the admin API |
| `translations[locale][image]` | Required for every translation sent |
| `translations[locale][mobileImage]` | Optional |
| `translations[locale][url]`, `[mainText]`, `[secondaryText]`, `[buttonText]` | Optional |

The files have to be images. A request that does not validate is answered with a `422` and its
violations.

### Updating a banner

Send only what changes. The translations that are not sent stay as they are, and so do the fields
left out of a translation:

```bash
curl -X PUT https://example.com/api/v2/admin/banners/summer_sale \
    -H "Authorization: Bearer $TOKEN" -H "Content-Type: application/json" \
    -d '{"enabled": false, "translations": {"en_US": {"mainText": "Winter sale"}}}'
```

The code cannot be changed, and the images are not part of this request.

### Replacing the images

Send the `locale` with an `image`, a `mobileImage` or both. A locale the banner does not have yet
gets a new translation, which needs its `image`:

```bash
curl -X POST https://example.com/api/v2/admin/banners/summer_sale/images \
    -H "Authorization: Bearer $TOKEN" \
    -F "locale=en_US" -F "image=@banner.jpg"
```

## Shop

Public and read only: the enabled banners of the channel being browsed, in their order and
translated to the requested locale.

| Method | Path | What it does |
| ------ | ---- | ------------ |
| GET | `/api/v2/shop/banners` | Lists the banners |
| GET | `/api/v2/shop/banners/{code}` | Shows a banner |

| Filter | Gives |
| ------ | ----- |
| `exists[taxons]=false` | The banners of the homepage |
| `taxons.code=t_shirts` | The banners assigned to that taxon. The ones of its parents are not included |

Each banner comes with what is needed to render it:

```json
{
    "code": "summer_sale",
    "position": 1,
    "url": "https://example.com/sale",
    "mainText": "Summer sale",
    "secondaryText": null,
    "buttonText": null,
    "imageName": "6acaf13d3460d648479325.jpg",
    "mobileImageName": null,
    "image": "https://example.com/media/cache/resolve/odiseo_banner_image/6acaf13d3460d648479325.jpg",
    "mobileImage": null
}
```

`image` and `mobileImage` are the URLs of the rendered images, in the format and quality set in the
[configuration](configuration.md).

---

[← Configuration](configuration.md) · [Docs index](README.md) · [Extending →](extending.md)
