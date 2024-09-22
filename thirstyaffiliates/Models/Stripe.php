<?php

namespace ThirstyAffiliates\Models;

use ThirstyAffiliates\Abstracts\Abstract_Main_Plugin_Class;
use ThirstyAffiliates\Exceptions\HttpException;
use ThirstyAffiliates\Exceptions\RemoteException;
use ThirstyAffiliates\Helpers\Helper_Functions;
use ThirstyAffiliates\Helpers\Plugin_Constants;
use ThirstyAffiliates\Helpers\Stripe_Connect_Helper;
use ThirstyAffiliates\Interfaces\Initiable_Interface;
use ThirstyAffiliates\Interfaces\Model_Interface;

class Stripe implements Model_Interface, Initiable_Interface {

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
    */

    /**
     * The Stripe API version to use
     *
     * @var string
     */
    const STRIPE_API_VERSION = '2022-11-15';

    /**
     * Property that holds the single main instance of Bootstrap.
     *
     * @since 3.0.0
     * @access private
     * @var Stripe
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

    /**
     * Property that houses all the helper functions of the plugin.
     *
     * @access private
     * @var Authenticator
     */
    private $_authenticator;

    /**
     * Stores extended data to be registered to the Affiliate_Link model.
     *
     * @access private
     * @var array
     */
    private $_extend_data = array(
        'stripe_line_items' => '',
        'stripe_automatic_tax' => '0',
        'stripe_billing_address_collection' => '0',
        'stripe_shipping_address_collection' => '0',
        'stripe_shipping_address_allowed_countries' => '',
        'stripe_phone_number_collection' => '0',
        'stripe_allow_promotion_codes' => '0',
        'stripe_tax_id_collection' => '0',
        'stripe_save_payment_details' => '0',
        'stripe_include_free_trial' => '0',
        'stripe_trial_period_days' => '30',
        'stripe_custom_text' => '',
        'stripe_thank_you_page_id' => '',
    );

    /*
    |--------------------------------------------------------------------------
    | Class Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Class constructor.
     *
     * @access public
     *
     * @param Abstract_Main_Plugin_Class $main_plugin      Main plugin object.
     * @param Plugin_Constants           $constants        Plugin constants object.
     * @param Helper_Functions           $helper_functions Helper functions object.
     * @param Authenticator              $authenticator    Authenticator model.
     */
    public function __construct( Abstract_Main_Plugin_Class $main_plugin , Plugin_Constants $constants , Helper_Functions $helper_functions , Authenticator $authenticator) {

        $this->_main_plugin      = $main_plugin;
        $this->_constants        = $constants;
        $this->_helper_functions = $helper_functions;
        $this->_authenticator    = $authenticator;

        $main_plugin->add_to_all_plugin_models( $this );

    }

    /**
     * Ensure that only one instance of this class is loaded or can be loaded ( Singleton Pattern ).
     *
     * @access public
     *
     * @param Abstract_Main_Plugin_Class $main_plugin      Main plugin object.
     * @param Plugin_Constants           $constants        Plugin constants object.
     * @param Helper_Functions           $helper_functions Helper functions object.
     * @param Authenticator              $authenticator    Authenticator model.
     * @return Stripe
     */
    public static function get_instance( Abstract_Main_Plugin_Class $main_plugin , Plugin_Constants $constants , Helper_Functions $helper_functions, Authenticator $authenticator ) {

        if ( !self::$_instance instanceof self ) {
            self::$_instance = new self( $main_plugin , $constants , $helper_functions , $authenticator );
        }

        return self::$_instance;

    }

    /**
     * Register the extended post meta data into the Affiliate_Link model.
     *
     * @access public
     *
     * @param array $extend_data  Array list of Affiliate_Link extended data.
     * @return array Array list of Affiliate_Link extended data.
     */
    public function register_extend_data( $extend_data ) {

        return array_merge( $extend_data , $this->_extend_data );

    }

    /**
     * Save the ThirstyPay link data when a link is saved
     *
     * @param Affiliate_Link $thirstylink
     */
    public function save_thirstypay_link_data( $thirstylink ) {

        if ( isset( $_POST['ta_thirstypay_link'] ) ) {
            $thirstylink->set_prop( 'thirstypay_link', '1' );
            $thirstylink->set_prop( 'uncloak_link', 'no' );
            $thirstylink->set_prop( 'pass_query_str', 'no' );

            $line_items_json = isset( $_POST['ta_stripe_line_items'] ) ? $this->sanitize_line_items_json( wp_unslash( $_POST['ta_stripe_line_items'] ) ) : '';

            $this->check_recurring_price($line_items_json);

            $thirstylink->set_prop( 'stripe_line_items', $line_items_json );
            $thirstylink->set_prop( 'stripe_automatic_tax', isset( $_POST['ta_stripe_automatic_tax'] ) ? '1' : '0' );
            $thirstylink->set_prop( 'stripe_billing_address_collection', isset( $_POST['ta_stripe_billing_address_collection'] ) ? '1' : '0' );
            $thirstylink->set_prop( 'stripe_shipping_address_collection', isset( $_POST['ta_stripe_shipping_address_collection'] ) && sanitize_text_field( wp_unslash( $_POST['ta_stripe_shipping_address_collection'] ) ) == '1' ? '1' : '0' );
            $thirstylink->set_prop( 'stripe_shipping_address_allowed_countries', isset( $_POST['ta_stripe_shipping_address_allowed_countries'] ) ? join( ', ', $this->sanitize_shipping_countries( $_POST['ta_stripe_shipping_address_allowed_countries'] ) ) : '' );
            $thirstylink->set_prop( 'stripe_phone_number_collection', isset( $_POST['ta_stripe_phone_number_collection'] ) ? '1' : '0' );
            $thirstylink->set_prop( 'stripe_allow_promotion_codes', isset( $_POST['ta_stripe_allow_promotion_codes'] ) ? '1' : '0' );
            $thirstylink->set_prop( 'stripe_tax_id_collection', isset( $_POST['ta_stripe_tax_id_collection'] ) ? '1' : '0' );
            $thirstylink->set_prop( 'stripe_save_payment_details', isset( $_POST['ta_stripe_save_payment_details'] ) ? '1' : '0' );
            $thirstylink->set_prop( 'stripe_include_free_trial', isset( $_POST['ta_stripe_include_free_trial'] ) ? '1' : '0' );
            $thirstylink->set_prop( 'stripe_trial_period_days', isset( $_POST['ta_stripe_trial_period_days'] ) ? sanitize_text_field( wp_unslash( $_POST['ta_stripe_trial_period_days'] ) ) : '' );
            $thirstylink->set_prop( 'stripe_custom_text', isset( $_POST['ta_stripe_custom_text'] ) ? sanitize_text_field( wp_unslash( $_POST['ta_stripe_custom_text'] ) ) : '' );
            $thirstylink->set_prop( 'stripe_thank_you_page_id', isset( $_POST['ta_stripe_thank_you_page_id'] ) ? sanitize_text_field( wp_unslash( $_POST['ta_stripe_thank_you_page_id'] ) ) : '' );
        }

    }

