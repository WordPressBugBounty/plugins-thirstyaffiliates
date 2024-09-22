<?php

namespace ThirstyAffiliates\Models;

use ThirstyAffiliates\Abstracts\Abstract_Main_Plugin_Class;

use ThirstyAffiliates\Interfaces\Model_Interface;
use ThirstyAffiliates\Interfaces\Activatable_Interface;
use ThirstyAffiliates\Interfaces\Initiable_Interface;

use ThirstyAffiliates\Helpers\Plugin_Constants;
use ThirstyAffiliates\Helpers\Helper_Functions;
use ThirstyAffiliates\Helpers\Onboarding_Helper;
use ThirstyAffiliates\Helpers\Addon_Install_Skin;

use ThirstyAffiliates_Pro\Models\CSV_Importer\CSV_Importer;

/**
 * Model that houses the logic of Onboarding
 *
 * @since 3.10.19
 */
class Onboarding implements Model_Interface , Activatable_Interface , Initiable_Interface {

  /*
  |--------------------------------------------------------------------------
  | Class Properties
  |--------------------------------------------------------------------------
  */

  /**
   * Property that holds the single main instance of Onboarding.
   *
   * @since 3.0.0
   * @access private
   * @var Onboarding
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
   * @return Onboarding
   */
  public static function get_instance( Abstract_Main_Plugin_Class $main_plugin , Plugin_Constants $constants , Helper_Functions $helper_functions ) {

      if ( !self::$_instance instanceof self )
          self::$_instance = new self( $main_plugin , $constants , $helper_functions );

      return self::$_instance;

  }

