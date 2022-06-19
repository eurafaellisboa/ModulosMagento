<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2019 ThemePunch
 */

namespace Nwdthemes\Revslider\Model\Revslider;

use \Nwdthemes\Revslider\Helper\Framework;
use \Nwdthemes\Revslider\Model\FrameworkAdapter as FA;
use \Nwdthemes\Revslider\Model\Revslider\Admin\Includes\RevSliderLoadBalancer;

class RevSliderUpdate {

    private $plugin_url  = 'https://www.sliderrevolution.com/';
	private $remote_url	 = 'check_for_updates.php';
	private $remote_url_info = 'revslider/revslider.php';
	private $plugin_slug = 'revslider';
	private $version;
	private $plugins;
	private $option;
	public $force = false;


	public function __construct($version) {
		$this->option = $this->plugin_slug . '_update_info';
		$this->_retrieve_version_info();
		$this->version = $version;
	}


	public function add_update_checks(){
		if($this->force === true){
			ini_set('max_execution_time', 300); //an update can follow, so set the execution time high for the runtime
			$transient = get_site_transient('update_plugins');
			$rs_t = $this->set_update_transient($transient);

			if(!empty($rs_t)){
				set_site_transient('update_plugins', $rs_t);
			}
		}

		FA::add_filter('pre_set_site_transient_update_plugins', array(&$this, 'set_update_transient'));
		FA::add_filter('plugins_api', array(&$this, 'set_updates_api_results'), 10, 3);
	}


	public function set_update_transient($transient){
		$this->_check_updates();

		if(isset($transient) && !isset($transient->response)){
			$transient->response = array();
		}

		if(!empty($this->data->basic) && is_object($this->data->basic)){
			if(version_compare($this->version, $this->data->basic->version, '<')){
				$this->data->basic->new_version = $this->data->basic->version;
				$transient->response[Framework::RS_PLUGIN_SLUG_PATH] = $this->data->basic;
			}
		}

		return $transient;
	}


	public function set_updates_api_results($result, $action, $args){
		$this->_check_updates();

		if(isset($args->slug) && $args->slug == $this->plugin_slug && $action == 'plugin_information'){
			if(is_object($this->data->full) && !empty($this->data->full)){
				$result = $this->data->full;
			}
		}

		return $result;
	}


	public function _check_updates(){
		// Get data
		if(empty($this->data)){
			$data = FA::get_option($this->option, false);
			$data = $data ? $data : new \stdClass;

			$this->data = is_object($data) ? $data : FA::maybe_unserialize($data);
		}

		$last_check = FA::get_option('revslider-update-check');
		if($last_check == false){ //first time called
			$last_check = time() - 172802;
			FA::update_option('revslider-update-check', $last_check);
		}

		// Check for updates
		if(time() - $last_check > 172800 || $this->force == true){
			$data = $this->_retrieve_update_info();

			if(isset($data->basic)) {
				FA::update_option('revslider-update-check', time());

				$this->data->checked = time();
				$this->data->basic	 = $data->basic;
				$this->data->full	 = $data->full;

				FA::update_option('revslider-stable-version', $data->full->stable);
				FA::update_option('revslider-latest-version', $data->full->version);
			}

		}

		// Save results
		FA::update_option($this->option, $this->data);
	}


	public function _retrieve_update_info(){
        $rslb = RevSliderGlobals::instance()->get('RevSliderLoadBalancer');
        $data = new \stdClass;

		// Build request
		$rattr = array(
			'code'		=> urlencode(FA::get_option('revslider-code', '')),
			'version'	=> urlencode(Framework::RS_REVISION)
		);

		if(FA::get_option('revslider-valid', 'false') !== 'true' && version_compare(Framework::RS_REVISION, FA::get_option('revslider-stable-version', '4.2'), '<')){ //We'll get the last stable only now!
			$rattr['get_stable'] = 'true';
		}

		$request = $rslb->call_url($this->remote_url_info, $rattr, 'updates');

		if(!FA::is_wp_error($request)){
			if($response = FA::maybe_unserialize($request['body'])){
				if(is_object($response)){
					$data = $response;
					$data->basic->url	= $this->plugin_url;
					$data->full->url	= $this->plugin_url;
					$data->full->external = 1;
				}
			}
		}

		return $data;
	}


	public function _retrieve_version_info(){
        $rslb       = RevSliderGlobals::instance()->get('RevSliderLoadBalancer');
        $last_check	= FA::get_option('revslider-update-check-short');

		// Check for updates
		if($last_check == false || time() - $last_check > 172800 || $this->force == true){
			FA::update_option('revslider-update-check-short', time());

			$hash = ($this->force === true) ? '' : FA::get_option('revslider-update-hash', '');
			$purchase = (FA::get_option('revslider-valid', 'false') == 'true') ? FA::get_option('revslider-code', '') : '';
			$data = array(
				'version' => urlencode(Framework::RS_REVISION),
				'item' => urlencode(Framework::$RS_PLUGIN_SLUG),
				'hash' => urlencode($hash),
				'code' => urlencode($purchase)
			);

			$request = $rslb->call_url($this->remote_url, $data, 'updates');
			$version_info = FA::wp_remote_retrieve_body($request);

			if(FA::wp_remote_retrieve_response_code($request) != 200 || FA::is_wp_error($version_info)){
				FA::update_option('revslider-connection', false);
				return false;
			}else{
				FA::update_option('revslider-connection', true);
			}

			if('actual' != $version_info){
				$version_info = json_decode($version_info);

				if(isset($version_info->hash))		FA::update_option('revslider-update-hash', $version_info->hash);
				if(isset($version_info->version))	FA::update_option('revslider-latest-version', $version_info->version);
				if(isset($version_info->stable))	FA::update_option('revslider-stable-version', $version_info->stable);
				if(isset($version_info->notices))	FA::update_option('revslider-notices', $version_info->notices);
				if(isset($version_info->additions))	FA::update_option('revslider-additions', $version_info->additions);
				if(isset($version_info->addons)){
					$addons = FA::get_option('revslider-addons', array());
					$addons = (is_object($addons)) ? (array)$addons : $addons;
					$addons = (!is_array($addons)) ? json_decode($addons, true) : $addons;

					$cur_addons_count = count($addons);
					$new_addons_count = count((array)$version_info->addons);
					if($cur_addons_count < $new_addons_count){
						$counter = $new_addons_count - $cur_addons_count;
						FA::update_option('rs-addons-counter', $counter);
					}

					FA::update_option('revslider-addons', $version_info->addons);
				}

				if(isset($version_info->deactivated) && $version_info->deactivated === true){
					if(FA::get_option('revslider-valid', 'false') == 'true'){
						//remove validation, add notice
						FA::update_option('revslider-valid', 'false');
						FA::update_option('revslider-deact-notice', true);
					}
				}
			}
		}

		//force that the update will be directly searched
		if($this->force == true) FA::update_option('revslider-update-check', '');

		return FA::get_option('revslider-latest-version', Framework::RS_REVISION);
	}
}
