<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2020 ThemePunch
 * @since	  6.2.0
 */

namespace Nwdthemes\Revslider\Model\Revslider\Admin\Includes;

use \Nwdthemes\Revslider\Helper\Framework;
use \Nwdthemes\Revslider\Model\FrameworkAdapter as FA;
use \Nwdthemes\Revslider\Model\Revslider\RevSliderFunctions;
use \Nwdthemes\Revslider\Model\Revslider\RevSliderGlobals;

class RevSliderLicense extends RevSliderFunctions {
	/**
	 * Activate the Plugin through the ThemePunch Servers
	 * @before 6.0.0: RevSliderOperations::checkPurchaseVerification();
	 * @before 6.2.0: RevSliderAdmin::activate_plugin();
	 **/
	public function activate_plugin($code){
        $rslb = RevSliderGlobals::instance()->get('RevSliderLoadBalancer');
		$data = array('code' => urlencode($code), 'version'	=> urlencode(Framework::RS_REVISION), 'product' => urlencode(Framework::$RS_PLUGIN_SLUG));

		$response	  = $rslb->call_url('activate.php', $data, 'updates');
		$version_info = FA::wp_remote_retrieve_body($response);

		if(FA::is_wp_error($version_info)) return false;

		if($version_info == 'valid'){
			FA::update_option('revslider-valid', 'true');
			FA::update_option('revslider-code', $code);
			return true;
		}elseif($version_info == 'exist'){
			return 'exist';
		}elseif($version_info == 'banned'){
			return 'banned';
		}

		return false;
	}


	/**
	 * Deactivate the Plugin through the ThemePunch Servers
	 * @before 6.0.0: RevSliderOperations::doPurchaseDeactivation();
	 * @before 6.2.0: RevSliderAdmin::deactivate_plugin();
	 **/
	public function deactivate_plugin(){
		$rslb = RevSliderGlobals::instance()->get('RevSliderLoadBalancer');
		$code = FA::get_option('revslider-code', '');
		$data = array('code' => urlencode($code), 'product' => urlencode(Framework::$RS_PLUGIN_SLUG));

		$res = $rslb->call_url('deactivate.php', $data, 'updates');
		$vi	 = FA::wp_remote_retrieve_body($res);

		if(FA::is_wp_error($vi)) return false;

		if($vi == 'valid'){
			FA::update_option('revslider-valid', 'false');
			FA::update_option('revslider-code', '');

			return true;
		}

		return false;
	}
}
