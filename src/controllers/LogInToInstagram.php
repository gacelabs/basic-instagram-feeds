<?php

namespace GaceLabs\Apps\Controllers;

use Exception;
use GaceLabs\Apps\Services\InstagramApi;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Dev\Debug;
use SilverStripe\SiteConfig\SiteConfig;

class LogInToInstagram extends Controller
{
	private static $allowed_actions = [];

	private static $url_handlers = [];

	public function index($request)
	{
		$InstagramApi = Injector::inst()->get(InstagramApi::class);
		return $this->redirect($InstagramApi->getLoginUrl());
	}
}
