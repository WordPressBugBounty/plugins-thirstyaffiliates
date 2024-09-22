<?php

namespace ThirstyAffiliates\Models;

use ThirstyAffiliates\Abstracts\Abstract_Main_Plugin_Class;

use ThirstyAffiliates\Interfaces\Model_Interface;
use ThirstyAffiliates\Interfaces\Activatable_Interface;
use ThirstyAffiliates\Interfaces\Initiable_Interface;

use ThirstyAffiliates\Helpers\Plugin_Constants;
use ThirstyAffiliates\Helpers\Helper_Functions;
use ThirstyAffiliates\Helpers\Onboarding_Helper;
use ThirstyAffiliates\Helpers\Authenticator_Helper;
use ThirstyAffiliates\Helpers\Stripe_Connect_Helper;

/**
 * Model that houses the logic of Onboarding
 *
 * @since 3.10.19
 */
class Authenticator implements Model_Interface , Activatable_Interface , Initiable_Interface {
  /*
  |--------------------------------------------------------------------------
  | Class Properties
  |--------------------------------------------------------------------------
  */

  /**
   * Property that holds the single main instance of Authenticator.
   *
   * @since 3.0.0
   * @access private
   * @var Authenticator
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

      $this->_constants           = $constants;
      $this->_helper_functions    = $helper_functions;

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
   * @return Authenticator
   */
  public static function get_instance( Abstract_Main_Plugin_Class $main_plugin , Plugin_Constants $constants , Helper_Functions $helper_functions ) {

      if ( !self::$_instance instanceof self )
          self::$_instance = new self( $main_plugin , $constants , $helper_functions );

      return self::$_instance;

  }


  /**
   * Execute codes that needs to run plugin activation.
   *
   * @since 3.0.0
   * @access public
   * @implements \ThirstyAffiliates\Interfaces\Activatable_Interface
   */
  public function activate() {
  }

  /**
   * Execute codes that needs to run on plugin initialization.
   *
   * @since 3.0.0
   * @access public
   * @implements \ThirstyAffiliates\Interfaces\Initiable_Interface
   */
  public function initialize() {
    add_action( 'admin_init', array($this, 'process_connect' ) );
    add_action( 'admin_init', array($this, 'process_disconnect' ) );
    add_action( 'admin_init', array( $this, 'delete_connection_data' ) );
  }

  /**
   * Execute the class.
   *
   * @access public
   * @implements \ThirstyAffiliates\Interfaces\Model_Interface
   */
  public function run() {
      if(!defined('TA_AUTH_SERVICE_DOMAIN')) {
        define('TA_AUTH_SERVICE_DOMAIN', 'auth.caseproof.com');
      }
      define('TA_AUTH_SERVICE_URL', 'https://' . TA_AUTH_SERVICE_DOMAIN);
  }

  /**
   * Validates the GET parameter and clears the saved connection data from the Authenticator.
   *
   * @access public
   * @return void
   */
  public static function delete_connection_data() {
    if ( isset( $_GET['ta-clear-connection-data'] ) ) {
      // Admins only
      if ( current_user_can( 'manage_options' ) ) {
        self::clear_connection_data();
      }
    }
  }

  /**
   * Clears the saved connection data from the Authenticator.
   *
   * @access public
   * @return void
   */
  public static function clear_connection_data() {
    delete_option('ta_authenticator_site_uuid');
    delete_option('ta_authenticator_account_email');
    delete_option('ta_authenticator_secret_token');
  }

  /**
   * Processes a connection to the Authenticator service.
   *
   * @access public
   * @return void
   */
  public function process_connect() {

    // Make sure we've entered our Authenticator process.
    if(!isset($_GET['ta-connect']) || $_GET['ta-connect'] !== 'true') {
      return;
    }

    // Validate the nonce on the WP side of things.
    if(!isset($_GET['nonce']) || !wp_verify_nonce($_GET['nonce'], 'ta-connect')) {
      return;
    }


    // Make sure the user is authorized.
    if(! $this->_helper_functions->is_admin() ) {
      return;
    }

    $site_uuid = sanitize_text_field($_GET['site_uuid']);
    $auth_code = sanitize_text_field($_GET['auth_code']);

    // GET request to obtain token.
    $response = wp_remote_get(TA_AUTH_SERVICE_URL . "/api/tokens/{$site_uuid}", array(
      'sslverify' => false,
      'headers'   => array(
        'accept' => 'application/json'
      ),
      'body'      => array(
        'auth_code' => $auth_code
      )
    ));

    $body = json_decode(wp_remote_retrieve_body($response), true);

    if(isset($body['account_email']) && !empty($body['account_email'])) {
      $email_saved = update_option('ta_authenticator_account_email', sanitize_text_field($body['account_email']), false);
    }

    if(isset($body['secret_token']) && !empty($body['secret_token'])) {
      $token_saved = update_option('ta_authenticator_secret_token', sanitize_text_field($body['secret_token']), false);
    }

    if(isset($body['user_uuid']) && !empty($body['user_uuid'])) {
      $user_uuid_saved = update_option('ta_authenticator_user_uuid', sanitize_text_field($body['user_uuid']), false);
    }

    if($site_uuid) {
      update_option('ta_authenticator_site_uuid', $site_uuid, false);
    }

    if ( isset( $_GET['stripe_connect'] ) && 'true' === $_GET['stripe_connect'] && isset( $_GET['method_id'] ) && ! empty( $_GET['method_id'] ) ) {
       wp_redirect( Stripe_Connect_Helper::get_stripe_connect_url( $_GET['method_id'] ) );
      exit;
    }

    $redirect_url = remove_query_arg(array(
      'ta-connect',
      'nonce',
      'site_uuid',
      'user_uuid',
      'auth_code',
      'license_key'
    ));

    $license_key = isset($_GET['license_key']) ? sanitize_text_field(wp_unslash($_GET['license_key'])) : '';

    if(!empty($license_key)) {
      try {

        $args = array(
          'domain' => urlencode($this->_helper_functions->site_domain())
        );

        $act = $this->send_mothership_request("/license_keys/activate/{$license_key}", $args, 'post');
        $license = $this->send_mothership_request( "/versions/info/{$license_key}", $args );

        $this->maybe_install_pro_plugin( $license );

        if( function_exists('ThirstyAffiliates_Pro') && is_object(ThirstyAffiliates_Pro()->get_model('Update')) ) {
          $update = ThirstyAffiliates_Pro()->get_model('Update');
          $update->activate_license($license_key);
        } else {
          set_site_transient( 'tap_license_info', $license, 24 * HOUR_IN_SECONDS );
          do_action( 'tap_license_activated', $act );
        }

      } catch (\Exception $e) {
        $redirect_url = add_query_arg('license_error', urlencode($e->getMessage()), $redirect_url);
      }
    }

    wp_redirect($redirect_url);
    exit;
  }

