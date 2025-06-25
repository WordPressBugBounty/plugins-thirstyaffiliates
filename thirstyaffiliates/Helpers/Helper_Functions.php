<?php
namespace ThirstyAffiliates\Helpers;

use ThirstyAffiliates\Abstracts\Abstract_Main_Plugin_Class;

use ThirstyAffiliates\Models\Affiliate_Link;

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Model that houses all the helper functions of the plugin.
 *
 * 3.0.0
 */
class Helper_Functions {

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
    */

    /**
     * Property that holds the single main instance of Helper_Functions.
     *
     * @since 3.0.0
     * @access private
     * @var Helper_Functions
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
     * Property that houses all the saved settings.
     *
     * @since 3.0.0
     * @access private
     */
    private $_settings = array();




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
     * @param Plugin_Constants 			 $constants Plugin constants object.
     */
    public function __construct( Abstract_Main_Plugin_Class $main_plugin , Plugin_Constants $constants ) {

        $this->_constants = $constants;

        $main_plugin->add_to_public_helpers( $this );

    }

    /**
     * Ensure that only one instance of this class is loaded or can be loaded ( Singleton Pattern ).
     *
     * @since 3.0.0
     * @access public
     *
     * @param Abstract_Main_Plugin_Class $main_plugin      Main plugin object.
     * @param Plugin_Constants 			 $constants Plugin constants object.
     * @return Helper_Functions
     */
    public static function get_instance( Abstract_Main_Plugin_Class $main_plugin , Plugin_Constants $constants ) {

        if ( !self::$_instance instanceof self )
            self::$_instance = new self( $main_plugin , $constants );

        return self::$_instance;

    }




    /*
    |--------------------------------------------------------------------------
    | Helper Functions
    |--------------------------------------------------------------------------
    */

    /**
     * Write data to plugin log file.
     *
     * @since 3.0.0
     * @access public
     *
     * @param mixed Data to log.
     */
    public function write_debug_log( $log )  {

        error_log( "\n[" . current_time( 'mysql' ) . "]\n" . $log . "\n--------------------------------------------------\n" , 3 , $this->_constants->LOGS_ROOT_PATH() . 'debug.log' );

    }

    /**
     * Check if current user is authorized to manage the plugin on the backend.
     *
     * @since 3.0.0
     * @access public
     *
     * @param \WP_User $user WP_User object.
     * @return boolean True if authorized, False otherwise.
     */
    public function current_user_authorized( $user = null ) {

        // Array of roles allowed to access/utilize the plugin
        $admin_roles = apply_filters( 'ucfw_admin_roles' , array( 'administrator' ) );

        if ( is_null( $user ) )
            $user = wp_get_current_user();

        if ( $user->ID )
            return count( array_intersect( ( array ) $user->roles , $admin_roles ) ) ? true : false;
        else
            return false;

    }

    /**
     * Returns the timezone string for a site, even if it's set to a UTC offset
     *
     * Duplicate of wp_timezone_string() for WP <5.3.
     *
     * @since 3.0.0
     * @access public
     *
     * @return string Valid PHP timezone string
     */
    public function get_site_current_timezone() {

        if ( function_exists( 'wp_timezone_string' ) ) {
            return wp_timezone_string();
        }

        $timezone_string = get_option( 'timezone_string' );

        if ( $timezone_string ) {
            return $timezone_string;
        }

        $offset  = (float) get_option( 'gmt_offset' );
        $hours   = (int) $offset;
        $minutes = ( $offset - $hours );

        $sign      = ( $offset < 0 ) ? '-' : '+';
        $abs_hour  = abs( $hours );
        $abs_mins  = abs( $minutes * 60 );
        $tz_offset = sprintf( '%s%02d:%02d', $sign, $abs_hour, $abs_mins );

        return $tz_offset;

    }