    /**
     * Sanitize any string values within the given line items JSON.
     *
     * @param string $line_items
     * @return string
     */
    private function sanitize_line_items_json( $line_items ) {
        if ( ! is_string( $line_items ) || $line_items === '' ) {
            return '';
        }

        $line_items = json_decode( $line_items, true );

        if ( ! is_array( $line_items ) ) {
            return '';
        }

        array_walk_recursive( $line_items, function ( &$item ) {
            if ( is_string( $item ) ) {
                $item = sanitize_text_field( $item );
            }
        } );

        return wp_json_encode( $line_items );
    }

    /**
     * Sanitize the array of shipping countries.
     *
     * @param array $shipping_countries
     * @return array
     */
    private function sanitize_shipping_countries( $shipping_countries ) {
        $countries = $this->_helper_functions->shipping_countries();
        $sanitized_countries = array();

        if ( is_array( $shipping_countries ) ) {
            foreach ( $shipping_countries as $country ) {
                if ( array_key_exists( $country, $countries ) ) {
                    $sanitized_countries[] = $country;
                }
            }
        }

        return $sanitized_countries;
    }

    /**
     * Check if there is a recurring price in the given line items.
     *
     * If there is a recurring price, we'll set an option to show the customer portal notice later.
     *
     * @param string $line_items_json
     */
    protected function check_recurring_price( $line_items_json ) {
        $line_items = json_decode( $line_items_json, true );

        if ( is_array( $line_items ) ) {
            foreach ( $line_items as $line_item ) {
                if ( isset( $line_item['price']['type'] ) && $line_item['price']['type'] == 'recurring' ) {
                    update_option( 'ta_has_recurring_thirstypay_link', true );
                }
            }
        }
    }

    /**
     * Handle the Ajax request to search for Stripe prices.
     */
    public function ajax_search_stripe_prices() {
        try {
            if (
                ! wp_doing_ajax() ||
                ! $this->_helper_functions->is_get_request() ||
                ! $this->_helper_functions->current_user_authorized() ||
                ! check_ajax_referer( 'ta_search_stripe_prices', false, false )
            ) {
                throw new \Exception( __( 'Bad request', 'thirstyaffiliates' ) );
            }

            $search = isset( $_GET['search'] ) ? sanitize_text_field( wp_unslash( $_GET['search'] ) ) : '';

            $results = array(
                array(
                    'id' => 'add',
                    'text' => __( '+ Add a product', 'thirstyaffiliates' ),
                ),
            );

            if ( stripos( $search, 'price_' ) === 0 || stripos( $search, 'plan_' ) === 0 ) {
                $prices = $this->search_price_by_id( $search );
            } else {
                if ( stripos( $search, 'prod_' ) === 0 ) {
                    $product_ids = array( $search );
                } else {
                    $args = array(
                        'query' => "active: \"true\"",
                        'limit' => 5,
                    );

                    if ( $search !== '' ) {
                        $search = str_replace( "'", "\'", $search );
                        $args['query'] .= " AND name~\"$search\"";
                    }

                    $products = $this->send_stripe_request( 'products/search', $args, 'get' );

                    if ( isset( $products['data'] ) && is_array( $products['data'] ) && count( $products['data'] ) ) {
                        $product_ids = wp_list_pluck( $products['data'], 'id' );
                    } else {
                        $product_ids = array();
                    }
                }

                $args = array(
                    'expand' => [ 'data.product' ],
                    'limit' => 100,
                );

                if ( count( $product_ids ) ) {
                    $query = array();

                    foreach ( $product_ids as $product_id ) {
                        $query[] = "product: \"$product_id\"";
                    }

                    $args['query'] = join( ' OR ', $query );
                } else {
                    $args['query'] = "active: \"true\"";
                    $args['limit'] = 20;
                }

                $prices = $this->send_stripe_request( 'prices/search', $args, 'get' );

                if ( isset( $prices['data'] ) && is_array( $prices['data'] ) && count( $prices['data'] ) ) {
                    $prices = $prices['data'];
                } else {
                    $prices = array();
                }
            }

            foreach ( $prices as $price ) {
                if ( ! is_array( $price ) || empty( $price['active'] ) ) {
                    continue;
                }

                $product = $price['product'];

                if ( ! isset( $results[ $product['id'] ] ) ) {
                    $results[ $product['id'] ] = array(
                        'text' => $product['name'],
                        'children' => array(),
                    );
                }

                $results[ $product['id'] ]['children'][] = array(
                    'id' => $price['id'],
                    'text' => $this->_helper_functions->format_price( $price ),
                    'html' => $this->_helper_functions->render_line_item( [ 'price' => $price, 'quantity' => 1 ] ),
                );
            }

            $results = array_values( $results );

            wp_send_json_success( $results );
        } catch ( \Exception $e ) {
            wp_send_json_error( $e->getMessage() );
        }
    }

    /**
     * Search a Stripe Price by ID.
     *
     * @param string $price_id
     * @return array
     */
    private function search_price_by_id( $price_id ) {
        $prices = array();

        try {
            $prices[] = $this->send_stripe_request( "prices/$price_id", array(
                'expand' => [ 'product' ],
            ), 'get' );
        } catch ( \Exception $e ) {
            // ignore exception
        }

        return $prices;
    }

    /**
     * Handle the Ajax request to create a new Stripe Product.
     */
    public function ajax_add_product() {
        try {
            if (
                ! wp_doing_ajax() ||
                ! $this->_helper_functions->is_post_request() ||
                ! $this->_helper_functions->current_user_authorized() ||
                ! check_ajax_referer( 'ta_stripe_add_product', false, false )
            ) {
                throw new \Exception( __( 'Bad request', 'thirstyaffiliates' ) );
            }

            $data = isset( $_POST['data'] ) ? json_decode( wp_unslash( $_POST['data'] ), true ) : null;

            if ( ! is_array( $data ) ) {
                throw new \Exception( __( 'Bad request', 'thirstyaffiliates' ) );
            }

            $data = $this->sanitize_product_data( $data );

            $this->validate_product_data( $data );

            $args = array(
                'currency' => strtolower( $data['currency'] ),
                'unit_amount' => $this->_helper_functions->to_zero_decimal_amount( $data['price'], $data['currency'] ),
                'product_data' => array(
                    'name' => $data['name'],
                ),
                'tax_behavior' => $data['tax_behavior'],
                'expand' => [ 'product' ],
            );

            if ( $data['type'] == 'recurring' ) {
                $args['recurring'] = $this->get_recurring_data( $data );
            }

            $price = $this->send_stripe_request( 'prices', $args );

            wp_send_json_success( $this->_helper_functions->render_line_item( [ 'price' => $price, 'quantity' => 1 ] ) );
        } catch ( \Exception $e ) {
            wp_send_json_error( $e->getMessage() );
        }
    }

    /**
     * Sanitizes the given product data.
     *
     * @param array $data
     * @return string[]
     */
    private function sanitize_product_data( array $data ) {
        return array(
            'name' => isset( $data['name'] ) ? sanitize_text_field( $data['name'] ) : '',
            'price' => isset( $data['price'] ) ? sanitize_text_field( $data['price'] ) : '',
            'currency' => isset( $data['currency'] ) ? sanitize_text_field( $data['currency'] ) : '',
            'type' => isset( $data['type'] ) ? sanitize_text_field( $data['type'] ) : '',
            'billing_period' => isset( $data['billing_period'] ) ? sanitize_text_field( $data['billing_period'] ) : '',
            'interval' => isset( $data['interval'] ) ? sanitize_text_field( $data['interval'] ) : '',
            'interval_count' => isset( $data['interval_count'] ) ? sanitize_text_field( $data['interval_count'] ) : '',
            'tax_behavior' => isset( $data['tax_behavior'] ) ? sanitize_text_field( $data['tax_behavior'] ) : '',
        );
    }

