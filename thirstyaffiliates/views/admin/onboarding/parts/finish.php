<?php 
use ThirstyAffiliates\Helpers\Onboarding_Helper;

if ( ! defined( 'ABSPATH' ) ) {die( 'You are not allowed to call this page directly.' );} ?>

<?php
$is_update_model = false;
if ( function_exists('ThirstyAffiliates_Pro') && is_object(ThirstyAffiliates_Pro()->get_model('Update')) ) {
  $is_update_model = true;
  ThirstyAffiliates_Pro()->get_model('Update')->manually_queue_update();
}

$license                         = get_site_transient('tap_license_info');
$current_license                 = $license && isset($license['product_slug']) ? $license['product_slug'] : '';
$features_list                   = Onboarding_Helper::features_list();
$features_data                   = Onboarding_Helper::get_selected_features_data();
$addons_not_installed            = isset( $features_data['addons_not_installed'] ) ? $features_data['addons_not_installed'] : array();
$addons_installed                = isset( $features_data['addons_installed'] ) ? $features_data['addons_installed'] : array();
$addons_upgrade_failed           = isset( $features_data['addons_upgrade_failed'] ) ? $features_data['addons_upgrade_failed'] : array();
$upgraded_edition                = isset($data['edition']) ? sanitize_text_field($data['edition']) : '';
$license_param                   = isset($data['license']) ? sanitize_text_field($data['license']) : '';
$features_not_enabled            = isset($features_data['features_not_enabled']) ? $features_data['features_not_enabled'] : array();
$upgrading_from_lite             = false;

$upgrade_type = Onboarding_Helper::is_upgrade_required(
  array(
    'addons_installed'     => $addons_installed,
    'addons_not_installed' => $addons_not_installed,
    'features_not_enabled' => $features_not_enabled
  )
);

// If there's no license currently stored, then look at the license passed back from the checkout process.
// This will ensure that we're picking up users upgrading from the Lite version to a Pro version.
if($is_update_model && !$current_license && !empty($upgraded_edition) && !empty($license_param)) {
  ThirstyAffiliates_Pro()->get_model('Update')->activate_license($license_param);
  $license = get_site_transient('ta_license_info');
  $current_license = $license && isset($license['product_slug']) ? $license['product_slug'] : '';
  $upgrading_from_lite = true;
}