  private function maybe_install_pro_plugin($license_info) {

    if( ! is_array( $license_info ) || ! isset( $license_info['url'] ) || empty( $license_info['url'] ) )  {
      return false;
    }

    $plugin = $license_info['url'];
    $main_file = 'thirstyaffiliates-pro/thirstyaffiliates-pro.php';

    $installed = file_exists(WP_PLUGIN_DIR . '/' . $main_file);
    $active = is_plugin_active($main_file);

    if($installed && $active) {
      return 1;
    }

    // If TA Pro is installed but not active, let's activate.
    if($installed && !$active) {
      $result = activate_plugins(wp_unslash($main_file));
    } else {
      $result = (int) Onboarding_Helper::download_and_activate_plugin($plugin);
    }

    return $result;
  }

  public function send_mothership_request( $endpoint,
                                           $args=array(),
                                           $method='get',
                                           $blocking=true ) {
    $domain = defined('TA_MOTHERSHIP_DOMAIN') ? TA_MOTHERSHIP_DOMAIN : 'http://mothership.caseproof.com';
    $uri = "{$domain}{$endpoint}";

    $arg_array = array(
      'method'    => strtoupper($method),
      'body'      => $args,
      'timeout'   => 15,
      'blocking'  => $blocking,
      'sslverify' => false
    );

    $resp = wp_remote_request($uri, $arg_array);

    // If we're not blocking then the response is irrelevant
    // So we'll just return true.
    if($blocking == false) {
      return true;
    }

    if(is_wp_error($resp)) {
      throw new \Exception(__('You had an HTTP error connecting to Caseproof\'s Mothership API', 'thirstyaffiliates'));
    }
    else {
      if(null !== ($json_res = json_decode($resp['body'], true))) {
        if(isset($json_res['error'])) {
          throw new \Exception($json_res['error']);
        }
        else {
          return $json_res;
        }
      }
      else {
        throw new \Exception(__( 'Your License Key was invalid', 'thirstyaffiliates'));
      }
    }

    return false;
  }

  /**
   * Processes a disconnect to the Authenticator service.
   *
   * @access public
   * @return void
   */
  public function process_disconnect() {
    // Make sure we've entered our Authenticator process.
    if(!isset($_GET['ta-disconnect']) || $_GET['ta-disconnect'] !== 'true') {
      return;
    }

    // Validate the nonce on the WP side of things.
    if(!isset($_GET['nonce']) || !wp_verify_nonce($_GET['nonce'], 'ta-disconnect')) {
      return;
    }

    // Make sure the user is authorized.
    if(!$this->_helper_functions->is_admin() ) {
      return;
    }

    $site_email = get_option('ta_authenticator_account_email');
    $site_uuid = get_option('ta_authenticator_site_uuid');

    do_action('ta_com_pre_disconnect', $site_uuid, $site_email);

    // Create token payload.
    $payload = array(
      'email'     => $site_email,
      'site_uuid' => $site_uuid
    );

    // Create JWT.
    $jwt = Authenticator_Helper::generate_jwt($payload);

    // DELETE request to obtain token.
    $response = wp_remote_request(TA_AUTH_SERVICE_URL . "/api/disconnect/thirstyaffiliates", array(
      'method'    => 'DELETE',
      'sslverify' => false,
      'headers'   => Authenticator_Helper::jwt_header($jwt, TA_AUTH_SERVICE_DOMAIN)
    ));

    $body = json_decode(wp_remote_retrieve_body($response), true);

    if(isset($body['disconnected']) && $body['disconnected'] === true) {
      delete_option( 'ta_authenticator_account_email' );
      delete_option( 'ta_authenticator_secret_token' );
      delete_option( 'ta_authenticator_user_uuid' );
      delete_option( 'ta_authenticator_site_uuid' );
    }

    wp_redirect(remove_query_arg(array('ta-disconnect', 'nonce')));
    exit;
  }

    /**
     * Get the license information
     *
     * @return array|false
     */
    public function get_license_info() {
        $license_info = get_site_transient( 'tap_license_info' );
        $license_key = $this->_helper_functions->get_option( 'tap_slmw_license_key' );

        if ( ! $license_info && ! empty( $license_key ) ) {
            try {
                $domain = urlencode( $this->_helper_functions->get_site_domain() );
                $args = compact( 'domain' );
                $license_info = $this->send_mothership_request( "/versions/info/{$license_key}", $args, 'post' );

                set_site_transient( 'tap_license_info', $license_info, 24 * HOUR_IN_SECONDS );
            } catch ( \Exception $e ) {
                // Fail silently, license info will return false.
            }
        }

        return $license_info;
    }
}