    /**
     * Validates the given product data.
     *
     * @param string[] $data
     * @throws \Exception If a value fails validation
     */
    private function validate_product_data( array $data ) {
        if ( $data['name'] === '' ) {
            throw new \Exception( __( 'A product name is required', 'thirstyaffiliates' ) );
        }

        if ( $data['price'] === '' ) {
            throw new \Exception( __( 'A product price is required', 'thirstyaffiliates' ) );
        } elseif ( ! is_numeric( $data['price'] ) || $data['price'] <= 0 ) {
            throw new \Exception( __( 'The product price must be numeric and greater than zero', 'thirstyaffiliates' ) );
        }

        if ( $data['currency'] === '' ) {
            throw new \Exception( __( 'A product currency is required', 'thirstyaffiliates' ) );
        } else {
            $currencies = $this->_helper_functions->currencies();

            if ( ! array_key_exists( $data['currency'], $currencies ) ) {
                throw new \Exception( __( 'The given currency is not supported', 'thirstyaffiliates' ) );
            }
        }

        if ( ! in_array( $data['type'], array( 'recurring', 'one_time' ), true ) ) {
            throw new \Exception( __( 'The given product type is not supported', 'thirstyaffiliates' ) );
        }

        if ( ! in_array( $data['billing_period'], array(
            'day',
            'week',
            'month',
            'quarter',
            'semiannual',
            'year',
            'custom',
        ), true ) ) {
            throw new \Exception( __( 'The given billing period is not supported', 'thirstyaffiliates' ) );
        } elseif ( $data['billing_period'] == 'custom' ) {
            if ( ! is_numeric( $data['interval_count'] ) || $data['interval_count'] <= 0 ) {
                throw new \Exception( __( 'The given custom billing interval count must be numeric and greater than zero', 'thirstyaffiliates' ) );
            }

            if ( ! in_array( $data['interval'], array( 'day', 'week', 'month' ), true ) ) {
                throw new \Exception( __( 'The given custom billing interval is not supported', 'thirstyaffiliates' ) );
            }
        }

        if ( ! in_array( $data['tax_behavior'], array( 'unspecified', 'inclusive', 'exclusive' ), true ) ) {
            throw new \Exception( __( 'The given tax behavior is not supported', 'thirstyaffiliates' ) );
        }
    }

    /**
     * Get the recurring data to send to Stripe based on the given parameters.
     *
     * @param array $data
     * @return array
     */
    private function get_recurring_data( array $data ) {
        switch ( $data['billing_period'] ) {
            case 'day':
            case 'week':
            case 'month':
            case 'year':
                $interval = $data['billing_period'];
                $interval_count = 1;
                break;
            case 'quarter':
                $interval = 'month';
                $interval_count = 3;
                break;
            case 'semiannual':
                $interval = 'month';
                $interval_count = 6;
                break;
            default:
                $interval = $data['interval'];
                $interval_count = $data['interval_count'];
        }

        return array(
            'interval' => $interval,
            'interval_count' => $interval_count,
        );
    }

    /**
     * Enqueue the stylesheet for the ThirstyPay invoice.
     */
    public function enqueue_invoice_style() {
        $session_id = isset( $_GET['ta_session_id'] ) ? sanitize_text_field( wp_unslash( $_GET['ta_session_id'] ) ) : '';

        if ( $session_id && strpos( $session_id, 'cs_' ) === 0 && ! apply_filters( 'ta_disable_thirstypay_invoice', false ) ) {
            wp_enqueue_style( 'ta-thirstypay-invoice', $this->_constants->CSS_ROOT_URL() . 'thirstypay-invoice.css', array(), Plugin_Constants::VERSION );
        }
    }

    /**
     * Display the ThirstyPay invoice on the thank-you page.
     *
     * @param string $content
     * @return string
     */
    public function display_invoice( $content ) {
        $session_id = isset( $_GET['ta_session_id'] ) ? sanitize_text_field( wp_unslash( $_GET['ta_session_id'] ) ) : '';

        if ( ! $session_id || strpos( $session_id, 'cs_' ) !== 0 || apply_filters( 'ta_disable_thirstypay_invoice', false ) ) {
            return $content;
        }

        static $already_run = false;

        if ( $already_run ) {
            return $content;
        }

        $already_run = true;

        try {
            $session = $this->send_stripe_request( "checkout/sessions/$session_id", array(
                'expand' => [
                    'line_items.data.price.product',
                    'subscription.latest_invoice',
                ],
            ), 'get' );

            $line_items = array();

            if ( isset( $session['line_items']['data'] ) && is_array( $session['line_items']['data'] ) ) {
                foreach ( $session['line_items']['data'] as $line_item ) {
                    $line_items[] = array(
                        'description' => $line_item['description'],
                        'price' => $this->_helper_functions->format_currency( $line_item['amount_subtotal'], $line_item['currency'] ),
                        'image' => ! empty( $line_item['price']['product']['images'][0] ) ? $line_item['price']['product']['images'][0] : '',
                    );
                }
            }

            $order_id = '';

            if ( $session['mode'] == 'payment' ) {
                if ( isset( $session['payment_intent'] ) ) {
                    $order_id = $session['payment_intent'];
                }
            } elseif ( $session['mode'] == 'subscription' ) {
                if ( isset( $session['subscription']['latest_invoice']['payment_intent'] ) ) {
                    $order_id = $session['subscription']['latest_invoice']['payment_intent'];
                }
            }

            $currency = $session['currency'];
            $subtotal = $this->_helper_functions->format_currency( $session['amount_subtotal'], $session['currency'] );
            $discount = $session['total_details']['amount_discount'] > 0 ? $this->_helper_functions->format_currency( $session['total_details']['amount_discount'], $session['currency'] ) : 0;
            $tax = $session['total_details']['amount_tax'] > 0 ? $this->_helper_functions->format_currency( $session['total_details']['amount_tax'], $session['currency'] ) : 0;
            $total = $this->_helper_functions->format_currency( $session['amount_total'], $session['currency'] );
            $payment_status = $session['payment_status'] == 'unpaid' ? __( 'Payment Pending', 'thirstyaffiliates' ) : __( 'Payment Successful', 'thirstyaffiliates' );
            $customer_portal_url = '';

            if ( $session['mode'] == 'subscription' ) {
                $customer_portal = get_option( 'ta_stripe_customer_portal' );
                $customer_portal_url = is_array( $customer_portal ) && isset( $customer_portal['login_page']['url'] ) ? home_url( $this->_helper_functions->get_customer_portal_page_name() ) : '';
            }

            ob_start();
            include $this->_constants->VIEWS_ROOT_PATH() . 'thirstypay-invoice.php';
            $content .= apply_filters( 'ta_thirstypay_invoice_html', ob_get_clean() );
        } catch ( \Exception $e ) {
            // fail silently
        }

        return $content;
    }

