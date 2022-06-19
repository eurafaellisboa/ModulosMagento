<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.sliderrevolution.com/
 * @copyright 2015 ThemePunch
 */

namespace Nwdthemes\Revslider\Model\Revslider\ExternalSources;

use \Nwdthemes\Revslider\Helper\Data;
use \Nwdthemes\Revslider\Model\FrameworkAdapter as FA;
use \Nwdthemes\Revslider\Model\Revslider\RevSliderFunctions;
use \Nwdthemes\Revslider\Model\Revslider\RevSliderSlide;
use \Nwdthemes\Revslider\Model\Revslider\RevSliderSlider;

/**
 * Facebook
 *
 * with help of the API this class delivers album images from Facebook
 *
 * @package    socialstreams
 * @subpackage socialstreams/facebook
 * @author     ThemePunch <info@themepunch.com>
 */

class RevSliderFacebook extends RevSliderFunctions {

    const URL_FB_AUTH = 'https://updates.themepunch.tools/fb/login.php';
    const URL_FB_API = 'https://updates.themepunch.tools/fb/api.php';

    const QUERY_SHOW = 'fb_show';
    const QUERY_TOKEN = 'fb_token';
    const QUERY_PAGE_ID = 'fb_page_id';
    const QUERY_CONNECTWITH = 'fb_page_name';
    const QUERY_ERROR = 'fb_error_message';

	/**
     * @var number  Transient time in seconds
     */
	private $transient_sec;

	public function __construct($transient_sec = 1200) {
		$this->transient_sec = 	$transient_sec;
	}

	/**
	 * @return int
	 */
	public function getTransientSec()
	{
		return $this->transient_sec;
	}

	/**
	 * @param int $transient_sec
	 */
	public function setTransientSec($transient_sec)
	{
		$this->transient_sec = $transient_sec;
	}

    public function add_actions()
    {
        FA::add_action('init', array(&$this, 'do_init'), 5);
        FA::add_action('admin_footer', array(&$this, 'footer_js'));
    }

