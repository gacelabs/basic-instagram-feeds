<?php

use GaceLabs\Apps\Services\InstagramApi;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Injector\Injector;

$app_id = Config::inst()->get('Instagram', 'app_id');
$app_secret = Config::inst()->get('Instagram', 'app_secret');
$InstagramApiInstance = new InstagramApi($app_id, $app_secret);
Injector::inst()->registerService($InstagramApiInstance, InstagramApi::class);