if(!empty($upgraded_edition) && !empty($current_license) && $upgraded_edition != $current_license) {
  // The user upgraded to another edition, but it has not processed yet
  ?>
  <h2 class="ta-wizard-step-title"><?php esc_html_e( 'Processing upgrade', 'thirstyaffiliates' ); ?></h2>
  <p class="ta-wizard-step-description">
    <?php esc_html_e( 'Please wait while the upgrade is processed, this may take a minute.', 'thirstyaffiliates' ); ?>
    <?php echo '<img src="'.esc_url(admin_url('images/wpspin_light.gif')).'" class="ta-icon-spinner" />'; ?>
  </p>
  <input type="hidden" id="ta-upgrade-wait-edition" value="1" />
<?php }
elseif ($upgrade_type !== false) {

  $cta_data    = Onboarding_Helper::get_upgrade_cta_data( $upgrade_type );
  $pricing_url = $cta_data['url'];

  $finish_description = $cta_data['heading'];
  $pricing_url = add_query_arg(
    array(
      'onboarding' => 1,
      'return_url' => urlencode( admin_url( 'options.php?page=thirstyaffiliates_onboarding&step=5&onboarding=1' ) ),
    ),
    $pricing_url
  );

  if ( ! empty( $addons_installed ) ) {
    $finish_description = '';
    $pricing_url       = '';
  }

  // if license mismatched and upgrade still required, show them the upgrade CTA interface.
  if ( $pricing_url != '' && $current_license != $upgrade_type ) : ?>
    <h2 class="ta-wizard-step-title"><?php esc_html_e( 'Finish setup', 'thirstyaffiliates' ); ?></h2>
    <p class="ta-wizard-step-description"><?php echo esc_html( $finish_description ); ?></p>

    <div class="ta-wizard-features">
      <?php

      $features_not_activated = array_merge($addons_not_installed, $features_not_enabled);

      foreach ( $features_not_activated as $i => $feature_slug ):
        $ta_active_class = 'ta-wizard-feature-activatedx';
        if ( in_array( $feature_slug, $addons_installed, true ) ) {
          $ta_active_class = 'ta-wizard-feature-activated';
        }

        $addons_installation_message       = '';
        $addons_installation_message_class = '';
        if ( in_array( $feature_slug, $addons_upgrade_failed, true ) ) {
          $addons_installation_message       = esc_html__( 'Unable to install. Please download and install manually.', 'thirstyaffiliates' );
          $addons_installation_message_class = 'error';
        }
        ?>
        <div class="ta-wizard-feature no-border no-padding">
          <div class="<?php echo esc_attr( $ta_active_class ); ?>">
            <h3><span class="step-complete"></span> <?php echo esc_html( $features_list[ $feature_slug ] ); ?></h3>
            <p class="<?php echo esc_attr( $addons_installation_message_class ); ?>"><?php echo esc_html( $addons_installation_message ); ?></p>
          </div>
          <div class="ta-wizard-feature-right"></div>
        </div>
      <?php endforeach; ?>

    </div>

    <div class="ta-wizard-button-group">
      <a href="<?php echo $pricing_url; ?>" id="ta-wizard-create-new-content" class="ta-wizard-button-blue"><?php echo esc_html( $cta_data['label'] ); ?></a>
    </div>

  <?php
  // lets run the upgrade.
  else :
    echo '<input type="hidden" id="ta_wizard_finalize_setup" value="1" />';

    if( Onboarding_Helper::is_pro_active() ) {
      $editions = ThirstyAffiliates_Pro()->helpers['Helper_Functions']->is_incorrect_edition_installed();
      if ( $editions || $upgrading_from_lite ) {
        echo '<input type="hidden" id="ta_wizard_install_correct_edition" value="1" />';
      }
    }
    ?>
    <h2 class="ta-wizard-step-title"><?php esc_html_e( 'Finishing setup', 'thirstyaffiliates' ); ?></h2>
    <p class="ta-wizard-step-description"><?php echo esc_html__( "Please don't close the browser.", 'thirstyaffiliates' ); ?> 
    <?php echo '<img src="'.esc_url(admin_url('images/wpspin_light.gif')).'" class="ta-icon-spinner ta-wizard-finish-step-processing" />'; ?>
    </p>
    <div class="ta-wizard-features">
      <?php foreach ( $features_data['addons_not_installed'] as $i => $addon_slug ) : ?>
        <div class="ta-wizard-feature no-border no-padding" id="ta-finish-step-addon-<?php echo esc_attr( $addon_slug ); ?>">
          <div class="ta-wizard-feature-activatedx">
            <h3><span class="step-complete"></span> <?php echo esc_html( $features_list[ $addon_slug ] ); ?></h3>
            <p class="ta-wizard-addon-text"></p>
          </div>
          <div class="ta-wizard-feature-right">
            <?php echo '<img src="'.esc_url(admin_url('images/wpspin_light.gif')).'" id="ta-wizard-finish-step-'. esc_attr( $addon_slug ).'" class="ta-icon-spinner" />'; ?>
          </div>
        </div>
        <?php if ( $i == 0 ) : ?>
          <input type="hidden" id="start_addon_slug_installable" value="<?php echo esc_attr( $addon_slug ); ?>" />
        <?php endif; ?>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
  <?php
} else {
  ?>
  <h2 class="ta-wizard-step-title"><?php esc_html_e( 'Finishing setup', 'thirstyaffiliates' ); ?></h2>
  <p class="ta-wizard-step-description">
    <?php esc_html_e( "Hold tight... We're getting your link(s) ready for top-notch performance!", 'thirstyaffiliates' ); ?>
    <?php echo '<img src="'.esc_url(admin_url('images/wpspin_light.gif')).'" class="ta-icon-spinner" />'; ?>
  </p>
  <input type="hidden" id="ta-finishing-setup-redirect" value="1" />
<?php } ?>