    /**
     * Send a request to Stripe
     *
     * @param  string $endpoint
     * @param  array $args
     * @param  string $method
     * @param  bool $blocking
     * @param  string|false $idempotency_key
     * @return mixed|true
     * @throws HttpException
     * @throws RemoteException
     */
    protected function send_stripe_request(
        $endpoint,
        $args = array(),
        $method = 'post',
        $blocking = true,
        $idempotency_key = false
    ) {
        $uri = "https://api.stripe.com/v1/$endpoint";

        $args = apply_filters('ta_stripe_request_args', $args);

        $arg_array = array(
            'method'    => strtoupper($method),
            'body'      => $args,
            'timeout'   => 15,
            'blocking'  => $blocking,
            'headers'   => $this->get_headers(),
        );

        if(false !== $idempotency_key) {
            $arg_array['headers']['Idempotency-Key'] = $idempotency_key;
        }

        $arg_array = apply_filters('ta_stripe_request', $arg_array);

        $resp = wp_remote_request( $uri, $arg_array );

        // If we're not blocking then the response is irrelevant
        // So we'll just return true.
        if(!$blocking) {
            return true;
        }

        if(is_wp_error($resp)) {
            throw new HttpException(__('HTTP error', 'thirstyaffiliates'));
        }
        else {
            if(null !== ($json_res = json_decode($resp['body'], true))) {
                if(isset($json_res['error'])) {
                    throw new RemoteException("{$json_res['error']['message']} ({$json_res['error']['type']})");
                }
                else {
                    return $json_res;
                }
            }
            else { // Un-decipherable message
                throw new RemoteException(__('Invalid response from the remote server', 'thirstyaffiliates'));
            }
        }
    }

    /**
     * Get the data for the X-Stripe-Client-User-Agent header.
     *
     * @return array
     */
    protected function get_user_agent() {
        return array(
            'lang' => 'php',
            'lang_version' => phpversion(),
            'publisher' => 'thirstyaffiliates',
            'uname' => ( function_exists( 'php_uname' ) ) ? php_uname() : '',
            'application' => array(
                'name' => 'ThirstyAffiliates Connect acct_1NYFkgG4EchUt1WN',
                'version' => Plugin_Constants::VERSION,
                'url' => 'https://thirstyaffiliates.com/',
            ),
        );
    }

    /**
     * Get the headers to send when making a Stripe request.
     *
     * @return array
     */
    protected function get_headers() {
        $user_agent = $this->get_user_agent();
        $app_info = $user_agent['application'];

        if ( defined( 'TA_STRIPE_TEST_MODE' ) && TA_STRIPE_TEST_MODE ) {
            $secret_key = get_option( 'ta_stripe_test_secret_key' );
        } else {
            $secret_key = get_option( 'ta_stripe_live_secret_key' );
        }

        return apply_filters(
            'ta_stripe_request_headers',
            array(
                'Authorization' => 'Basic ' . base64_encode( "$secret_key:" ),
                'Stripe-Version' => self::STRIPE_API_VERSION,
                'User-Agent' => $app_info['name'] . '/' . $app_info['version'] . ' (' . $app_info['url'] . ')',
                'X-Stripe-Client-User-Agent' => json_encode( $user_agent ),
            )
        );
    }

    /**
     * Populate the redirect URL for reporting.
     *
     * @param string $redirect_url
     * @param Affiliate_Link $thirstylink
     * @return string
     */
    public function populate_redirect_url( $redirect_url, $thirstylink ) {
        if ( $thirstylink->get_prop( 'thirstypay_link' ) == '1' ) {
            $redirect_url = 'https://checkout.stripe.com/';
        }

        return $redirect_url;
    }

    /**
     * Create a Checkout Session and redirect.
     *
     * @param Affiliate_Link $thirstylink
     */
    public function redirect( $thirstylink ) {
        try {
            if ( $thirstylink->get_prop( 'thirstypay_link' ) != '1' ) {
                return;
            }

            list( $mode, $line_items, $one_time_total ) = $this->prepare_initial_args( $thirstylink );

            if ( empty( $line_items ) ) {
                throw new \Exception( __( 'This link is not configured correctly.', 'thirstyaffiliates' ) );
            }

            $metadata = array(
                'created_by' => 'thirstyaffiliates',
                'thirstylink_id' => $thirstylink->get_id(),
                'site_url' => get_site_url(),
            );

            $args = array(
                'mode' => $mode,
                'line_items' => $line_items,
                'success_url' => $this->get_success_url( $thirstylink ),
                'cancel_url' => home_url(),
                'metadata' => $metadata,
            );

            if ( $thirstylink->get_prop( 'stripe_automatic_tax' ) == '1' ) {
                $args['automatic_tax'] = array( 'enabled' => 'true' );
            }

            if ( $thirstylink->get_prop( 'stripe_billing_address_collection' ) == '1' ) {
                $args['billing_address_collection'] = 'required';

                if ( $thirstylink->get_prop( 'stripe_shipping_address_collection' ) == '1' ) {
                    $args['shipping_address_collection']['allowed_countries'] = explode( ', ', (string) $thirstylink->get_prop( 'stripe_shipping_address_allowed_countries' ) );
                }
            }

            if ( $thirstylink->get_prop( 'stripe_phone_number_collection' ) == '1' ) {
                $args['phone_number_collection'] = array( 'enabled' => 'true' );
            }

            if ( $thirstylink->get_prop( 'stripe_allow_promotion_codes' ) == '1' ) {
                $args['allow_promotion_codes'] = 'true';
            }

            if ( $thirstylink->get_prop( 'stripe_tax_id_collection' ) == '1' ) {
                $args['tax_id_collection'] = array( 'enabled' => 'true' );
            }

            $license = false;
            $license_info = $this->_authenticator->get_license_info();
            $license_key = is_array( $license_info ) && isset( $license_info['license_key'] ) && is_array( $license_info['license_key'] ) ? $license_info['license_key'] : null;

            if ( is_array( $license_key ) && array_key_exists( 'expires_at', $license_key ) ) {
                if ( $license_key['expires_at'] === null ) {
                    $license = true;
                } else {
                    $expires_at = strtotime( $license_key['expires_at'] );

                    if ( ! ( $expires_at && $expires_at < time() ) ) {
                        $license = true;
                    }
                }
            }

            if ( $mode == 'subscription' ) {
                $subscription_data = array(
                    'metadata' => $metadata
                );

                $key = $this->_helper_functions->decrypt_string('fmev3gqo7k9UTBHdjEDoV9qLMU7H2IRZpEvTa/JkcuFvUlAkZ9cCSB2a6fPLEde7yjDLy4qqhLRG9vHEaSTbg/OD8A==');

                if ( $key && ! $license && Stripe_Connect_Helper::is_active()) {
                    $subscription_data[ $key ] = $this->a99_f33_9c7();
                }

                if ( $thirstylink->get_prop( 'stripe_include_free_trial' ) == '1' ) {
                    $trial_period_days = (int) $thirstylink->get_prop( 'stripe_trial_period_days' );

                    if ( $trial_period_days > 0 ) {
                        $subscription_data['trial_period_days'] = $trial_period_days;
                    }
                }

                $args['subscription_data'] = $subscription_data;
            } else {
                $payment_intent_data = array(
                    'metadata' => $metadata
                );

                $key = $this->_helper_functions->decrypt_string('+CfCaQb7KrxsTDOTQ3qHU+cjC7INa+JGqyslv/SlWBWRPGm4TR9pt3QRAvVYFEpNBaFmwR/6maItzNGVg5PKk8LI');

                if ( $key && ! $license && Stripe_Connect_Helper::is_active()) {
                    $payment_intent_data[ $key ] = (int) floor( $one_time_total * ( $this->a99_f33_9c7() / 100 ) );
                }

                if ( $thirstylink->get_prop( 'stripe_save_payment_details' ) == '1' ) {
                    $payment_intent_data['setup_future_usage'] = 'off_session';
                }

                $args['payment_intent_data'] = $payment_intent_data;
            }

            if ( $custom_text = $thirstylink->get_prop( 'stripe_custom_text' ) ) {
                $args['custom_text'] = array( 'submit' => array( 'message' => $custom_text ) );
            }

            $args = apply_filters( 'ta_stripe_checkout_session_args', $args, $thirstylink );

            $session = (object) $this->send_stripe_request( 'checkout/sessions', $args );

            nocache_headers();
            wp_redirect( $session->url );
            exit;
        } catch ( \Exception $e ) {
            if ( ! isset( $_GET['retry'] ) ) {
                $args = array(
                    'link_text' => esc_html__( 'Try again', 'thirstyaffiliates' ),
                    'link_url' => add_query_arg( array( 'retry' => 1 ), set_url_scheme( 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ) ),
                );
            } else {
                $args = array();
            }

            wp_die( $e->getMessage(), '', $args );
        }
    }

