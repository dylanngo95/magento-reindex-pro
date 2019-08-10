# Magento2 reindex Pro

[![Latest Stable Version](https://poser.pugx.org/jundat/module-reindex/v/stable.svg)](https://packagist.org/packages/jundat/module-reindex)
[![Total Downloads](https://poser.pugx.org/jundat/module-reindex/downloads)](https://packagist.org/packages/jundat/module-reindex)
[![Latest Unstable Version](https://poser.pugx.org/jundat/module-reindex/v/unstable.svg)](https://packagist.org/packages/jundat/php-reindex)
[![License](https://poser.pugx.org/jundat/module-reindex/license.svg)](https://packagist.org/packages/jundat/module-reindex)

## Install

* Copy all code to folder: /app/code/Jundat/Reindex

Run command line in project:

```php

    php bin/magento setup:upgrade
    php bin/magento c:c
    php bin/magento c:f
    
```

## Use

* Go to the admin page

* Goto Stores -> Settings -> Configuration -> Jundat -> Reindex

* Select Reindex: Asynchronous / Synchronous -> save

* Go to System -> Tools -> Index Management

* Choose Action Reindex and select indexer if you want to reindex this
