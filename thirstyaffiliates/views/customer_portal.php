<?php if ( ! defined( 'ABSPATH' ) ) { die( 'You are not allowed to call this page directly.' ); }
/** @var $this \ThirstyAffiliates\Models\Stripe */
?>
<div id="ta-customer-portal-sub-box" class="ta-sub-box-white">
    <div class="ta-arrow ta-white ta-up ta-sub-box-arrow"> </div>
    <table class="form-table">
        <tbody>
            <tr>
                <th scope="row">
                    <label for="ta-portal-headline">
                        <?php esc_html_e( 'Portal Header', 'thirstyaffiliates' ); ?>
                        <?php
                            $this->_helper_functions->info_tooltip(
                                'ta-options-customer-portal-headline',
                                esc_html__( 'Portal Header', 'thirstyaffiliates' ),
                                esc_html__( 'The messaging shown to customers in the portal.', 'thirstyaffiliates' )
                            );
                        ?>
                    </label>
                </th>
                <td>
                    <input type="text" id="ta-portal-headline" name="ta_portal_headline" class="regular-text" maxlength="60" value="<?php echo esc_attr( $this->_helper_functions->get_portal_config_value( 'business_profile.headline' ) ); ?>">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="ta-portal-privacy-policy-url">
                        <?php esc_html_e( 'Privacy Policy URL', 'thirstyaffiliates' ); ?>
                        <?php
                            $this->_helper_functions->info_tooltip(
                                'ta-options-customer-portal-privacy-policy',
                                esc_html__( 'Privacy Policy URL', 'thirstyaffiliates' ),
                                esc_html__( 'A link to the business\'s publicly available privacy policy.', 'thirstyaffiliates' )
                            );
                        ?>
                    </label>
                </th>
                <td>
                    <input type="text" id="ta-portal-privacy-policy-url" name="ta_portal_privacy_policy_url" class="regular-text" value="<?php echo esc_attr( $this->_helper_functions->get_portal_config_value( 'business_profile.privacy_policy_url' ) ); ?>">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="ta-portal-terms-of-service-url">
                        <?php esc_html_e( 'Terms of Service URL', 'thirstyaffiliates' ); ?>
                        <?php
                            $this->_helper_functions->info_tooltip(
                                'ta-options-customer-portal-terms-of-service',
                                esc_html__( 'Terms of Service URL', 'thirstyaffiliates' ),
                                esc_html__( 'A link to the business\'s publicly available terms of service.', 'thirstyaffiliates' )
                            );
                        ?>
                    </label>
                </th>
                <td>
                    <input type="text" id="ta-portal-terms-of-service-url" name="ta_portal_terms_of_service_url" class="regular-text" value="<?php echo esc_attr( $this->_helper_functions->get_portal_config_value( 'business_profile.terms_of_service_url' ) ); ?>">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="ta-portal-customer-update"><?php esc_html_e( 'Customer Update', 'thirstyaffiliates' ); ?></label>
                </th>
                <td>
                    <label for="ta-portal-customer-update"><input type="checkbox" id="ta-portal-customer-update" name="ta_portal_customer_update_enabled" <?php checked( $this->_helper_functions->get_portal_config_value( 'features.customer_update.enabled' ) ); ?>> <?php esc_html_e( 'Allow customers to update their data', 'thirstyaffiliates' ); ?><span class="ta-recommended"><?php esc_html_e( 'Recommended', 'thirstyaffiliates' ); ?></span></label>
                    <div class="ta-portal-sub-options<?php echo ! $this->_helper_functions->get_portal_config_value( 'features.customer_update.enabled' ) ? ' ta-hidden' : ''; ?>">
                        <?php
                            $allowed_updates = array(
                                'name' => __( 'Allow updating names', 'thirstyaffiliates' ),
                                'email' => __( 'Allow updating email addresses', 'thirstyaffiliates' ),
                                'address' => __( 'Allow updating billing addresses', 'thirstyaffiliates' ),
                                'shipping' => __( 'Allow updating shipping addresses', 'thirstyaffiliates' ),
                                'phone' => __( 'Allow updating phone numbers', 'thirstyaffiliates' ),
                                'tax_id' => __( 'Allow updating tax IDs', 'thirstyaffiliates' ),
                            );
                        ?>
                        <?php foreach ( $allowed_updates as $key => $label ) : ?>
                            <div>
                                <label><input type="checkbox" name="ta_portal_allowed_updates[]" value="<?php echo esc_attr( $key ); ?>" <?php checked( in_array( $key, $this->_helper_functions->get_portal_config_value( 'features.customer_update.allowed_updates' ), true ) ); ?>> <?php echo esc_html( $label ); ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="ta-portal-payment-method-update"><?php esc_html_e( 'Payment Method Update', 'thirstyaffiliates' ); ?></label>
                </th>
                <td>
                    <label for="ta-portal-payment-method-update"><input type="checkbox" id="ta-portal-payment-method-update" name="ta_portal_payment_method_update_enabled" <?php checked( $this->_helper_functions->get_portal_config_value( 'features.payment_method_update.enabled' ) ); ?>> <?php esc_html_e( 'Allow customers to update their payment method', 'thirstyaffiliates' ); ?><span class="ta-recommended"><?php esc_html_e( 'Recommended', 'thirstyaffiliates' ); ?></span></label>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="ta-portal-subscription-cancel"><?php esc_html_e( 'Subscription Cancel', 'thirstyaffiliates' ); ?></label>
                </th>
                <td>
                    <label for="ta-portal-subscription-cancel"><input type="checkbox" id="ta-portal-subscription-cancel" name="ta_portal_subscription_cancel_enabled" <?php checked( $this->_helper_functions->get_portal_config_value( 'features.subscription_cancel.enabled' ) ); ?>> <?php esc_html_e( 'Allow subscriptions to be cancelled', 'thirstyaffiliates' ); ?><span class="ta-recommended"><?php esc_html_e( 'Recommended', 'thirstyaffiliates' ); ?></span></label>
                    <div class="ta-portal-sub-options<?php echo ! $this->_helper_functions->get_portal_config_value( 'features.subscription_cancel.enabled' ) ? ' ta-hidden' : ''; ?>">
                        <div>
                            <select id="ta-portal-subscription-cancel-mode" name="ta_portal_subscription_cancel_mode">
                                <option value="at_period_end" <?php selected( $this->_helper_functions->get_portal_config_value( 'features.subscription_cancel.mode' ), 'at_period_end' ); ?>><?php esc_html_e( 'Cancel at end of billing period (customer can renew until then)', 'thirstyaffiliates' ); ?></option>
                                <option value="immediately" <?php selected( $this->_helper_functions->get_portal_config_value( 'features.subscription_cancel.mode' ), 'immediately' ); ?>><?php esc_html_e( 'Cancel subscriptions immediately', 'thirstyaffiliates' ); ?></option>
                            </select>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="ta-portal-invoice-history"><?php esc_html_e( 'Invoice History', 'thirstyaffiliates' ); ?></label>
                </th>
                <td>
                    <label for="ta-portal-invoice-history"><input type="checkbox" id="ta-portal-invoice-history" name="ta_portal_invoice_history_enabled" <?php checked( $this->_helper_functions->get_portal_config_value( 'features.invoice_history.enabled' ) ); ?>> <?php esc_html_e( 'Show a history of paid invoices', 'thirstyaffiliates' ); ?><span class="ta-recommended"><?php esc_html_e( 'Recommended', 'thirstyaffiliates' ); ?></span></label>
                </td>
            </tr>
        </tbody>
    </table>
</div>
