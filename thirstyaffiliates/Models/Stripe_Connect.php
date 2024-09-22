<?php

namespace ThirstyAffiliates\Models;

use ThirstyAffiliates\Abstracts\Abstract_Main_Plugin_Class;

use ThirstyAffiliates\Interfaces\Model_Interface;
use ThirstyAffiliates\Interfaces\Activatable_Interface;
use ThirstyAffiliates\Interfaces\Initiable_Interface;

use ThirstyAffiliates\Helpers\Plugin_Constants;
use ThirstyAffiliates\Helpers\Helper_Functions;
use ThirstyAffiliates\Helpers\Authenticator_Helper;
use ThirstyAffiliates\Helpers\Stripe_Connect_Helper;

/**
 * Model that houses the logic of Stripe Connect
 *
 * @since 3.10.19
 */
class Stripe_Connect implements Model_Interface , Activatable_Interface , Initiable_Interface {
  /*
  |--------------------------------------------------------------------------
  | Class Properties
  |--------------------------------------------------------------------------
  */

  /**
   * Property that holds the single main instance of Stripe Connect.
   *
   * @since 3.0.0
   * @access private
   * @var Stripe_Connect
   */
  private static $_instance;

  /**
   * Model that houses the main plugin object.
   *
   * @since 3.0.0
   * @access private
   * @var Abstract_Main_Plugin_Class
   */
  private $_main_plugin;

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
   * @return Stripe_Connect
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
    add_action( 'admin_init', array( $this, 'persist_display_keys' ) );
    add_action( 'admin_notices', array( $this, 'enable_payment_links_notice' ), 9999 );
    add_action( 'update_option_home', array( $this, 'url_changed' ), 10, 3 );
    add_action( 'update_option_siteurl', array( $this, 'url_changed' ), 10, 3 );
    add_action( 'wp_ajax_ta_stripe_connect_update_creds', array( $this, 'process_update_creds' ) );
    add_action( 'wp_ajax_ta_stripe_connect_refresh', array( $this, 'process_refresh_tokens' ) );
    add_action( 'wp_ajax_ta_stripe_connect_disconnect', array( $this, 'process_disconnect' ) );
    add_filter( 'ta_settings_option_sections', array( $this, 'register_settings_section' ) );
    add_filter( 'ta_settings_section_options' , array( $this , 'register_thirstypay_settings_options' ) );
    add_action( 'ta_before_settings_section_fields' , array( $this , 'render_thirstypay_settings_page' ) );
  }

  /**
  * Display an admin notice for enabling ThirstyPay links.
  *
  * @return void
  */
  public function enable_payment_links_notice() {
    if ( ! $this->_helper_functions->is_stripe_connection_active() && ! get_option('ta_dismiss_notice_ta_thirstypay_stripe') ) {
    ?>
    <style>
      .ta-warning-notice-icon {
        color: #72aee6 !important;
        font-size: 32px !important;
        vertical-align: top !important;
      }

      .ta-warning-notice-title {
        vertical-align: top !important;
        margin-left: 18px !important;
        font-size: 18px !important;
        font-weight: normal !important;
        line-height: 32px !important;
      }
      </style>
      <div class="notice notice-info ta-notice is-dismissible ta-notice-dismiss-permanently" id="ta_stripe_connect_upgrade_notice" data-notice="ta_thirstypay_stripe">
        <p style="margin-top: 12px;"><span class="dashicons dashicons-cart ta-warning-notice-icon"></span><em class="ta-warning-notice-title"><?php esc_html_e( 'Thirsty for More Revenue?', 'thirstyaffiliates' ); ?></em></p>
        <p>
          <?php
            printf(
              esc_html__('Quench it with %1$sNEW ThirstyPay™ Links!%2$s Make taking payments a cinch right on your site with %1$sbrandable checkout links%2$s. No fuss, all fun –  just simple, secure money moves that put more in your pocket.', 'thirstyaffiliates'),
              '<strong>',
              '</strong>'
            );
          ?>
        </p>
        <p style="margin-bottom: 12px;"><a href="<?php echo esc_url(admin_url('edit.php?post_type=thirstylink&page=thirstypay-links')); ?>" class="button button-primary"><?php esc_html_e('Learn More', 'thirstyaffiliates'); ?></a></p>
      </div>
      <?php
    }
  }

  /**
   * Run the process for updating a webhook when a site's home or site URL changes
   *
   * @param  string   $old_url  Old setting (URL)
   * @param  string   $new_url  New setting
   * @param  string   $option   Option name
   *
   * @return string
   */
  public function url_changed( $old_url, $new_url, $option ) {
    if ( $new_url !== $old_url ) {
      $this->maybe_update_domain();
    }
  }

  /**
   * This checks if the current site's domain has changed from what we have stored on the Authentication service.
   * If the domain has changed, we need to update the site on the Auth service, and the connection on the Stripe Connect service.
   *
   * @return void
   */
  public function maybe_update_domain() {

    $old_site_url = get_option( 'ta_old_site_url',  get_site_url() );

    // Exit if the home URL hasn't changed
    if($old_site_url==get_site_url()) {
      return;
    }

    $site_uuid = get_option( 'ta_authenticator_site_uuid' );

    $payload = array(
      'site_uuid' => $site_uuid
    );

    $jwt = Authenticator_Helper::generate_jwt( $payload );
    $domain = parse_url( get_site_url(), PHP_URL_HOST );

    // Request to change the domain with the auth service (site.domain)
    $response = wp_remote_post( TA_AUTH_SERVICE_URL . "/api/domains/update", array(
      'sslverify' => false,
      'headers' => Authenticator_Helper::jwt_header($jwt, TA_AUTH_SERVICE_DOMAIN),
      'body' => array(
        'domain' => $domain
      )
    ) );

    $body = json_decode( wp_remote_retrieve_body( $response ), true );

    // Store for next time
    update_option( 'ta_old_site_url', get_site_url() );
  }

  /**
  * Process a request to retrieve credentials after a connection
  *
  * @return void
  */
  public function process_update_creds() {

    // Security check
    if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'stripe-update-creds' ) ) {
      wp_die(__('Sorry, updating your credentials failed. (security)', 'thirstyaffiliates'));
    }

    // Check for the existence of any errors passed back from the service
      if ( isset( $_GET['error'] ) ) {
        wp_die( sanitize_text_field( urldecode( $_GET['error'] ) ) );
      }

    // Make sure we have a method ID
    if ( ! isset( $_GET['pmt'] ) ) {
      wp_die(__('Sorry, updating your credentials failed. (pmt)', 'thirstyaffiliates'));
    }

    // Make sure the user is authorized.
    if(! $this->_helper_functions->is_admin() ) {
      return;
    }

    $this->update_connect_credentials();

    $stripe_action = ( ! empty( $_GET['stripe-action'] ) ? sanitize_text_field( $_GET['stripe-action'] ) : 'updated' );

    $redirect_url = add_query_arg( array(
        'stripe-action' => $stripe_action
      ), admin_url('edit.php?post_type=thirstylink&page=thirsty-settings&tab=ta_thirstypay_settings')
    );

    wp_redirect($redirect_url);
    exit;
  }

  /** Fetches the credentials from Stripe-Connect and updates them in the payment method. */
  private function update_connect_credentials() {

    $site_uuid = get_option( 'ta_authenticator_site_uuid' );

    $payload = array(
      'site_uuid' => $site_uuid
    );

    $method_id = Stripe_Connect_Helper::get_method_id();
    $jwt = Authenticator_Helper::generate_jwt( $payload );

    // Make sure the request came from the Connect service
    $response = wp_remote_get( TA_STRIPE_SERVICE_URL . "/api/credentials/".$method_id, array(
      'headers' => Authenticator_Helper::jwt_header($jwt, TA_STRIPE_SERVICE_DOMAIN)
    ) );

    $creds = json_decode( wp_remote_retrieve_body( $response ), true );

    update_option( 'ta_stripe_status', 1 );
    update_option( 'ta_stripe_test_secret_key', sanitize_text_field( $creds['test_secret_key'] ) );
    update_option( 'ta_stripe_test_publishable_key', sanitize_text_field( $creds['test_publishable_key'] ) );
    update_option( 'ta_stripe_live_secret_key', sanitize_text_field( $creds['live_secret_key'] ) );
    update_option( 'ta_stripe_live_publishable_key', sanitize_text_field( $creds['live_publishable_key'] ) );

    update_option( 'ta_stripe_connect_status', 'connected' );
    update_option( 'ta_stripe_service_account_id', sanitize_text_field( $creds['service_account_id'] ) );
    update_option( 'ta_stripe_service_account_name', sanitize_text_field( $creds['service_account_name'] ) );
  }

  /**
  * Process a request to refresh tokens
  *
  * @return void
  */
  public function process_refresh_tokens() {

    // Security check
    if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'stripe-refresh' ) ) {
      wp_die(__('Sorry, the refresh failed.', 'thirstyaffiliates'));
    }

    // Make sure we have a method ID
    if ( ! isset( $_GET['method-id'] ) ) {
      wp_die(__('Sorry, the refresh failed.', 'thirstyaffiliates'));
    }

    // Make sure the user is authorized
    if ( ! $this->_helper_functions->is_admin() ) {
      wp_die(__('Sorry, you don\'t have permission to do this.', 'thirstyaffiliates'));
    }

    $method_id = Stripe_Connect_Helper::get_method_id();
    $site_uuid = get_option( 'ta_authenticator_site_uuid' );

    $payload = array(
      'site_uuid' => $site_uuid
    );

    $jwt = Authenticator_Helper::generate_jwt( $payload );

    // Send request to Connect service
    $response = wp_remote_post( TA_STRIPE_SERVICE_URL . "/api/refresh/{$method_id}", array(
      'headers' => Authenticator_Helper::jwt_header( $jwt, TA_STRIPE_SERVICE_DOMAIN ),
    ) );

    $body = json_decode( wp_remote_retrieve_body( $response ), true );

    if ( ! isset( $body['connect_status'] ) || 'refreshed' !== $body['connect_status'] ) {
      wp_die(__('Sorry, the refresh failed.', 'thirstyaffiliates'));
    }

    update_option( 'ta_stripe_status', 1 );
    update_option( 'ta_stripe_test_secret_key', sanitize_text_field( $body['test_secret_key'] ) );
    update_option( 'ta_stripe_test_publishable_key', sanitize_text_field( $body['test_publishable_key'] ) );
    update_option( 'ta_stripe_live_secret_key', sanitize_text_field( $body['live_secret_key'] ) );
    update_option( 'ta_stripe_live_publishable_key', sanitize_text_field( $body['live_publishable_key'] ) );

    update_option( 'ta_stripe_connect_status', 'connected' );
    update_option( 'ta_stripe_service_account_id', sanitize_text_field( $body['service_account_id'] ) );
    update_option( 'ta_stripe_service_account_name', sanitize_text_field( $body['service_account_name'] ) );

    $redirect_url = add_query_arg( array(
        'stripe-action' => 'refreshed'
      ), admin_url('edit.php?post_type=thirstylink&page=thirsty-settings&tab=ta_thirstypay_settings')
    );

    wp_redirect($redirect_url);
    exit;
  }

  /**
  * Process a request to disconnect
  *
  * @return void
  */
  public function process_disconnect() {

    // Security check
    if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'stripe-disconnect' ) ) {
      wp_die(__('Sorry, the disconnect failed.', 'thirstyaffiliates'));
    }

    // Make sure the user is authorized
    if ( ! $this->_helper_functions->is_admin() ) {
      wp_die(__('Sorry, you don\'t have permission to do this.', 'thirstyaffiliates'));
    }

    $res = $this->disconnect();

    if(!$res) {
      wp_die(__('Sorry, the disconnect failed.', 'thirstyaffiliates'));
    }

    update_option( 'ta_stripe_connect_status', 'disconnected' );

    update_option( 'ta_stripe_status', 0 );

    $redirect_url = add_query_arg( array(
        'stripe-action' => 'disconnected'
      ), admin_url('edit.php?post_type=thirstylink&page=thirsty-settings&tab=ta_thirstypay_settings')
    );

    wp_redirect($redirect_url);
    exit;
  }

  private function disconnect() {

    $site_uuid = get_option( 'ta_authenticator_site_uuid' );
    $method_id = Stripe_Connect_Helper::get_method_id();

    // Attempt to disconnect at the service
    $payload = array(
      'method_id' => $method_id,
      'site_uuid' => $site_uuid
    );

    $jwt = Authenticator_Helper::generate_jwt( $payload );

    // Send request to Connect service
    $response = wp_remote_request( TA_STRIPE_SERVICE_URL . "/api/disconnect/{$method_id}", array(
      'method' => 'DELETE',
      'headers' => Authenticator_Helper::jwt_header($jwt, TA_STRIPE_SERVICE_DOMAIN),
    ) );

    $body = json_decode( wp_remote_retrieve_body( $response ), true );

    if ( ! isset( $body['connect_status'] ) || 'disconnected' !== $body['connect_status'] ) {
      return false;
    }

    return true;
  }


  /**
   * Register Payments settings section.
   *
   * @param array $settings_sections Array of settings sections.
   *
   * @return array Filtered array of settings sections.
   *
   */
  public function register_settings_section( $settings_sections ) {

      if ( array_key_exists( 'ta_thirstypay_settings', $settings_sections ) ) {
          return $settings_sections;
      }

      $settings_sections['ta_thirstypay_settings'] = array(
          'title' => __( 'Payments', 'thirstyaffiliates' ),
          'desc'  => '',
      );

      return $settings_sections;

  }

  public function register_thirstypay_settings_options( $settings_section_options ) {
    if ( array_key_exists( 'ta_thirstypay_settings' , $settings_section_options ) )
      return $settings_section_options;

    $settings_section_options[ 'ta_thirstypay_settings' ] = apply_filters(
        'ta_thirstypay_settings',
        array(
            array(
                'id'      => 'ta_stripe_live_publishable_key',
                'title'   => __( 'Live Publishable Key*' , 'thirstyaffiliates' ),
                'desc'    => '',
                'type'    => 'text',
                'condition_cb' => function() { return isset( $_GET['display-keys'] ); }
            ),
            array(
                'id'      => 'ta_stripe_live_secret_key',
                'title'   => __( 'Live Secret Key*' , 'thirstyaffiliates' ),
                'desc'    => '',
                'type'    => 'text',
                'condition_cb' => function() { return isset( $_GET['display-keys'] ); }
            ),
            array(
                'id'      => 'ta_stripe_test_publishable_key',
                'title'   => __( 'Test Publishable Key*' , 'thirstyaffiliates' ),
                'desc'    => '',
                'type'    => 'text',
                'condition_cb' => function() { return isset( $_GET['display-keys'] ); }
            ),
            array(
                'id'      => 'ta_stripe_test_secret_key',
                'title'   => __( 'Test Secret Key*' , 'thirstyaffiliates' ),
                'desc'    => '',
                'type'    => 'text',
                'condition_cb' => function() { return isset( $_GET['display-keys'] ); }
            ),
        )
    );

    return $settings_section_options;
  }

  /**
   * When the ?display-keys query param is set, set a cookie to persist the "selection"
   *
   * @return void
   */
  public function persist_display_keys() {
    if ( isset($_GET['page']) && $_GET['page']=='thirsty-settings' && isset( $_GET['display-keys'] ) ) {
      setcookie( 'ta_stripe_display_keys', '1', time() + HOUR_IN_SECONDS, '/' );
    }
  }

  /**
   * Execute the class.
   *
   * @access public
   * @implements \ThirstyAffiliates\Interfaces\Model_Interface
   */
  public function run() {
    if(!defined('TA_STRIPE_SERVICE_DOMAIN')) {
      define('TA_STRIPE_SERVICE_DOMAIN', 'stripe.thirstyaffiliates.com');
    }

    define('TA_STRIPE_SERVICE_URL', 'https://' . TA_STRIPE_SERVICE_DOMAIN);
    define('TA_SCRIPT_URL',site_url('/index.php?plugin=thirsty'));
  }

  public function render_thirstypay_settings_page( $active_tab ) {

    if( 'ta_thirstypay_settings' === $active_tab ) {
      require_once( $this->_constants->VIEWS_ROOT_PATH() . '/admin/thirstypay-options.php' );
    }

  }

}
