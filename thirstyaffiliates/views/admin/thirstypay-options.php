<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); } ?>
<?php
use ThirstyAffiliates\Helpers\Stripe_Connect_Helper;
use ThirstyAffiliates\Helpers\Authenticator_Helper;

$classes = '';
$show_keys = false;
if ( ! isset( $_GET['display-keys'] ) && ! isset( $_COOKIE['ta_stripe_display_keys'] ) && ! defined( 'TA_DISABLE_STRIPE_CONNECT' ) ) {
  $classes = 'class="ta-hidden"';
} else {
  $show_keys = true;
}

$account_email = get_option( Authenticator_Helper::OPTION_KEY_AUTH_ACCOUNT_EMAIL );
$secret = get_option( Authenticator_Helper::OPTION_KEY_AUTH_ACCOUNT_SECRET );
$site_uuid = get_option( Authenticator_Helper::OPTION_KEY_AUTH_ACCOUNT_SITE_UUID );
$service_account_name = get_option( 'ta_stripe_service_account_name' );
$id = Stripe_Connect_Helper::get_method_id();

// If we're authenticated then let's present a stripe url otherwise an authenticator url
if( $account_email && $secret && $site_uuid ) {
  $stripe_connect_url = Stripe_Connect_Helper::get_stripe_connect_url();
} else {
  $return_url = admin_url('edit.php?post_type=thirstylink&page=thirsty-settings&tab=ta_thirstypay_settings');
  $stripe_connect_url = Authenticator_Helper::get_auth_connect_url( $return_url, array(
    'stripe_connect' => 'true',
    'method_id' => $id
  ));
}

$connect_status = Stripe_Connect_Helper::stripe_connect_status();
?>
<div class="ta-settings-form">
  <table class="form-table ta-payment-stripe-connect">
    <tbody>
      <tr valign="top">
        <td colspan="2">
          <div class="ta-payment-option-prompt">
            <div><img src="<?php echo $this->_constants->IMAGES_ROOT_URL(); ?>payments/Stripe_with_Tagline.svg" alt="Stripe logo"></div>
            <table class="stripe-feature-list" width="500px">
                <tr>
                  <td>
                    <ul class="stripe-features">
                      <li><img src="<?php echo $this->_constants->IMAGES_ROOT_URL(); ?>payments/Check_Mark.svg"/><?php _e( "Accept all Major Credit Cards", 'thirstyaffiliates' ); ?></li>
                      <li><img src="<?php echo $this->_constants->IMAGES_ROOT_URL(); ?>payments/Check_Mark.svg"/><?php _e( "Flexible subscriptions and billing terms", 'thirstyaffiliates' ); ?></li>
                      <li><img src="<?php echo $this->_constants->IMAGES_ROOT_URL(); ?>payments/Check_Mark.svg"/><?php _e( "25+ ways to pay", 'thirstyaffiliates' ); ?></li>
                    </ul>
                  </td>
                  <td>
                    <ul class="stripe-features">
                      <li><img src="<?php echo $this->_constants->IMAGES_ROOT_URL(); ?>payments/Check_Mark.svg"/><?php _e( "Accept Apple Pay", 'thirstyaffiliates' ); ?></li>
                      <li><img src="<?php echo $this->_constants->IMAGES_ROOT_URL(); ?>payments/Check_Mark.svg"/><?php _e( "Accept Google Wallet", 'thirstyaffiliates' ); ?></li>
                      <li><img src="<?php echo $this->_constants->IMAGES_ROOT_URL(); ?>payments/Check_Mark.svg"/><?php _e( "Fraud prevention tools", 'thirstyaffiliates' ); ?></li>
                    </ul>
                  </td>
                </tr>
            </table>

            <?php if ( 'connected' === $connect_status ) : ?>
              <?php
                $refresh_url = add_query_arg( array( 'action' => 'ta_stripe_connect_refresh', 'method-id' => $id, '_wpnonce' => wp_create_nonce('stripe-refresh') ), admin_url('admin-ajax.php') );
                $disconnect_url = add_query_arg( array( 'action' => 'ta_stripe_connect_disconnect', 'method-id' => $id, '_wpnonce' => wp_create_nonce('stripe-disconnect') ), admin_url('admin-ajax.php') );
                $disconnect_confirm_msg = __( 'Disconnecting from this Stripe Account will prevent ThirstyPay™ links from working.', 'thirstyaffiliates' );
              ?>

              <div id="stripe-connected-actions" class="ta-payment-option-prompt connected">
                <span>
                  <?php if ( empty( $service_account_name ) ): ?>
                    <?php _e( 'Connected to Stripe', 'thirstyaffiliates' ); ?>
                  <?php else: ?>
                    <?php printf( __( 'Connected to: %1$s %2$s %3$s', 'thirstyaffiliates' ), '<strong>', $service_account_name, '</strong>' ); ?>
                  <?php endif; ?>
                </span>
                <span <?php echo $classes; ?>>
                  <a href="<?php echo $refresh_url; ?>"
                     class="stripe-btn ta_stripe_refresh_button button-secondary"><?php _e( 'Refresh Stripe Credentials', 'thirstyaffiliates' ); ?></a>
                 </span>
                 <a href="<?php echo $disconnect_url; ?>" class="stripe-btn ta_stripe_disconnect_button button-secondary"
                    data-disconnect-msg="<?php echo $disconnect_confirm_msg; ?>">
                   <?php _e( 'Disconnect', 'thirstyaffiliates' ); ?>
                 </a>
              </div>
            <?php endif; ?>

            <?php if ( 'disconnected' === $connect_status ) : ?>
              <div>
                <h4><strong><?php _e( 'Re-Connect to Stripe', 'thirstyaffiliates' ); ?></strong></h4>
                <p><?php _e( 'Stripe has been disconnected so it may stop working ThirstyPay™ links at any time. <br />To prevent this, re-connect your Stripe account by clicking the "Connect with Stripe" button below.', 'thirstyaffiliates' ); ?></p>
                <p>
                  <a href="<?php echo $stripe_connect_url; ?>"
                      data-id="<?php echo $id; ?>"
                      class="ta-stripe-connect-new">
                        <img src="<?php echo $this->_constants->IMAGES_ROOT_URL() . 'payments/stripe-connect.png'; ?>" alt="<?php esc_attr_e( '"Connect with Stripe" button', 'thirstyaffiliates' ); ?>" width="200">
                    </a>
                </p>
              </div>
            <?php elseif ( 'connected' !== $connect_status ) : ?>
                <a data-id="<?php echo $id; ?>" href="<?php echo $stripe_connect_url; ?>" class="ta-stripe-connect-new">
                  <img src="<?php echo $this->_constants->IMAGES_ROOT_URL() . 'payments/stripe-connect.png'; ?>" alt="<?php esc_attr_e( '"Connect with Stripe" button', 'thirstyaffiliates' ); ?>" width="200">
              </a>
            <?php endif; ?>
          </div>

        </td>
      </tr>

    </tbody>
  </table>
</div>
<script>
  jQuery(document).ready(function($) {
    jQuery('body').on('click', '.ta_stripe_disconnect_button', function(e) {
      var proceed = confirm( jQuery(this).data('disconnect-msg') );
      if ( false === proceed ) {
        e.preventDefault();
      }
    });
  });
</script>
