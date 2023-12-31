<?php

namespace GaceLabs\Apps\Extensions;

use Exception;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Core\Config\Config;
use SilverStripe\Control\Director;
use SilverStripe\Dev\Debug;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\View\Requirements;

class InstagramSiteConfigExtension extends DataExtension
{
	private static $db = [
		'InstagramToken' => 'Text',
		'TokenExpires' => 'Varchar'
	];

	public function updateCMSFields(FieldList $fields)
	{
		$btn_url = Director::absoluteBaseURL() . 'login-to-instagram';
		$btn_text = 'Connect Account';
		if (!empty($this->owner->InstagramToken)) {
			$btn_text = 'Refresh Account';
			$btn_url = Director::absoluteURL('/dev/tasks/set-instagram-cache?limit=0&refresh=1');
		}
		$generated_token = Config::inst()->get('Instagram', 'generated_token') ?: $this->owner->InstagramToken;

		if (!empty($generated_token) AND empty($this->owner->InstagramToken)) {
			$this->owner->InstagramToken = $generated_token;
			$this->owner->write();
		}

		// Debug::endshow($generated_token);
		$fields->addFieldsToTab('Root.Instagram', [
			TextField::create('TokenExpires', 'Access Expires')->setReadonly(true),
			TextareaField::create('InstagramToken', 'Your Access Token')->setRows(2)->setValue($generated_token),
			LiteralField::create(
				'FacebookButton',
				'<div class="fb-button">
					<a href="' . $btn_url . '"><i class="fa fa-instagram"></i> '.$btn_text.'</a>
				</div>'
			)
		]);

		Requirements::customCSS('.fb-button a { color: rgb(255, 255, 255); box-shadow: rgba(0, 0, 0, 0.1) 0px 2px 3px 0px; text-transform: uppercase; font-weight: bold; padding: 15px 30px; border-radius: 4px; background: rgb(64, 128, 255); text-decoration: none; transition: all 0.3s ease 0s; } .fb-button a i { font-size: 25px; vertical-align: sub; }');
		Requirements::insertHeadTags('<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">');
	}

	public function getInstagramPosts($limit = null, $cached = true)
	{
		$fields = 'caption,id,media_type,media_url,permalink,thumbnail_url,timestamp,username';
		$url = 'https://graph.instagram.com/';
		$accessToken = $this->owner->InstagramToken;

		if ($accessToken) {
			$url .= 'me/media?fields=' . $fields . '&access_token=' . $accessToken;
			if ($limit) {
				$url .= '&limit=' . $limit;
			}

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$output = curl_exec($ch);
			curl_close($ch);

			$list = ArrayList::create();
			// @todo check if feed data has been returned
			$json = json_decode($output, true);
			// Debug::endshow($json);
			if (empty($json)) {
				throw new Exception('Error: getInstagramPosts() - cURL error: ' . curl_error($ch), curl_errno($ch));
			} else {
				if (count($json['data'])) {
					foreach ($json['data'] as $item) {
						$updatedData = [
							'ID' => $item['id'] ?? '',
							'Username' => $item['username'] ?? '',
							'Caption' => isset($item['caption']) ? DBField::create_field('Text', $item['caption']) : '',
							'Link' => $item['permalink'] ?? '',
							'Image' => isset($item['thumbnail_url']) ? $item['thumbnail_url'] : $item['media_url'],
							'Timestamp' => isset($item['timestamp']) ? DBField::create_field('Datetime', $item['timestamp']) : ''
						];
						$list->push($updatedData);
					}
					/* re-modify results */
					$this->owner->extend('updateInstagramPosts', $list, $json);
					$this->refresh($cached);
				} else {
					throw new Exception('Error: getInstagramPosts() - FB warning: no post found in instagram feeds');
				}
			}

			return $list;
		}
	}

	public function getCachedFeed($limit = null, $cached = false)
	{
		$this->refresh($cached);
		$cacheFile = Config::inst()->get('Instagram', 'cache_file') ?? 'instagram-cache.txt';
		$path = PUBLIC_PATH . DIRECTORY_SEPARATOR . $cacheFile;
		$fetched = empty(@file_get_contents($path)) ? 'a:0:{}' : file_get_contents($path);
		$cache = ArrayList::create(unserialize($fetched));
		/* re-modify results */
		if ($cache->Count()) {
			// Debug::endshow($cache);
			$this->owner->extend('updateCachedFeed', $cache);
			if ($limit) {
				$cache->limit($limit);
			}
			return $cache;
		} else {
			return false;
		}
	}

	private function refresh($cached = true)
	{
		if ($cached) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, Director::absoluteURL('/dev/tasks/set-instagram-cache'));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			// curl_exec($ch);
			curl_close($ch);
		}
	}
}
