<?php

namespace GaceLabs\Apps\Services;

use SilverStripe\SiteConfig\SiteConfig;
use Exception;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\Director;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Dev\Debug;

/**
 * Description
 * 
 * @package silverstripe
 * @subpackage mysite
 */
class InstagramApi
{
	const API_URL = 'https://graph.instagram.com/';
	const API_OAUTH_URL = 'https://api.instagram.com/oauth/authorize';
	const API_OAUTH_TOKEN_URL = 'https://api.instagram.com/oauth/access_token';
	const API_TOKEN_EXCHANGE_URL = 'https://graph.instagram.com/access_token';
	const API_TOKEN_REFRESH_URL = 'https://graph.instagram.com/refresh_access_token';

	private $_appId;
	private $_appSecret;
	private $_redirectUri;
	private $_accesstoken;
	private $_timeout = 90000;
	private $_connectTimeout = 30000;
	private $_scopes = ['user_profile', 'user_media', 'instagram_graph_user_profile', 'instagram_graph_user_media'];

	public function __construct(
		$appId,
		$appSecret,
		$redirectUri = null,
		$timeout = 90000,
		$connectTimeout = 20000
	) {
		$this->_redirectUri = $redirectUri ?: Director::absoluteURL('/social-media-auth/instagram-redirect');
		$this->_appId = $appId;
		$this->_appSecret = $appSecret;
		$this->_timeout = $timeout;
		$this->_connectTimeout = $connectTimeout;

		$SiteConfig = SiteConfig::current_site_config();
		if ($token = $SiteConfig->InstagramToken) {
			$this->_accesstoken = $token;
		}
	}

	public function getLoginUrl($scopes = ['user_profile', 'user_media', 'instagram_graph_user_profile', 'instagram_graph_user_media'], $state = '')
	{
		if (is_array($scopes) and count(array_intersect($scopes, $this->_scopes)) === count($scopes)) {
			$authURL = self::API_OAUTH_URL . '?client_id=' . $this->_appId . '&redirect_uri=' . urlencode($this->_redirectUri) . '&scope=' . implode(
				',', $scopes
			) . '&response_type=code' . ($state != '' ? '&state=' . $state : '');
			// Debug::endshow($authURL);
			return $authURL;
		}
		throw new Exception("Error: getLoginUrl() - The parameter isn't an array or invalid scope permissions used.");
	}

	public function getOAuthToken($code, $tokenOnly = false)
	{
		$apiData = array(
			'client_id' => $this->_appId,
			'client_secret' => $this->_appSecret,
			'grant_type' => 'authorization_code',
			'redirect_uri' => $this->_redirectUri,
			'code' => $code
		);

		$result = $this->_makeOAuthCall(self::API_OAUTH_TOKEN_URL, $apiData);

		return !$tokenOnly ? $result : $result->access_token;
	}

	public function getLongLivedToken($token, $tokenOnly = false)
	{
		$apiData = array(
			'client_secret' => $this->_appSecret,
			'grant_type' => 'ig_exchange_token',
			'access_token' => $token
		);

		$result = $this->_makeOAuthCall(self::API_TOKEN_EXCHANGE_URL, $apiData, 'GET');

		return !$tokenOnly ? $result : $result->access_token;
	}

	public function refreshToken($token, $tokenOnly = false)
	{
		$apiData = array(
			'grant_type' => 'ig_refresh_token',
			'access_token' => $token
		);

		$result = $this->_makeOAuthCall(self::API_TOKEN_REFRESH_URL, $apiData, 'GET');

		return !$tokenOnly ? $result : $result->access_token;
	}

	private function _makeOAuthCall($apiHost, $params, $method = 'POST')
	{
		$paramString = null;

		if (isset($params) and is_array($params)) {
			$paramString = '?' . http_build_query($params);
		}

		$apiCall = $apiHost . (('GET' === $method) ? $paramString : null);
		Debug::show($apiCall);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $apiCall);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_TIMEOUT_MS, $this->_timeout);

		if ($method === 'POST') {
			curl_setopt($ch, CURLOPT_POST, count($params));
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
		}

		$jsonData = curl_exec($ch);
		// Debug::endshow($jsonData);

		if (!$jsonData) {
			throw new Exception('Error: _makeOAuthCall() - cURL error: ' . curl_error($ch));
		}

		curl_close($ch);
		return json_decode($jsonData);
	}

	protected function _makeCall($function, $params = null, $method = 'GET')
	{
		if (!isset($this->_accesstoken)) {
			throw new Exception("Error: _makeCall() | $function - This method requires an authenticated users access token.");
		}

		$authMethod = '?access_token=' . $this->_accesstoken;

		$paramString = null;

		if (isset($params) && is_array($params)) {
			$paramString = '&' . http_build_query($params);
		}

		$apiCall = self::API_URL . $function . $authMethod . (('GET' === $method) ? $paramString : null);

		$headerData = array('Accept: application/json');

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $apiCall);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headerData);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, $this->_connectTimeout);
		curl_setopt($ch, CURLOPT_TIMEOUT_MS, $this->_timeout);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HEADER, true);

		$jsonData = curl_exec($ch);

		if (!$jsonData) {
			throw new Exception('Error: _makeCall() - cURL error: ' . curl_error($ch), curl_errno($ch));
		}

		list($headerContent, $jsonData) = explode("\r\n\r\n", $jsonData, 2);

		curl_close($ch);

		return json_decode($jsonData, true);
	}
}