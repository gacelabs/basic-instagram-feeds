## Overview

<!-- [![CI](https://github.com/silverstripe/silverstripe-installer/actions/workflows/ci.yml/badge.svg)](https://github.com/silverstripe/silverstripe-installer/actions/workflows/ci.yml) -->
[![Silverstripe supported module](https://img.shields.io/badge/silverstripe-supported-0071C4.svg)](https://www.silverstripe.org/software/addons/silverstripe-commercially-supported-module-list/)

A plugin for Silverstripe that request instagram feed information from the [Instagram Basic Display API](https://developers.facebook.com/docs/instagram-basic-display-api/getting-started/).

## Requirements
* PHP ^7.0 | ^8.2
* Silverstripe 4.*

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
  generated_token: 'YOUR-INSTAGRAM-USER-TOKEN'
```
`Get generated_token value:`
Go to Instagram Basic Display at your [Facebook developer site](https://developers.facebook.com/apps/), then navigate to `Basic Display > User Token Generator tab` 
add Instagram Test Users then click `Generate Token` button to get the your generated token for that instagram user

* set own cache file and redirect uri:
```yml
Instagram:
  app_id: 'YOUR-INSTAGRAM-APP-ID'
  app_secret: 'YOUR-INSTAGRAM-APP-SECRET'
  cache_file: 'YOUR-CACHE-TXT-FILENAME'
  redirect_uri: 'YOUR-INSTAGRAM-REDIRECT-URI'
```
`NOTE for redirect_uri:` 
Add it on your Instagram Client OAuth Settings [Facebook developer site](https://developers.facebook.com/apps/)
then echo the GET parameter "code" into your redirect uri script to get your access token, 
please refer to [plugin controller](https://github.com/gacelabs/basic-instagram-feeds/blob/main/src/controllers/SocialMediaAuthController.php)

`How to run:`
After installation and applying usage, run `dev/build` then go to CMS Menu `Settings > Instagram Tab` 
and click "Connect Account" button to initialized the first access token

## Pull the data

* In Back-end 
```php
$Posts = SiteConfig::current_site_config()->getInstagramPosts();

// or pull it from the cache file 
$Posts = SiteConfig::current_site_config()->getCachedFeed();
```
* In Front-end 
```html
<%-- Default --%>
<% if $SiteConfig.getInstagramPosts.Count %>
  <% loop $SiteConfig.getInstagramPosts %>
    <%-- code here --%>
  <% end_loop %>
<% end_if %>

<%-- From cache file --%>
<% if $SiteConfig.getCachedFeed.Count %>
  <% loop $SiteConfig.getCachedFeed %>
    <%-- code here --%>
  <% end_loop %>
<% end_if %>
```
## Returned fields
 * ID
 * Username
 * Caption
 * Link
 * Image
 * Timestamp

<!-- ## For refreshing the Instagram Token & cache file -->

<!-- You can set a cron job with this url [https://your-project.com/dev/tasks/set-instagram-cache](https://your-project.com/dev/tasks/set-instagram-cache) to refresh, 
If current token is older than 24 hours but younger than 60 days. -->

## Re-extending the SiteConfigExtension Class

* Updating the result data

```php
namespace Your\NameSpace;

use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\ArrayList;

class YourAnotherSiteConfigExtension extends DataExtension
{
  /**
   * Updates the default results.
   * 
   * @param ArrayList $list (assembled result)
   * @param array $data (instagram posts results)
   */
  public function updateInstagramPosts(ArrayList $list, $data)
  {
    // code
  }

  /**
   * Updates the cache results.
   * 
   * @param $cache (parsed result)
   */
  public function updateCachedFeed($cache)
  {
    // code
  }
}
```


## Bugtracker

Bugs are tracked on github.com ([plugin issues](https://github.com/gacelabs/basic-instagram-feeds/issues)).

## Links

 * [Create Instagram App](https://github.com/gacelabs/basic-instagram-feeds/blob/main/docs/instagram.md)
 * [Plugin](https://github.com/gacelabs/basic-instagram-feeds)
 * [Developer](https://github.com/gacelabs)
