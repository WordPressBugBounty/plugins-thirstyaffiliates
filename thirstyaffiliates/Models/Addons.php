<?php

namespace ThirstyAffiliates\Models;

use ThirstyAffiliates\Abstracts\Abstract_Main_Plugin_Class;

use ThirstyAffiliates\Interfaces\Model_Interface;

use ThirstyAffiliates\Helpers\Plugin_Constants;
use ThirstyAffiliates\Helpers\Helper_Functions;
use ThirstyAffiliates\Helpers\Onboarding_Helper;
use ThirstyAffiliates\Helpers\Addon_Install_Skin;

/**
 * Model that houses the logic for Addons
 *
 * @since 3.0.0
 */
class Addons implements Model_Interface {

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
    */

    /**
     * Property that holds the single main instance of Addons.
     *
     * @since 3.0.0
     * @access private
     * @var Addons
     */
    private static $_instance;

    /**
     * Model that houses all the plugin constants.
     *
     * @since 3.0.0
     * @access private
     * @var Plugin_Constants
     */
    private $_constants;

    /**
     * Property that houses all the helper functions of the plugin.
     *
     * @since 3.0.0
     * @access private
     * @var Helper_Functions
     */
    private $_helper_functions;

    /*
    |--------------------------------------------------------------------------
    | Class Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Class constructor.
     *
     * @since 3.0.0
     * @access public
     *
     * @param Abstract_Main_Plugin_Class $main_plugin      Main plugin object.
     * @param Plugin_Constants           $constants        Plugin constants object.
     * @param Helper_Functions           $helper_functions Helper functions object.
     */
    public function __construct( Abstract_Main_Plugin_Class $main_plugin , Plugin_Constants $constants , Helper_Functions $helper_functions ) {

        $this->_constants        = $constants;
        $this->_helper_functions = $helper_functions;

        $main_plugin->add_to_all_plugin_models( $this );

    }

    /**
     * Ensure that only one instance of this class is loaded or can be loaded ( Singleton Pattern ).
     *
     * @since 3.0.0
     * @access public
     *
     * @param Abstract_Main_Plugin_Class $main_plugin      Main plugin object.
     * @param Plugin_Constants           $constants        Plugin constants object.
     * @param Helper_Functions           $helper_functions Helper functions object.
     * @return Addons
     */
    public static function get_instance( Abstract_Main_Plugin_Class $main_plugin , Plugin_Constants $constants , Helper_Functions $helper_functions ) {

        if ( !self::$_instance instanceof self )
            self::$_instance = new self( $main_plugin , $constants , $helper_functions );

        return self::$_instance;

    }

    /**
     * Execute shortcodes class.
     *
     * @since 3.0.0
     * @access public
     */
    public function run() {
        $this->hooks();
    }

    /**
    * Register hooks.
    */
    private function hooks() {
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('in_admin_header', array($this, 'hide_admin_notices'), 9999);
        add_action('wp_ajax_ta_addon_activate', array($this, 'ajax_addon_activate'));
        add_action('wp_ajax_ta_addon_deactivate', array($this, 'ajax_addon_deactivate'));
        add_action('wp_ajax_ta_addon_install', array($this, 'ajax_addon_install'));
    }

