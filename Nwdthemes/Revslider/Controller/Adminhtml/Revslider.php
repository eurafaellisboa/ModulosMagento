<?php

namespace Nwdthemes\Revslider\Controller\Adminhtml;

use \Nwdthemes\Revslider\Model\Revslider\RevSliderFunctions;
use \Nwdthemes\Revslider\Model\Revslider\Admin\RevSliderAdmin;
use \Nwdthemes\Revslider\Model\Revslider\Front\RevSliderFront;

abstract class Revslider extends \Magento\Backend\App\Action {

    /**
     * @var \Nwdthemes\Revslider\Helper\Framework
     */
    protected $_frameworkHelper;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Nwdthemes\Revslider\Helper\Framework $frameworkHelper,
        \Nwdthemes\Revslider\Helper\Plugin $pluginHelper
    ) {
        $this->_frameworkHelper = $frameworkHelper;

        parent::__construct($context);

        $this->_wp_magic_quotes();

        $this->_frameworkHelper->add_action('before_plugins_loaded', array('\Nwdthemes\Revslider\Model\Revslider\Front\RevSliderFront', 'create_tables'));
        $this->_frameworkHelper->add_action('before_plugins_loaded', array('\Nwdthemes\Revslider\Model\Revslider\Admin\Includes\RevSliderPluginUpdate', 'do_update_checks'));

        new RevSliderFront();

        $this->_frameworkHelper->do_action('before_plugins_loaded');
        $pluginHelper->deactivateOldPlugins();
        $pluginHelper->loadPlugins($this->_frameworkHelper);

        new RevSliderAdmin();

        $this->_frameworkHelper->do_action('init');
        $this->_frameworkHelper->do_action('admin_init');
    }

    /**
     *  Add magic quotes for WP compatiblity
     */

    private function _wp_magic_quotes() {
        // If already slashed, strip.
        if (function_exists('get_magic_quotes_gpc')) {
            $reflection = new \ReflectionFunction('get_magic_quotes_gpc');
            if ( ! $reflection->isDeprecated()) {
                if ( get_magic_quotes_gpc() ) {
                    $_GET    = RevSliderFunctions::stripslashes_deep( $_GET    );
                    $_POST   = RevSliderFunctions::stripslashes_deep( $_POST   );
                    $_COOKIE = RevSliderFunctions::stripslashes_deep( $_COOKIE );
                }
            }
        }

        // Escape with wpdb.
        $_GET    = $this->_add_magic_quotes( $_GET    );
        $_POST   = $this->_add_magic_quotes( $_POST   );
        $_COOKIE = $this->_add_magic_quotes( $_COOKIE );
        $_SERVER = $this->_add_magic_quotes( $_SERVER );

        // Force REQUEST to be GET + POST.
        $_REQUEST = array_merge( $_GET, $_POST );
    }

    /**
     * Walks the array while sanitizing the contents.
     *
     * @param array $array Array to walk while sanitizing contents.
     * @return array Sanitized $array.
     */

    private function _add_magic_quotes( $array ) {
        foreach ( (array) $array as $k => $v ) {
            if ( is_array( $v ) ) {
                $array[$k] = $this->_add_magic_quotes( $v );
            } elseif (is_string($v)) {
                $array[$k] = addslashes( $v );
            }
        }
        return $array;
    }

}
