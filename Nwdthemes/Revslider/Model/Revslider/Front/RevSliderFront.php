<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2019 ThemePunch
 */

namespace Nwdthemes\Revslider\Model\Revslider\Front;

use \Nwdthemes\Revslider\Helper\Framework;
use \Nwdthemes\Revslider\Helper\Query;
use \Nwdthemes\Revslider\Model\FrameworkAdapter as FA;
use \Nwdthemes\Revslider\Model\Revslider\RevSliderFunctions;
use \Nwdthemes\Revslider\Model\Revslider\RevSliderGlobals;
use \Nwdthemes\Revslider\Model\Revslider\RevSliderOutput;
use \Nwdthemes\Revslider\Model\Revslider\RevSliderSlider;

class RevSliderFront extends RevSliderFunctions {

	const TABLE_BACKUP			 = 'nwdthemes_revslider_backup';
	const TABLE_SLIDER			 = 'nwdthemes_revslider_sliders';
	const TABLE_SLIDES			 = 'nwdthemes_revslider_slides';
	const TABLE_STATIC_SLIDES	 = 'nwdthemes_revslider_static_slides';
	const TABLE_CSS				 = 'nwdthemes_revslider_css';
	const TABLE_LAYER_ANIMATIONS = 'nwdthemes_revslider_animations';
	const TABLE_NAVIGATIONS		 = 'nwdthemes_revslider_navigations';
	const TABLE_SETTINGS		 = 'nwdthemes_revslider_settings'; //existed prior 5.0 and still needed for updating from 4.x to any version after 5.x
    const CURRENT_TABLE_VERSION  = '1.0.9';

	const YOUTUBE_ARGUMENTS		 = 'hd=1&amp;wmode=opaque&amp;showinfo=0&amp;rel=0';
	const VIMEO_ARGUMENTS		 = 'title=0&amp;byline=0&amp;portrait=0&amp;api=1';

	public function __construct() {
		FA::add_action('wp_enqueue_scripts', array('\Nwdthemes\Revslider\Model\Revslider\Front\RevSliderFront', 'add_actions'));
	}


	/**
	 * START: DEPRECATED FUNCTIONS THAT ARE IN HERE FOR OLD ADDONS TO WORK PROPERLY
	 **/

	/**
	 * old version of add_admin_bar();
	 **/
	public static function putAdminBarMenus(){
		return RevSliderFront::add_admin_bar();
	}

	/**
	 * END: DEPRECATED FUNCTIONS THAT ARE IN HERE FOR OLD ADDONS TO WORK PROPERLY
	 **/

	/**
	 * Add all actions that the frontend needs here
	 **/
	public static function add_actions(){

        $func    = RevSliderGlobals::instance()->get('RevSliderFunctions');
		$global	 = $func->get_global_settings();

		FA::add_action('wp_head', array('\Nwdthemes\Revslider\Model\Revslider\Front\RevSliderFront', 'add_meta_generator'));
		FA::add_action('wp_head', array('\Nwdthemes\Revslider\Model\Revslider\Front\RevSliderFront', 'js_set_start_size'), 99);
		FA::add_action('admin_head', array('\Nwdthemes\Revslider\Model\Revslider\Front\RevSliderFront', 'js_set_start_size'), 99);
        FA::add_action('wp_footer', array('\Nwdthemes\Revslider\Model\Revslider\Front\RevSliderFront', 'add_inline_css'));
		FA::add_action('wp_footer', array('\Nwdthemes\Revslider\Model\Revslider\Front\RevSliderFront', 'load_icon_fonts'));
		FA::add_action('wp_footer', array('\Nwdthemes\Revslider\Model\Revslider\Front\RevSliderFront', 'load_google_fonts'));
        FA::add_action('wp_footer', array('\Nwdthemes\Revslider\Model\Revslider\Front\RevSliderFront', 'add_waiting_script'), 1);
        FA::add_action('wp_print_footer_scripts', array('\Nwdthemes\Revslider\Model\Revslider\Front\RevSliderFront', 'add_inline_js'), 100);

        //defer JS Loading
        if($func->_truefalse($func->get_val($global, array('script', 'defer'), true)) === true){
            FA::add_filter('script_loader_tag', array('\Nwdthemes\Revslider\Model\Revslider\Front\RevSliderFront', 'add_defer_forscript'), 11, 2);
        }

		//Async JS Loading
        if($func->_truefalse($func->get_val($global, array('script', 'async'), true)) === true){
            FA::add_filter('script_loader_tag', array('\Nwdthemes\Revslider\Model\Revslider\Front\RevSliderFront', 'add_async_forscript'), 11, 2);
        }
	}