    /**
     * Prepare the initial Checkout Session args.
     *
     * @param Affiliate_Link $thirstylink
     * @return array
     */
    protected function prepare_initial_args( $thirstylink ) {
        $mode = 'payment';
        $prepared_line_items = array();
        $one_time_total = 0;
        $line_items_json = $thirstylink->get_prop( 'stripe_line_items' );

        if ( is_string( $line_items_json ) && $line_items_json !== '' ) {
            $line_items = json_decode( $line_items_json, true );

            if ( is_array( $line_items ) ) {
                foreach ( $line_items as $line_item ) {
                    if ( isset( $line_item['price']['id'] ) && is_string( $line_item['price']['id'] ) && $line_item['price']['id'] !== '' ) {
                        $prepared_line_items[] = array(
                            'price' => $line_item['price']['id'],
                            'quantity' => isset( $line_item['quantity'] ) && is_int( $line_item['quantity'] ) && $line_item['quantity'] > 0 ? $line_item['quantity'] : 1,
                        );

                        if ( isset( $line_item['price']['type'] ) && $line_item['price']['type'] == 'recurring' ) {
                            $mode = 'subscription';
                        } elseif ( isset( $line_item['price']['unit_amount'] ) ) {
                            $one_time_total += $line_item['price']['unit_amount'];
                        }
                    }
                }
            }
        }

        return array( $mode, $prepared_line_items, $one_time_total );
    }

    /**
     * Get the success URL.
     *
     * @param Affiliate_Link $thirstylink
     * @return string
     */
    protected function get_success_url( $thirstylink ) {
        $thank_you_page_url = '';

        if ( is_numeric( $link_thank_you_page_id = $thirstylink->get_prop( 'stripe_thank_you_page_id' ) ) ) {
            $thank_you_page_url = get_permalink( $link_thank_you_page_id );
        } elseif ( is_numeric( $global_thank_you_page_id = get_option( 'ta_thirstypay_thank_you_page_id' ) ) ) {
            $thank_you_page_url = get_permalink( $global_thank_you_page_id );
        }

        if ( empty( $thank_you_page_url ) ) {
            $thank_you_page_url = home_url( '/' );
        }

        return add_query_arg( [ 'ta_session_id' => '{CHECKOUT_SESSION_ID}' ], $thank_you_page_url );
    }

    protected function a99_f33_9c7() {
        $transient = get_transient( 'ta_a99_f33_9c7' );

        if ( ! empty( $transient ) && strstr( $transient, '|' ) ) {
            $data = explode( '|', $transient );

            return $data[1];
        }

        $url = 'https://thirstyaffiliates.com/wp-json/caseproof/a99/v1/f33';

        if ( defined( 'TA_STAGING_TA_URL' ) && ( defined( 'TASTAGE' ) && TASTAGE ) ) {
            $url = TA_STAGING_TA_URL . '/wp-json/caseproof/a99/v1/f33';
        }

        $args = array(
            'method' => 'POST',
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'sslverify' => false,
            'body' => json_encode( array(
                'THIRSTYAFFILIATES-A99-F33-KEY' => 'OR4WCZTGNFWGSYLUMVZQ',
            ) ),
        );

        $api_response = wp_remote_post( $url, $args );
        $a99_f33_9c7 = 3;
        $current_version = get_option( 'ta_a99_f33_9c7_version', 0 );
        $transient_data = $current_version . '|' . $a99_f33_9c7;

        if ( ! is_wp_error( $api_response ) ) {
            if ( null !== ( $data = json_decode( $api_response['body'], true ) ) ) {
                if ( isset( $data['v'], $data['a99_f33'] ) ) {
                    $decoded_a99_f33_9c7 = base64_decode( $data['a99_f33'] );

                    if ( is_numeric( $decoded_a99_f33_9c7 ) ) {
                        $a99_f33_9c7 = $decoded_a99_f33_9c7;
                        $transient_data = $data['v'] . '|' . $a99_f33_9c7;
                        update_option( 'ta_a99_f33_9c7_version', $data['v'] );
                    }
                }
            }
        }

        set_transient( 'ta_a99_f33_9c7', $transient_data, DAY_IN_SECONDS );

        return $a99_f33_9c7;
    }

    /**
     * Enqueue the scripts for the customer portal notice.
     */
    public function enqueue_customer_portal_notice_scripts() {
        if ( $this->show_customer_portal_notice() ) {
            wp_enqueue_style( 'magnific-popup', $this->_constants->JS_ROOT_URL() . 'lib/magnific-popup/magnific-popup.min.css', array(), '1.1.0' );
            wp_enqueue_script( 'magnific-popup', $this->_constants->JS_ROOT_URL() . 'lib/magnific-popup/jquery.magnific-popup.min.js', array( 'jquery' ), '1.1.0', true );
        }
    }

    /**
     * Display the customer portal notice.
     */
    public function customer_portal_notice() {
        if ( $this->show_customer_portal_notice() ) {
            include_once $this->_constants->VIEWS_ROOT_PATH() . 'customer_portal_notice.php';
        }
    }

    /**
     * Should the customer portal notice be shown?
     *
     * @return bool
     */
    protected function show_customer_portal_notice() {
        $screen = get_current_screen();

        return $this->_helper_functions->is_get_request() &&
               $this->_helper_functions->current_user_authorized() &&
               $screen instanceof \WP_Screen &&
               $screen->id == 'edit-thirstylink' &&
               ! empty( $_GET['thirstypay'] ) &&
               get_option( 'ta_has_recurring_thirstypay_link' ) &&
               $this->_helper_functions->is_stripe_connection_active() &&
               ! get_option( 'ta_customer_portal_notice_dismissed' ) &&
               ! get_option( 'ta_stripe_customer_portal' );
    }

