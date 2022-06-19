<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2019 ThemePunch
 */

namespace Nwdthemes\Revslider\Model\Revslider;

use \Magento\Framework\App\ObjectManager;
use \Nwdthemes\Revslider\Helper\Framework;
use \Nwdthemes\Revslider\Model\Revslider\Front\RevSliderFront;

class RevSliderGlobals {
	const SLIDER_REVISION = Framework::RS_REVISION;
	const TABLE_SLIDERS_NAME = RevSliderFront::TABLE_SLIDER;
	const TABLE_SLIDES_NAME = RevSliderFront::TABLE_SLIDES;
	const TABLE_STATIC_SLIDES_NAME = RevSliderFront::TABLE_STATIC_SLIDES;
	const TABLE_SETTINGS_NAME = RevSliderFront::TABLE_SETTINGS;
	const TABLE_CSS_NAME = RevSliderFront::TABLE_CSS;
	const TABLE_LAYER_ANIMS_NAME = RevSliderFront::TABLE_LAYER_ANIMATIONS;
	const TABLE_NAVIGATION_NAME = RevSliderFront::TABLE_NAVIGATIONS;
	public static $table_sliders;
	public static $table_slides;
	public static $table_static_slides;

	/**
	 * Stores the singleton instance of the class
	 * @var RevSliderGlobals
	 */
	private static $instance;

	/**
	 * store global objects
	 * @var array
	 */
	private $storage = array();

	/**
	 * List of namespaces
	 * @var array
	 */
	private $_namespaces = [
        'Nwdthemes\Revslider\Model\Revslider',
        'Nwdthemes\Revslider\Model\Revslider\Admin\Includes',
        'Nwdthemes\Revslider\Model\Revslider\ExternalSources'
    ];

	protected function __construct()
	{
	}

	/**
	 * Instance accessor. If instance doesn't exist, we'll initialize the class.
	 *
	 * @return RevSliderGlobals
	 */
	public static function instance()
	{
		if (!isset(self::$instance)) {
			self::$instance = new RevSliderGlobals();
		}
		return self::$instance;
	}

	/**
	 * store $object under $key in $storage
	 * @param $key
	 * @param $object
	 */
	function add($key, $object) {
		$this->storage[$key] = $object;
	}

	/**
	 * get object from storage
	 * @param $key
	 * @return mixed|null
	 */
	function get($key) {
		if ( ! array_key_exists($key, $this->storage)) {
            $classFound = false;
            foreach ($this->_namespaces as $namespace) {
                $className = "\\$namespace\\$key";
                if (class_exists($className)) {
                    $this->add($key, ObjectManager::getInstance()->get($className));
                    $classFound = true;
                    break;
                }
            }
            if ( ! $classFound) {
                throw new \Exception("Сlass $key not found!");
            }
        }
		return $this->storage[$key];
	}

}
