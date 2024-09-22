<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/** @var $this \ThirstyAffiliates\Models\Affiliate_Links_CPT */
/** @var $thirstylink \ThirstyAffiliates\Models\Affiliate_Link */
wp_nonce_field( 'thirsty_affiliates_cpt_nonce', '_thirstyaffiliates_nonce' ); ?>
<input type="hidden" name="ta_thirstypay_link" value="1">

<table class="form-table">
    <tr>
        <th scope="row">
            <?php esc_html_e('Product', 'thirstyaffiliates'); ?>*
        </th>
        <td>
            <div id="ta-stripe-line-items">
                <?php
                    $hide_product_selector = false;
                    $stripe_line_items = $thirstylink->get_prop( 'stripe_line_items' );

                    if(!empty($stripe_line_items)) {
                        $line_items = json_decode($stripe_line_items, true);

                        if(is_array($line_items)) {
                            foreach($line_items as $line_item) {
                                if(is_array($line_item)) {
                                    echo $this->_helper_functions->render_line_item($line_item);
                                    $hide_product_selector = true;
                                }
                            }
                        }
                    }
                ?>
            </div>
            <div id="ta-stripe-product-selector" class="ta-width-500<?php echo $hide_product_selector ? ' ta-hidden' : ''; ?>">
                <select id="ta-stripe-product-select"></select>
            </div>
            <textarea class="ta-hidden" name="ta_stripe_line_items"><?php echo esc_textarea($stripe_line_items); ?></textarea>
            <div id="ta-stripe-add-product-popup" class="ta-popup mfp-hide">
                <div class="ta-stripe-add-product-title"><?php esc_html_e('Add a new product', 'thirstyaffiliates'); ?></div>
                <div class="ta-stripe-add-product-options">
                    <div class="ta-stripe-add-product-option">
                        <label for="ta_stripe_add_product_name"><?php esc_html_e('Name', 'thirstyaffiliates'); ?></label>
                        <div class="ta-stripe-add-product-field">
                            <input type="text" id="ta_stripe_add_product_name" placeholder="<?php esc_attr_e('Premium Plan, sunglasses, etc.', 'thirstyaffiliates'); ?>">
                        </div>
                    </div>
                    <div class="ta-stripe-add-product-option">
                        <label for="ta_stripe_add_product_price"><?php esc_html_e('Price', 'thirstyaffiliates'); ?></label>
                        <div class="ta-stripe-add-product-field">
                            <div class="ta-stripe-add-product-price">
                                <input type="text" id="ta_stripe_add_product_price" placeholder="0.00">
                                <select id="ta_stripe_add_product_currency" aria-label="<?php esc_html_e('Currency', 'thirstyaffiliates'); ?>">
                                    <?php foreach($this->_helper_functions->currencies() as $code => $name) : ?>
                                        <option value="<?php echo esc_attr($code); ?>" <?php selected($default_currency, $code); ?>><?php echo esc_attr("$code - $name"); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="ta-stripe-add-product-option ta-stripe-recurring-tiles">
                        <div>
                            <input type="radio" id="ta_stripe_add_product_type_recurring" name="ta_stripe_recurring" value="recurring">
                            <label for="ta_stripe_add_product_type_recurring"><?php esc_html_e('Recurring', 'thirstyaffiliates'); ?></label>
                        </div>
                        <div>
                            <input type="radio" id="ta_stripe_add_product_type_one_time" name="ta_stripe_recurring" value="one_time" checked>
                            <label for="ta_stripe_add_product_type_one_time"><?php esc_html_e('One time', 'thirstyaffiliates'); ?></label>
                        </div>
                    </div>
                    <div id="ta-stripe-add-product-recurring-options" class="ta-stripe-add-product-option ta-hidden">
                        <label for="ta_stripe_add_product_billing_period"><?php esc_html_e('Billing period', 'thirstyaffiliates'); ?></label>
                        <div class="ta-stripe-add-product-field">
                            <select id="ta_stripe_add_product_billing_period">
                                <option value="day"><?php esc_html_e('Daily', 'thirstyaffiliates'); ?></option>
                                <option value="week"><?php esc_html_e('Weekly', 'thirstyaffiliates'); ?></option>
                                <option value="month" selected><?php esc_html_e('Monthly', 'thirstyaffiliates'); ?></option>
                                <option value="quarter"><?php esc_html_e('Every 3 months', 'thirstyaffiliates'); ?></option>
                                <option value="semiannual"><?php esc_html_e('Every 6 months', 'thirstyaffiliates'); ?></option>
                                <option value="year"><?php esc_html_e('Yearly', 'thirstyaffiliates'); ?></option>
                                <option value="custom"><?php esc_html_e('Custom', 'thirstyaffiliates'); ?></option>
                            </select>
                        </div>
                        <div id="ta-stripe-add-product-recurring-custom" class="ta-stripe-add-product-field ta-hidden">
                            <div class="ta-stripe-add-product-recurring-custom">
                                <span><?php esc_html_e('every', 'thirstyaffiliates'); ?></span>
                                <input type="text" id="ta_stripe_add_product_interval_count" class="small-text" value="2" aria-label="<?php esc_html_e('Interval count', 'thirstyaffiliates'); ?>">
                                <select id="ta_stripe_add_product_interval" aria-label="<?php esc_html_e('Interval', 'thirstyaffiliates'); ?>">
                                    <option value="day"><?php esc_html_e('days', 'thirstyaffiliates'); ?></option>
                                    <option value="week"><?php esc_html_e('weeks', 'thirstyaffiliates'); ?></option>
                                    <option value="month" selected><?php esc_html_e('months', 'thirstyaffiliates'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="ta-stripe-add-product-option">
                        <label for="ta_stripe_add_product_tax_behavior"><?php esc_html_e('Include tax in price', 'thirstyaffiliates'); ?></label>
                        <div class="ta-stripe-add-product-field">
                            <select id="ta_stripe_add_product_tax_behavior">
                                <option value="unspecified"><?php esc_html_e('Default', 'thirstyaffiliates'); ?></option>
                                <option value="inclusive"><?php esc_html_e('Price is inclusive of tax', 'thirstyaffiliates'); ?></option>
                                <option value="exclusive"><?php esc_html_e('Price is exclusive of tax', 'thirstyaffiliates'); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="ta-stripe-add-product-buttons">
                        <button type="button" id="ta-stripe-add-product-cancel" class="button button-secondary"><?php esc_html_e('Cancel', 'thirstyaffiliates'); ?></button>
                        <button type="button" id="ta-stripe-add-product-save" class="button button-primary"><?php esc_html_e('Add product', 'thirstyaffiliates'); ?></button>
                    </div>
                </div>
            </div>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <?php esc_html_e('Options', 'thirstyaffiliates'); ?>
        </th>
        <td>
            <div id="ta-stripe-thirstypay-link-options">
                <div class="ta-stripe-thirstypay-link-option">
                    <label>
                        <input type="checkbox" name="ta_stripe_automatic_tax" <?php checked($thirstylink->get_prop( 'stripe_automatic_tax' ) == '1'); ?>>
                        <?php esc_html_e('Collect tax automatically', 'thirstyaffiliates'); ?>
                    </label>
                    <?php
                        $this->_helper_functions->info_tooltip(
                            'ta-stripe-automatic-tax',
                            esc_html__('Collect tax automatically', 'thirstyaffiliates'),
                            sprintf(
                                /* translators: %s: link to Stripe Tax setup docs */
                                esc_html__('Tax will be calculated automatically based on the address provided by the customer. Stripe Tax must be enabled on the connected Stripe account. Additional charges may apply. Please visit %s to get started.', 'thirstyaffiliates'),
                                sprintf('<a href="%1$s" target="_blank">%1$s</a>', 'https://stripe.com/docs/tax/set-up')
                            )
                        );
                    ?>
                </div>
                <div class="ta-stripe-thirstypay-link-option">
                    <label>
                        <input type="checkbox" id="ta_stripe_billing_address_collection" name="ta_stripe_billing_address_collection" <?php checked( $thirstylink->get_prop( 'stripe_billing_address_collection' ) == '1' ); ?>>
                        <?php esc_html_e('Collect customers\' addresses', 'thirstyaffiliates'); ?>
                    </label>
                    <div class="ta-stripe-thirstypay-link-sub-options<?php echo $thirstylink->get_prop( 'stripe_billing_address_collection' ) != '1' ? ' ta-hidden' : ''; ?>">
                        <div class="ta-stripe-thirstypay-link-sub-option">
                            <label>
                                <input type="radio" name="ta_stripe_shipping_address_collection" value="0" <?php checked($thirstylink->get_prop( 'stripe_shipping_address_collection' ) != '1'); ?>>
                                <?php esc_html_e('Billing addresses only', 'thirstyaffiliates'); ?>
                            </label>
                        </div>
                        <div class="ta-stripe-thirstypay-link-sub-option">
                            <label>
                                <input type="radio" name="ta_stripe_shipping_address_collection" value="1" <?php checked($thirstylink->get_prop( 'stripe_shipping_address_collection' ) == '1'); ?>>
                                <?php esc_html_e('Billing and shipping addresses', 'thirstyaffiliates'); ?>
                            </label>
                            <div class="ta-stripe-thirstypay-link-sub-option ta-width-500<?php echo $thirstylink->get_prop( 'stripe_shipping_address_collection' ) != '1' ? ' ta-hidden' : ''; ?>">
                                <select multiple id="ta_stripe_shipping_address_allowed_countries" name="ta_stripe_shipping_address_allowed_countries[]">
                                    <?php $shipping_address_allowed_countries = explode( ', ', (string) $thirstylink->get_prop( 'stripe_shipping_address_allowed_countries' ) ); ?>
                                    <?php foreach($this->_helper_functions->shipping_countries() as $code => $name) : ?>
                                        <option value="<?php echo esc_attr($code); ?>"<?php selected(in_array($code, $shipping_address_allowed_countries, true)); ?>><?php echo esc_html($name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ta-stripe-thirstypay-link-option">
                    <label>
                        <input type="checkbox" name="ta_stripe_phone_number_collection" <?php checked($thirstylink->get_prop( 'stripe_phone_number_collection' ) == '1'); ?>>
                        <?php esc_html_e('Require customers to provide a phone number', 'thirstyaffiliates'); ?>
                    </label>
                </div>
                <div class="ta-stripe-thirstypay-link-option">
                    <label>
                        <input type="checkbox" name="ta_stripe_allow_promotion_codes" <?php checked($thirstylink->get_prop( 'stripe_allow_promotion_codes' ) == '1'); ?>>
                        <?php esc_html_e('Allow promotion codes', 'thirstyaffiliates'); ?>
                    </label>
                </div>
                <div class="ta-stripe-thirstypay-link-option">
                    <label>
                        <input type="checkbox" name="ta_stripe_tax_id_collection" <?php checked($thirstylink->get_prop( 'stripe_tax_id_collection' ) == '1'); ?>>
                        <?php esc_html_e('Allow business customers to provide tax IDs', 'thirstyaffiliates'); ?>
                    </label>
                    <?php
                        $this->_helper_functions->info_tooltip(
                            'ta-stripe-tax-id-collection',
                            esc_html__('Allow business customers to provide tax IDs', 'thirstyaffiliates'),
                            sprintf(
                                /* translators: %s: link to Stripe Tax ID docs */
                                esc_html__('This displays an additional field for businesses to provide their VAT or other tax ID. Stripe only displays this field to customers in certain countries. %s', 'thirstyaffiliates'),
                                sprintf('<a href="%1$s" target="_blank">%2$s</a>', 'https:stripe.com/docs/billing/customer/tax-ids', esc_html__('View docs', 'thirstyaffiliates'))
                            )
                        );
                    ?>
                </div>
                <div class="ta-stripe-thirstypay-link-option">
                    <label>
                        <input type="checkbox" name="ta_stripe_save_payment_details" <?php checked($thirstylink->get_prop( 'stripe_save_payment_details' ) == '1'); ?>>
                        <?php esc_html_e('Save payment details for future use', 'thirstyaffiliates'); ?>
                    </label>
                </div>
                <div class="ta-stripe-thirstypay-link-option ta-stripe-recurring-only">
                    <label>
                        <input type="checkbox" id="ta_stripe_include_free_trial" name="ta_stripe_include_free_trial" <?php checked($thirstylink->get_prop( 'stripe_include_free_trial' ) == '1'); ?>>
                        <?php esc_html_e('Include a free trial', 'thirstyaffiliates'); ?>
                    </label>
                    <div class="ta-stripe-thirstypay-link-sub-options<?php echo $thirstylink->get_prop( 'stripe_include_free_trial' ) != '1' ? ' ta-hidden' : ''; ?>">
                        <div class="ta-stripe-thirstypay-link-sub-option">
                            <label>
                                <input type="text" name="ta_stripe_trial_period_days" class="small-text" value="<?php echo esc_attr($thirstylink->get_prop( 'stripe_trial_period_days' )); ?>">
                                <?php esc_html_e('days', 'thirstyaffiliates'); ?>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label for="ta_stripe_custom_text"><?php esc_html_e('Checkout Message', 'thirstyaffiliates'); ?></label>
            <?php
                $this->_helper_functions->info_tooltip(
                    'ta-stripe-custom-text',
                    esc_html__( 'Checkout Message', 'thirstyaffiliates' ),
                    esc_html__( 'Custom text that should be displayed alongside the payment confirmation button (up to 1000 characters).', 'thirstyaffiliates' )
                );
            ?>
        </th>
        <td>
            <input type="text" id="ta_stripe_custom_text" name="ta_stripe_custom_text" class="large-text" maxlength="1000" value="<?php echo esc_attr( $thirstylink->get_prop( 'stripe_custom_text' ) ); ?>">
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label for="ta_stripe_thank_you_page_id"><?php esc_html_e('Thank You Page', 'thirstyaffiliates'); ?></label>
        </th>
        <td>
            <?php
                $empty_option = __( 'Default (Homepage)', 'thirstyaffiliates' );
                $global_thank_you_page_id = get_option( 'ta_thirstypay_thank_you_page_id' );
                $thank_you_page = is_numeric( $global_thank_you_page_id ) ? get_post( $global_thank_you_page_id ) : null;

                if ( $thank_you_page instanceof WP_Post) {
                    $empty_option = sprintf(
                        /* translators: %s: thank you page title */
                        __( 'Default (%s)', 'thirstyaffiliates' ),
                        $thank_you_page->post_title
                    );
                }

                $this->_helper_functions->wp_pages_dropdown(
                    'ta_stripe_thank_you_page_id',
                    $thirstylink->get_prop( 'stripe_thank_you_page_id' ),
                    false,
                    $empty_option
                );

                if ( ! $thank_you_page instanceof WP_Post && empty( $thirstylink->get_prop( 'stripe_thank_you_page_id' ) ) ) {
                    printf(
                        '<a class="ta-stripe-ty-page-settings" href="%1$s">%2$s</a>',
                        esc_url( admin_url( 'edit.php?post_type=thirstylink&page=thirsty-settings&tab=ta_thirstypay_settings' ) ),
                        esc_html__( 'Settings', 'thirstyaffiliates' )
                    );
                }
            ?>
        </td>
    </tr>
</table>
