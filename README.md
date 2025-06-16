

# VIVOMEDIA.StructuralChangeReload

In some cases you need to reload the whole collection or page if the structure of nodes within a collection has changed. Common examples are slider, grids, galleries.

This package allows defining collections, which will get rerendered if a direct child get moved, inserted or removed. 



_Please note: This package is only working for "flat collections", which means NodeTypes, which are actually the collection and don't have tethered child collections._   


## Configuration

**Reload the collection** if the structure has changed.
```
Vendor.Site:Content.ExampleCollection:
  options:
    reloadIfStructureHasChanged: TRUE
```

**Reload the page** if the structure has changed.
```
Vendor.Site:Content.ExampleCollection:
  options:
    reloadPageIfStructureHasChanged: TRUE
```

Inspired by [SiteGeist.StageFog](https://github.com/sitegeist/Sitegeist.StageFog)