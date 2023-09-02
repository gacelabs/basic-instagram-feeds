<?php

namespace GaceLabs\Apps\Extensions;

use Exception;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Core\Config\Config;
use SilverStripe\Control\Director;
use SilverStripe\Dev\Debug;
use SilverStripe\Forms\DateField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\View\Requirements;

class SiteConfigExtension extends DataExtension
{
	private static $db = [
		'InstagramToken' => 'Text',
		'TokenExpires' => 'Varchar'
	];

	public function updateCMSFields(FieldList $fields)
	{
		$fields->addFieldsToTab('Root.Instagram', [
			TextField::create('TokenExpires', 'Access Expires')->setReadonly(true),
			TextareaField::create('InstagramToken', 'Your Access Token')->setRows(2)->setReadonly(true),
			LiteralField::create(
				'FacebookButton',
				'<div class="fb-button">
					<a href="' . Director::absoluteBaseURL() . 'login-to-instagram"><i class="fa fa-instagram"></i> Connect Account</a>
				</div>'
			)
		]);

		Requirements::insertHeadTags('<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">');
	}

	public function getInstagramPosts($limit = null)
	{
		$fields = 'caption,id,media_type,media_url,permalink,thumbnail_url,timestamp,username';
		$url = 'https://graph.instagram.com/';
		$accessToken = $this->owner->InstagramToken;

		if ($accessToken) {
			if ($limit) {
				$url .= 'me/media?fields=' . $fields . '&access_token=' . $accessToken . '&limit=' . $limit;
			}

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$output = curl_exec($ch);
			curl_close($ch);

			// @todo check if feed data has been returned
			if (!$output) {
				throw new Exception('Error: getInstagramPosts() - cURL error: ' . curl_error($ch), curl_errno($ch));
			}

			return json_decode($output, true)['data'];
		}
	}
}