    /**
     * add css to the footer
     **/
    public static function add_inline_css(){
        $css     = RevSliderGlobals::instance()->get('RevSliderCssParser');
        /**
         * Fix for WordPress versions below 3.7
         **/
        $custom_css = $css->get_static_css();
        $custom_css = $css->compress_css($custom_css);

        if(!empty(Framework::$rs_css_collection)){
            $custom_css .= Framework::RS_T2;
            $custom_css .= implode("\n".Framework::RS_T2, Framework::$rs_css_collection);
        }

        $custom_css = (trim($custom_css) == '') ? '#rs-demo-id {}' : $custom_css;

        FA::wp_add_inline_style('rs-plugin-settings', $custom_css);
    }

    /**
     * add all the JavaScript from the Sliders to the footer
     **/
    public static function add_inline_js(){

        if(empty(Framework::$rs_js_collection)) return true;
        if(empty(Framework::$rs_js_collection['revapi'])) return true;

        echo '<script type="text/javascript" id="rs-initialisation-scripts">'."\n";
        echo Framework::RS_T2.'if(window.RS_MODULES === undefined) window.RS_MODULES = {};'."\n";
        echo Framework::RS_T2.'if(RS_MODULES.modules === undefined) RS_MODULES.modules = {};'."\n";
        echo Framework::RS_T2.'var    '.implode(',', Framework::$rs_js_collection['revapi']) . ';'."\n";
        if(!empty(Framework::$rs_js_collection['js'])){
            echo "\n" . implode("\n", Framework::$rs_js_collection['js']);
        }
        if(!empty(Framework::$rs_js_collection['minimal'])){
            echo "\n" . Framework::$rs_js_collection['minimal'];
        }

        echo Framework::RS_T.'</script>'."\n";

    }


	/**
	 * Add Meta Generator Tag in FrontEnd
	 * @since: 5.0
	 */
	public static function add_meta_generator(){
		echo FA::apply_filters('revslider_meta_generator', '<meta name="generator" content="Powered by Slider Revolution ' . Framework::RS_REVISION . ' - responsive, Mobile-Friendly Slider Plugin for WordPress with comfortable drag and drop interface." />' . "\n");
	}

	/**
	 * Load Used Icon Fonts
	 * @since: 5.0
	 */
	public static function load_icon_fonts(){
        $func    = RevSliderGlobals::instance()->get('RevSliderFunctions');
		$global	= $func->get_global_settings();
		$ignore_fa = $func->_truefalse($func->get_val($global, 'fontawesomedisable', false));

        echo Framework::RS_T3.'<link rel="preload" as="font" id="rs-icon-set-revicon-woff" href="' . Framework::$RS_PLUGIN_URL . 'public/assets/fonts/revicons/revicons.woff?5510888" type="font/woff" crossorigin="anonymous" media="all" />'."\n";
        echo ($ignore_fa === false && (Framework::$fa_icon_var == true || Framework::$fa_var == true)) ? Framework::RS_T3.'<link rel="preload" as="font" id="rs-icon-set-fa-icon-woff" type="font/woff2" crossorigin="anonymous" href="' . Framework::$RS_PLUGIN_URL . 'public/assets/fonts/font-awesome/fonts/fontawesome-webfont.woff2?v=4.7.0" media="all" />'."\n" : '';
        echo ($ignore_fa === false && (Framework::$fa_icon_var == true || Framework::$fa_var == true)) ? Framework::RS_T3.'<link rel="stylesheet" property="stylesheet" id="rs-icon-set-fa-icon-css" href="' . Framework::$RS_PLUGIN_URL . 'public/assets/fonts/font-awesome/css/font-awesome.min.css" type="text/css" media="all" />'."\n" : '';

        echo (Framework::$pe_7s_var) ? Framework::RS_T3.'<link rel="stylesheet" property="stylesheet" id="rs-icon-set-pe-7s-css" href="' . Framework::$RS_PLUGIN_URL . 'public/assets/fonts/pe-icon-7-stroke/css/pe-icon-7-stroke.min.css" type="text/css" media="all" />'."\n" : '';
    }


