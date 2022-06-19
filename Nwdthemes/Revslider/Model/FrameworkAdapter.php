<?php

namespace Nwdthemes\Revslider\Model;

use Nwdthemes\Revslider\Helper\Framework;

/**
 * Class to access the Framework helper methods
 */
class FrameworkAdapter
{
    /**
     * Framework helper
     *
     * @var Framework
     */
    protected static $_framework;

    /**
     * Inject framework helper
     *
     * @param Framework
     */
    public static function injectFramework(Framework $framework)
    {
        if (self::$_framework == null) {
            self::$_framework = $framework;
        }
    }

    /**
     * Get framework helper
     *
     * @return Framework
     */
    public static function getFramework()
    {
        if (self::$_framework == null) {
            throw new \Exception("Framework helper is not injected into adapter yet!");
        } else {
            return self::$_framework;
        }
    }

    /**
     * Call framework method
     *
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public static function __callStatic($method, $arguments) {
        if ($framework = self::getFramework()) {
            if ( ! method_exists($framework, $method)) {
                throw new \Exception("There is no $method() method in the framework helper!");
            } else {
                return call_user_func_array([$framework, $method], $arguments);
            }
        }
    }

	/**
	 *	Call wp_cache_get as it have reference argument
	 *
	 * @param string $cacheKey
     * @param string $cacheGroup
     * @param bool $force
     * @param bool $found
	 * @return var
	 */
	public static function wp_cache_get($cacheKey, $cacheGroup = '', $force = false, &$found = null) {
        if ($framework = self::getFramework()) {
            return $framework->wp_cache_get($cacheKey, $cacheGroup, $force, $found);
        }
	}

}