<?php

namespace GaceLabs\Apps\Tasks;

use GaceLabs\Apps\Services\InstagramApi;
use SilverStripe\Dev\BuildTask;
use SilverStripe\ORM\ArrayList;
use SilverStripe\Control\Director;
use SilverStripe\Control\Controller;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Dev\Debug;
use SilverStripe\ORM\DB;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\SiteConfig\SiteConfig;

/**
 * Class InstagramCacheTask
 */
class InstagramCacheTask extends BuildTask
{
	protected $title = 'Set Instagram Cache';

	protected $description = 'Updates the cached instagram data';

	private static $segment = 'set-instagram-cache';

	public function run($request)
	{
		set_time_limit(0);

		$limit = $request->getVar('limit') ?? null;

		$cacheFile = Config::inst()->get('Instagram', 'cache_file') ?: 'instagram-cache.txt';
		// Debug::endshow($cacheFile);
		@fopen($cacheFile, "w");

		if (file_exists($cacheFile)) {
			$siteConfig = SiteConfig::current_site_config();
			$accessToken = $siteConfig->InstagramToken;

			// If current token is older than 24 hours but younger than 60 days, we can refresh it
			$expiryDate = $siteConfig->TokenExpires;
			$isOldEnough = date('Y/m/d H:i:s', strtotime('-24 hours', time())) > $expiryDate;
			$isYoungEnough = date('Y/m/d H:i:s') < $expiryDate;

			if ($isOldEnough AND $isYoungEnough) {
				$InstagramApi = Injector::inst()->get(InstagramApi::class);
				$new = $InstagramApi->refreshToken($accessToken);
				$siteConfig->InstagramToken = $new->access_token;
				$siteConfig->TokenExpires = $new->expires_in;
				$siteConfig->write();
			}

			$rawData = $siteConfig->getInstagramPosts($limit, false);
			if ($rawData) {
				$data = $this->setArrayData($rawData);
				$this->setCache($data, $cacheFile);
				DB::alteration_message('Cache has been updated', 'success');
			} else {
				DB::alteration_message('No access token present', 'error');
			}
		} else {
			DB::alteration_message('Instagram "cache_file" not set in config', 'error');
		}
	}

	public function setArrayData($output)
	{
		$list = ArrayList::create();

		foreach ($output as $item) {
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

		return $list;
	}

	private function setCache($cache, $cacheFile)
	{
		$path = PUBLIC_PATH . DIRECTORY_SEPARATOR . $cacheFile;
		file_put_contents($path, serialize($cache));
	}
}
