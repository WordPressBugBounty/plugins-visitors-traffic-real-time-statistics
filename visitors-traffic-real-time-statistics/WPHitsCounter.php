<?php

class WPHitsCounter
{

	var $pageId;
	var $pageTitle;
	var $postType;
	var $ipAddress;
	var $ipIsUnknown;
	var $userAgent;
	var $referer;
	var $refererSite;
	var $browser;
	var $searchEngine;
	var $countryInternetCode;
	var $countryId;
	var $keyWords;
	var $requestUri;

	/**

	 * Constructor

	 *

	 * @param integer $page_id

	 * @param string $page_title Optional

	 * @param string $post_type Optional

	 */
	public function __construct($page_id, $page_title = NULL, $post_type = NULL)
	{

		global $_SERVER;

		$this->ipAddress = ahcfree_get_client_ip_address();


		if ($this->ipAddress == 'UNKNOWN') {

			$this->ipIsUnknown = true;

			$this->ipAddress = 'UNKNOWN' . uniqid();
		} else {

			$this->ipIsUnknown = false;
		}



		$this->userAgent = ahc_free_sanitize_text_or_array_field($_SERVER['HTTP_USER_AGENT']);

		$this->pageId = (isset($page_id)) ? $page_id : ahc_free_sanitize_text_or_array_field($_GET['page_id']);

		$this->pageTitle = $page_title;

		$this->postType = $post_type;

		$this->requestUri = trim($_SERVER['REQUEST_URI'], '/');

		//$post_permalink = get_the_permalink($this->pageId);
		//$protocol_arr = array('http://','https://','www.');	
		//$link = str_replace($protocol_arr,'',$post_permalink);
		//$this->requestUri = trim(str_replace($_SERVER['HTTP_HOST'],'',$link),'/');

		if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {

			$hostName = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);

			if ($hostName != $_SERVER['SERVER_NAME']) {

				$this->referer = ahc_free_sanitize_text_or_array_field($_SERVER['HTTP_REFERER']);

				$this->refererSite = $hostName;
			}
		}

		$this->searchEngine = NULL;

		$this->keyWords = NULL;

