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
		// Debug::endshow($InstagramApi->getLoginUrl());
		// return $this->redirect($InstagramApi->getLoginUrl());
		// $code = file_get_contents($InstagramApi->getLoginUrl());
		// $code = file_get_contents('https://www.instagram.com/oauth/authorize?client_id=328483909555824&redirect_uri=https://gacelabs-redirect.infinityfreeapp.com/instagram_ruri.php&scope=user_profile,user_media,instagram_graph_user_profile,instagram_graph_user_media&response_type=code');
		// Debug::endshow($code);
		$url = $InstagramApi->getLoginUrl();
		// Debug::endshow($url);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		// curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		// curl_setopt($ch, CURLOPT_HEADER, true);

		/* curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); */

		$code = curl_exec($ch);
		
		if (!$code) {
			throw new Exception('Error: '.__CLASS__.'() - cURL error: ' . curl_error($ch), curl_errno($ch));
		}

		curl_close($ch);
		// Debug::endshow($code);
		return $this->redirect(Director::absoluteURL('/social-media-auth/instagram-redirect?code='.$code));
	}
}