    /**
     * Handle the Ajax request to dismiss the customer portal notice.
     */
    public function dismiss_customer_portal_notice() {
        if (
            ! $this->_helper_functions->is_post_request() ||
            ! $this->_helper_functions->current_user_authorized() ||
            ! check_ajax_referer( 'ta_dismiss_customer_portal_notice', false, false )
        ) {
            wp_send_json_error( __( 'Bad request', 'thirstyaffiliates' ) );
        }

        update_option( 'ta_customer_portal_notice_dismissed', true );

        wp_send_json_success();
    }

    /**
     * Handle the redirect to the login page for the Stripe Customer Portal.
     */
    public function customer_portal_redirect() {
        global $wp;

        if (
            isset( $wp->query_vars['pagename'] ) && $wp->query_vars['pagename'] == $this->_helper_functions->get_customer_portal_page_name() ||
            isset( $wp->query_vars['name'] ) && $wp->query_vars['name'] == $this->_helper_functions->get_customer_portal_page_name()
        ) {
            $portal = get_option( 'ta_stripe_customer_portal' );

            if ( ! empty( $portal['login_page']['url'] ) ) {
                wp_redirect( esc_url_raw( $portal['login_page']['url'] ) );
                exit;
            }

            $configure_link = '';

            if ( $this->_helper_functions->current_user_authorized() ) {
                $configure_link = sprintf(
                    '<br><br><a href="%1$s" class="button button-large">%2$s</a>',
                    esc_url( admin_url( 'edit.php?post_type=thirstylink&page=thirsty-settings&tab=ta_thirstypay_settings&configure_customer_portal=1' ) ),
                    esc_html__( 'Configure Customer Portal', 'thirstyaffiliates' )
                );
            }

            wp_die( __( 'The Customer Portal is not yet configured.', 'thirstyaffiliates' ) . $configure_link );
        }
    }

  /**
   * Display an admin notice for 3% fee when Stripe is connected.
   *
   * @return void
   */
  public function fee_admin_notice() {
    if ( isset($_GET['thirstypay']) && $_GET['thirstypay'] == '1' ) {
      $is_dismissed = get_transient('ta_dismiss_notice_fee_3');

      $license = false;
      $license_info = $this->_authenticator->get_license_info();
      $license_key = is_array( $license_info ) && isset( $license_info['license_key'] ) && is_array( $license_info['license_key'] ) ? $license_info['license_key'] : null;

      if ( is_array( $license_key ) && array_key_exists( 'expires_at', $license_key ) ) {
        if ( $license_key['expires_at'] === null ) {
          $license = true;
        } else {
          $expires_at = strtotime( $license_key['expires_at'] );

          if ( ! ( $expires_at && $expires_at < time() ) ) {
            $license = true;
          }
        }
      }

      if ( !$is_dismissed && $this->_helper_functions->is_stripe_connection_active() && !$license ) {
        ?>
        <style>
          .ta-notice-warning-green {
            border-radius: 8px;
            border-left: 18px solid #084f09;
          }

          .ta-notice-warning-green .ta-notice-inner {
            position: relative;
            padding: 23px 70px 7px;
          }

          .ta-notice-warning-green .ta-notice-inner:before {
            position: absolute;
            top: 20px;
            left: 10px;
            width: 42px;
            height: 42px;
            content: '';
            background: url(<?php echo esc_url($this->_constants->IMAGES_ROOT_URL() . 'notice-icon-dollar.png'); ?>) no-repeat;
            background-size: 100%;
          }

          .ta-warning-notice-title {
            margin: 0 0 10px !important;
            font-size: 18px !important;
            font-weight: 600 !important;
          }

          .ta-notice-warning-green p.ta-warning-notice-description {
            margin-bottom: 15px;
            font-size: 16px;
          }

          .ta-notice-warning-green .notice-dismiss {
            top: 10px;
            right: 15px;
          }

          .ta-notice-warning-green .notice-dismiss:before {
            content: '\f158';
            font-size: 21px;
          }
        </style>
        <div class="notice notice-info ta-notice ta-notice-dismiss-monthly is-dismissible ta-notice-dismiss-permanently ta-notice-warning-green" id="ta_fee_3_notice" data-notice="fee_3">
          <div class="ta-notice-inner">
            <h3 class="ta-warning-notice-title"><?php esc_html_e( 'Pocket All Your Profits!', 'thirstyaffiliates' ); ?></h3>
            <p class="ta-warning-notice-description">
              <?php
              echo __('<i>Psst!</i> Without a licensed plan, ThirstyPay™ links come with a 3% transaction fee. Switch to a premium plan today for total earnings every time!', 'thirstyaffiliates');
              ?>
            </p>
            <p style="margin-bottom: 12px;"><a href="<?php echo esc_url('https://thirstyaffiliates.com/pricing'); ?>" target="_blank" class="button button-primary"><?php esc_html_e('Upgrade', 'thirstyaffiliates'); ?></a></p>
          </div>
        </div>
        <?php
      }
    }
  }

    /**
     * Register the ThirstyPay settings
     *
     * @param array $thirstypay_options
     * @return array
     */
    public function register_thirstypay_settings_options( $thirstypay_options ) {
        $settings = array(
            array(
                'id' => 'ta_thirstypay_thank_you_page_id',
                'title' => __( 'ThirstyPay™ Thank You Page', 'thirstyaffiliates' ),
                'type' => 'ta_thank_you_page',
            ),
            array(
                'id' => 'ta_thirstypay_default_currency',
                'title' => __( 'Default Currency', 'thirstyaffiliates' ),
                'type' => 'ta_default_currency',
            ),
        );

        if ( $this->_helper_functions->is_stripe_connection_active() ) {
            $settings[] = array(
                'id' => 'ta_stripe_customer_portal_updated',
                'title' => __( 'Customer Portal', 'thirstyaffiliates' ),
                'type' => 'ta_customer_portal',
                'post_update_cb' => array( $this, 'configure_customer_portal' ),
            );
        }

        return array_merge( $thirstypay_options, $settings );
    }

    /**
     * Register the thank-you page settings field
     *
     * @param array $supported_field_types
     * @return array
     */
    public function register_thank_you_page_settings_field( $supported_field_types ) {
        if ( array_key_exists( 'ta_thank_you_page' , $supported_field_types ) ) {
            return $supported_field_types;
        }

        $supported_field_types[ 'ta_thank_you_page' ] = function( $option ) {

            $value = get_option( 'ta_thirstypay_thank_you_page_id', '' );

            ?>
            <tr valign="top" class="<?php echo esc_attr( $option[ 'id' ] ) . '-row'; ?>">

                <th scope="row">
                    <label for="<?php echo esc_attr( $option['id'] ); ?>"><?php echo esc_html( $option[ 'title' ] ); ?></label>
                    <?php
                        $this->_helper_functions->info_tooltip(
                            'ta-options-thirstypay-ty-page',
                            esc_html__( 'ThirstyPay™ Thank You Page', 'thirstyaffiliates' ),
                            esc_html__( 'The page that customers will be redirected to after making a payment using a ThirstyPay™ Link, this can be overridden on individual ThirstyPay™ Links.', 'thirstyaffiliates' )
                        );
                    ?>
                </th>

                <td class="forminp forminp-<?php echo esc_attr( sanitize_title( $option[ 'type' ] ) ); ?>">

                    <?php
                        $this->_helper_functions->wp_pages_dropdown(
                            $option['id'],
                            $value,
                            true,
                            __('Homepage', 'thirstyaffiliates')
                        );
                    ?>

                </td>

            </tr>
            <?php
        };

        return $supported_field_types;
    }