	/**
     * check if we have QUERY_ARG set
     * try to login the user
     */
    public function do_init()
    {
        // are we on revslider page?
        if (!isset(Data::$_GET['page']) || Data::$_GET['page'] != 'revslider') return;

        //fb returned error
        if (isset(Data::$_GET[self::QUERY_ERROR])) return;

        //we need token and slide ID to proceed with saving token
        if (!isset(Data::$_GET[self::QUERY_TOKEN]) || !isset(Data::$_GET['id'])) return;

        $token = Data::$_GET[self::QUERY_TOKEN];
        $connectwith = isset(Data::$_GET[self::QUERY_CONNECTWITH]) ? Data::$_GET[self::QUERY_CONNECTWITH] : '';
        $page_id = isset(Data::$_GET[self::QUERY_PAGE_ID]) ? Data::$_GET[self::QUERY_PAGE_ID] : '';
        $id = Data::$_GET['id'];

        $slider    = new RevSliderSlider();
        $slide    = new RevSliderSlide();

        $slide->init_by_id($id);
        $slider_id = $slide->get_slider_id();
        if(intval($slider_id) == 0){
            Data::$_GET[self::QUERY_ERROR] = __('Slider could not be loaded', 'revslider');
            return;
        }

        $slider->init_by_id($slider_id);
        if($slider->inited === false){
            Data::$_GET[self::QUERY_ERROR] = __('Slider could not be loaded', 'revslider');
            return;
        }

        $slider->set_param(array('source', 'facebook', 'token_source'), 'account');
        $slider->set_param(array('source', 'facebook', 'appId'), $token);
        $slider->set_param(array('source', 'facebook', 'page_id'), $page_id);
        $slider->set_param(array('source', 'facebook', 'connect_with'), $connectwith);
        $slider->update_params([]);

        //redirect
        $url = FA::set_url_scheme('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        $url = str_replace(self::QUERY_TOKEN . '=' . Data::$_GET[self::QUERY_TOKEN], self::QUERY_SHOW . '=1', $url);
        FA::wp_redirect($url);
    }

    public function footer_js() {
        // are we on revslider page?
        if (!isset(Data::$_GET['page']) || Data::$_GET['page'] != 'revslider') return;

        if (isset(Data::$_GET[self::QUERY_SHOW]) || isset(Data::$_GET[self::QUERY_ERROR])) {
            echo '<script>require(["jquery", "revsliderEditor"], function(jQuery, RVS) { jQuery(document).ready(function(){ RVS.DOC.one("builderInitialised", function(){RVS.F.mainMode({mode:"sliderlayout", forms:["*sliderlayout*#form_slidercontent"], set:true, uncollapse:true,slide:RVS.S.slideId});RVS.F.updateSliderObj({path:"settings.sourcetype",val:"facebook"});RVS.F.updateEasyInputs({container:jQuery("#form_slidercontent"), trigger:"init", visualUpdate:true});}); }); });</script>';
        }

        if (isset(Data::$_GET[self::QUERY_ERROR])) {
            $err = __('Facebook API error: ', 'revslider') . Data::$_GET[self::QUERY_ERROR];
            echo '<script>require(["jquery", "revsliderEditor"], function(jQuery, RVS) { jQuery(document).ready(function(){ RVS.DOC.one("builderInitialised", function(){ RVS.F.showInfo({content:"' . $err . '", type:"warning", showdelay:1, hidedelay:5, hideon:"", event:"" }); });}); });</script>';
        }
	}

    public static function get_login_url()
    {
        $state = '[return_url]';
        return self::URL_FB_AUTH . '?state=' . $state;
    }

    protected function _make_api_call($args = [])
    {
        $response = FA::wp_remote_post(self::URL_FB_API, array(
            'body'         => $args,
            'timeout'     => 45
        ));

        if(FA::is_wp_error($response)) {
            return array(
                'error' => true,
                'message' => 'Facebook API error: ' . $response->get_error_message(),
            );
        }

        $responseData = json_decode($response['body'], true);
        if(empty($responseData)) {
            return array(
                'error' => true,
                'message' => 'Facebook API error: Empty response body or wrong data format',
            );
        }

        return $responseData;
    }

    protected function _get_transient_fb_data($requestData)
    {
        $transient_name = 'revslider_' . md5(json_encode($requestData));
		if($this->transient_sec > 0 && false !== ($data = FA::get_transient($transient_name))){
			return $data;
		}

        $responseData = $this->_make_api_call($requestData);
        //code that use this function do not process errors
        //return empty array
        if($responseData['error']){
            return array();
        }

        if(isset($responseData['data'])){
            FA::set_transient($transient_name, $responseData['data'], $this->transient_sec);
            return $responseData['data'];
        }

        return array();
    }

    /**
     * Get Photosets List from User
     *
     * @param    string    $access_token     page access token
     * @param    string    $page_id     page id
     * @return    mixed
     */
    public function get_photo_sets($access_token, $page_id){
        return $this->_make_api_call(array(
            'token' => $access_token,
            'page_id' => $page_id,
            'action' => 'albums',
        ));
    }

	/**
	 * Get Photosets List from User as Options for Selectbox
	 *
     * @param    string    $access_token     page access token
     * @param    string    $page_id     page id
     * @return    mixed    options html string | array('error' => true, 'message' => '...');
     */
    public function get_photo_set_photos_options($access_token, $page_id){
        $photo_sets = $this->get_photo_sets($access_token, $page_id);

        if($photo_sets['error']){
			return $photo_sets;
		}

		$return = array();
        if(is_array($photo_sets['data'])){
            foreach($photo_sets['data'] as $photo_set){
                $return[] = '<option title="'.$photo_set['name'].'" value="'.$photo_set['id'].'">'.$photo_set['name'].'</option>"';
            }
		}
		return $return;
	}

	/**
     * Get Photoset Photos
     *
     * @param    string    $access_token     page access token
     * @param    string    $album_id     Album ID
     * @param    int     $item_count     items count
     * @return    array
     */
    public function get_photo_set_photos($access_token, $album_id, $item_count = 8){
        $requestData = array(
            'token' => $access_token,
            'action' => 'photos',
            'album_id' => $album_id,
            'limit' => $item_count,
        );
        return $this->_get_transient_fb_data($requestData);
    }

	/**
     * Get Feed
     *
     * @param    string    $access_token     page access token
     * @param    string    $page_id     page id
     * @param    int     $item_count     items count
     * @return    array
     */
    public function get_photo_feed($access_token, $page_id, $item_count = 8){
        $requestData = array(
            'token' => $access_token,
            'page_id' => $page_id,
            'action' => 'feed',
            'limit' => $item_count,
        );
        return $this->_get_transient_fb_data($requestData);
    }

}