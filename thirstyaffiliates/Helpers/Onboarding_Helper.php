<?php
namespace ThirstyAffiliates\Helpers;

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Onboarding_Helper {
  public static function maybe_set_steps_completed($step) {
    $steps_completed = self::get_steps_completed();
    if( $step > $steps_completed ){
      self::set_steps_completed($step);
    }
  }

  public static function set_steps_completed($step) {
    update_option( 'ta_onboarding_steps_completed', $step, false );

    if( $step == 0 ){
      self::unmark_content_steps_skipped();
    }
  }

  public static function get_steps_completed() {
    return get_option( 'ta_onboarding_steps_completed', 0 );
  }

  public static function set_selected_features($features) {
    update_option('ta_onboarding_features', $features, false);
  }

  public static function get_selected_features_data() {
    $metadata = get_option('ta_onboarding_features', true);
    $data = is_array($metadata) ? $metadata : array();
    return $data;
  }

  public static function get_selected_features() {
    $data = self::get_selected_features_data();
    $features = (isset($data['features']) && is_array($data['features'])) ? $data['features'] : array();
    return $features;
  }

  public static function is_addon_selectable($plugin_slug) {
    $plugins = get_plugins();
    $plugin_file_slug = $plugin_slug . '.php';
    $is_installed = ! empty($plugins[$plugin_file_slug]);
    $selectable = true;
    if($is_installed){
      if(is_plugin_active($plugin_file_slug)){ // if addon is already installed and active, it must not be selectable.
          $selectable = false;
      }
    }

    return $selectable;
  }

  public static function features_addons_selectable_list() {
    return array(
      'thirstyaffiliates-product-displays' => self::is_addon_selectable('thirstyaffiliates-product-displays/thirstyaffiliates-product-displays'),
      'monsterinsights' => self::is_addon_selectable('google-analytics-for-wordpress/googleanalytics'),
    );
  }

  public static function set_link_id($id) {
    update_option( 'ta_onboarding_link_id', $id, false );

    if(count(self::get_skipped_steps())) {
      self::unmark_content_steps_skipped();
      self::set_steps_completed(3);
    }
  }

  public static function get_link_id() {
    return get_option( 'ta_onboarding_link_id', 0 );
  }

  public static function set_has_imported_links($value) {
    update_option('ta_onboarding_has_imported_links', 1, false);

    if(count(self::get_skipped_steps())) {
      self::unmark_content_steps_skipped();
      self::set_steps_completed(3);
    }
  }

  public static function get_has_imported_links() {
    return get_option('ta_onboarding_has_imported_links', 0);
  }

  public static function mark_content_steps_skipped() {
    update_option( 'ta_onboarding_content_steps_skipped', 1, false );
  }

  public static function unmark_content_steps_skipped() {
    update_option( 'ta_onboarding_content_steps_skipped', 0, false );
  }

  public static function set_category_id($id) {
    update_option('ta_onboarding_category_id', $id, false);

    if(count(self::get_skipped_steps()) && $id > 0) {
      self::unmark_content_steps_skipped();
      $content_id = self::get_link_id();
      if($content_id > 0) {
        self::set_steps_completed(3);
      }
    }
  }

  public static function get_category_id() {
    return get_option( 'ta_onboarding_category_id', 0 );
  }

  public static function get_skipped_steps() {
    $is_skipped = get_option( 'ta_onboarding_content_steps_skipped', 0 );

    if($is_skipped) {
      return array(3, 4);
    }
    return array();
  }

  public static function get_license_type() {
    $li = get_site_transient('tap_license_info');

    if($li) {
      return $li['product_slug'];
    }

    return false;
  }

  public static function get_completed_step_urls_html() {
    ob_start();
    ?>
    <?php if((int) get_option('ta_onboarding_content_steps_skipped') !== 1): ?>

      <?php 
      $category_id = self::get_category_id();
      $link_id = self::get_link_id();
      $has_imported_links = self::get_has_imported_links();
      ?>
      <?php if($link_id > 0 || $has_imported_links || $category_id > 0): ?>
      <h2 class="ta-wizard-step-title"><?php esc_html_e('Check out what you set up...', 'thirstyaffiliates'); ?></h2>
      <div class="ta-wizard-selected-content ta-wizard-selected-content-full-scape">
        <div id="ta-wizard-completed-step-urls">
          <?php
            $category_id = self::get_category_id();
            $link_id = self::get_link_id();
            $has_imported_links = self::get_has_imported_links();
        
            if($link_id > 0 && !$has_imported_links):
              $link_post = get_post($link_id);
              $link_url = get_permalink( $link_post );
          ?>
            <div class="ta-wizard-selected-content-column">
              <div class="ta-wizard-selected-content-heading"><?php esc_html_e('Link', 'thirstyaffiliates'); ?></div>
              <div class="ta-wizard-selected-content-name"><a href="<?php echo esc_url($link_url); ?>"><?php echo esc_html($link_post->post_title); ?></a></div>
            </div>
          <?php endif; ?>

          <?php if($has_imported_links): ?>
            <div class="ta-wizard-selected-content-column">
              <div class="ta-wizard-selected-content-heading"><?php esc_html_e('ThirstyAffiliates', 'thirstyaffiliates'); ?></div>
              <div class="ta-wizard-selected-content-name">
                <?php printf(
                  __('View your imported links on the <a href="%1$s" target="_blank">ThirstyAffiliates page</a>', 'thirstyaffiliates'),
                  esc_url(admin_url('edit.php?post_type=thirstylink'))
                ); ?>
              </div>
            </div>
          <?php endif; ?>

          <?php if($category_id > 0):
            $category = get_term($category_id);
          ?>
            <hr>
            <div class="ta-wizard-selected-content-column">
              <div class="ta-wizard-selected-content-heading"><?php esc_html_e('Link Category', 'thirstyaffiliates'); ?></div>
            <div class="ta-wizard-selected-content-name"><?php echo esc_html($category->name); ?></div>
          </div>
          <?php endif; ?>
        </div>
      </div>
      <?php endif; ?>
    <?php endif; ?>
    <?php
    return ob_get_clean();
  }

  public static function is_upgrade_required($atts) {
    $addons_installed = isset($atts['addons_installed']) ? $atts['addons_installed'] : array();
    $addons_not_installed = isset($atts['addons_not_installed']) ? $atts['addons_not_installed'] : array();
    $features_not_enabled = isset($atts['features_not_enabled']) ? $atts['features_not_enabled'] : array();

    if(!is_array($addons_installed)) {
      $addons_installed = array();
    }

    if(!is_array($addons_not_installed)) {
      $addons_not_installed = array();
    }

    // If there are no addons or features bail out.
    if(empty($addons_not_installed) && empty($features_not_enabled)) {
      return false;
    }

    foreach($addons_not_installed as $k => $addon_slug) {
      if(in_array($addon_slug,$addons_installed, true)) {
        unset($addons_not_installed[$k]); // already installed.
      }
    }

    // If there are no more add-ons requiring installation and there's no features enabled, bail out.
    if(empty($addons_not_installed) && empty($features_not_enabled)) {
      return false;
    }

    // If there are missing features, then we know the user isn't on a Pro plan.
    if(!empty($features_not_enabled)) {
      if(in_array('thirstyaffiliates-product-displays', $addons_not_installed)) {
        return 'thirstyaffiliates-plus';
      } else {
        return 'thirstyaffiliates-basic';
      }
    }

    return 'thirstyaffiliates-advanced';
  }

  public static function get_upgrade_cta_data($type) {
    $data = array(
      'thirstyaffiliates-basic' => array(
        'token' => esc_html__('Basic', 'thirstyaffiliates'),
        'url' => 'https://thirstyaffiliates.com/register/basic',
        'label' => esc_html__('Upgrade', 'thirstyaffiliates'),
        'heading' => esc_html__('Seems like you\'re eyeing one of our premium features! To unveil this remarkable offering, upgrade to one of our pro plans and let the possibilities unfold.', 'thirstyaffiliates')
      ),
      'thirstyaffiliates-advanced' => array(
        'token' => esc_html__('Advanced', 'thirstyaffiliates'),
        'url' => 'https://thirstyaffiliates.com/register/advanced',
        'label' => esc_html__('Upgrade', 'thirstyaffiliates'),
        'heading' => esc_html__('Seems like you\'re eyeing one of our premium features! To unveil this remarkable offering, upgrade to one of our pro plans and let the possibilities unfold.', 'thirstyaffiliates')
      ),
      'thirstyaffiliates-plus' => array(
        'token' => esc_html__('Plus', 'thirstyaffiliates'),
        'url' => 'https://thirstyaffiliates.com/register/plus',
        'label' => esc_html__('Upgrade','thirstyaffiliates'),
        'heading' => esc_html__("Seems like you're eyeing one of our premium features! To unveil this remarkable offering, upgrade to one of our pro plans and let the possibilities unfold.", 'thirstyaffiliates')
      )
    );

    $data = apply_filters('ta_onboarding_cta_data', $data);

    $cta_data = array();
    if(isset($data[$type])) {
      $cta_data = $data[$type];
    }

    return $cta_data;
  }

  public static function is_pro_active(){

    // Makes sure the plugin is defined before trying to use it
    if ( !function_exists( 'is_plugin_active' ) )
        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
          
    return ( is_plugin_active( 'thirstyaffiliates-pro/thirstyaffiliates-pro.php' ) || defined('TAP_EDITION') ) && function_exists('ThirstyAffiliates_Pro');
  }

  public static function features_list() {
    return array(
      'thirstyaffiliates-link-tracking' => esc_html__('Link Tracking', 'thirstyaffiliates'),
      'thirstyaffiliates-uncloak-links' => esc_html__('Uncloak Links', 'thirstyaffiliates'),
      'thirstyaffiliates-keyword-replacements' => esc_html__('AutoLinker', 'thirstyaffiliates'),
      'thirstyaffiliates-event-notifications' => esc_html__('Event Notifications', 'thirstyaffiliates'),
      'thirstyaffiliates-amazon-import' => esc_html__('Amazon Import', 'thirstyaffiliates'),
      'thirstyaffiliates-link-health' => esc_html__('Link Health', 'thirstyaffiliates'),
      'thirstyaffiliates-import-export' => esc_html__('Import/Export Links', 'thirstyaffiliates'),
      'thirstyaffiliates-geographic-redirects' => esc_html__('Geographic Redirects', 'thirstyaffiliates'),
      'thirstyaffiliates-product-displays' => esc_html__('ThirstyAffiliates Product Displays', 'thirstyaffiliates'),
      'monsterinsights' => esc_html__('MonsterInsights', 'thirstyaffiliates')
    );
  }

  public static function get_features() {
    return array(
      'features' => array(
        'thirstyaffiliates-link-tracking' => 'ta_enable_stats_reporting_module',
        'thirstyaffiliates-uncloak-links' => 'ta_uncloak_link_per_link',
        'thirstyaffiliates-keyword-replacements' => 'tap_enable_autolinker',
        'thirstyaffiliates-event-notifications' => 'tap_enable_event_notification',
        'thirstyaffiliates-amazon-import' => 'tap_enable_azon',
        'thirstyaffiliates-link-health' => 'tap_enable_link_health_checker',
        'thirstyaffiliates-import-export' => 'tap_enable_csv_importer',
        'thirstyaffiliates-geographic-redirects' => 'tap_enable_geolocation'
      ),
      'addons'   => array(
        'thirstyaffiliates-product-displays' => 'ThirstyAffiliates Product Displays',
        'monsterinsights' => 'Monster Insights'
      )
    );
  }

  public static function get_pro_features() {
    return array(
        'thirstyaffiliates-keyword-replacements',
        'thirstyaffiliates-event-notifications',
        'thirstyaffiliates-amazon-import',
        'thirstyaffiliates-link-health',
        'thirstyaffiliates-import-export',
        'thirstyaffiliates-geographic-redirects'
    );
  }

  public static function download_and_activate_plugin($plugin_url) {

      // Prepare variables
      $url = esc_url_raw(
        add_query_arg(
          array(
            'post_type' => 'thirstylink',
            'page' => 'thirstyaffiliates_onboarding',
            'onboarding' => '1',
          ),
          admin_url('edit.php')
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

      if($plugin == 'https://downloads.wordpress.org/plugin/google-analytics-for-wordpress.latest-stable.zip') {
        update_option('thirstyaffiliates_installed_monsterinsights', true);
      }

      // Flush the cache and return the newly installed plugin basename
      wp_cache_flush();

      if($installer->plugin_info()) {
        $plugin_basename = $installer->plugin_info();

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

  public static function get_upgrade_pricing_url() {

    $features_enabled = self::get_selected_features();
    $edition_slug = in_array('thirstyaffiliates-product-displays', $features_enabled) ? 'thirstyaffiliates-plus' : 'thirstyaffiliates-basic';
    $cta_data = self::get_upgrade_cta_data($edition_slug);
    $pricing_url = $cta_data['url'];

    $pricing_url = add_query_arg(
      array(
        'onboarding' => 1,
        'return_url' => urlencode(admin_url('options.php?page=thirstyaffiliates_onboarding&step=3')),
      ),
      $pricing_url
    );

    return $pricing_url;
  }

  public static function get_links_count(){
    return wp_count_posts(Plugin_Constants::AFFILIATE_LINKS_CPT)->publish;
  }
}
