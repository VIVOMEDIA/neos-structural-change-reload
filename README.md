# Neos CMS - Structural Change Reload

[![Latest Stable Version](https://poser.pugx.org/vivomedia/neos-structural-change-reload/v/stable)](https://packagist.org/packages/vivomedia/neos-structural-change-reload)
[![Total Downloads](https://poser.pugx.org/vivomedia/neos-structural-change-reload/downloads)](https://packagist.org/packages/vivomedia/neos-structural-change-reload)
[![License](https://poser.pugx.org/vivomedia/neos-structural-change-reload/license)](https://packagist.org/packages/vivomedia/neos-structural-change-reload)

In some cases you need to reload the whole collection or page when the structure of nodes within a collection has changed. Common examples are sliders, grids or galleries.

This package allows defining collections, which will get re-rendered if a direct child gets moved, inserted or removed.

_Please note: This package is only working for "flat collections", which means NodeTypes, which are actually the collection and don't have tethered child collections._   

## Install

Install with composer

```
composer require vivomedia/neos-structural-change-reload 
```

## Configuration

**Reload the collection** if the structure has changed.
```
Vendor.Site:Content.ExampleCollection:
  superTypes:
    'Neos.Neos:Content': true
    'Neos.Neos:ContentCollection': true
  options:
    reloadIfStructureHasChanged: true
```

**Reload the page** if the structure has changed.
```
Vendor.Site:Content.ExampleCollection:
  superTypes:
    'Neos.Neos:Content': true
    'Neos.Neos:ContentCollection': true
  options:
    reloadPageIfStructureHasChanged: true
```

Inspired by [SiteGeist.StageFog](https://github.com/sitegeist/Sitegeist.StageFog)