    public function enqueue_scripts($hook) {
       
        if(preg_match('/_page_thirstyaffiliates-addons$/', $hook)) {

          wp_enqueue_style('ta-addons-css', $this->_constants->CSS_ROOT_URL() . 'admin/addons.css', array(), $this->_constants->VERSION());

          if(Onboarding_Helper::is_pro_active()) {
            wp_enqueue_script('list-js', $this->_constants->JS_ROOT_URL()  . 'lib/list.min.js', array(), '1.5.0');
            wp_enqueue_script('jquery-match-height', $this->_constants->JS_ROOT_URL()  . 'lib/jquery.matchHeight-min.js', array(), '0.7.2');
            wp_enqueue_script('ta-addons-js', $this->_constants->JS_ROOT_URL()  . 'app/admin_addons.js', array('list-js', 'jquery-match-height'), $this->_constants->VERSION());

            wp_localize_script('ta-addons-js', 'TAAddons', array(
              'ajax_url' => admin_url('admin-ajax.php'),
              'nonce' => wp_create_nonce('ta_addons'),
              'active' => __('Active', 'thirstyaffiliates'),
              'inactive' => __('Inactive', 'thirstyaffiliates'),
              'activate' => __('Activate', 'thirstyaffiliates'),
              'deactivate' => __('Deactivate', 'thirstyaffiliates'),
              'install_failed' => __('Could not install add-on.', 'thirstyaffiliates'),
              'plugin_install_failed' => __('Could not install plugin.', 'thirstyaffiliates'),
            ));
          }
        }
    }

    // Removing admin notices here as they get hidden behind the blurred screen for Lite versions.
    public function hide_admin_notices() {
        $screen = get_current_screen();

        if(!$screen || $screen->id != 'thirstylink_page_thirstyaffiliates-addons') {
          return;
        }

        remove_all_actions('admin_notices');
        remove_all_actions('all_admin_notices');
    }

    public function route() {
        if( Onboarding_Helper::is_pro_active() && method_exists(ThirstyAffiliates_Pro(), 'get_model') ) {
            $tap_update = ThirstyAffiliates_Pro()->get_model('Update');
            if ( !is_object($tap_update) ) {
                $section_title = esc_html__('Add-ons', 'thirstyaffiliates');
                $upgrade_link = 'https://thirstyaffiliates.com/pricing?utm_source=plugin_admin&utm_medium=link&utm_campaign=addons&utm_content=pro_features';
                require_once($this->_constants->VIEWS_ROOT_PATH() . '/admin/addons/addon-page-lite.php');
                return;
            }
            $force = isset($_GET['refresh']) && $_GET['refresh'] == 'true';

            $addons = array();
            if( method_exists($tap_update, 'addons') ) {
              $addons = $tap_update->addons(true, $force, true);  
            }
            
            $plugins = get_plugins();

            require_once($this->_constants->VIEWS_ROOT_PATH() . '/admin/addons/addon-page-pro.php');
        } else {
            $section_title = esc_html__('Add-ons', 'thirstyaffiliates');
            $upgrade_link = 'https://thirstyaffiliates.com/pricing?utm_source=plugin_admin&utm_medium=link&utm_campaign=addons&utm_content=pro_features';

            require_once($this->_constants->VIEWS_ROOT_PATH() . '/admin/addons/addon-page-lite.php');
        }
    }


    public function ajax_addon_activate() {
        if(!isset($_POST['plugin'])) {
          wp_send_json_error(__('Bad request.', 'thirstyaffiliates'));
        }

        if(!current_user_can('activate_plugins')) {
          wp_send_json_error(__('Sorry, you don\'t have permission to do this.', 'thirstyaffiliates'));
        }

        if(!check_ajax_referer('ta_addons', false, false)) {
          wp_send_json_error(__('Security check failed.', 'thirstyaffiliates'));
        }

        $result = activate_plugins(wp_unslash($_POST['plugin']));
        $type = isset($_POST['type']) ? sanitize_key($_POST['type']) : 'add-on';

        if(is_wp_error($result)) {
          if($type == 'plugin') {
            wp_send_json_error(__('Could not activate plugin. Please activate from the Plugins page manually.', 'thirstyaffiliates'));
          } else {
            wp_send_json_error(__('Could not activate add-on. Please activate from the Plugins page manually.', 'thirstyaffiliates'));
          }
        }

        if($type == 'plugin') {
          wp_send_json_success(__('Plugin activated.', 'thirstyaffiliates'));
        } else {
          wp_send_json_success(__('Add-on activated.', 'thirstyaffiliates'));
        }
    }

