<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<?php 
use ThirstyAffiliates\Helpers\Onboarding_Helper;
use ThirstyAffiliates\Helpers\Authenticator_Helper;

$li = '';
if(Onboarding_Helper::is_pro_active()){
  $helper_functions =  ThirstyAffiliates_Pro()->helpers[ 'Helper_Functions' ];

  $license_activated = $helper_functions->get_option( \ThirstyAffiliates_Pro\Helpers\Plugin_Constants::OPTION_LICENSE_ACTIVATED );
  $license_key = $helper_functions->get_option( \ThirstyAffiliates_Pro\Helpers\Plugin_Constants::OPTION_LICENSE_KEY );
  $is_legacy_license_key = $helper_functions->is_legacy_license_key($license_key);
  $site_domain = $helper_functions->get_site_domain();

  if ( ! empty( $license_key ) && ! $is_legacy_license_key ) {
      $li = get_site_transient( 'tap_license_info' );
  }

  if ( empty( $li ) && is_object(ThirstyAffiliates_Pro()->get_model('Update')) ) {
    ThirstyAffiliates_Pro()->get_model('Update')->manually_queue_update();
    $li = get_site_transient( 'tap_license_info' );
  }
}

if(!empty($li)): 
?>
<div id="ta-wizard-license-wrapper" class="ta-hidden">
  <h2 class="ta-wizard-step-title"><?php esc_html_e('Your License', 'thirstyaffiliates'); ?></h2>
  <?php require_once $this->_constants->VIEWS_ROOT_PATH() . 'admin/onboarding/active_license.php'; ?>
</div>
<?php else: ?>
  <h2 class="ta-wizard-step-title"><?php esc_html_e('Activate License', 'thirstyaffiliates'); ?></h2>
  <p class="ta-wizard-step-description"><?php esc_html_e("Let's kick things off by activating your license.", 'thirstyaffiliates'); ?></p>

  <div class="ta-wizard-button-group">
    <a href="<?php echo esc_url(Authenticator_Helper::get_auth_connect_url(admin_url('options.php?page=thirstyaffiliates_onboarding&step=1'), array('onboarding' => 'true'))); ?>" class="ta-wizard-button-blue"><?php esc_html_e('Activate', 'thirstyaffiliates'); ?></a>

    <?php if( ! Onboarding_Helper::is_pro_active() ) : ?>
    <button type="button" class="ta-wizard-button-link ta-wizard-go-to-step" data-step="2" data-context="skip"><span><?php esc_html_e('Skip', 'thirstyaffiliates'); ?></span></button>
    <?php endif; ?>
  </div>

 

  <?php if(isset($_GET['license_error'])) : ?>
    <div class="notice notice-error inline">
      <p><?php echo esc_html(sanitize_text_field(wp_unslash($_GET['license_error']))); ?></p>
    </div>
  <?php endif; ?>
<?php endif; ?>