    /**
     * Convert UTC offset to timezone.
     *
     * @since 1.2.0
     * @access public
     * @deprecated 3.9.2
     *
     * @param float|int|string $utc_offset UTC offset.
     * @return string valid PHP timezone string
     */
    public function convert_utc_offset_to_timezone( $utc_offset ) {

        _deprecated_function(  'ThirstyAffiliates\Helpers\Helper_Functions::convert_utc_offset_to_timezone', '3.9.2');

        // adjust UTC offset from hours to seconds
        $utc_offset *= 3600;

        // attempt to guess the timezone string from the UTC offset
        if ( $timezone = timezone_name_from_abbr( '' , $utc_offset , 0 ) )
            return $timezone;

        // last try, guess timezone string manually
        $is_dst = date( 'I' );

        foreach ( timezone_abbreviations_list() as $abbr )
            foreach ( $abbr as $city )
                if ( $city[ 'dst' ] == $is_dst && $city[ 'offset' ] == $utc_offset && isset( $city[ 'timezone_id' ] ) )
                    return $city[ 'timezone_id' ];

        // fallback to UTC
        return 'UTC';

    }

    /**
     * Get all user roles.
     *
     * @since 3.0.0
     * @access public
     *
     * @global \WP_Roles $wp_roles Core class used to implement a user roles API.
     *
     * @return array Array of all site registered user roles. User role key as the key and value is user role text.
     */
    public function get_all_user_roles() {

        global $wp_roles;
        return $wp_roles->get_names();

    }