	/**
	 * Load Used Google Fonts
	 * add google fonts of all sliders found on the page
	 * @since: 6.0
	 */
	public static function load_google_fonts(){
		$func	= RevSliderGlobals::instance()->get('RevSliderFunctions');
		$fonts	= $func->print_clean_font_import();
		if(!empty($fonts)){
			echo $fonts."\n";
		}
	}

    /**
     * add the scripts that needs to be waited on
     * @since: 6.4.12
     **/
    public static function add_waiting_script(){
        $func    = RevSliderGlobals::instance()->get('RevSliderFunctions');
        $global    = $func->get_global_settings();
        $wait    = array();
        $wait    = FA::apply_filters('revslider_modify_waiting_scripts', $wait);
        ?>

        <script type="text/javascript">
            window.RS_MODULES = window.RS_MODULES || {};
            window.RS_MODULES.modules = window.RS_MODULES.modules || {};
            window.RS_MODULES.waiting = window.RS_MODULES.waiting || [];
            window.RS_MODULES.defered = <?php echo ($func->_truefalse($func->get_val($global, array('script', 'defer'), true)) === true) ? 'true' : 'false'; ?>;
            <?php if (!empty($wait)) {?>
            window.RS_MODULES.waiting = window.RS_MODULES.waiting.concat([ <?php echo '"'. implode('","', $wait) . '"'; ?>]);
            <?php }; ?>window.RS_MODULES.moduleWaiting = window.RS_MODULES.moduleWaiting || {};
            window.RS_MODULES.type = 'compiled';
        </script>
        <?php
    }

	/**
	 * add admin menu points in ToolBar Top
	 * @since: 5.0.5
	 * @before: putAdminBarMenus()
	 */
	public static function add_admin_bar(){
		if(!FA::is_super_admin() || !FA::is_admin_bar_showing()){
			return;
		}

		?>
		<script type="text/javascript">
			function rs_adminBarToolBarTopFunction() {
				if(jQuery('#wp-admin-bar-revslider-default').length > 0 && jQuery('rs-module-wrap').length > 0){
					var aliases = new Array();
					jQuery('rs-module-wrap').each(function(){
						aliases.push(jQuery(this).data('alias'));
					});

					if(aliases.length > 0){
						jQuery('#wp-admin-bar-revslider-default li').each(function(){
							var li = jQuery(this),
								t = li.find('.ab-item .rs-label').data('alias'); //text()
							t = t!==undefined && t!==null ? t.trim() : t;
							if(jQuery.inArray(t,aliases)!=-1){
							}else{
								li.remove();
							}
						});
					}
				}else{
					jQuery('#wp-admin-bar-revslider').remove();
				}
			}
			var adminBarLoaded_once = false
			if (document.readyState === "loading")
				document.addEventListener('readystatechange',function(){
					if ((document.readyState === "interactive" || document.readyState === "complete") && !adminBarLoaded_once) {
						adminBarLoaded_once = true;
						rs_adminBarToolBarTopFunction()
					}
				});
			else {
				adminBarLoaded_once = true;
				rs_adminBarToolBarTopFunction();
			}
		</script>
		<?php
}

	/**
	 * add admin nodes
	 * @since: 5.0.5
	 */
	public static function add_admin_menu_nodes(){
		if(!FA::is_super_admin() || !FA::is_admin_bar_showing()){
			return;
		}

		self::_add_node('<span class="rs-label">Slider Revolution</span>', false, FA::admin_url('admin.php?page=revslider'), array('class' => 'revslider-menu'), 'revslider'); //<span class="wp-menu-image dashicons-before dashicons-update"></span>

		//add all nodes of all Slider
		$sl = new RevSliderSlider();
		$sliders = $sl->get_slider_for_admin_menu();

		if(!empty($sliders)){
			foreach ($sliders as $id => $slider){
				self::_add_node('<span class="rs-label" data-alias="' . FA::esc_attr($slider['alias']) . '">' . FA::esc_html($slider['title']) . '</span>', 'revslider', FA::getBackendUrl('nwdthemes_revslider/revslider/builder') . '?id=' . $id, array('class' => 'revslider-sub-menu'), FA::esc_attr($slider['alias'])); //<span class="wp-menu-image dashicons-before dashicons-update"></span>
			}
		}
	}