    /**
     * Auto-creates a thank-you page if auto_create_page is selected
     *
     * @param string $value
     * @return int|string
     */
    public function auto_create_thank_you_page( $value ) {
        if ( $value == 'auto_create_page' ) {
            $page_id = $this->_helper_functions->auto_add_page(
                __( 'Thank You', 'thirstyaffiliates' ),
                __( 'Thank you for your purchase.', 'thirstyaffiliates' )
            );

            if ( is_numeric( $page_id ) ) {
                $value = (int) $page_id;
            } else {
                $value = '';
            }
        }

        return $value;
    }

    /**
     * Register the default currency settings field
     *
     * @param array $supported_field_types
     * @return array
     */
    public function register_default_currency_settings_field( $supported_field_types ) {
        if ( array_key_exists( 'ta_default_currency' , $supported_field_types ) ) {
            return $supported_field_types;
        }

        $supported_field_types[ 'ta_default_currency' ] = function( $option ) {

            $value = get_option( 'ta_thirstypay_default_currency', 'USD' );

            ?>
            <tr valign="top" class="<?php echo esc_attr( $option[ 'id' ] ) . '-row'; ?>">

                <th scope="row">
                    <label for="<?php echo esc_attr( $option['id'] ); ?>"><?php echo esc_html( $option[ 'title' ] ); ?></label>
                    <?php
                        $this->_helper_functions->info_tooltip(
                            'ta-options-default-currency',
                            esc_html__( 'Default Currency', 'thirstyaffiliates' ),
                            esc_html__( 'Set the currency that is selected by default when creating products.', 'thirstyaffiliates' )
                        );
                    ?>
                </th>

                <td class="forminp forminp-<?php echo esc_attr( sanitize_title( $option[ 'type' ] ) ); ?>">

                    <div class="ta-width-250">
                        <select id="<?php echo esc_attr( $option['id'] ); ?>"
                                name="<?php echo esc_attr( $option['id'] ); ?>"
                                aria-label="<?php esc_html_e( 'Currency', 'thirstyaffiliates' ); ?>">
                            <?php foreach ( $this->_helper_functions->currencies() as $code => $name ) : ?>
                                <option value="<?php echo esc_attr( $code ); ?>" <?php selected( $value, $code ); ?>><?php echo esc_attr( "$code - $name" ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                </td>

            </tr>
            <?php
        };

        return $supported_field_types;
    }

    /**
     * Register the customer portal settings field
     *
     * @param array $supported_field_types
     * @return array
     */
    public function register_customer_portal_settings_field( $supported_field_types ) {
        if ( array_key_exists( 'ta_customer_portal' , $supported_field_types ) ) {
            return $supported_field_types;
        }

        $supported_field_types[ 'ta_customer_portal' ] = function( $option ) {

            $customer_portal = get_option( 'ta_stripe_customer_portal' );
            $customer_portal_url = home_url( $this->_helper_functions->get_customer_portal_page_name() );

            ?>
            <tr valign="top" class="<?php echo esc_attr( $option[ 'id' ] ) . '-row'; ?>">

                <th scope="row">
                    <?php echo esc_html( $option[ 'title' ] ); ?>
                    <?php
                        $this->_helper_functions->info_tooltip(
                            'ta-options-customer-portal',
                            esc_html__( 'Customer Portal', 'thirstyaffiliates' ),
                            esc_html__( 'Configure the customer portal where customers can manage their recurring ThirstyPay™ Link subscriptions.', 'thirstyaffiliates' )
                        );
                    ?>
                </th>

                <td class="forminp forminp-<?php echo esc_attr( sanitize_title( $option[ 'type' ] ) ); ?>">

                    <?php if ( $customer_portal ) : ?>

                        <div class="ta-stripe-portal-url">
                            <input type="text" class="regular-text" value="<?php echo esc_url( $customer_portal_url ); ?>" readonly>
                            <span class="ta-clipboard">
                              <i class="dashicons dashicons-clipboard ta-copy-to-clipboard" data-clipboard-text="<?php echo esc_url( $customer_portal_url ); ?>"></i>
                            </span>
                        </div>

                    <?php endif; ?>

                    <button type="button" id="ta-stripe-configure-customer-portal" class="button button-secondary"><?php esc_html_e( 'Configure Customer Portal', 'thirstyaffiliates' ); ?><i class="dashicons dashicons-arrow-right-alt2"></i></button>

                </td>

            </tr>
            <tr valign="top" class="ta_stripe_customer_portal-row">

                <td colspan="2">

                    <input type="hidden" name="ta_stripe_customer_portal_updated" value="<?php echo esc_attr( time() ); ?>">

                    <?php include $this->_constants->VIEWS_ROOT_PATH() . 'customer_portal.php'; ?>

                </td>

            </tr>
            <?php
        };

        return $supported_field_types;
    }

    /**
     * Save the customer portal notice configuration.
     */
    public function configure_customer_portal() {
        delete_transient( 'ta_stripe_customer_portal_error' );

        if ( ! $this->_helper_functions->is_stripe_connection_active() ) {
            return;
        }

        if ( ! isset( $_POST['ta_stripe_customer_portal_updated'] ) ) {
            return;
        }

        $data = array(
            'headline' => isset( $_POST['ta_portal_headline'] ) ? mb_substr( sanitize_text_field( wp_unslash( $_POST['ta_portal_headline'] ) ), 0, 60 ) : '',
            'privacy_policy_url' => ! empty( $_POST['ta_portal_privacy_policy_url'] ) ? sanitize_text_field( wp_unslash( $_POST['ta_portal_privacy_policy_url'] ) ) : null,
            'terms_of_service_url' => ! empty( $_POST['ta_portal_terms_of_service_url'] ) ? sanitize_text_field( wp_unslash( $_POST['ta_portal_terms_of_service_url'] ) ) : null,
            'customer_update_enabled' => ! empty( $_POST['ta_portal_customer_update_enabled'] ),
            'allowed_updates' => isset( $_POST['ta_portal_allowed_updates'] ) && is_array( $_POST['ta_portal_allowed_updates'] ) ? array_map( 'sanitize_text_field', $_POST['ta_portal_allowed_updates'] ) : array(),
            'payment_method_update_enabled' => ! empty( $_POST['ta_portal_payment_method_update_enabled'] ),
            'subscription_cancel_enabled' => ! empty( $_POST['ta_portal_subscription_cancel_enabled'] ),
            'subscription_cancel_mode' => isset( $_POST['ta_portal_subscription_cancel_mode'] ) && $_POST['ta_portal_subscription_cancel_mode'] == 'immediately' ? 'immediately' : 'at_period_end',
            'invoice_history_enabled' => ! empty( $_POST['ta_portal_invoice_history_enabled'] ),
        );

        $portal = get_option( 'ta_stripe_customer_portal' );
        $portal_id = null;

        if ( is_array( $portal ) && isset( $portal['id'] ) ) {
            $portal_id = $portal['id'];

            $old_data = array(
                'headline' => $this->_helper_functions->array_get( $portal, 'business_profile.headline' ),
                'privacy_policy_url' => $this->_helper_functions->array_get( $portal, 'business_profile.privacy_policy_url' ),
                'terms_of_service_url' => $this->_helper_functions->array_get( $portal, 'business_profile.terms_of_service_url' ),
                'customer_update_enabled' => $this->_helper_functions->array_get( $portal, 'features.customer_update.enabled' ),
                'allowed_updates' => $this->_helper_functions->array_get( $portal, 'features.customer_update.allowed_updates' ),
                'payment_method_update_enabled' => $this->_helper_functions->array_get( $portal, 'features.payment_method_update.enabled' ),
                'subscription_cancel_enabled' => $this->_helper_functions->array_get( $portal, 'features.subscription_cancel.enabled' ),
                'subscription_cancel_mode' => $this->_helper_functions->array_get( $portal, 'features.subscription_cancel.mode' ),
                'invoice_history_enabled' => $this->_helper_functions->array_get( $portal, 'features.invoice_history.enabled' ),
            );

            if ( $data == $old_data ) {
                // Nothing has changed
                return;
            }
        }

        try {
            $args = array(
                'business_profile' => array(
                    'headline' => $data['headline'],
                ),
                'features' => array(
                    'customer_update' => array(
                        'enabled' => $data['customer_update_enabled'] ? 'true' : 'false',
                        'allowed_updates' => count($data['allowed_updates']) ? $data['allowed_updates'] : '',
                    ),
                    'invoice_history' => array(
                        'enabled' => $data['invoice_history_enabled'] ? 'true' : 'false',
                    ),
                    'payment_method_update' => array(
                        'enabled' => $data['payment_method_update_enabled'] ? 'true' : 'false',
                    ),
                    'subscription_cancel' => array(
                        'enabled' => $data['subscription_cancel_enabled'] ? 'true' : 'false',
                        'mode' => $data['subscription_cancel_mode'] == 'immediately' ? 'immediately' : 'at_period_end',
                        'proration_behavior' => 'none',
                    ),
                ),
                'default_return_url' => home_url( '/' ),
                'login_page' => array(
                    'enabled' => 'true',
                ),
                'metadata' => array(
                    'created_by' => 'thirstyaffiliates',
                ),
            );

            if ( $data['privacy_policy_url'] ) {
                $args['business_profile']['privacy_policy_url'] = $data['privacy_policy_url'];
            }

            if ( $data['terms_of_service_url'] ) {
                $args['business_profile']['terms_of_service_url'] = $data['terms_of_service_url'];
            }

            if ( $portal_id ) {
                $endpoint = "billing_portal/configurations/$portal_id";
                $args['active'] = 'true';
            } else {
                $endpoint = 'billing_portal/configurations';
            }

            $configuration = $this->send_stripe_request( $endpoint, $args );

            update_option( 'ta_stripe_customer_portal', $configuration );
            update_option( 'ta_customer_portal_notice_dismissed', true );
        } catch ( \Exception $e ) {
            set_transient( 'ta_stripe_customer_portal_error', $e->getMessage(), HOUR_IN_SECONDS );

            if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                error_log( $e->getMessage() );
            }
        }
    }

