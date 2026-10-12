[← Installation](installation.md) · [Docs index](README.md) · [Displaying banners →](displaying-banners.md)

# Managing banners

Banners live under **Catalog → Banners** in the admin panel.

## The list

Every banner is listed with its image, its mobile image, where it is shown, its position and whether
it is enabled. The list is sorted by position, and can be filtered by code, taxon, state and
creation date.

The **Shown on** column says *Homepage* for a banner without taxons, and names the taxons otherwise.

![The list of banners](images/admin-list.png)

## The form

The form is split into three cards.

![The banner form](images/admin-form.png)

### Content

One section per locale of the store, each with its own:

| Group | Fields |
| ----- | ------ |
| Images | **Image**, required. **Mobile image**, optional: it replaces the image on small screens |
| Texts | **Main text** and **Secondary text**, shown over the image |
| Link | **URL**, and the **Button text** |

The recommended sizes are 1920 × 600 px for the image and 768 × 1000 px for the mobile one. Any
size works: the banner is rendered at the proportions of the file you upload.

Editing a banner without choosing a file keeps the image it has. Choosing one replaces it.

How the link behaves depends on the button text:

- With a **URL and no button text**, the whole banner is the link.
- With a **URL and a button text**, the banner shows a button that links to the URL.
- Without a URL, the banner is not a link.

### General

- **Code**, unique, cannot be changed once the banner is created.
- **Enabled**. A disabled banner is not shown anywhere.
- **Position**. Banners are shown from the lowest position to the highest one.

### Visibility

- **Channels**. The banner is shown on the channels checked here, and on none if none is checked.
- **Taxons**. Leave it empty to show the banner on the homepage. With taxons, the banner is shown
  only on the product list of those taxons.

A taxon without banners of its own shows the ones of its closest parent that has any, so a banner
on *T-shirts* is also shown on *T-shirts / Men* until *Men* gets a banner of its own.

See [Displaying banners](displaying-banners.md) for where and how they are rendered.

---

[← Installation](installation.md) · [Docs index](README.md) · [Displaying banners →](displaying-banners.md)
