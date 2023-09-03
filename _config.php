<?php

use GaceLabs\Apps\Services\InstagramApi;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Injector\Injector;

$ConfigInst = Config::inst();
$app_id = $ConfigInst->get('Instagram', 'app_id') ?: null;
$app_secret = $ConfigInst->get('Instagram', 'app_secret') ?: null;
$InstagramApiInstance = new InstagramApi($app_id, $app_secret);
Injector::inst()->registerService($InstagramApiInstance, InstagramApi::class);