	/**
	 * add admin node
	 * @since: 5.0.5
	 */
	public static function _add_node($title, $parent = false, $href = '', $custom_meta = array(), $id = ''){
		if(!FA::is_super_admin() || !FA::is_admin_bar_showing()){
			return;
		}

		$id = ($id == '') ? strtolower(str_replace(' ', '-', $title)) : $id;

		//links from the current host will open in the current window
		$meta = (strpos($href, site_url()) !== false) ? array() : array('target' => '_blank'); //external links open in new tab/window
		$meta = array_merge($meta, $custom_meta);

		global $wp_admin_bar;
		$wp_admin_bar->add_node(array('parent'=> $parent, 'id' => $id, 'title' => $title, 'href' => $href, 'meta' => $meta));
	}

	/**
	 * adds async loading
	 * @since: 5.0
     * @updated: 6.4.12
     */
    public static function add_defer_forscript($tag, $handle){
        if(strpos($tag, 'rs6') === false && strpos($tag, 'rbtools.min.js') === false && strpos($tag, 'revolution.addon.') === false && strpos($tag, 'public/assets/js/libs/') === false && strpos($tag, 'pixi.min.js') === false && strpos($tag, 'lottie.min.js') === false){
            return $tag;
        }elseif(FA::is_admin()){
            return $tag;
        }else{
            return str_replace(' id=', ' defer id=', $tag);
        }
    }

    /**
     * adds async loading
     * @since: 5.0
     * @updated: 6.4.12
	 */
    public static function add_async_forscript($tag, $handle){
        if(strpos($tag, 'rs6') === false && strpos($tag, 'rbtools.min.js') === false && strpos($tag, 'revolution.addon.') === false && strpos($tag, 'public/assets/js/libs/') === false && strpos($tag, 'pixi.min.js') === false && strpos($tag, 'lottie.min.js') === false){
            return $tag;
        }elseif(FA::is_admin()){
            return $tag;
        }else{
            return str_replace(' id=', ' async id=', $tag);
        }
	}

	/**
	 * Add functionality to gutenberg, elementor, visual composer and so on
	 **/
	public static function add_post_editor(){
		/**
		 * Page Editor Extensions
		 **/
		if(function_exists('is_user_logged_in') && FA::is_user_logged_in()){
			//only include gutenberg for production
			if(FA::is_admin() && defined('ABSPATH')){
				include_once(ABSPATH . 'wp-admin/includes/plugin.php');
				if(function_exists('is_plugin_active') && !FA::is_plugin_active('revslider-gutenberg/plugin.php')){
					require_once(Framework::$RS_PLUGIN_PATH . 'admin/includes/shortcode_generator/gutenberg/gutenberg-block.php');
					new RevSliderGutenberg('gutenberg/');
				}
			}
		}

		//Elementor Functionality
		require_once(Framework::$RS_PLUGIN_PATH . 'admin/includes/shortcode_generator/elementor/elementor.class.php');
		FA::add_action('init', array('RevSliderElementor', 'init'));
	}

