<?php

namespace GaceLabs\Apps\Controllers;

use Exception;
use GaceLabs\Apps\Services\InstagramApi;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Dev\Debug;
use SilverStripe\SiteConfig\SiteConfig;

class SocialMediaAuthController extends Controller
{
	private static $allowed_actions = [
		'instagram',
		'facebook',
		'callback',
		'delete'
	];

	private static $url_handlers = [
		'instagram-redirect' => 'instagram',
		'facebook-redirect' => 'facebook'
	];

	public function instagram($request)
	{
		if (!$code = $request->getVar('code')) {
			throw new Exception('Error Processing Request, getVar("code") not found!');
		}

		$InstagramApi = Injector::inst()->get(InstagramApi::class);
		$shortLivedToken = $InstagramApi->getOAuthToken($code, true);
		$new = $InstagramApi->getLongLivedToken($shortLivedToken, true);

		$siteConfig = SiteConfig::current_site_config();
		$siteConfig->InstagramToken = $new->access_token;
		$siteConfig->TokenExpires = $new->expires_in;
		$siteConfig->write();

		if (!Director::is_cli()) {
			$this->redirect('/admin/settings/#Root_Instagram');
		}
		// echo $new->access_token;
	}
	
	public function facebook($request)
	{
		// Debug::endshow($request);
		throw new Exception('Error Processing Request, '.__FUNCTION__.' not yet made!');
	}
	
	public function callback($request)
	{
		// Debug::endshow($request);
		throw new Exception('Error Processing Request, '.__FUNCTION__.' not yet made!');
	}

	public function delete($request)
	{
		// Debug::endshow($request);
		throw new Exception('Error Processing Request, '.__FUNCTION__.' not yet made!');
	}
}