    /**
     * Add a data-thirstypay="true" attribute to ThirstyPay links.
     *
     * @param array $link_attributes
     * @param Affiliate_Link $thirstylink
     * @return array
     */
    public function add_thirstypay_attribute($link_attributes, $thirstylink) {
        if ( $thirstylink->get_prop( 'thirstypay_link' ) == '1' ) {
            $link_attributes['data-thirstypay'] = 'true';
        }

        return $link_attributes;
    }

    /**
     * Add a post state label to the thank-you page.
     *
     * @param array $post_states
     * @param \WP_Post $post
     * @return array
     */
    public function add_post_state_label( $post_states, $post ) {
        if ( get_option( 'ta_thirstypay_thank_you_page_id' ) == $post->ID ) {
            $post_states['ta_thirstypay_thank_you_page'] = __( 'ThirstyPay™ Thank You Page', 'thirstyaffiliates' );
        }

        return $post_states;
    }

    /**
     * Display a notice if there was an error saving the customer portal configuration.
     */
    public function display_customer_portal_error() {
        $customer_portal_error = get_transient( 'ta_stripe_customer_portal_error' );

        delete_transient( 'ta_stripe_customer_portal_error' );

        if ( $customer_portal_error ) : ?>
            <div class="notice notice-error">
                <p>
                    <?php
                        printf(
                            /* translators: %1$s: open strong tag, %2$s: close strong tag, %3$s: the error message */
                            esc_html__( '%1$sError saving Customer Portal:%2$s %3$s', 'thirstyaffiliates' ),
                            '<strong>',
                            '</strong>',
                            esc_html( $customer_portal_error )
                        );
                    ?>
                </p>
            </div>
        <?php endif;
    }

    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
    */

    /**
     * Method that houses codes to be executed on init hook.
     *
     * @access public
     * @inherit ThirstyAffiliates\Interfaces\Initiable_Interface
     */
    public function initialize() {

        add_action( 'wp_ajax_ta_search_stripe_prices' , array( $this, 'ajax_search_stripe_prices' ) );
        add_action( 'wp_ajax_ta_stripe_add_product', array( $this, 'ajax_add_product' ) );
        add_action( 'wp_ajax_ta_dismiss_customer_portal_notice', array( $this, 'dismiss_customer_portal_notice' ) );
        add_action( 'admin_notices', array( $this, 'fee_admin_notice' ) );

    }

    /**
     * Execute model.
     *
     * @implements \ThirstyAffiliates\Interfaces\Model_Interface
     *
     * @access public
     */
    public function run() {

        add_filter( 'ta_affiliate_link_extended_data', array( $this , 'register_extend_data' ), 1);
        add_filter( 'ta_save_affiliate_link_post', array( $this, 'save_thirstypay_link_data' ) );
        add_filter( 'ta_filter_redirect_url', array( $this, 'populate_redirect_url' ), 10, 2 );
        add_action( 'ta_before_link_redirect', array( $this, 'redirect' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_customer_portal_notice_scripts' ) );
        add_action( 'admin_footer', array( $this, 'customer_portal_notice' ) );
        add_filter( 'ta_thirstypay_settings', array( $this, 'register_thirstypay_settings_options' ) );
        add_filter( 'ta_supported_field_types' , array( $this , 'register_thank_you_page_settings_field' ) );
        add_filter( 'ta_supported_field_types' , array( $this , 'register_default_currency_settings_field' ) );
        add_filter( 'pre_update_option_ta_thirstypay_thank_you_page_id', array( $this, 'auto_create_thank_you_page' ) );
        add_filter( 'ta_supported_field_types' , array( $this , 'register_customer_portal_settings_field' ) );
        add_action( 'parse_request', array( $this, 'customer_portal_redirect' ) );
        add_action( 'wp_enqueue_scripts' , array( $this, 'enqueue_invoice_style' ) );
        add_filter( 'the_content', array( $this, 'display_invoice' ) );
        add_filter( 'ta_link_insert_extend_data_attributes', array( $this, 'add_thirstypay_attribute' ), 10, 2);
        add_filter( 'display_post_states', array( $this, 'add_post_state_label' ), 10, 2 );
        add_action( 'ta_settings_errors', array( $this, 'display_customer_portal_error' ) );

    }

}