    /**
     * Get user IP address.
     *
     * @since 3.0.0
     * @since 3.3.2 Added condition to disable IP address collection (for GDRP compliance).
     * @access public
     *
     * @return string User's IP address.
     */
    public function get_user_ip_address() {

        $ip = '';

        if ( get_option( 'ta_disable_ip_address_collection' ) === 'yes' ) {
            return $ip;
        }

        if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
            $ip = sanitize_text_field( wp_unslash( $_SERVER['HTTP_CLIENT_IP'] ) );
        } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            $ip = explode( ',', sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) );
            $ip = trim( $ip[0] );
        } elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
            $ip = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
        }

        return apply_filters( 'ta_get_user_ip_address', $ip );
    }

    /**
     * Get the thirstylink slug set on the settings.
     *
     * @since 3.0.0
     * @access public
     *
     * @return string $link_prefix Thirstyling link prefix.
     */
    public function get_thirstylink_link_prefix() {

        $link_prefix = get_option( 'ta_link_prefix' , 'recommends' );

        if ( $link_prefix === 'custom' )
            $link_prefix = get_option( 'ta_link_prefix_custom' , 'recommends' );

        return $link_prefix ? $link_prefix : 'recommends';
    }

    /**
     * Get the affiliate link post default category slug.
     *
     * @since 3.0.0
     * @access public
     *
     * @param int   $link_id Affiliate Link ID.
     * @param array $terms   Affiliate link categories.
     * @return string Affiliate link default category slug.
     */
    public function get_default_category_slug( $link_id , $terms = array() ) {

        if ( ! is_array( $terms ) || empty( $terms ) )
            $terms = get_the_terms( $link_id , Plugin_Constants::AFFILIATE_LINKS_TAX );

        if ( is_wp_error( $terms ) || empty( $terms ) )
            return;

        $link_cat_obj = array_shift( $terms );

        return $link_cat_obj->slug;
    }

    /**
     * Search affiliate links query
     *
     * @since 3.0.0
     * @since 3.6 Add suport for Gutenberg.
     * @since 3.7 Add option to add list of affiliate link attached images for Gutenberg.
     * @access public
     *
     * @param string $keyword  Search keyword.
     * @param int    $paged    WP_Query paged value.
     * @param string $category Affiliate link category to search.
     * @param array  $exclude  List of posts to be excluded.
     * @param array  $is_gutenberg Toggle if searching for Gutenberg link picker.
     * @param array  $with_images  Toggle if search for affiliate links with images.
     * @return array List of affiliate link IDs.
     */
    public function search_affiliate_links_query( $keyword = '' , $paged = 1 , $category = '' , $exclude = array() , $is_gutenberg = false , $with_images = false ) {

        $args = array(
            'post_type'    => Plugin_Constants::AFFILIATE_LINKS_CPT,
            'post_status'  => 'publish',
            's'            => $keyword,
            'fields'       => 'ids',
            'paged'        => $paged,
            'post__not_in' => $exclude
        );

        if ( $paged == -1 ) {
            $args['posts_per_page'] = -1;
            unset( $args['paged'] );
        }

        if ( $category ) {

            $args[ 'tax_query' ] = array(
                array(
                    'taxonomy' => Plugin_Constants::AFFILIATE_LINKS_TAX,
                    'field'    => 'slug',
                    'terms'    => $category
                )
            );
        }

        if ( $is_gutenberg ) {
            unset( $args[ 'fields' ] );
            unset( $args[ 'paged' ] );

            $args[ 'posts_per_page' ] = 20;
        }

        if ( $with_images ) {

            $args[ 'meta_query' ] = array(
                array(
                    'key'     => Plugin_Constants::META_DATA_PREFIX . 'image_ids',
                    'compare' => 'EXISTS'
                )
            );
        }

        $query = new \WP_Query( $args );

        return $is_gutenberg ? array_map( function( $post ) use ( $with_images ) {

            $image_ids = get_post_meta( $post->ID , Plugin_Constants::META_DATA_PREFIX . 'image_ids' , true );

            return array(
                'id'    => $post->ID,
                'link'  => get_permalink( $post ),
                'title' => $post->post_title,
                'images' => $with_images ? $this->get_image_srcs( $image_ids ) : false,
                'thirstypay' => get_post_meta( $post->ID, '_ta_thirstypay_link', true ) === '1'
            );
        } , $query->posts ) : $query->posts;
    }

    /**
     * Get image and src attribute for list of image ids.
     *
     * @since 3.7
     * @access public
     *
     * @param array  $image_ids List of image ids.
     * @param string $size      Size of image to output.
     * @return array List of image ids with src attribute.
     */
    public function get_image_srcs( $image_ids , $size = 'thumbnail' ) {

        if ( ! is_array( $image_ids ) || empty( $image_ids ) ) return array();

        return array_filter(
            array_map( function( $id ) use ( $size ) {
                if ( ! is_numeric( $id ) ) {
                    return array(
                        'id' => null,
                        'src' => $id
                    );
                }

                $src = wp_get_attachment_image_src( $id , $size );

                if (is_array($src) && !empty($src)) {
                    return array(
                        'id' => $id,
                        'src' => $src[0]
                    );
                }

                return false;
            } , $image_ids)
        );
    }

    /**
     * Check if affiliate link needs to be uncloaked.
     *
     * @deprecated 3.2.0
     *
     * @since 3.0.0
     * @access public
     *
     * @param Affiliate_Link $thirstylink Thirsty affiliate link object.
     * @return boolean Sets to true when affiliate link needs to be uncloaked.
     */
    public function is_uncloak_link( $thirstylink ) {

        return $thirstylink->is( 'uncloak_link' );
    }

    /**
     * Error log with a trace.
     *
     * @since 3.0.0
     * @access public
     */
    public function ta_error_log( $msg ) {

        $trace  = debug_backtrace();
        $caller = array_shift( $trace );

        error_log( $msg . ' | Trace: ' . $caller[ 'file' ] . ' on line ' . $caller[ 'line' ] );

    }

    /**
     * Utility function that determines if a plugin is active or not.
     *
     * @since 3.0.0
     * @access public
     *
     * @param string $plugin_basename Plugin base name. Ex. woocommerce/woocommerce.php
     * @return boolean True if active, false otherwise.
     */
    public function is_plugin_active( $plugin_basename ) {

        // Makes sure the plugin is defined before trying to use it
        if ( !function_exists( 'is_plugin_active' ) )
            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

        return is_plugin_active( $plugin_basename );

    }

    /**
     * Send email.
     *
     * @since 3.0.0
     * @access public
     *
     * @param array  $recipients  Array of recipients emails.
     * @param string $subject     Email subject.
     * @param string $message     Email message.
     * @param array  $headers     Array of email headers.
     * @param array  $attachments Array of email attachments.
     * @return boolean True if email sending is triggered, note it does not mean that the email was received, it just denotes that the email sending is triggered. False if email sending is not triggered.
     */
    public function send_email( $recipients , $subject , $message , $headers = array() , $attachments = array() ) {

        $from_name  = apply_filters( 'ta_email_from_name' , get_bloginfo( 'name' ) );
        $from_email = apply_filters( 'ta_email_from_email' , get_option( 'admin_email' ) );

        $headers[] = 'From: ' . $from_name  . ' <' . $from_email . '>';
        $headers[] = 'Content-Type: text/html; charset=' . get_option( 'blog_charset' );

        return wp_mail( $recipients , $subject , $message , $headers , $attachments );

    }

    /**
     * Get Affiliate_Link data object.
     *
     * @since 3.0.0
     * @access public
     *
     * @param int $id Affiliate_Link post ID.
     * @return Affiliate_Link Affiliate Link object.
     */
    public function get_affiliate_link( $id = 0 ) {

        return new Affiliate_Link( $id );
    }

    /**
     * Retrieve all categories as an option array list.
     *
     * @since 3.0.0
     * @since 3.4.0 Add support for slug options.
     * @access public
     *
     * @return array List of category options.
     */
    public function get_all_category_as_options( $use_slug = false ) {

        $options = array();

        $categories = get_terms( array(
            'taxonomy'   => Plugin_Constants::AFFILIATE_LINKS_TAX,
            'hide_empty' => false,
        ) );

        if ( ! is_wp_error( $categories ) ) {

            foreach( $categories as $category ) {
                $key             = $use_slug ? $category->slug : $category->term_id;
                $options[ $key ] = $category->name;
            }

        } else {

            // TODO: Handle error

        }

        return $options;
    }

    /**
     * Set default term when affiliate link is saved.
     *
     * @since 3.2.0
     * @access public
     *
     * @param int $post_id Affiliate link post ID.
     */
    public function save_default_affiliate_link_category( $post_id ) {

        $default_category = Plugin_Constants::DEFAULT_LINK_CATEGORY;
        $taxonomy_slug    = Plugin_Constants::AFFILIATE_LINKS_TAX;

        if ( get_option( 'ta_disable_cat_auto_select' ) == 'yes' || get_the_terms( $post_id , $taxonomy_slug ) )
            return;

        // create the default term if it doesn't exist
        if ( ! term_exists( $default_category , $taxonomy_slug ) )
            wp_insert_term( $default_category , $taxonomy_slug );

        $default_term = get_term_by( 'name' , $default_category , $taxonomy_slug );

        wp_set_post_terms( $post_id , $default_term->term_id , $taxonomy_slug );
    }

    /**
     * This function is an alias for WP get_option(), but will return the default value if option value is empty or invalid.
     *
     * @since 3.2.0
     * @access public
     *
     * @param string $option_name   Name of the option of value to fetch.
     * @param mixed  $default_value Defaut option value.
     * @return mixed Option value.
     */
    public function get_option( $option_name , $default_value = '' ) {

        $option_value = get_option( $option_name , $default_value );

        return ( gettype( $option_value ) === gettype( $default_value ) && $option_value ) ? $option_value : $default_value;
    }

    /**
     * Get blocked bots from settings or default value.
     *
     * @since 3.3.2
     * @access public
     *
     * @return array List of blocked bots.
     */
    public function get_blocked_bots() {

        $bots_string = $this->get_option( 'ta_blocked_bots' , Plugin_Constants::DEFAULT_BLOCKED_BOTS );
        return str_replace( ',' , '|' , $bots_string );
    }

    /**
     * Check if useragent is bot.
     *
     * @since 3.3.3
     * @access public
     *
     * @return bool True if detected as bot, otherwise false.
     */
    public function is_user_agent_bot() {

        $user_agent = isset( $_SERVER[ 'HTTP_USER_AGENT' ] ) ? sanitize_text_field( wp_unslash( ( $_SERVER[ 'HTTP_USER_AGENT' ] ) ) ) : '';
        $user_agent = strtolower( $user_agent );
        $bots       = apply_filters( 'ta_useragent_bots_phrase_list' , $this->get_blocked_bots() );
        $pattern    = '/' . $bots . '/i';

        return preg_match( $pattern , $user_agent );
    }

    /**
     * Get screen ID.
     *
     * @since 3.3.3
     * @access public
     */
    public function get_screen_id( $object_id ) {

        $screen_id = null;

        if ( isset( $_GET[ 'post_type' ] ) && $_GET[ 'post_type' ] == Plugin_Constants::AFFILIATE_LINKS_CPT ) {

            if ( isset( $_GET[ 'taxonomy' ] ) )
                $screen_id = 'edit-' . sanitize_text_field( wp_unslash( $_GET[ 'taxonomy' ] ) );
            elseif ( isset( $_GET[ 'page' ] ) )
                $screen_id = 'thirstylink_page_' . sanitize_text_field( wp_unslash( $_GET[ 'page' ] ) );
            else
                $screen_id = 'edit-thirstylink';

        } elseif ( $object_id && get_post_type( $object_id ) === Plugin_Constants::AFFILIATE_LINKS_CPT )
            $screen_id = 'thirstylink';

        return apply_filters( 'ta_get_screen_id' , $screen_id );
    }

    /**
     * Get visistor's browser and device.
     *
     * @since 3.4.0
     * @access public
     *
     * @return string Browser and device.
     */
    public function get_visitor_browser_device() {

        if ( get_option( 'ta_disable_browser_device_collection' ) === 'yes' || ! ini_get( "browscap" ) )
            return;

        $browser = get_browser( null );
        return is_object( $browser ) ? $browser->browser . '|' . $browser->platform . '|' . $browser->device_type : '';
    }

    /**
     * Check if a page builder is active or not. Currently supports: Beaver Builder and Elementor.
     *
     * @since 3.4.0
     * @access public
     */
    public function is_page_builder_active() {

        $is_bb_active = class_exists( 'FLBuilderModel' ) && \FLBuilderModel::is_builder_active();
        $elementor    = ( class_exists( 'Elementor\Core\Editor\Editor' ) || class_exists( 'Elementor\Editor' ) ) && isset( $_GET[ 'action' ] ) && $_GET[ 'action' ] == 'elementor';

        return $is_bb_active || $elementor;
    }

    /**
     * Get string in between two strins.
     * Source: http://www.justin-cook.com/2006/03/31/php-parse-a-string-between-two-strings/
     *
     * @since 3.6
     */
    public function get_string_between( $string , $start , $end ) {

        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    /**
     * Get the capability required to access an admin interface.
     *
     * @param string $interface The key of the interface.
     * @param string $default The default capability to return if the interface capability is not set.
     * @return string
     */
    public function get_capability_for_interface( $interface, $default ) {
        $option = $this->get_option( 'tap_plugin_visibility_admin_interfaces', array() );

        if ( is_array( $option ) && ! empty( $option[ $interface ] ) ) {
            return $option[ $interface ];
        }

        return $default;
    }

    public function include_pluggables($function_name) {
        if(!function_exists($function_name)) {
          require_once(ABSPATH.WPINC.'/pluggable.php');
        }
    }

    public function get_currentuserinfo() {
        $this->include_pluggables('wp_get_current_user');
        $current_user = wp_get_current_user();

        if(isset($current_user->ID) && $current_user->ID > 0) {
          return $current_user;
        }
        else {
          return false;
        }
    }

    public function is_user_logged_in() {
        $this->include_pluggables('is_user_logged_in');
        return is_user_logged_in();
    }

    public function is_logged_in_and_an_admin() {
        return ($this->is_user_logged_in() and $this->is_admin());
    }

    public function is_logged_in_and_a_subscriber() {
        return ($this->is_user_logged_in() and $this->is_subscriber());
    }

    public function current_user_can($role) {
        $this->include_pluggables('wp_get_current_user');
        return current_user_can($role);
    }

    public function is_admin() {
        return $this->current_user_can('manage_options');
    }

    public function is_subscriber() {
        return $this->current_user_can('subscriber');
    }

    public function is_post_request() {
        if(isset($_SERVER['REQUEST_METHOD'])) {
          return (strtolower($_SERVER['REQUEST_METHOD']) == 'post');
        }
        else {
          return (isset($_POST) && !empty($_POST));
        }
    }

    public function site_domain() {
        return preg_replace('#^https?://(www\.)?([^\?\/]*)#', '$2', get_option('home'));
    }

    /**
     * Get the HTML for the 'NEW' badge
     *
     * @return string
     */
    public function new_badge() {
        return sprintf( '<span class="ta-new-badge">%s</span>', esc_html__( 'NEW', 'thirstyaffiliates' ) );
    }

    /**
     * Is the link with the given ID a ThirstyPay link?
     *
     * @param int $link_id
     * @return bool
     */
    public function is_thirstypay_link( $link_id ) {
        $link = new Affiliate_Link( $link_id );

        return $link->get_prop( 'thirstypay_link' ) == '1';
    }

    /**
     * Render a Stripe line item and return the HTML.
     *
     * @param array $line_item
     * @return string
     */
    public function render_line_item( array $line_item ) {
        $price = $line_item['price'];

        ob_start();
        ?>
        <div class="ta-stripe-line-item-box" data-line-item="<?php echo esc_attr( wp_json_encode( $line_item ) ); ?>">
            <div class="ta-stripe-price-image">
                <?php if ( isset( $price['product']['images'][0] ) ) : ?>
                    <div class="ta-stripe-price-image-url" style="background-image: url('<?php echo esc_url_raw($price['product']['images'][0]); ?>');"></div>
                <?php else : ?>
                    <div class="ta-stripe-price-image-default">
                        <svg aria-hidden="true" height="16" width="16" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path d="M13.788 3.119a1.04 1.04 0 0 1-.31.283L8.5 6.362a.97.97 0 0 1-.998 0l-4.98-2.96a1.04 1.04 0 0 1-.309-.283L6.99.279a1.97 1.97 0 0 1 2.02 0zm1.194 1.647c.012.09.018.182.018.274v5.92c0 .743-.385 1.43-1.01 1.802l-4.98 2.96a1.97 1.97 0 0 1-2.02 0l-4.98-2.96A2.092 2.092 0 0 1 1 10.96V5.04c0-.092.006-.184.018-.274.147.133.308.252.481.355l4.98 2.96a2.97 2.97 0 0 0 3.042 0l4.98-2.96c.173-.103.334-.222.481-.355z" fill-rule="evenodd"></path></svg>
                    </div>
                <?php endif; ?>
            </div>
            <div class="ta-stripe-product-name-price">
                <div class="ta-stripe-product-name"><?php echo esc_html( $price['product']['name'] ); ?></div>
                <div class="ta-stripe-product-price"><?php echo esc_html( $this->format_price( $price ) ); ?></div>
            </div>
            <div class="ta-stripe-line-item-x">
                <svg aria-hidden="true" height="12" width="12" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path d="m8 6.585 4.593-4.592a1 1 0 0 1 1.415 1.416L9.417 8l4.591 4.591a1 1 0 0 1-1.415 1.416L8 9.415l-4.592 4.592a1 1 0 0 1-1.416-1.416L6.584 8l-4.59-4.591a1 1 0 1 1 1.415-1.416z" fill-rule="evenodd"></path></svg>
            </div>
        </div>
        <?php

        return ob_get_clean();
    }

    /**
     * Format the pricing terms of Stripe Price.
     *
     * @param array $price
     * @return string
     */
    public function format_price( $price ) {
        $interval = '';

        if ( $price['recurring'] ) {
            $interval = $price['recurring']['interval'];

            if ( (int) $price['recurring']['interval_count'] !== 1 ) {
                $interval = sprintf( '%d %ss', $price['recurring']['interval_count'], $interval );
            }

            $interval = ' / ' . $interval;
        }

        return sprintf(
            '%s %s%s',
            strtoupper( $price['currency'] ),
            $this->format_unit_amount( $price['unit_amount'], $price['currency'] ),
            $interval
        );
    }

    /**
     * Format a Stripe unit_amount.
     *
     * @param int $amount
     * @param string $currency
     * @return string
     */
    public function format_unit_amount( $amount, $currency ) {
        $amount = (float) $amount;

        if ( $this->is_zero_decimal_currency( $currency ) ) {
            return number_format_i18n( $amount );
        }

        return number_format_i18n( $amount / 100, 2 );
    }

    /**
     * Is the currency a zero-decimal currency?
     *
     * @see https://stripe.com/docs/currencies#zero-decimal
     * @param string $currency
     * @return bool
     */
    public function is_zero_decimal_currency( $currency ) {
        $zero_decimal_currencies = apply_filters( 'ta_stripe_zero_decimal_currencies', array(
            'BIF', 'CLP', 'DJF', 'GNF', 'JPY', 'KMF', 'KRW', 'MGA', 'PYG', 'RWF', 'UGX', 'VND', 'VUV', 'XAF', 'XOF', 'XPF',
        ) );

        return in_array( strtoupper( $currency ), $zero_decimal_currencies, true );
    }

    /**
     * Format the given amount and currency
     *
     * @param int|float $amount
     * @param string $currency
     * @return string
     */
    public function format_currency( $amount, $currency ) {
        return sprintf(
            '%s %s',
            strtoupper( $currency ),
            $this->format_unit_amount( $amount, $currency )
        );
    }

    /**
     * Get the list of countries.
     *
     * @return array
     */
    public function countries() {
        $countries = require $this->_constants->_I18N_ROOT_PATH . 'countries.php';

        return apply_filters( 'ta_countries', $countries );
    }

    /**
     * Get the list of shipping countries.
     *
     * @return array
     */
    public function shipping_countries() {
        $countries = $this->countries();
        $unsupported = array( 'AS', 'CX', 'CC', 'CU', 'HM', 'IR', 'KP', 'MH', 'FM', 'NF', 'MP', 'PW', 'SD', 'SY', 'UM', 'VI' );

        foreach ( $unsupported as $key ) {
            unset( $countries[ $key ] );
        }

        return apply_filters( 'ta_stripe_shipping_countries', $countries );
    }

    /**
     * Convert the given amount to zero decimal.
     *
     * @param float|int|string $amount
     * @param string $currency
     * @return int
     */
    public function to_zero_decimal_amount( $amount, $currency ) {
        if ( $this->is_zero_decimal_currency( $currency ) ) {
            return (int) $amount;
        }

        return (int) ( $amount * 100 );
    }

    /**
     * Get the list of currencies.
     *
     * @return array
     */
    public function currencies() {
        $countries = require $this->_constants->_I18N_ROOT_PATH . 'currencies.php';

        return apply_filters( 'ta_currencies', $countries );
    }

    /**
     * Is the current request a GET request?
     *
     * @return bool
     */
    public function is_get_request() {
        return isset( $_SERVER['REQUEST_METHOD'] ) && strtoupper( $_SERVER['REQUEST_METHOD'] ) == 'GET';
    }

    /**
     * Display an info tooltip.
     *
     * @param string $id
     * @param string $title
     * @param string $info
     */
    public function info_tooltip( $id, $title, $info ) {
        ?>
        <span id="ta-tooltip-<?php echo esc_attr( $id ); ?>" class="ta-tooltip">
              <span class="ta-tooltip-icon"></span>
              <span class="ta-data-title ta-hidden"><?php echo $title; ?></span>
              <span class="ta-data-info ta-hidden"><?php echo $info; ?></span>
        </span>
        <?php
    }

    /**
     * Get a value from an array, allowing dot notation.
     *
     * @param array $array
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function array_get( $array, $key = null, $default = null ) {
        if ( is_null( $key ) ) {
            return $array;
        }

        if ( isset( $array[ $key ] ) ) {
            return $array[ $key ];
        }

        foreach ( explode( '.', $key ) as $segment ) {
            if ( ! is_array( $array ) || ! array_key_exists( $segment, $array ) ) {
                return $default;
            }

            $array = $array[ $segment ];
        }

        return $array;
    }

    /**
     * Get a value from the portal configuration or return the default if no portal is saved.
     *
     * @param string $key
     * @return mixed
     */
    public function get_portal_config_value( $key ) {
        $portal = get_option( 'ta_stripe_customer_portal' );

        $defaults = array(
            'business_profile' => array(
                'headline' => __( 'Manage your Payments', 'thirstyaffiliates' ),
                'privacy_policy_url' => '',
                'terms_of_service_url' => '',
            ),
            'default_return_url' => home_url( '/' ),
            'features' => array(
                'customer_update' => array(
                    'allowed_updates' => array( 'name', 'email', 'address', 'shipping', 'phone' ),
                    'enabled' => true,
                ),
                'invoice_history' => array(
                    'enabled' => true,
                ),
                'payment_method_update' => array(
                    'enabled' => true,
                ),
                'subscription_cancel' => array(
                    'enabled' => true,
                ),
            ),
        );

        $default = $this->array_get( $defaults, $key );

        if ( is_array( $portal ) ) {
            return $this->array_get( $portal, $key, $default );
        }

        return $default;
    }

    /**
     * Get the name of the page that will redirect to the customer portal.
     *
     * @return string
     */
    public function get_customer_portal_page_name() {
        return apply_filters( 'ta_customer_portal_page_name', 'ta-customer-portal' );
    }

    /**
     * Get the array of all pages
     *
     * @return \stdClass[]
     */
    public function get_pages() {
        global $wpdb;

        $orderby_allowed = array( 'ID', 'post_title', 'post_date' );
        $orderby = apply_filters( 'ta_page_orderby', 'ID' );
        $orderby = in_array( $orderby, $orderby_allowed ) ? $orderby : 'ID';
        $query = "SELECT * FROM {$wpdb->posts} WHERE post_status = %s AND post_type = %s ORDER BY $orderby";
        $query = $wpdb->prepare( $query, "publish", "page" );
        $results = $wpdb->get_results( $query );

        if ( $results ) {
            return $results;
        } else {
            return array();
        }
    }

    /**
     * Auto-add a new WordPress page with the given name and content
     *
     * @param string $page_name
     * @param string $content
     * @return int|\WP_Error
     */
    public function auto_add_page( $page_name, $content = '' ) {
        return wp_insert_post( array(
            'post_title' => $page_name,
            'post_content' => $content,
            'post_type' => 'page',
            'post_status' => 'publish',
            'comment_status' => 'closed',
        ) );
    }

    /**
     * Render a select menu to choose a WP page
     *
     * @param string $field_name
     * @param string|int $selected_page_id
     * @param bool $auto_page
     * @param false|string $empty_option
     */
    public function wp_pages_dropdown( $field_name, $selected_page_id = '', $auto_page = false, $empty_option = false ) {
        ?>
        <select name="<?php echo esc_attr( $field_name ); ?>" id="<?php echo esc_attr( $field_name ); ?>">
            <?php if ( $empty_option ) : ?>
                <option value=""><?php echo esc_html( $empty_option ); ?></option>
            <?php endif ?>
            <?php if ( $auto_page ) : ?>
                <option value="auto_create_page"><?php esc_html_e( '- Auto Create New Page -', 'thirstyaffiliates' ); ?></option>
            <?php endif; ?>
            <?php foreach ( $this->get_pages() as $page ) : ?>
                <option value="<?php echo esc_attr( $page->ID ); ?>" <?php selected( $selected_page_id, $page->ID ); ?>><?php echo esc_html( $page->post_title ); ?></option>
            <?php endforeach; ?>
        </select>
        <?php if ( is_numeric( $selected_page_id ) ) : ?>
            <?php $permalink = get_permalink( $selected_page_id ); ?>
            <?php if ( $permalink ) : ?>
                <a href="<?php echo esc_url( admin_url( "post.php?post={$selected_page_id}&action=edit" ) ); ?>" target="_blank" class="button"><?php esc_html_e( 'Edit', 'thirstyaffiliates' ); ?></a>
                <a href="<?php echo esc_url( $permalink ); ?>" target="_blank" class="button"><?php esc_html_e( 'View', 'thirstyaffiliates' ); ?></a>
            <?php endif; ?>
        <?php endif;
    }

    /**
     * Decrypts an encrypted string.
     *
     * @param string $encrypted_string
     * @param string $encoding
     * @return false|string
     */
    public static function decrypt_string( $encrypted_string, $encoding = 'base64' ) {
        if ( version_compare( PHP_VERSION, '7.1.0', '<' ) ) {
            return false;
        }

        if ( $encrypted_string != null ) {
            $encrypted_string = $encoding == "hex" ? hex2bin( $encrypted_string ) : ( $encoding == "base64" ? base64_decode( $encrypted_string ) : $encrypted_string );
            $keysalt = substr( $encrypted_string, 0, 16 );
            $key = hash_pbkdf2( "sha512", 'thirstyaffiliates', $keysalt, 20000, 32, true );
            $ivlength = openssl_cipher_iv_length( "aes-256-gcm" );
            $iv = substr( $encrypted_string, 16, $ivlength );
            $tag = substr( $encrypted_string, - 16 );

            return openssl_decrypt( substr( $encrypted_string, 16 + $ivlength, - 16 ), "aes-256-gcm", $key, OPENSSL_RAW_DATA, $iv, $tag );
        }

        return '';
    }

    /**
     * Get the site domain
     *
     * @return string
     */
    public function get_site_domain() {
        return preg_replace( '#^https?://(www\.)?([^\?\/]*)#', '$2', home_url() );
    }

    /**
     * Is the connection to Stripe active?
     *
     * @return bool
     */
    public function is_stripe_connection_active() {
        if ( defined( 'TA_STRIPE_TEST_MODE' ) && TA_STRIPE_TEST_MODE ) {
            return ! empty( get_option( 'ta_stripe_test_secret_key' ) );
        }

        return ! empty( get_option( 'ta_stripe_live_secret_key' ) );
    }
}
