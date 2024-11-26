<?php
namespace ThirstyAffiliates\Models;

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

use ThirstyAffiliates\Helpers\Plugin_Constants;
use ThirstyAffiliates\Interfaces\Model_Interface;
use ThirstyAffiliates\Abstracts\Abstract_Main_Plugin_Class;
use ThirstyAffiliates\Helpers\Helper_Functions;

/**
 * Notifications.
 *
 * Class for logging in-plugin notifications.
 * Includes:
 *     Notifications from our remote feed
 *     Plugin-related notifications (e.g. recent sales performances)
 */
class Notifications implements Model_Interface {

  /**
   * Property that holds the single main instance of Shortcodes.
   *
   * @since 3.1.0
   * @access private
   * @var Notifications
   */
  private static $_instance;

  /**
   * Model that houses the main plugin object.
   *
   * @since 3.1.0
   * @access private
   * @var Redirection
   */
  private $_main_plugin;

  /**
   * Model that houses all the plugin constants.
   *
   * @since 3.1.0
   * @access private
   * @var Plugin_Constants
   */
  private $_constants;

  /**
   * Property that houses all the helper functions of the plugin.
   *
   * @since 3.1.0
   * @access private
   * @var Helper_Functions
   */
  private $_helper_functions;

  /**
   * Option value.
   *
   * @var bool|array
   */
  public $option = false;

  /**
   * Initialize class.
   */
  public function run() {
  }

  /**
   * Class constructor.
   *
   * @since 3.1.0
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
   * @since 3.1.0
   * @access public
   *
   * @param Abstract_Main_Plugin_Class $main_plugin      Main plugin object.
   * @param Plugin_Constants           $constants        Plugin constants object.
   * @param Helper_Functions           $helper_functions Helper functions object.
   * @return Notifications
   */
  public static function get_instance( Abstract_Main_Plugin_Class $main_plugin , Plugin_Constants $constants , Helper_Functions $helper_functions ) {

      if ( !self::$_instance instanceof self )
          self::$_instance = new self( $main_plugin , $constants , $helper_functions );

      return self::$_instance;

  }

  /**
   * Check if user has access and is enabled.
   *
   * @return bool
   */
  public static function has_access() {

    $access = false;

    if (
      current_user_can( 'manage_options' )
      && ! get_option( 'thirstyaff_hide_announcements' )
    ) {
      $access = true;
    }

    return apply_filters( 'thirstyaff_admin_notifications_has_access', $access );
  }
}
