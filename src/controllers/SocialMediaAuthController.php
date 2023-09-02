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

	public function setData($type, $request)
	{
		// Debug::endshow($request);
		$siteConfig = SiteConfig::current_site_config();

		$token = $request->access_token;
		$expires = $request->expires_in;

		if ($type == 'instagram') {
			if ($token) {
				$siteConfig->InstagramToken = $token;
			}
		} else if ($type == 'facebook') {
			if ($token) {
				$siteConfig->FacebookToken = $token;
			}
		}
		if ($expires) {
			$siteConfig->TokenExpires = date('Y/m/d H:i:s', strtotime('+' . $expires . ' seconds'));
		}

		if ($token && $expires) {
			$siteConfig->write();
		}

		if (!Director::is_cli()) {
			Controller::curr()->redirect('/admin/settings/#Root_Instagram');
		}
	}

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
		$this->setData(__FUNCTION__, $new);
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
