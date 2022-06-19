<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2019 ThemePunch
 */

namespace Nwdthemes\Revslider\Model\Revslider;

use \Nwdthemes\Revslider\Model\FrameworkAdapter as FA;

class RevSliderFavorite extends RevSliderFunctions {

	/**
	 * change the setting of a favorization
     * @param string $do
     * @param string $type
     * @param mixed $id
     * @return array
     **/
	public function set_favorite($do, $type, $id){
		$fav = FA::get_option('rs_favorite', array());
		$id	 = FA::esc_attr($id);

        if(!isset($fav[$type])) $fav[$type] = array();
        $key = array_search($id, $fav[$type]);
        if($key === false){
            if($do == 'add') $fav[$type][] = $id;
        }elseif($do == 'remove'){
            unset($fav[$type][$key]);
        }elseif($do == 'replace'){
            $fav[$type] = $id;
        }
		FA::update_option('rs_favorite', $fav);

		return $fav;
	}

	/**
	 * get a certain favorite type
     * @param string $type
     * @return array
     **/
	public function get_favorite($type){
		$fav = FA::get_option('rs_favorite', array());
        return $this->get_val($fav, $type, array());
    }

	/**
	 * return if certain element is in favorites
     * @param string $type
     * @param mixed $id
     * @return bool
     **/
	public function is_favorite($type, $id){
		$favs = $this->get_favorite($type);
        return array_search($id, $favs) !== false;
    }

}