	/**
	 * Add Meta Generator Tag in FrontEnd
	 * @since: 5.4.3
	 * @before: add_setREVStartSize()
		//NOT COMPRESSED VERSION
		function setREVStartSize(e){
            window.RSIW = window.RSIW===undefined ? window.innerWidth : window.RSIW;
            window.RSIH = window.RSIH===undefined ? window.innerHeight : window.RSIH;
            try {
                var pw = document.getElementById(e.c).parentNode.offsetWidth,
                    newh;
                pw = pw===0 || isNaN(pw) ? window.RSIW : pw;
                e.tabw = e.tabw===undefined ? 0 : parseInt(e.tabw);
                e.thumbw = e.thumbw===undefined ? 0 : parseInt(e.thumbw);
                e.tabh = e.tabh===undefined ? 0 : parseInt(e.tabh);
                e.thumbh = e.thumbh===undefined ? 0 : parseInt(e.thumbh);
                e.tabhide = e.tabhide===undefined ? 0 : parseInt(e.tabhide);
                e.thumbhide = e.thumbhide===undefined ? 0 : parseInt(e.thumbhide);
                e.mh = e.mh===undefined || e.mh=="" || e.mh==="auto" ? 0 : parseInt(e.mh,0);
                if(e.layout==="fullscreen" || e.l==="fullscreen")
                    newh = Math.max(e.mh,window.RSIH);
                else{
                    e.gw = Array.isArray(e.gw) ? e.gw : [e.gw];
                    for (var i in e.rl) if (e.gw[i]===undefined || e.gw[i]===0) e.gw[i] = e.gw[i-1];
                    e.gh = e.el===undefined || e.el==="" || (Array.isArray(e.el) && e.el.length==0)? e.gh : e.el;
                    e.gh = Array.isArray(e.gh) ? e.gh : [e.gh];
                    for (var i in e.rl) if (e.gh[i]===undefined || e.gh[i]===0) e.gh[i] = e.gh[i-1];

                    var nl = new Array(e.rl.length),
                        ix = 0,
                        sl;
                    e.tabw = e.tabhide>=pw ? 0 : e.tabw;
                    e.thumbw = e.thumbhide>=pw ? 0 : e.thumbw;
                    e.tabh = e.tabhide>=pw ? 0 : e.tabh;
                    e.thumbh = e.thumbhide>=pw ? 0 : e.thumbh;
                    for (var i in e.rl) nl[i] = e.rl[i]<window.RSIW ? 0 : e.rl[i];
                    sl = nl[0];
                    for (var i in nl) if (sl>nl[i] && nl[i]>0) { sl = nl[i]; ix=i;}
                    var m = pw>(e.gw[ix]+e.tabw+e.thumbw) ? 1 : (pw-(e.tabw+e.thumbw)) / (e.gw[ix]);
                    newh =  (e.gh[ix] * m) + (e.tabh + e.thumbh);
                }
                if(window.rs_init_css===undefined) window.rs_init_css = document.head.appendChild(document.createElement("style"));
                document.getElementById(e.c).height = newh+"px";
                window.rs_init_css.innerHTML += "#"+e.c+"_wrapper { height: "+newh+"px }";
            } catch(e){
                console.log("Failure at Presize of Slider:" + e)
            }
        };
	 */
	public static function js_set_start_size(){
		if(Framework::$revslider_rev_start_size_loaded === true) return false;

		$script = '<script type="text/javascript">';
		$script .= 'define("setREVStartSize",function(){return function(t){window.RSIW=void 0===window.RSIW?window.innerWidth:window.RSIW,window.RSIH=void 0===window.RSIH?window.innerHeight:window.RSIH;try{var h=0===(h=document.getElementById(t.c).parentNode.offsetWidth)||isNaN(h)?window.RSIW:h;if(t.tabw=void 0===t.tabw?0:parseInt(t.tabw),t.thumbw=void 0===t.thumbw?0:parseInt(t.thumbw),t.tabh=void 0===t.tabh?0:parseInt(t.tabh),t.thumbh=void 0===t.thumbh?0:parseInt(t.thumbh),t.tabhide=void 0===t.tabhide?0:parseInt(t.tabhide),t.thumbhide=void 0===t.thumbhide?0:parseInt(t.thumbhide),t.mh=void 0===t.mh||""==t.mh||"auto"===t.mh?0:parseInt(t.mh,0),"fullscreen"===t.layout||"fullscreen"===t.l)w=Math.max(t.mh,window.RSIH);else{for(var e in t.gw=Array.isArray(t.gw)?t.gw:[t.gw],t.rl)void 0!==t.gw[e]&&0!==t.gw[e]||(t.gw[e]=t.gw[e-1]);for(var e in t.gh=void 0===t.el||""===t.el||Array.isArray(t.el)&&0==t.el.length?t.gh:t.el,t.gh=Array.isArray(t.gh)?t.gh:[t.gh],t.rl)void 0!==t.gh[e]&&0!==t.gh[e]||(t.gh[e]=t.gh[e-1]);var i,n=new Array(t.rl.length),r=0;for(e in t.tabw=t.tabhide>=h?0:t.tabw,t.thumbw=t.thumbhide>=h?0:t.thumbw,t.tabh=t.tabhide>=h?0:t.tabh,t.thumbh=t.thumbhide>=h?0:t.thumbh,t.rl)n[e]=t.rl[e]<window.RSIW?0:t.rl[e];for(e in i=n[0],n)i>n[e]&&0<n[e]&&(i=n[e],r=e);var h=h>t.gw[r]+t.tabw+t.thumbw?1:(h-(t.tabw+t.thumbw))/t.gw[r],w=t.gh[r]*h+(t.tabh+t.thumbh)}void 0===window.rs_init_css&&(window.rs_init_css=document.head.appendChild(document.createElement("style"))),document.getElementById(t.c).height=w+"px",window.rs_init_css.innerHTML+="#"+t.c+"_wrapper { height: "+w+"px }"}catch(t){console.log("Failure at Presize of Slider:"+t)}}});';
		$script .= '</script>' . "\n";
		echo FA::apply_filters('revslider_add_setREVStartSize', $script);

		Framework::$revslider_rev_start_size_loaded = true;
	}