		$this->countryId = NULL;
	}

	//--------------------------------------------
	//--------------------------------------------

	/**

	 * Trace visitor hit

	 *

	 * @return void

	 */
	public function traceVisitorHit()
	{



		//$this->cleanUnwantedRecords();

		$this->cleanHitsTable();

		if (!$this->isHitRecorded()) {

			$visitorRecorded = $this->isVisitorRecorded();

			$this->getBrowser();

			$this->getCountryId();

			usleep(10000);

			if (!empty($this->refererSite)) {

				$this->getSearchEngine();
			}


			/*
	    if (!$this->isTodayPreparedInDb()) {

		$this->PrepareForTodayInDb();
	    }*/



			if (!$visitorRecorded) {

				$this->updateVisitsTime(1, 1);

				$this->updateVisitors(1, 1);
			} else {

				$this->updateVisitsTime(0, 1);

				$this->updateVisitors(0, 1);
			}


			if (!empty($this->pageId) && !empty($this->pageTitle) && ($this->postType == 'post' or $this->postType == 'product' or $this->postType == 'page')) {



				$this->updateTitleTraffic($this->pageId, $this->pageTitle);
			}



			if (!empty($this->keyWords) && !empty($this->searchEngine)) {

				$this->updateKeywords($this->ipAddress, $this->keyWords, $this->referer, $this->searchEngine, $this->browser, $this->countryId);
			}



			if (!empty($this->refererSite)) {

				$this->updateReferingSites($this->refererSite);
			}



			if (!empty($this->searchEngine)) {

				$this->updateSearchingVisits($this->searchEngine);
			}



			if (!empty($this->countryId)) {

				if ($visitorRecorded) {

					$this->updateCountries($this->countryId, 0, 1);
				} else {

					$this->updateCountries($this->countryId, 1, 1);
				}
			}

			$this->updateBrowsers($this->browser);



			if (!$visitorRecorded) {

				$this->updateRecentVisitors($this->ipAddress, $this->referer, $this->searchEngine, $this->browser, $this->countryId);
			}



			$this->recordThisHits();
		}
	}

	//--------------------------------------------

	/**

	 * Is visit is already recorded

	 *

	 * @return boolean

	 */
	protected function isHitRecorded()
	{

		global $wpdb;
		$custom_timezone_offset = ahcfree_get_current_timezone_offset();

		$wpdb->insert(
			'ahc_online_users',
			array(
				'date'			=> ahcfree_localtime('Y-m-d H:i:s'),
				'hit_ip_address' => $this->ipAddress,
				'hit_page_id'	=>	$this->pageId,
				'site_id'	=>	get_current_blog_id()
			)
		);

		//$sql = "SELECT COUNT(`hit_id`) AS ct  FROM `ahc_hits` WHERE DATE(`hit_date`) = '". ahcfree_localtime("Y-m-d") ."' AND `hit_ip_address` = %s AND `hit_page_id` = %s";

		$sql = "SELECT COUNT(`hit_id`) AS ct FROM `ahc_hits` WHERE DATE(CONVERT_TZ(CONCAT_WS(' ',hit_date,hit_time),'" . AHCFREE_SERVER_CURRENT_TIMEZONE . "','" . $custom_timezone_offset . "')) = '" . ahcfree_localtime("Y-m-d") . "' AND `hit_ip_address` = %s AND `hit_page_id` = %s AND `site_id` = %d ";

		$result = $wpdb->get_results($wpdb->prepare($sql, $this->ipAddress, $this->pageId, get_current_blog_id()), OBJECT);

		if ($result !== false) {

			return ((int) $result[0]->ct > 0);
		}
	}

	//--------------------------------------------

	/**

	 * Is visitor is already recorded

	 *

	 * @return boolean

	 */
	protected function isVisitorRecorded()
	{

		global $wpdb;

		$custom_timezone_offset = ahcfree_get_current_timezone_offset();

		/*$sql = "SELECT COUNT(`hit_id`) AS ct  FROM `ahc_hits` WHERE DATE(`hit_date`) = '". gmdate("Y-m-d") ."' AND `hit_ip_address` = %s";*/

		$sql = "SELECT COUNT(`hit_id`) AS ct  FROM `ahc_hits` WHERE site_id = " . get_current_blog_id() . " and DATE(CONVERT_TZ(CONCAT_WS(' ',hit_date,hit_time),'" . AHCFREE_SERVER_CURRENT_TIMEZONE . "','" . $custom_timezone_offset . "')) = '" . ahcfree_localtime("Y-m-d") . "' AND `hit_ip_address` = %s";

		$result = $wpdb->get_results($wpdb->prepare($sql, $this->ipAddress), OBJECT);

		if ($result !== false) {

			return ((int) $result[0]->ct > 0);
		}
	}

	//--------------------------------------------

	/**

	 * Detect client browser

	 *

	 * @return void

	 */
	public function get_browser_id($browser)
	{
		global $wpdb;
		$sql = "SELECT `bsr_id` FROM `ahc_browsers` WHERE `bsr_name` = %s and site_id = %d ";

		$results = $wpdb->get_results($wpdb->prepare($sql, $browser, get_current_blog_id()), OBJECT);

		if ($results !== false && !empty($results)) {
			return $results[0]->bsr_id;
		}
		return 0;
	}
	protected function getBrowser()
	{
		if (strpos($this->userAgent, 'MSIE') !== false || strpos($this->userAgent, 'Trident') !== false) {
			// Internet Explorer
			$this->browser = $this->get_browser_id('IE');
		} elseif (strpos($this->userAgent, 'Firefox') !== false) {
			// Firefox
			$this->browser = $this->get_browser_id('Firefox');
		} elseif (strpos($this->userAgent, 'Chrome') !== false) {
			if (strpos($this->userAgent, 'Edg') !== false) {
				// Edge (based on Chromium)
				$this->browser = $this->get_browser_id('Edge');
			} elseif (strpos($this->userAgent, 'OPR') !== false || strpos($this->userAgent, 'Opera') !== false) {
				// Opera (based on Chromium)
				$this->browser = $this->get_browser_id('Opera');
			} else {
				// Chrome
				$this->browser = $this->get_browser_id('Chrome');
			}
		} elseif (strpos($this->userAgent, 'Safari') !== false && strpos($this->userAgent, 'Chrome') === false) {
			// Safari (make sure it is not Chrome)
			$this->browser = $this->get_browser_id('Safari');
		} elseif (strpos($this->userAgent, 'Opera Mini') !== false) {
			// Opera Mini
			$this->browser = $this->get_browser_id('Opera Mini');
		} elseif (strpos($this->userAgent, 'Netscape') !== false) {
			// Netscape
			$this->browser = $this->get_browser_id('Netscape');
		} elseif (strpos($this->userAgent, 'Gecko') !== false) {
			// Gecko/Mozilla
			$this->browser = $this->get_browser_id('Gecko/Mozilla');
		} elseif (strpos($this->userAgent, 'iPad') !== false) {
			// iPad
			$this->browser = $this->get_browser_id('iPad');
		} elseif (strpos($this->userAgent, 'Android') !== false) {
			// Android
			$this->browser = $this->get_browser_id('Android');
		} elseif (strpos($this->userAgent, 'AIR') !== false) {
			// AIR
			$this->browser = $this->get_browser_id('AIR');
		} elseif (strpos($this->userAgent, 'Fluid') !== false) {
			// Fluid
			$this->browser = $this->get_browser_id('Fluid');
		} elseif (strpos($this->userAgent, 'Maxthon') !== false) {
			// Maxthon
			$this->browser = $this->get_browser_id('Maxthon');
		} else {
			// Unknown
			$this->browser = $this->get_browser_id('unknown');
		}
	}


	//--------------------------------------------

	/**

	 * Detect country internet code

	 *

	 * @return void

	 */


	protected function getCountryInternetCode()
	{
		if (!$this->ipIsUnknown) {

			// First try: ipinfo.io (50,000 requests/month free, no key needed)
			$ip_data = ahcfree_advanced_get_link("https://ipinfo.io/" . $this->ipAddress . "/country");
			if ($ip_data && is_string($ip_data) && strlen(trim($ip_data)) == 2) {
				$this->countryInternetCode = trim($ip_data);
				return;
			}

			// Fallback: ipapi.co (30,000 requests/month free, no key needed)
			$ip_data = ahcfree_advanced_get_link("https://ipapi.co/" . $this->ipAddress . "/country/");
			if ($ip_data && is_string($ip_data) && strlen(trim($ip_data)) == 2) {
				$this->countryInternetCode = trim($ip_data);
				return;
			}

			// Last resort: Your original APIs
			$ip_data = ahcfree_advanced_get_link("http://ip-api.com/json/" . $this->ipAddress);
			$countryCode = isset($ip_data->countryCode) ? $ip_data->countryCode : '';
			if (trim($countryCode) != '' && strlen($countryCode) == 2) {
				$this->countryInternetCode = $countryCode;
			} else {
				$ip_data = ahcfree_advanced_get_link("https://geoip-db.com/json/" . $this->ipAddress);
				$this->countryInternetCode = isset($ip_data->country_code) ? $ip_data->country_code : '';
				if (empty($ip_data->country_code)) {
					$ip_data = ahcfree_advanced_get_link("http://www.geoplugin.net/json.gp?ip=" . $this->ipAddress);
					$this->countryInternetCode = isset($ip_data->geoplugin_countryCode) ? $ip_data->geoplugin_countryCode : '';
				}
			}
		}

		if (empty($this->countryInternetCode)) {
			$this->countryInternetCode = 'XX';
		}
	}

	//--------------------------------------------

	/**

	 * Detect country ID

	 *

	 * @uses wpdb::prepare()

	 * @uses wpdb::get_results()

	 *

	 * @return void

	 */
	protected function getCountryId()
	{

		global $wpdb;

		$this->getCountryInternetCode();

		if (!empty($this->countryInternetCode)) {

			$sql = "SELECT `ctr_id` FROM `ahc_countries` WHERE `ctr_internet_code` = %s  and site_id = %d ";

			$results = $wpdb->get_results($wpdb->prepare($sql, $this->countryInternetCode, get_current_blog_id()), OBJECT);

			if ($results !== false && !empty($results)) {

				$this->countryId = $results[0]->ctr_id;

				return;
			}
		}
	}

	//--------------------------------------------

	/**

	 * Detect search engine

	 *

	 * @uses wpdb::prepare()

	 * @uses wpdb::get_results()

	 *

	 * @return void

	 */
	protected function getSearchEngine()
	{

		global $wpdb;

		$sql = "SELECT `srh_id`, `srh_query_parameter`, `srh_identifier` FROM `ahc_search_engines`";

		$results = $wpdb->get_results($sql, OBJECT);

		if ($results !== false) {

			foreach ($results as $s) {

				if (strpos($this->referer, $s->srh_identifier . '.') !== false) {

					$this->searchEngine = $s->srh_id;

					$this->getKeyWords($s->srh_query_parameter);
				}
			}
		}
	}

	//--------------------------------------------

	/**

	 * Detect search engine

	 *

	 * @uses wpdb::prepare()

	 * @uses wpdb::get_results()

	 *

	 * @return void

	 */
	protected function getKeyWords($query_param)
	{

		$query = parse_url($this->referer, PHP_URL_QUERY);

		$query = rawurldecode($query);

		$arr = array();

		parse_str($query, $arr);

		if (isset($arr[$query_param])) {

			$this->keyWords = $arr[$query_param];
		}
	}

	//--------------------------------------------

	/**

	 * Is there a record prepared for today's visits

	 *

	 * @uses wpdb::get_results()

	 *

	 * @return boolean

	 */
	protected function isTodayPreparedInDb()
	{

		global $wpdb;

		$del_sql = "DELETE v1 FROM ahc_visitors v1, ahc_visitors v2 WHERE v1.`vst_id` > v2.`vst_id` AND v1.`vst_date` = v2.`vst_date`";

		//$del_sql = "DELETE v1 FROM ahc_visitors v1, ahc_visitors v2 WHERE v1.`vst_id` > v2.`vst_id` AND CONVERT_TZ(v1.vst_date, '" . AHCFREE_SERVER_CURRENT_TIMEZONE . "','" . $custom_timezone_offset . "') = CONVERT_TZ(v2.vst_date, '" . AHCFREE_SERVER_CURRENT_TIMEZONE . "','" . $custom_timezone_offset . "') ";

		$del_result = $wpdb->get_results($del_sql, OBJECT);


		$sql = "SELECT COUNT(`vst_id`) AS ct  FROM `ahc_visitors` WHERE site_id = " . get_current_blog_id() . " and DATE(`vst_date`) = '" . gmdate("Y-m-d") . "'";

		//$sql = "SELECT COUNT(`vst_id`) AS ct  FROM `ahc_visitors` WHERE DATE(CONVERT_TZ(vst_date,'" . AHCFREE_SERVER_CURRENT_TIMEZONE . "','" . $custom_timezone_offset . "')) = DATE(CONVERT_TZ(NOW( ),'" . AHCFREE_SERVER_CURRENT_TIMEZONE . "','" . $custom_timezone_offset . "'))";

		$result = $wpdb->get_results($sql, OBJECT);

		if ($result !== false) {

			return ((int) $result[0]->ct > 0);
		}
	}

	//--------------------------------------------

	/**

	 * Prepared a record for today's visits

	 *

	 * @uses wpdb::query()

	 *

	 * @return boolean

	 */
	protected function PrepareForTodayInDb()
	{

		global $wpdb;

		$sql = "INSERT INTO `ahc_visitors` (`vst_date`, `vst_visitors`, `vst_visits`, `site_id`) VALUES ('" . gmdate("Y-m-d H:i:s") . "', 0, 0,'" . get_current_blog_id() . "')";

		if ($wpdb->query($sql) !== false) {

			return true;
		}

		return false;
	}

	//--------------------------------------------

	/**

	 * Clean daily hits table

	 *

	 * @uses wpdb::query()

	 *

	 * @return boolean

	 */
	public function cleanHitsTable()
	{
		global $wpdb;

		// Get current time using saved timezone and calculate 90 days ago
		$current_time = $this->getCurrentLocalTime();

		try {
			$timezone = new DateTimeZone($current_time['timezone']);
			$datetime = new DateTime('now', $timezone);
			$datetime->modify('-90 days');
			$threshold_date = $datetime->format('Y-m-d H:i:s');
		} catch (Exception $e) {
			// Fallback calculation using saved timezone
			$threshold_date = date('Y-m-d H:i:s', strtotime('-90 days'));
		}

		// Delete from ahc_online_users where date is older than 90 days
		$sql1 = $wpdb->prepare("DELETE FROM ahc_online_users WHERE `date` < %s", $threshold_date);
		$wpdb->query($sql1);

		// Delete from ahc_hits where hit_date is older than 90 days
		$sql2 = $wpdb->prepare("DELETE FROM `ahc_hits` WHERE `hit_date` < %s", $threshold_date);
		return ($wpdb->query($sql2) !== false);
	}

	//--------------------------------------------

	/**

	 * Update browser visits

	 *

	 * @uses wpdb::prepare()

	 * @uses wpdb::query()

	 *

	 * @param integer $bsr_id

	 * @return boolean

	 */
	protected function updateBrowsers($bsr_id)
	{

		global $wpdb;

		$sql = "UPDATE `ahc_browsers` SET bsr_visits = bsr_visits + 1 WHERE bsr_id = %d  and site_id=%d";

		if ($wpdb->query($wpdb->prepare($sql, $bsr_id, get_current_blog_id())) !== false) {

			return true;
		}

		return false;
	}

	//--------------------------------------------

	/**

	 * Update country visits

	 *

	 * @uses wpdb::prepare()

	 * @uses wpdb::query()

	 *

	 * @param integer $ctr_id

	 * @param integer $visitors Optional

	 * @param integer $visits Optional

	 * @return boolean

	 */
	protected function updateCountries($ctr_id, $visitors = 0, $visits = 0)
	{

		global $wpdb;

		$sql = "UPDATE `ahc_countries` SET ctr_visitors = ctr_visitors + %d, ctr_visits = ctr_visits + %d WHERE ctr_id = %d  and site_id = %d";

		return ($wpdb->query($wpdb->prepare($sql, $visitors, $visits, $ctr_id, get_current_blog_id())) !== false);
	}

	//--------------------------------------------

	/**

	 * Update visits sum order by search engine

	 *

	 * @uses wpdb::prepare()

	 * @uses wpdb::get_results()

	 * @uses wpdb::query()

	 *

	 * @param integer $srh_id

	 * @return boolean

	 */
	protected function updateSearchingVisits($srh_id)
	{
		global $wpdb;

		// Get current time using saved timezone
		$current_time = $this->getCurrentLocalTime();
		$custom_timezone_offset = ahcpro_get_current_timezone_offset();

		$sql = "SELECT vtsh_id FROM `ahc_searching_visits` WHERE site_id = %d AND srh_id = %d AND DATE(vtsh_date) = '" . $current_time['date'] . "'";

		$result = $wpdb->get_results($wpdb->prepare($sql, get_current_blog_id(), $srh_id), OBJECT);

		if ($result !== false) {
			if ($wpdb->num_rows > 0) {
				$sql2 = "UPDATE `ahc_searching_visits` SET vtsh_visits = vtsh_visits + 1 WHERE vtsh_id = %d and site_id=%d";
				return ($wpdb->query($wpdb->prepare($sql2, $result[0]->vtsh_id, get_current_blog_id())) !== false);
			} else {
				$sql2 = "INSERT INTO `ahc_searching_visits` (srh_id, vtsh_date, vtsh_visits,site_id) 
                    VALUES (%d, %s, 1,%d)";
				return ($wpdb->query($wpdb->prepare($sql2, $srh_id, $current_time['full'], get_current_blog_id())) !== false); // FIXED: Use saved timezone
			}
		} else {
			return false;
		}
	}


	//--------------------------------------------

	/**

	 * Update visitors count

	 *

	 * @uses wpdb::prepare()

	 * @uses wpdb::query()

	 *

	 * @param integer $visitors Optional

	 * @param integer $visits Optional

	 * @return boolean

	 */
	protected function updateVisitors($visitors = 0, $visits = 0)
	{
		global $wpdb;

		// Get current time using saved timezone
		$current_time = $this->getCurrentLocalTime();

		$sql = "INSERT INTO `ahc_daily_visitors_stats` (vst_date, vst_visitors, vst_visits,site_id) values(%s, %d, %d, %d )";
		$wpdb->query($wpdb->prepare($sql, $current_time['full'], $visitors, $visits, get_current_blog_id())); // FIXED: Use saved timezone

		$sql = "INSERT INTO `ahc_visitors` (vst_date, vst_visitors, vst_visits,site_id) values(%s, %d, %d , %d )";
		return ($wpdb->query($wpdb->prepare($sql, $current_time['full'], $visitors, $visits, get_current_blog_id())) !== false); // FIXED: Use saved timezone
	}
	//--------------------------------------------

	/**

	 * Update referring sites visits table

	 *

	 * @uses wpdb::prepare()

	 * @uses wpdb::query()

	 * @uses wpdb::get_results()

	 *

	 * @param string $rfr_site_name. referring site name

	 * @return boolean

	 */
	protected function updateReferingSites($rfr_site_name)
	{

		global $wpdb;

		$sql = "SELECT rfr_id FROM `ahc_refering_sites` where rfr_site_name = %s and site_id=%d";

		$result = $wpdb->get_results($wpdb->prepare($sql, $rfr_site_name, get_current_blog_id()), OBJECT);

		if ($result !== false) {

			if (!empty($result)) {

				$sql2 = "UPDATE `ahc_refering_sites` SET rfr_visits = rfr_visits + 1 WHERE rfr_id = %d and site_id=%d";

				return ($wpdb->query($wpdb->prepare($sql2, $result[0]->rfr_id, get_current_blog_id())) !== false);
			} else {

				$sql2 = "INSERT INTO `ahc_refering_sites` (rfr_site_name, rfr_visits,site_id) 

						VALUES(%s, 1,%d)";

				return ($wpdb->query($wpdb->prepare($sql2, $rfr_site_name, get_current_blog_id())) !== false);
			}
		} else {

			return false;
		}
	}

	//--------------------------------------------

	/**

	 * Update recent visitors table

	 *

	 * @uses wpdb::prepare()

	 * @uses wpdb::query()

	 *

	 * @param string $vtr_ip_address. IP address

	 * @param string $vtr_referer Optional. Referring site name

	 * @param integer $srh_id Optional. Search engine ID

	 * @param integer $bsr_id Optional. Browser ID

	 * @param integer $ctr_id Optional. Country ID

	 * @return boolean

	 */
	protected function updateRecentVisitors($vtr_ip_address, $vtr_referer = '', $srh_id = NULL, $bsr_id = NULL, $ctr_id = NULL)
	{
		global $wpdb;

		// Get current time using saved timezone
		$current_time = $this->getCurrentLocalTime();

		$ahc_city = '';
		$ahc_region = '';

		// Performance: Use only 2 APIs - primary and fallback
		$apis = array(
			// Primary: ipapi.co - Fast, reliable, 1000 requests/day free
			array(
				'url' => "https://ipapi.co/{$vtr_ip_address}/json/",
				'city_field' => 'city',
				'region_field' => 'region',
				'timeout' => 3 // Quick timeout for performance
			),
			// Fallback: ip-api.com - Fast, reliable, unlimited free (non-commercial)
			array(
				'url' => "http://ip-api.com/json/{$vtr_ip_address}",
				'city_field' => 'city',
				'region_field' => 'regionName',
				'timeout' => 5 // Slightly longer timeout for fallback
			)
		);

		// Performance: Try only primary, then fallback if needed
		foreach ($apis as $api) {
			try {
				// Performance: Add timeout to prevent slow responses
				$ip_data = $this->optimized_get_link($api['url'], $api['timeout']);
				if (!empty($ip_data)) {
					$city = isset($ip_data->{$api['city_field']}) ? trim($ip_data->{$api['city_field']}) : '';
					$region = isset($ip_data->{$api['region_field']}) ? trim($ip_data->{$api['region_field']}) : '';
					// Performance: If we get valid data, use it and stop
					if (!empty($city) && $city != 'null' && strlen($city) > 1) {
						$ahc_city = $city;
					}
					if (!empty($region) && $region != 'null' && strlen($region) > 1) {
						$ahc_region = $region;
					}
					// Performance: If we got city data, we're done (don't need to try fallback)
					if (!empty($ahc_city)) {
						break;
					}
				}
			} catch (Exception $e) {
				// Performance: Log error only in debug mode to avoid overhead
				if (defined('WP_DEBUG') && WP_DEBUG) {
					error_log("Geolocation API error for {$api['url']}: " . $e->getMessage());
				}
				continue;
			}
		}

		// Performance: Use prepared statement with proper placeholders
		$sql = "INSERT INTO `ahc_recent_visitors`
        (vtr_ip_address, vtr_referer, srh_id, bsr_id, ctr_id, ahc_city, ahc_region, vtr_date, vtr_time, site_id)
        VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %d)";

		return ($wpdb->query($wpdb->prepare(
			$sql,
			$vtr_ip_address,
			$vtr_referer,
			$srh_id,
			$bsr_id,
			$ctr_id,
			$ahc_city,
			$ahc_region,
			$current_time['date'],    // FIXED: Use saved timezone instead of gmdate
			$current_time['time'],    // FIXED: Use saved timezone instead of gmdate
			get_current_blog_id()
		)) !== false);
	}
	private function optimized_get_link($url, $timeout = 5)
	{
		// Performance: Check if we have a cached result (optional - implement if needed)
		$cache_key = 'geo_' . md5($url);
		$cached = wp_cache_get($cache_key, 'geolocation');
		if ($cached !== false) {
			return $cached;
		}

		// Performance: Use WordPress HTTP API with optimized settings
		$args = array(
			'timeout' => $timeout,
			'redirection' => 2, // Limit redirects
			'httpversion' => '1.1',
			'user-agent' => 'Mozilla/5.0 (compatible; WordPressBotGeo/1.0)',
			'headers' => array(
				'Accept' => 'application/json',
				'Accept-Encoding' => 'gzip, deflate'
			),
			'compress' => true,
			'decompress' => true,
			'sslverify' => false // Performance: Skip SSL verification for speed (consider security implications)
		);

		$response = wp_remote_get($url, $args);

		// Performance: Quick error checking
		if (is_wp_error($response)) {
			throw new Exception('HTTP request failed: ' . $response->get_error_message());
		}

		$response_code = wp_remote_retrieve_response_code($response);
		if ($response_code !== 200) {
			throw new Exception('HTTP error: ' . $response_code);
		}

		$body = wp_remote_retrieve_body($response);
		if (empty($body)) {
			throw new Exception('Empty response body');
		}

		// Performance: Parse JSON response
		$data = json_decode($body);
		if (json_last_error() !== JSON_ERROR_NONE) {
			throw new Exception('Invalid JSON response');
		}

		// Performance: Cache successful results for 1 hour
		wp_cache_set($cache_key, $data, 'geolocation', 3600);

		return $data;
	}
	//--------------------------------------------

	/**

	 * Update key words table

	 *

	 * @uses wpdb::prepare()

	 * @uses wpdb::query()

	 *

	 * @param string $vtr_ip_address. IP address

	 * @param string $kwd_keywords. Key word

	 * @param string $kwd_referer. Referring site name.

	 * @param integer $srh_id. Search engine ID

	 * @param integer $bsr_id. Browser ID

	 * @param integer $ctr_id Optional. Country ID

	 * @return boolean

	 */
	protected function updateKeywords($kwd_ip_address, $kwd_keywords, $kwd_referer, $srh_id, $bsr_id, $ctr_id = NULL)
	{
		global $wpdb;

		// Get current time using saved timezone
		$current_time = $this->getCurrentLocalTime();

		$sql = "INSERT INTO `ahc_keywords` (kwd_ip_address, kwd_keywords, kwd_referer, srh_id, ctr_id, bsr_id, kwd_date, kwd_time,site_id) 
            VALUES (%s, %s, %s, %d, %d, %d, %s, %s, %d)";

		return ($wpdb->query($wpdb->prepare(
			$sql,
			$kwd_ip_address,
			$kwd_keywords,
			$kwd_referer,
			$srh_id,
			$ctr_id,
			$bsr_id,
			$current_time['date'],    // FIXED: Use saved timezone instead of gmdate
			$current_time['time'],    // FIXED: Use saved timezone instead of gmdate
			get_current_blog_id()
		)) !== false);
	}

	//--------------------------------------------

	/**

	 * Clean unwanted records. Only keeping a limit of fresh records. Limit is set by AHCFREE_RECENT_VISITORS_LIMIT

	 *

	 * @uses wpdb::prepare()

	 * @uses wpdb::get_results()

	 * @uses wpdb::query()

	 *

	 * @return boolean

	 */
	protected function cleanUnwantedRecords()
	{

		global $wpdb;

		$sql11 = "SELECT vtr_id FROM `ahc_recent_visitors` where site_id = %d  ORDER BY vtr_id LIMIT %d";

		$result = $wpdb->get_results($wpdb->prepare($sql11, get_current_blog_id(), AHCFREE_RECENT_VISITORS_LIMIT), OBJECT);

		if ($result !== false) {

			$ids1 = array();

			$length = count($result);

			foreach ($result as $r) {

				$ids1[] = $r->vtr_id;
			}

			$ids1 = implode(',', $ids1);

			$sql12 = "DELETE FROM `ahc_recent_visitors`" . ((!empty($ids1)) ? " WHERE site_id = " . get_current_blog_id() . " and  vtr_id NOT IN (" . $ids1 . ")" : "");



			$sql21 = "SELECT kwd_id FROM `ahc_keywords` where site_id=%d  ORDER BY kwd_id LIMIT %d";

			$result2 = $wpdb->get_results($wpdb->prepare($sql21, get_current_blog_id(), AHCFREE_RECENT_KEYWORDS_LIMIT), OBJECT);

			if ($result2 !== false) {

				$ids2 = array();

				foreach ($result2 as $r) {

					$ids2[] = $r->kwd_id;
				}

				$ids2 = implode(',', $ids2);

				$sql22 = "DELETE FROM `ahc_keywords`" . ((!empty($ids2)) ? " WHERE kwd_id NOT IN (" . $ids2 . ")" : "");



				if ($wpdb->query($sql12) !== false) {

					return ($wpdb->query($sql22) !== false);
				}
			}
		}

		return false;
	}

	//--------------------------------------------

	/**

	 * Update traffic by title table

	 *

	 * @uses wpdb::prepare()

	 * @uses wpdb::get_results()

	 * @uses wpdb::query()

	 *

	 * @param integer $til_page_id

	 * @param string $til_page_title

	 * @return boolean

	 */
	protected function updateTitleTraffic($til_page_id, $til_page_title)
	{

		global $wpdb;

		$sql = "SELECT til_id FROM `ahc_title_traffic` where til_page_id = %s  and site_id=%d";

		$result = $wpdb->get_results($wpdb->prepare($sql, $til_page_id, get_current_blog_id()), OBJECT);

		if ($result !== false) {

			if (!empty($result)) {

				$sql2 = "UPDATE `ahc_title_traffic` 

						SET til_hits = til_hits + 1, til_page_title = %s 

						WHERE til_id = %d and site_id = %d";

				return ($wpdb->query($wpdb->prepare($sql2, $til_page_title, $result[0]->til_id, get_current_blog_id())) !== false);
			} else {

				$sql2 = "INSERT INTO `ahc_title_traffic` (til_page_id, til_page_title, til_hits,site_id)  

						VALUES(%s, %s, 1, %d)";

				return ($wpdb->query($wpdb->prepare($sql2, $til_page_id, $til_page_title, get_current_blog_id())) !== false);
			}
		} else {

			return false;
		}
	}

	//--------------------------------------------

	/**

	 * Update visitor's & visits' times table

	 *

	 * @uses wpdb::prepare()

	 * @uses wpdb::query()

	 *

	 * @param integer $visitors Optional

	 * @param integer $visits Optional

	 * @return boolean

	 */
	protected function updateVisitsTime($visitors = 0, $visits = 0)
	{
		global $wpdb;

		// Get current time using saved timezone
		$current_time = $this->getCurrentLocalTime();
		$time = $current_time['time']; // FIXED: Use saved timezone instead of gmdate

		$sql = "UPDATE `ahc_visits_time` SET vtm_visitors = vtm_visitors + %d, vtm_visits = vtm_visits + %d 
				WHERE  TIME(vtm_time_from) <= '$time' AND TIME(vtm_time_to) >= '$time' and site_id=" . get_current_blog_id();
		$query = $wpdb->prepare($sql, $visitors, $visits);
		$result = ($wpdb->query($query) !== false);

		$sql = "UPDATE `ahc_visits_time` SET vtm_visitors = 1
				WHERE vtm_visitors = 0 AND TIME(vtm_time_from) <= '$time' AND TIME(vtm_time_to) >= '$time' and site_id=" . get_current_blog_id();
		$query = $wpdb->query($sql);

		$sql = "UPDATE `ahc_visits_time` SET vtm_visits = 1
				WHERE vtm_visits = 0 AND TIME(vtm_time_from) <= '$time' AND TIME(vtm_time_to) >= '$time' and site_id = " . get_current_blog_id();
		$query = $wpdb->query($sql);

		return $result;
	}

	//--------------------------------------------

	/**

	 * Record (insert) the visit

	 *

	 * @uses wpdb::prepare()

	 * @uses wpdb::query()

	 *

	 * @return boolean

	 */
	protected function recordThisHits()
	{
		global $wpdb;

		// Get current time using saved timezone
		$current_time = $this->getCurrentLocalTime();

		$sql = "INSERT INTO `ahc_hits`
            (`hit_ip_address`, `hit_user_agent`, `hit_request_uri`, `hit_page_id`, `hit_page_title`, `ctr_id`, `hit_referer`, `hit_referer_site`,
            `srh_id`, `hit_search_words`, `bsr_id`, `hit_date`, `hit_time`, `site_id`)
            VALUES (%s, %s, %s, %s, %s, %d, %s, %s, %d, %s, %d, %s, %s, %d)";

		$result = $wpdb->query($wpdb->prepare(
			$sql,
			$this->ipAddress,
			$this->userAgent,
			$this->requestUri,
			$this->pageId,
			$this->pageTitle,
			$this->countryId,
			$this->referer,
			$this->refererSite,
			$this->searchEngine,
			$this->keyWords,
			$this->browser,
			$current_time['date'],    // FIXED: Use saved timezone instead of gmdate
			$current_time['time'],    // FIXED: Use saved timezone instead of gmdate
			get_current_blog_id()
		));

		return ($result !== false);
	}

	//--------------------------------------------
	/**
	 * Get current time in the site's configured timezone
	 * This replaces the problematic gmdate() calls
	 */

	protected function getCurrentLocalTime()
	{
		// Always store in UTC to avoid double timezone conversion
		$utc_timezone = new DateTimeZone('UTC');
		$datetime = new DateTime('now', $utc_timezone);

		$result = array(
			'date' => $datetime->format('Y-m-d'),
			'time' => $datetime->format('H:i:s'),
			'full' => $datetime->format('Y-m-d H:i:s'),
			'timezone' => 'UTC'
		);

		error_log('Storing in UTC: ' . json_encode($result));
		return $result;
	}
}
