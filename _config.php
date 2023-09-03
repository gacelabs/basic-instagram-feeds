<?php

use GaceLabs\Apps\Services\InstagramApi;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Injector\Injector;

$ConfigInst = Config::inst();
$app_id = $ConfigInst->get('Instagram', 'app_id') ?: null;
$app_secret = $ConfigInst->get('Instagram', 'app_secret') ?: null;
$redirect_uri = $ConfigInst->get('Instagram', 'redirect_uri') ?: null;
$InstagramApiInstance = new InstagramApi($app_id, $app_secret, $redirect_uri);
Injector::inst()->registerService($InstagramApiInstance, InstagramApi::class);