	/**
	 * sets the post saving value to true, so that the output echo will not be done
	 **/
	public static function set_post_saving(){
	}

	/**
	 * check the current post for the existence of a short code
	 * @before: hasShortcode()
	 */
	public static function has_shortcode($shortcode = ''){
		$found = false;

		if(empty($shortcode)) return false;
		if(!FA::is_singular()) return false;

		$post = FA::get_post(FA::get_the_ID());
		if(stripos($post->post_content, '[' . $shortcode) !== false) $found = true;

		return $found;
	}

	/**
	 * Create Tables
	 * @only_base needs to be false
	 *  it can only be true by fixing database issues
	 *  this protects that the _bkp tables are not filled after
	 *  we are already on version 6.0
	 **/
	public static function create_tables($only_base = false){
		$table_version = FA::get_option('revslider_table_version', '1.0.0');

		if(version_compare($table_version, self::CURRENT_TABLE_VERSION, '<')){
			$wpdb = FA::getQueryHelper();

			//create CSS entries
			$result = $wpdb->get_row("SELECT COUNT( DISTINCT id ) AS NumberOfEntrys FROM " . $wpdb->prefix . self::TABLE_CSS);
			if(!empty($result) && $result->NumberOfEntrys == 0){
				$css_class = RevSliderGlobals::instance()->get('RevSliderCssParser');
				$css_class->import_css_captions();
			}

			FA::update_option('revslider_table_version', self::CURRENT_TABLE_VERSION);
			//$table_version = self::CURRENT_TABLE_VERSION;
		}


		/**
		 * check if table version is below 1.0.8.
		 * if yes, duplicate the tables into _bkp
		 * this way, we can revert back to v5 if any slider
		 * has issues in the v6 migration process
		 **/
		if(version_compare($table_version, '1.0.8', '<') && ($only_base === false || $only_base === '')){
			FA::backupDB();
		}
	}


	/**
	 * get the images from posts/pages for yoast seo
	 **/
	public static function get_images_for_seo($url, $type, $user){
		if(in_array($type, array('user', 'term'), true)) return $url;
		if(!is_object($user) || !isset($user->ID)) return $url;

		$post = FA::get_post($user->ID);
		if(is_a($post, 'WP_Post') && FA::has_shortcode($post->post_content, 'rev_slider')){
			preg_match_all('/\[rev_slider.*alias=.(.*)"\]/', $post->post_content, $shortcodes);

			if(isset($shortcodes[1]) && $shortcodes[1] !== ''){
				foreach($shortcodes[1] as $s){
					if(!RevSliderSlider::alias_exists($s)) continue;

					$sldr = new RevSliderSlider();
					$sldr->init_by_alias($s);
					$sldr->get_slides();
					$imgs = $sldr->get_images();
					if(!empty($imgs)){
						if(!isset($url['images'])) $url['images'] = array();
						foreach($imgs as $v){
							$url['images'][] = $v;
						}
					}
				}
			}
		}

		return $url;
	}

}
