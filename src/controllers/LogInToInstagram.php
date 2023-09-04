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
		$url = $InstagramApi->getLoginUrl();
		return $this->redirect($url);
		/* echo $this->redirect($url);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$code = curl_exec($ch);
	
		if (!$code) {
			throw new Exception('Instagram valid OAuth redirect uri ' . (curl_error($ch) ?: 'GET parameter "code" was empty'), curl_errno($ch));
		}
		curl_close($ch);

		return $this->redirect(Director::absoluteURL('/social-media-auth/instagram-redirect?code='.trim($code))); */
	}
}