    public function ajax_addon_deactivate() {
        if(!isset($_POST['plugin'])) {
          wp_send_json_error(__('Bad request.', 'thirstyaffiliates'));
        }

        if(!current_user_can('deactivate_plugins')) {
          wp_send_json_error(__('Sorry, you don\'t have permission to do this.', 'thirstyaffiliates'));
        }

        if(!check_ajax_referer('ta_addons', false, false)) {
          wp_send_json_error(__('Security check failed.', 'thirstyaffiliates'));
        }

        deactivate_plugins(wp_unslash($_POST['plugin']));
        $type = isset($_POST['type']) ? sanitize_key($_POST['type']) : 'add-on';

        if($type == 'plugin') {
          wp_send_json_success(__('Plugin deactivated.', 'thirstyaffiliates'));
        } else {
          wp_send_json_success(__('Add-on deactivated.', 'thirstyaffiliates'));
        }
    }

    public function ajax_addon_install() {
        if(!isset($_POST['plugin'])) {
          wp_send_json_error(__('Bad request.', 'thirstyaffiliates'));
        }

        if(!current_user_can('install_plugins') || !current_user_can('activate_plugins')) {
          wp_send_json_error(__('Sorry, you don\'t have permission to do this.', 'thirstyaffiliates'));
        }

        if(!check_ajax_referer('ta_addons', false, false)) {
          wp_send_json_error(__('Security check failed.', 'thirstyaffiliates'));
        }

        $type = isset($_POST['type']) ? sanitize_key($_POST['type']) : 'add-on';

        if($type == 'plugin') {
          $error = esc_html__('Could not install plugin.', 'thirstyaffiliates');
        } else {
          $error = esc_html__('Could not install add-on.', 'thirstyaffiliates');
        }

        // Set the current screen to avoid undefined notices
        set_current_screen('thirstylink_page_thirstyaffiliates-addons');

        // Prepare variables
        $url = esc_url_raw(
          add_query_arg(
            array(
              'page' => 'thirstyaffiliates-addons',
              'post_type' => Plugin_Constants::AFFILIATE_LINKS_CPT,
            ),
            admin_url('edit.php')
          )
        );

        $creds = request_filesystem_credentials($url, '', false, false, null);

        // Check for file system permissions
        if(false === $creds) {
          wp_send_json_error($error);
        }

        if(!WP_Filesystem($creds)) {
          wp_send_json_error($error);
        }

        // We do not need any extra credentials if we have gotten this far, so let's install the plugin
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

        // Do not allow WordPress to search/download translations, as this will break JS output
        remove_action('upgrader_process_complete', array('Language_Pack_Upgrader', 'async_upgrade'), 20);

        // Create the plugin upgrader with our custom skin
        $installer = new \Plugin_Upgrader(new Addon_Install_Skin());

        $plugin = wp_unslash($_POST['plugin']);
        $installer->install($plugin);

        // Flush the cache and return the newly installed plugin basename
        wp_cache_flush();

        if($installer->plugin_info()) {
          $plugin_basename = $installer->plugin_info();

          // Activate the plugin silently
          $activated = activate_plugin($plugin_basename);

          if(!is_wp_error($activated)) {
            wp_send_json_success(
              array(
                'message'   => $type == 'plugin' ? __('Plugin installed & activated.', 'thirstyaffiliates') : __('Add-on installed & activated.', 'thirstyaffiliates'),
                'activated' => true,
                'basename'  => $plugin_basename
              )
            );
          } else {
            wp_send_json_success(
              array(
                'message'   => $type == 'plugin' ? __('Plugin installed.', 'thirstyaffiliates') : __('Add-on installed.', 'thirstyaffiliates'),
                'activated' => false,
                'basename'  => $plugin_basename
              )
            );
          }
        }

        wp_send_json_error( $error );
    }
}
