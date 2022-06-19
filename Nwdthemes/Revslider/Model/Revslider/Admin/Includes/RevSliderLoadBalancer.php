<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2019 ThemePunch
 */

namespace Nwdthemes\Revslider\Model\Revslider\Admin\Includes;

use \Nwdthemes\Revslider\Helper\Data;
use \Nwdthemes\Revslider\Helper\Framework;
use \Nwdthemes\Revslider\Model\FrameworkAdapter as FA;

class RevSliderLoadBalancer {

	public $servers = array();

	/**
	 * set the server list on construct
	 **/
	public function __construct() {
		$this->servers = FA::get_option('revslider_servers', array());
		$this->servers = (empty($this->servers)) ? array('themepunch.tools') : $this->servers;
	}

	/**
	 * get the url depending on the purpose, here with key, you can switch do a different server
	 **/
	public function get_url($purpose, $key = 0, $force_http = false){
		$url	 = ($force_http ) ? 'http://' : 'https://';
		$use_url = (!isset($this->servers[$key])) ? reset($this->servers) : $this->servers[$key];

		switch($purpose){
			case 'updates':
				$url .= 'updates.';
				break;
			case 'templates':
				$url .= 'templates.';
				break;
			case 'library':
				$url .= 'library.';
				break;
			default:
				return false;
		}

		$url .= $use_url;

		return $url;
	}

	/**
	 * refresh the server list to be used, will be done once in a month
	 **/
	public function refresh_server_list($force = false){

		$rs_rsl		= (isset(Data::$_GET['rs_refresh_server'])) ? true : false;
		$last_check	= FA::get_option('revslider_server_refresh', false);

		if($force === true || $rs_rsl == true || $last_check === false || time() - $last_check > 60 * 60 * 24 * 14){
			//$url = $this->get_url('updates');
			$url	 = 'https://updates.themepunch.tools';
			$request = FA::wp_remote_post($url.'/get_server_list.php', array(
				'body'		 => array(
					'item'		=> urlencode(Framework::$RS_PLUGIN_SLUG),
					'version'	=> urlencode(Framework::RS_REVISION)
				),
				'timeout'	 => 45
			));

			if(!FA::is_wp_error($request)){
				if($response = FA::maybe_unserialize($request['body'])){
					$list = json_decode($response, true);
					FA::update_option('revslider_servers', $list);
				}
			}

			FA::update_option('revslider_server_refresh', time());
		}
	}

	/**
	 * move the server list, to take the next server as the one currently seems unavailable
	 **/
	public function move_server_list(){

		$servers	= $this->servers;
		$a			= array_shift($servers);
		$servers[]	= $a;

		$this->servers = $servers;
		FA::update_option('revslider_servers', $servers);
	}

	/**
	 * call an themepunch URL and retrieve data
	 **/
	public function call_url($url, $data, $subdomain = 'updates', $force_http = false){

		//add version if not passed
		$data['version'] = (!isset($data['version'])) ? urlencode(Framework::RS_REVISION) : $data['version'];

		$done	= false;
		$count	= 0;

		do{
			$server	 = $this->get_url($subdomain, 0, $force_http);

			$request = FA::wp_remote_post($server.'/'.$url, array(
				'body'		 => $data,
				'timeout'	 => 45
			));

			$response_code = FA::wp_remote_retrieve_response_code($request);
			if($response_code == 200){
				$done = true;
			}else{
				$this->move_server_list();
			}

			$count++;
		}while($done == false && $count < 5);

		return $request;
	}
}
