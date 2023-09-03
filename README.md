## Overview

[![CI](https://github.com/silverstripe/silverstripe-installer/actions/workflows/ci.yml/badge.svg)](https://github.com/silverstripe/silverstripe-installer/actions/workflows/ci.yml)
[![Silverstripe supported module](https://img.shields.io/badge/silverstripe-supported-0071C4.svg)](https://www.silverstripe.org/software/addons/silverstripe-commercially-supported-module-list/)

Base project folder for a Silverstripe ([http://silverstripe.org](http://silverstripe.org)) installation. Required modules are installed via [http://github.com/silverstripe/recipe-cms](http://github.com/silverstripe/recipe-cms). For information on how to change the dependencies in a recipe, please have a look at [https://github.com/silverstripe/recipe-plugin](https://github.com/silverstripe/recipe-plugin). In addition, installer includes [theme/simple](https://github.com/silverstripe-themes/silverstripe-simple) as a default theme.

## Installation

```sh
composer require gacelabs/basic-instagram-feeds
```

## Usage

* Add this lines into your projects _config/app.yml or _config/mysite.yml:
```yml
Instagram:
  app_id: 'YOUR-INSTAGRAM-APP-ID'
  app_secret: 'YOUR-INSTAGRAM-APP-SECRET'
```
* Or set it like this if you want to set own cache file and redirect uri:
```yml
Instagram:
  app_id: 'YOUR-INSTAGRAM-APP-ID'
  app_secret: 'YOUR-INSTAGRAM-APP-SECRET'
  cache_file: 'YOUR-CACHE-TXT-FILENAME'
  redirect_uri: 'YOUR-INSTAGRAM-REDIRECT-URI'
```
* Pull the data in the back-end 
```php
	// set desired post limit as the functions parameter
	$limit = 10;
	SiteConfig::current_site_config()->getInstagramPosts($limit);
```
* Pull the data in the front-end 
```twig
	$SiteConfig.getInstagramPosts(10)
```

## For refreshing the Instagram Token

You can set a cron job with this url [https://your-project.com/dev/tasks/set-instagram-cache](https://your-project.com/dev/tasks/set-instagram-cache) to refresh, 
If current token is older than 24 hours but younger than 60 days.

## Bugtracker

Bugs are tracked on github.com ([plugin issues](https://github.com/gacelabs/basic-instagram-feeds/issues)).

## Links

 * [Plugin](https://github.com/gacelabs/basic-instagram-feeds)
 * [Developer](https://github.com/gacelabs)