  /*
  |--------------------------------------------------------------------------
  | Fulfill Implemented Interface Contracts
  |--------------------------------------------------------------------------
  */

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
    add_action( 'wp_ajax_ta_onboarding_mark_steps_complete' , array( $this , 'mark_steps_complete' ) );
    add_action( 'admin_notices' , array( $this , 'remove_all_admin_notices' ), 0 );
    add_action( 'wp_ajax_ta_onboarding_save_features' , array( $this , 'save_features' ) );
    add_action( 'wp_ajax_ta_onboarding_load_link_step_content' , array( $this , 'load_link_step_content' ) );
    add_action( 'wp_ajax_ta_onboarding_load_create_new_content' , array( $this , 'load_create_new_content' ) );
    add_action( 'wp_ajax_ta_onboarding_save_new_link' , array( $this , 'save_new_link' ) );
    add_action( 'wp_ajax_ta_onboarding_set_content' , array( $this , 'set_content' ) );
    add_action( 'wp_ajax_ta_onboarding_unset_content' , array( $this , 'unset_content' ) );
    add_action( 'wp_ajax_ta_onboarding_save_new_category' , array( $this , 'save_new_category' ) );
    add_action( 'wp_ajax_ta_onboarding_get_category' , array( $this , 'get_category' ) );
    add_action( 'wp_ajax_ta_onboarding_import_links' , array( $this , 'import_links' ) );
    add_action( 'wp_ajax_ta_onboarding_unset_category' , array( $this , 'unset_category' ) );
    add_action( 'wp_ajax_ta_onboarding_mark_content_steps_skipped' , array( $this , 'mark_content_steps_skipped' ) );
    add_action( 'wp_ajax_ta_onboarding_load_finish_step' , array( $this , 'load_finish_step' ) );
    add_action( 'wp_ajax_ta_onboarding_re_render_links_list' , array( $this , 're_render_links_list' ) );
    add_action( 'wp_ajax_ta_onboarding_load_complete_step' , array( $this , 'load_complete_step' ) );
    add_action( 'wp_ajax_ta_onboarding_install_correct_edition' , array( $this , 'install_correct_edition' ) );
    add_action( 'wp_ajax_ta_onboarding_install_addons' , array( $this , 'install_addons' ) );
    add_action( 'wp_ajax_ta_onboarding_finish' , array( $this , 'finish' ) );
    add_action( 'tap_license_activated' , array( $this , 'license_activated' ) );
    add_action( 'tap_license_deactivated' , array( $this , 'license_deactivated' ) );
    add_filter( 'monsterinsights_shareasale_id' , array( $this , 'monsterinsights_shareasale_id' ) );
 }

  private function is_onboarding_page() {
    $screen = get_current_screen();
    return ($screen && is_string($screen->id) && preg_match('/_page_thirstyaffiliates_onboarding$/', $screen->id));
  }

  public function remove_all_admin_notices() {
    if($this->is_onboarding_page()) {
      remove_all_actions('admin_notices');
    }
  }

  private function get_screen_id($hook=null) {
    if(is_null($hook)) {
      $screen = get_current_screen();
      $hook = $screen->id;
    }

    return $hook;
  }

  private function is_ta_page() {
    $hook = $this->get_screen_id();
    return (strstr($hook, 'thirstylink') !== false);
  }

  public function admin_notice() {
    if(!$this->is_ta_page() || !$this->_helper_functions->is_logged_in_and_an_admin()) {
      return;
    }

    if(!get_option('ta_onboarded') || get_option('ta_onboarding_complete') == '1' || get_transient('ta_dismiss_notice_continue_onboarding')) {
      return;
    }
    ?>
    <div class="notice notice-info ta-notice-dismiss-daily is-dismissible" data-notice="continue_onboarding">
      <p>
        <?php
        printf(
          // translators: %1$s open link tag, %2$s: close link tag
          esc_html__("Welcome back! Looks like you got sidetracked before finishing up your ThirstyAffiliates setup. No worries! %1\$sClick here%2\$s and we'll zip you back to where you left off.", 'thirstyaffiliates'),
          '<a href="' . esc_url(admin_url('options.php?page=thirstyaffiliates_onboarding&step=1')) . '">',
          '</a>'
        );
        ?>
      </p>
    </div>
    <?php
  }

  /**
   * Execute Onboarding class.
   *
   * @since 3.0.0
   * @access public
   * @implements \ThirstyAffiliates\Interfaces\Model_Interface
   */
  public function run() {
    add_action( 'admin_notices' , array( $this , 'admin_notice' ) );
    add_filter( 'submenu_file' , array( $this , 'highlight_menu_item' ) );
  }

  public function highlight_menu_item($submenu_file) {
    remove_submenu_page('thirstylink', 'thirstyaffiliates_onboarding');
    if($this->is_onboarding_page()) {
        $submenu_file = 'edit.php?post_type=thirstylink';
    }

    return $submenu_file;
  }

  private function validate_request($nonce_action) {
    if(!$this->_helper_functions->is_post_request()) {
      wp_send_json_error(__('Bad request.', 'thirstyaffiliates'));
    }

    if(!$this->_helper_functions->is_logged_in_and_an_admin()) {
      wp_send_json_error(__('Sorry, you don\'t have permission to do this.', 'thirstyaffiliates'));
    }

    if(!check_ajax_referer($nonce_action, false, false)) {
      wp_send_json_error(__('Security check failed.', 'thirstyaffiliates'));
    }
  }

  private function get_request_data($nonce_action) {
    $this->validate_request($nonce_action);

    if(!isset($_POST['data']) || !is_string($_POST['data'])) {
      wp_send_json_error(__('Bad request.', 'thirstyaffiliates'));
    }

    $data = json_decode(wp_unslash($_POST['data']), true);

    if(!is_array($data)) {
      wp_send_json_error(__('Bad request.', 'thirstyaffiliates'));
    }

    return $data;
  }

  public function save_features() {
    $data = $this->get_request_data('ta_onboarding_save_features');

    $valid_features = Onboarding_Helper::get_features();
    $pro_features = Onboarding_Helper::get_pro_features();
    $features = array();
    $addons = array();

    foreach($data as $feature) {
      if(array_key_exists($feature, $valid_features['features'])) {
        $features[] = $feature;
      }

      if(array_key_exists($feature, $valid_features['addons'])) {
        $addons[] = $feature;
      }
    }

    // Figure out which features were left disabled.
    $missing_keys = array_diff_key($valid_features['features'], array_flip($features));
    $features_disabled = array_keys($missing_keys);

    $all_features = !empty($features) ? array_merge($features, $features_disabled) : array();

    $data = array();
    $data['features'] = array_merge($features, $addons);
    $data['addons_not_installed'] = array();
    $data['features_not_enabled'] = array();

    // Loop over each add-on then attempt to install and activate them.
    if(!empty($addons)) {

      $license_addons = array();
      if( function_exists('ThirstyAffiliates_Pro') && is_object(ThirstyAffiliates_Pro()->get_model('Update')) ) {
        $update = ThirstyAffiliates_Pro()->get_model('Update');
        if( method_exists($update, 'addons') ) {
          $license_addons = $update->addons(true, true, true);
        }
      }

      // lets try to install and activate add-on.
      foreach( $addons as $addon_slug ) {
        $response = $this->maybe_install_activate_addons($license_addons,$addon_slug);
        if( -1 === (int) $response ) {
          $data['addons_not_installed'][] = $addon_slug;
        }
      }
    }

    // Enable or disable any features that the user enabled/disabled.
    if(!empty($all_features)) {
      foreach($all_features as $feature) {
        $status = in_array($feature, $features) ? 'yes' : 'no';

        if(!Onboarding_Helper::is_pro_active() && in_array($feature, $pro_features) && $status) {
          $data['features_not_enabled'][] = $feature;
        } else {
          $this->enable_disable_feature($valid_features['features'], $feature, $status);
        }
      }
    }

    Onboarding_Helper::set_selected_features($data);
    Onboarding_Helper::maybe_set_steps_completed(2);

    wp_send_json_success($data);
  }

  private function enable_disable_feature($features, $feature, $status) {
    if( isset($features[$feature]) ) {
      update_option( $features[$feature], $status, false );
    }
  }

  public function maybe_install_activate_addons($license_addons,$addon_slug) {
    $return_value = -1;

    if( is_object($license_addons) && isset($license_addons->$addon_slug)) {
      $addon_info = $license_addons->$addon_slug;

      $plugin_url = $addon_info->url;

      $installed = isset($addon_info->extra_info->directory) && is_dir(WP_PLUGIN_DIR . '/' . $addon_info->extra_info->directory);
      $active = isset($addon_info->extra_info->main_file) && is_plugin_active($addon_info->extra_info->main_file);

      if($installed && $active) { // already installed and active.
        return 1;
      }
      elseif($installed && !$active) { // already installed and inactive.

        if(isset($addon_info->extra_info->main_file)) {
          $this->maybe_install_dependent_plugin($addon_slug);
          $result = activate_plugins(wp_unslash($addon_info->extra_info->main_file));
          return (int) is_wp_error($result);
        }
        else {
          return 0;
        }
      }
      else {
        return (int) $this->download_and_activate_addon($addon_info, $plugin_url, $addon_slug);
      }
    }

    // Install/activate MI if applicable.
    if($addon_slug == 'monsterinsights') {
      return $this->maybe_install_dependent_plugin($addon_slug);
    }

    return $return_value;
  }

  private function maybe_install_dependent_plugin($addon_slug) {
    if('monsterinsights' === (string)$addon_slug){
      $plugin = 'https://downloads.wordpress.org/plugin/google-analytics-for-wordpress.latest-stable.zip';
      $main_file = 'google-analytics-for-wordpress/googleanalytics.php';

      $installed = is_dir(WP_PLUGIN_DIR . '/' . 'google-analytics-for-wordpress');
      $active = is_plugin_active($main_file);

      if($installed && $active) {
        return 1;
      }

      // If MI is installed but not active, let's activate.
      if($installed && !$active) {
        $result = activate_plugins(wp_unslash($main_file));
      } else {
        $result = (int) Onboarding_Helper::download_and_activate_plugin($plugin);
      }

      if( $result ) {
        // Disable the MonsterInsights redirect after plugin activation.
        delete_transient( '_monsterinsights_activation_redirect' );
      }

      return $result;
    }
  }

  public function load_link_step_content() {
    $data = $this->get_request_data('ta_onboarding_load_link_step_content');

    ob_start();
    require $this->_constants->VIEWS_ROOT_PATH() . '/admin/onboarding/parts/thirsty-link.php';
    $html = ob_get_clean();

    wp_send_json_success(array('html' => $html));
  }

  public function load_create_new_content() {
    $data = self::get_request_data('ta_onboarding_load_create_new_content');

    ob_start();
    require $this->_constants->VIEWS_ROOT_PATH() . '/admin/onboarding/parts/link_popup.php';
    $html = ob_get_clean();

    wp_send_json_success(array('html' => $html));
  }

  public function save_new_link() {

    $data = $this->get_request_data('ta_onboarding_save_new_link');

    if(empty($data['target_url']) || empty($data['slug']) || empty($data['redirection'])) {
      wp_send_json_error(esc_html__('Bad request.', 'thirstyaffiliates'));
    }

    if(!current_user_can('publish_posts')) {
      wp_send_json_error(esc_html__('Sorry, you don\'t have permission to do this.', 'thirstyaffiliates'));
    }

    $target_url = sanitize_text_field($data['target_url']);
    $slug = sanitize_text_field($data['slug']);
    $redirection = sanitize_text_field($data['redirection']);

    $post_id = wp_insert_post([
      'post_type' => Plugin_Constants::AFFILIATE_LINKS_CPT,
      'post_title' => wp_slash($slug),
      'post_status' => 'publish',
    ], true);


    if(!$post_id) {
      wp_send_json_error(esc_html__('There was a problem attempting to create your link. Please try again.', 'thirstyaffiliates'));
    }

    update_post_meta( $post_id , Plugin_Constants::META_DATA_PREFIX . 'destination_url' , esc_url_raw($target_url) );
    update_post_meta( $post_id , Plugin_Constants::META_DATA_PREFIX . 'redirect_type' , (int) $redirection );

    Onboarding_Helper::set_link_id($post_id);
    Onboarding_Helper::maybe_set_steps_completed(2);

    $post = get_post($post_id, ARRAY_A);
    wp_send_json_success(array(
      'heading' => esc_html__('Your Link', 'thirstyaffiliates'),
      'link' => array(
        'link_cpt_id' => $post['ID'],
        'name' => $post['post_title'],
      )
    ));
  }

  public function set_content() {
    $data = self::get_request_data('ta_onboarding_set_content');

    if(!current_user_can('publish_posts')) {
      wp_send_json_error(esc_html__('Sorry, you don\'t have permission to do this.', 'thirstyaffiliates'));
    }

    if(empty($data['content_id'])) {
      wp_send_json_error(esc_html__('Bad request.', 'thirstyaffiliates'));
    }

    $content_id = absint($data['content_id']);
    $post = get_post($content_id, ARRAY_A);

    if( !$post || empty($post) || $post['post_type'] !== Plugin_Constants::AFFILIATE_LINKS_CPT ){
      wp_send_json_error(esc_html__('Invalid request.', 'thirstyaffiliates'));
    }

    Onboarding_Helper::set_link_id($content_id);
    Onboarding_Helper::maybe_set_steps_completed(3);

    wp_send_json_success(array(
      'link_data' => $post
    ));
  }

  public function unset_content() {
    $data = self::get_request_data('ta_onboarding_unset_content');

    Onboarding_Helper::set_link_id(0);

    $imported_links = $data['imported_links'];

    $link = get_post($data['content_id']);

    if($link && $link->post_type === Plugin_Constants::AFFILIATE_LINKS_CPT) {
      wp_delete_post( (int) $data[ 'content_id' ], true );
    }

    if($imported_links) {
      wp_send_json_success(array('count' => Onboarding_Helper::get_links_count() ));
    } else {
      wp_send_json_success();
    }
  }

  public function save_new_category() {
    $data = self::get_request_data('ta_onboarding_save_new_category');

    if(empty($data['name'])) {
      wp_send_json_error(esc_html__('Bad request.', 'thirstyaffiliates'));
    }

    if(!current_user_can('publish_posts')) {
      wp_send_json_error(esc_html__('Sorry, you don\'t have permission to do this.', 'thirstyaffiliates'));
    }

    $name = sanitize_text_field($data['name']);
    $link_ids = isset($data['link_ids']) && !empty($data['link_ids']) ? array_map('absint', $data['link_ids']) : '';

    $taxonomy = 'thirstylink-category';

    $result = wp_insert_term($name, $taxonomy);

    if(is_wp_error($result)) {
      wp_send_json_error($result->get_error_message());
    }

    $term_id = $result['term_id'];

    if(empty($link_ids)) {
      $link_id = Onboarding_Helper::get_link_id();

      wp_set_post_terms($link_id, $term_id, $taxonomy);
    } else {
      foreach($link_ids as $link_id) {
        $link = get_post($link_id);

        wp_set_post_terms($link->ID, $term_id, $taxonomy);
      }
    }

    $term = get_term($term_id);

    Onboarding_Helper::set_category_id($term_id);
    Onboarding_Helper::maybe_set_steps_completed(4);

    wp_send_json_success(array('term' => $term));
  }

  public function get_category() {
    $data = self::get_request_data('ta_onboarding_get_category');

    if(empty($data['category_id'])) {
      wp_send_json_error(esc_html__('Bad request.', 'thirstyaffiliates'));
    }

    if(!current_user_can('publish_posts')) {
      wp_send_json_error(esc_html__('Sorry, you don\'t have permission to do this.', 'thirstyaffiliates'));
    }

    $term_id = sanitize_text_field($data['category_id']);
    $term = get_term($term_id);

    wp_send_json_success(array('term' => $term));
  }

  public function import_links() {
    self::validate_request('ta_onboarding_import_links');

    $csv_file = isset($_FILES['import']) ? $_FILES['import'] : '';

    if( empty($csv_file) ) {
      wp_send_json_error(esc_html__('There was a problem processing your CSV file. Please try the import again.', 'thirstyaffiliates'));
    } else if( !function_exists('ThirstyAffiliates_Pro') ) {
      wp_send_json_error(esc_html__('There was a problem processing your import. It is a pro only feature and CSV Import/Export module must be enabled.', 'thirstyaffiliates'));
    }

    $csv_importer = new CSV_Importer( ThirstyAffiliates_Pro()->helpers[ 'Plugin_Constants' ], ThirstyAffiliates_Pro()->helpers[ 'Helper_Functions' ] );
    $result = $csv_importer->import();

    wp_send_json_success($result);
  }

  public function unset_category() {
    $data = self::get_request_data('ta_onboarding_unset_category');
    $taxonomy = 'thirstylink-category';

    wp_delete_term($data['category_id'], $taxonomy);

    Onboarding_Helper::set_category_id(0);
  }

  public function mark_content_steps_skipped() {
    $data = self::get_request_data('ta_onboarding_mark_content_steps_skipped');
    Onboarding_Helper::mark_content_steps_skipped();
    Onboarding_Helper::maybe_set_steps_completed(4);
  }

  public function load_finish_step() {
    $data = self::get_request_data('ta_onboarding_load_finish_step');

    ob_start();
    require $this->_constants->VIEWS_ROOT_PATH() . '/admin/onboarding/parts/finish.php';
    $html = ob_get_clean();

    wp_send_json_success(array('html' => $html));
  }

  public function render_links_list() {

    $pretty_links = get_posts(array(
      'numberposts'      => -1,
      'orderby'          => 'date',
      'order'            => 'DESC',
      'post_type'        => Plugin_Constants::AFFILIATE_LINKS_CPT,
      'suppress_filters' => true,
    ));

    $current_page = isset($data['link_page']) ? absint($data['link_page']) : 1;

    ob_start();
    require $this->_constants->VIEWS_ROOT_PATH() . '/admin/onboarding/created-links-list.php';
    $html = ob_get_clean();

    return $html;
  }

  public function re_render_links_list() {
    $data = self::get_request_data('ta_onboarding_re_render_links_list');

    $pretty_links = get_posts(array(
      'numberposts'      => -1,
      'post_status'      => 'publish',
      'orderby'          => 'date',
      'order'            => 'DESC',
      'post_type'        => Plugin_Constants::AFFILIATE_LINKS_CPT,
      'suppress_filters' => true,
    ));

    $current_page = isset($data['page']) && $data['page'] > 1 ? absint($data['page']) : 1;

    ob_start();
    require $this->_constants->VIEWS_ROOT_PATH() . '/admin/onboarding/created-links-list.php';
    $html = ob_get_clean();

    wp_send_json_success(array('html' => $html));
  }

  public function mark_steps_complete() {
    $data = $this->get_request_data('ta_onboarding_mark_steps_complete');
    Onboarding_Helper::maybe_set_steps_completed($data['step']);
  }

  public function load_complete_step() {
    $data = $this->get_request_data('ta_onboarding_load_complete_step');

    wp_send_json_success(array('html' => Onboarding_Helper::get_completed_step_urls_html()));
  }

  public function license_activated() {

    if(!isset($_GET['page']) || !isset($_GET['step'])) {
      return;
    }

    Onboarding_Helper::maybe_set_steps_completed(1);

    if((string) $_GET['page'] === 'thirstyaffiliates_onboarding' && (int) $_GET['step'] === 1) {
      // to rebuild the tap_license_info transient.

      if ( function_exists('ThirstyAffiliates_Pro') && is_object(ThirstyAffiliates_Pro()->get_model('Update')) ) {
        $tap_update = ThirstyAffiliates_Pro()->get_model('Update');
        $tap_update->manually_queue_update();

        $li = get_site_transient('tap_license_info');

        $editions = ThirstyAffiliates_Pro()->helpers['Helper_Functions']->is_incorrect_edition_installed();

        if(is_array($editions) && $editions['license']['index'] > $editions['installed']['index'] ||
          $tap_update->is_activated() && !file_exists( WP_PLUGIN_DIR . '/thirstyaffiliates-pro/thirstyaffiliates-pro.php' ) && !empty($li) && is_array($li) && !empty($li['url']) && ThirstyAffiliates_Pro()->helpers['Helper_Functions']->is_url($li['url'])) {
          $result = ThirstyAffiliates_Pro()->helpers['Helper_Functions']->install_plugin_silently($li['url'], array('overwrite_package' => true));
          if($result === true) {
            do_action('tap_plugin_edition_changed');
          }
        }
      }
    }
  }

  public function license_deactivated() {
    Onboarding_Helper::set_steps_completed(0);
  }

  public function install_correct_edition() {
    $this->validate_request('ta_onboarding_install_correct_edition');

    if( ! function_exists('ThirstyAffiliates_Pro') ) {
      wp_send_json_error(__('Invalid request.', 'thirstyaffiliates'));
    }

    $li = get_site_transient('tap_license_info');

    if(!empty($li) && is_array($li) && !empty($li['url']) && ThirstyAffiliates_Pro()->helpers['Helper_Functions']->is_url($li['url'])) {
      $result = ThirstyAffiliates_Pro()->helpers['Helper_Functions']->install_plugin_silently($li['url'], array('overwrite_package' => true));

      if($result instanceof \WP_Error) {
        wp_send_json_error($result->get_error_message());
      }
      elseif($result === true) {
        do_action('tap_plugin_edition_changed');
        wp_send_json_success(__('The correct edition of ThirstyAffiliates has been installed successfully.', 'thirstyaffiliates'));
      }
      else {
        wp_send_json_error(__('Failed to install the correct edition of ThirstyAffiliates, please download it from ThirstyAffiliates.com and install it manually.', 'thirstyaffiliates'));
      }
    }

    wp_send_json_error(__('License data not found', 'thirstyaffiliates'));
  }

  public function monsterinsights_shareasale_id($id) {
    if(get_option('thirstyaffiliates_installed_monsterinsights')) {
      $id = '409876';
    }

    return $id;
  }

  public function finish() {
    $this->validate_request('ta_onboarding_finish');

    update_option('ta_onboarding_complete', '1', false);

    wp_send_json_success();
  }

  private function download_and_activate_addon($addon_info,$plugin_url, $addon_slug = '') {

    if(!$addon_info->installable){
      return -1; // upgrade required.
    }

    // Prepare variables
    $url = esc_url_raw(
      add_query_arg(
        array(
          'page' => 'thirstyaffiliates-addons',
          'onboarding' => '1',
        ),
        admin_url('admin.php')
      )
    );

    $creds = request_filesystem_credentials($url, '', false, false, null);

    // Check for file system permissions
    if(false === $creds) {
      return false;
    }

    if(!WP_Filesystem($creds)) {
      return false;
    }

    // We do not need any extra credentials if we have gotten this far, so let's install the plugin
    require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

    // Do not allow WordPress to search/download translations, as this will break JS output
    remove_action('upgrader_process_complete', array('Language_Pack_Upgrader', 'async_upgrade'), 20);

    // Create the plugin upgrader with our custom skin
    $installer = new \Plugin_Upgrader(new Addon_Install_Skin());

    $plugin = wp_unslash($plugin_url);
    $installer->install($plugin);

    // Flush the cache and return the newly installed plugin basename
    wp_cache_flush();

    if($installer->plugin_info()) {
      $plugin_basename = $installer->plugin_info();

      $this->maybe_install_dependent_plugin($addon_slug);

      // Activate the plugin silently
      $activated = activate_plugin($plugin_basename);

      if(!is_wp_error($activated)) {
        return true;
      } else {
        return false;
      }
    }

    return false;
  }

  public function install_addons() {
    $this->validate_request('ta_onboarding_install_addons');

    $data = $this->get_request_data('ta_onboarding_install_addons');

    if(empty($data['addon_slug'])) {
      wp_send_json_error(__('Bad request.', 'thirstyaffiliates'));
    }

    if(!current_user_can('publish_posts')) {
      wp_send_json_error(__('Sorry, you don\'t have permission to do this.', 'thirstyaffiliates'));
    }

    $features_data = Onboarding_Helper::get_selected_features_data();
    if(!isset($features_data['addons_installed'])){
      $features_data['addons_installed'] = array();
    }

    if(!isset($features_data['addons_upgrade_failed'])){
      $features_data['addons_upgrade_failed'] = array();
    }

    if(!empty($features_data['addons_not_installed'])) {
      if(in_array($data['addon_slug'], $features_data['addons_not_installed'], true)) {

        $license_addons = array();
        if( function_exists('ThirstyAffiliates_Pro') && is_object(ThirstyAffiliates_Pro()->get_model('Update')) ) {
          $update = ThirstyAffiliates_Pro()->get_model('Update');
          if( method_exists($update, 'addons') ) {
            $license_addons = $update->addons(true, true, true);
          }
        }

        // lets try to install and activate add-on.
        foreach ($features_data['addons_not_installed'] as $i => $addon_slug) {
          if($addon_slug == $data['addon_slug']) {
            $response = $this->maybe_install_activate_addons($license_addons, $addon_slug);
            $next_addon = isset($features_data['addons_not_installed'][$i + 1]) ? $features_data['addons_not_installed'][$i + 1] : '';

            if(1 === (int) $response) {
              $features_data['addons_installed'][] = $addon_slug;
              $features_data['addons_installed'] = array_unique($features_data['addons_installed']);

              Onboarding_Helper::set_selected_features($features_data);
              wp_send_json_success(array('addon_slug' => $addon_slug, 'message' => '', 'status' => 1, 'next_addon' => $next_addon));
            }
            else {
              $features_data['addons_upgrade_failed'][] = $addon_slug;
              $features_data['addons_upgrade_failed'] = array_unique($features_data['addons_upgrade_failed']);

              Onboarding_Helper::set_selected_features($features_data);
              wp_send_json_success(array('addon_slug' => $addon_slug, 'message' => esc_html__('Unable to install. Please download and install manually.', 'thirstyaffiliates'), 'status' => 0, 'next_addon' => $next_addon));
            }
          }
        }
      }
    }
  }

}
