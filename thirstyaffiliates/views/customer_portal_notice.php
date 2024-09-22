<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }
/** @var $this \ThirstyAffiliates\Models\Stripe */
?>
<div id="ta-customer-portal-notice-popup" class="ta-popup mfp-hide" data-nonce="<?php echo esc_attr( wp_create_nonce( 'ta_dismiss_customer_portal_notice' ) ); ?>">
    <h2><?php esc_html_e( 'Customer Portal', 'thirstyaffiliates' ); ?></h2>
    <p>
        <?php
            printf(
                /* translators: %1$s: open strong tag, %2$s: close strong tag */
                esc_html__( 'Now that you have a recurring ThirstyPay™ Link, did you know that you can let your customers manage their own subscriptions using the %1$sCustomer Portal%2$s?', 'thirstyaffiliates' ),
                '<strong>',
                '</strong>'
            );
        ?>
    </p>
    <img src="<?php echo esc_url( $this->_constants->IMAGES_ROOT_URL() . 'stripe-customer-portal.jpg' ); ?>" alt="">
    <p class="ta-customer-portal-notice-buttons">
        <a href="#" id="ta-customer-portal-notice-close"><?php esc_html_e( 'No thanks', 'thirstyaffiliates' ); ?></a>
        <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=thirstylink&page=thirsty-settings&tab=ta_thirstypay_settings&configure_customer_portal=1' ) ); ?>" class="button button-primary"><?php esc_html_e( 'Configure Customer Portal', 'thirstyaffiliates' ); ?></a>
    </p>
</div>
