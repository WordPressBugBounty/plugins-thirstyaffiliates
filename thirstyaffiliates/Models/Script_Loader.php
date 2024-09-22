<?php
namespace ThirstyAffiliates\Models;

use ThirstyAffiliates\Abstracts\Abstract_Main_Plugin_Class;

use ThirstyAffiliates\Interfaces\Model_Interface;

use ThirstyAffiliates\Helpers\Plugin_Constants;
use ThirstyAffiliates\Helpers\Helper_Functions;
use ThirstyAffiliates\Helpers\Onboarding_Helper;

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Script_Loader implements Model_Interface {

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
    */

    /**
     * Property that holds the single main instance of Bootstrap.
     *
     * @since 3.0.0
     * @access private
     * @var Bootstrap
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

    /**
     * Property that houses the Guided_Tour model.
     *
     * @since 3.0.0
     * @access private
     * @var Guided_Tour
     */
    private $_guided_tour;




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
    public function __construct( Abstract_Main_Plugin_Class $main_plugin , Plugin_Constants $constants , Helper_Functions $helper_functions , Guided_Tour $guided_tour ) {

        $this->_constants        = $constants;
        $this->_helper_functions = $helper_functions;
        $this->_guided_tour      = $guided_tour;

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
     * @return Bootstrap
     */
    public static function get_instance( Abstract_Main_Plugin_Class $main_plugin , Plugin_Constants $constants , Helper_Functions $helper_functions , Guided_Tour $guided_tour ) {

        if ( !self::$_instance instanceof self )
            self::$_instance = new self( $main_plugin , $constants , $helper_functions , $guided_tour );

        return self::$_instance;

    }

    /**
     * Load backend js and css scripts.
     *
     * @since 3.0.0
     * @since 3.4.0 Moved the code to load scripts for the wp editor to its own function.
     * @access public
     *
     * @global \WP_Post $post WP_Post object of the current screen.
     *
     * @param string $handle Unique identifier of the current backend page.
     */
    public function load_backend_scripts( $handle ) {

        global $post;

        $screen = get_current_screen();

        $post_type = get_post_type();
        if ( !$post_type && isset( $_GET[ 'post_type' ] ) ) {
            $post_type = sanitize_text_field( wp_unslash( $_GET['post_type'] ) );
        }

        // Global admin styles and scripts.
        wp_enqueue_style( 'ta-admin-global', $this->_constants->CSS_ROOT_URL() . 'admin/ta-admin-global.css', array(), Plugin_Constants::VERSION );
        wp_enqueue_script( 'ta-admin-global', $this->_constants->JS_ROOT_URL() . '/app/ta-admin-global.js', array( 'jquery' ), Plugin_Constants::VERSION );

        wp_localize_script( 'ta-admin-global', 'TAAdminGlobal', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'dismiss_notice_nonce' => wp_create_nonce( 'ta_dismiss_notice' ),
        ) );

        // Link picker styles and scripts.
        if ( is_admin() && current_user_can( 'edit_posts' ) && ! in_array( $screen->base , array( 'customize' ) ) ) {

            $this->load_wp_editor_scripts();

            if ( in_array( $screen->base , array( 'widgets' ) ) ) {
                wp_enqueue_script( 'ta_tinymce_widget_editor', $this->_constants->JS_ROOT_URL() . 'lib/thirstymce/widget-editor.js', array( 'jquery' , 'thickbox' ), Plugin_Constants::VERSION , true );
                wp_localize_script( 'ta_tinymce_widget_editor' , 'ta_widget_editor_url' , $this->_constants->JS_ROOT_URL() . 'lib/thirstymce' );
            }
        }

        if ( $screen->base === 'thirstylink_page_thirsty-settings' ) {

            // Settings

            wp_enqueue_style( 'selectize' , $this->_constants->JS_ROOT_URL() . 'lib/selectize/selectize.default.css' , array() , Plugin_Constants::VERSION , 'all' );
            wp_enqueue_style( 'ta_settings_css' , $this->_constants->CSS_ROOT_URL() . 'admin/ta-settings.css' , array() , Plugin_Constants::VERSION , 'all' );
            wp_enqueue_script( 'selectize', $this->_constants->JS_ROOT_URL() . 'lib/selectize/selectize.min.js' , array( 'jquery' , 'jquery-ui-core' , 'jquery-ui-sortable' ) , Plugin_Constants::VERSION , true );
            wp_enqueue_script( 'ta_settings_js', $this->_constants->JS_ROOT_URL() . 'app/ta-settings.js', array( 'jquery' ), Plugin_Constants::VERSION , true );
            wp_localize_script( 'ta_settings_js' , 'ta_settings_var' , array(
                'i18n_custom_link_prefix_valid_val' => __( 'Please provide a value for "Custom Link Prefix" option' , 'thirstyaffiliates' )
            ) );

            if ( isset( $_GET[ 'tab' ] ) && $_GET[ 'tab' ] === 'ta_import_export_settings' ) {

                // Import/Export

                wp_enqueue_style( 'ta_import_export_css' , $this->_constants->JS_ROOT_URL() . 'app/import_export/dist/import-export.css' , array() , 'all' );

                wp_enqueue_script( 'ta_import_export_js' , $this->_constants->JS_ROOT_URL() . 'app/import_export/dist/import-export.js' , array() , true );
                wp_localize_script( 'ta_import_export_js' , 'import_export_var' , array(
                    'please_input_settings_string' => __( 'Please input settings string' , 'thirstyaffiliates' ),
                    'settings_string_copied'       => __( 'Settings string copied' , 'thirstyaffiliates' ),
                    'failed_copy_settings_string'  => __( 'Failed to copy settings string' , 'thirstyaffiliates' ),
                    'import_settings_nonce' => wp_create_nonce( 'ta_import_settings' ),
                ) );

            } elseif ( isset( $_GET[ 'tab' ] ) && $_GET[ 'tab' ] === 'ta_help_settings' ) {

                // Migration

                wp_enqueue_style( 'ta_migration_css' , $this->_constants->JS_ROOT_URL() . 'app/migration/dist/migration.css' , array() , 'all' );

                wp_enqueue_script( 'ta_migration_js' , $this->_constants->JS_ROOT_URL() . 'app/migration/dist/migration.js' , array() , true );
                wp_localize_script( 'ta_migration_js' , 'migration_var' , array(
                    'i18n_migration_failed' => __( 'Failed to do data migration' , 'thirstyaffiliates' ),
                    'i18n_confirm_migration' => __( 'Are you sure you want to migrate your ThirstyAffiliates data to version 3 format?' , 'thirstyaffiliates' ),
                    'migration_nonce' => wp_create_nonce( 'ta_migrate_old_plugin_data' )
                ) );

            } elseif ( isset( $_GET[ 'tab' ] ) && $_GET[ 'tab' ] === 'ta_thirstypay_settings' ) {

                wp_enqueue_script( 'clipboard' );
                wp_enqueue_style( 'jquery_tiptip' , $this->_constants->CSS_ROOT_URL() . 'lib/jquery-tiptip/jquery-tiptip.css' , array() , Plugin_Constants::VERSION , 'all' );
                wp_enqueue_script( 'jquery_tiptip' , $this->_constants->JS_ROOT_URL() . 'lib/jquery-tiptip/jquery.tipTip.min.js' , array() , Plugin_Constants::VERSION , true );

            }

        } elseif ( $screen->base == 'post' && $post_type == Plugin_Constants::AFFILIATE_LINKS_CPT ) {

            // Single Affiliate Link Edit Page

            wp_enqueue_style( 'thickbox' );
            wp_enqueue_style( 'jquery_tiptip' , $this->_constants->CSS_ROOT_URL() . 'lib/jquery-tiptip/jquery-tiptip.css' , array() , Plugin_Constants::VERSION , 'all' );
            wp_enqueue_style( 'ta_affiliate-link-page_css' , $this->_constants->JS_ROOT_URL() . 'app/affiliate_link_page/dist/affiliate-link-page.css' , array() , Plugin_Constants::VERSION , 'all' );

            wp_enqueue_media();
            wp_dequeue_script( 'autosave' ); // Disable autosave
            wp_enqueue_script( 'thickbox' , true );
            wp_enqueue_script( 'jquery_tiptip' , $this->_constants->JS_ROOT_URL() . 'lib/jquery-tiptip/jquery.tipTip.min.js' , array() , Plugin_Constants::VERSION , true );
            wp_enqueue_script( 'ta_affiliate-link-page_js' , $this->_constants->JS_ROOT_URL() . 'app/affiliate_link_page/dist/affiliate-link-page.js' , array() , Plugin_Constants::VERSION , true );

            wp_localize_script( 'ta_affiliate-link-page_js', 'ta_affiliate_link_page_params', array(
                'insert_external_image_nonce' => wp_create_nonce( 'ta_insert_external_image' ),
                'add_attachments_nonce' => wp_create_nonce( 'ta_add_attachments_to_affiliate_link' ),
                'remove_attachments_nonce' => wp_create_nonce( 'ta_remove_attachments_from_affiliate_link' ),
                'get_category_slug_nonce' => wp_create_nonce( 'ta_get_category_slug' ),
                'link_inserted_scanner_nonce' => wp_create_nonce( 'ta_link_inserted_scanner' ),
            ) );

            if ( ! empty( $_REQUEST['thirstypay'] ) || ( $post instanceof \WP_Post && $this->_helper_functions->is_thirstypay_link( $post->ID ) ) ) {

                wp_enqueue_style( 'select2', $this->_constants->JS_ROOT_URL() . 'lib/select2/select2.min.css', array(), '4.0.13' );
                wp_enqueue_style( 'select2-ta-theme', $this->_constants->CSS_ROOT_URL() . 'admin/select2-ta-theme.css', array(), Plugin_Constants::VERSION );
                wp_enqueue_style( 'magnific-popup', $this->_constants->JS_ROOT_URL() . 'lib/magnific-popup/magnific-popup.min.css', array(), '1.1.0' );
                wp_enqueue_style( 'ta-thirstypay-stripe', $this->_constants->CSS_ROOT_URL() . 'admin/thirstypay-stripe.css', array(), Plugin_Constants::VERSION );

                wp_enqueue_script( 'select2', $this->_constants->JS_ROOT_URL() . 'lib/select2/select2.min.js', array( 'jquery' ), '4.0.13', true );
                wp_enqueue_script( 'magnific-popup', $this->_constants->JS_ROOT_URL() . 'lib/magnific-popup/jquery.magnific-popup.min.js', array( 'jquery' ), '1.1.0', true );
                wp_enqueue_script( 'ta-thirstypay-stripe', $this->_constants->JS_ROOT_URL() . 'app/thirstypay-stripe.js', array( 'jquery' ), Plugin_Constants::VERSION, true );

                wp_localize_script( 'ta-thirstypay-stripe', 'ta_thirstypay_stripe_params', array(
                    'ajax_url' => admin_url( 'admin-ajax.php' ),
                    'find_or_add_product' => __( 'Find or add a product...', 'thirstyaffiliates' ),
                    'search_stripe_prices_nonce' => wp_create_nonce( 'ta_search_stripe_prices' ),
                    'select_shipping_countries' => __( 'Select shipping countries', 'thirstyaffiliates' ) . '*',
                    'add_product_nonce' => wp_create_nonce( 'ta_stripe_add_product' ),
                    'this_field_is_required' => __( 'This field is required', 'thirstyaffiliates' ),
                ) );

            }

        } elseif ( $screen->id == 'thirstylink_page_thirsty-reports' ) {

            wp_enqueue_style( 'jquery-ui-styles' , $this->_constants->CSS_ROOT_URL() . 'lib/jquery-ui/jquery-ui.min.css' , array() , '1.11.4', 'all' );
            wp_enqueue_style( 'ta_reports_css' , $this->_constants->CSS_ROOT_URL() . 'admin/ta-reports.css' , array( 'jquery-ui-styles' ) , Plugin_Constants::VERSION , 'all' );

            wp_enqueue_script( 'jquery-ui-core' );
            wp_enqueue_script( 'jquery-ui-datepicker' );
            wp_enqueue_script( 'ta_reports_js', $this->_constants->JS_ROOT_URL() . 'app/ta-reports.js', array( 'jquery' , 'jquery-ui-core' , 'jquery-ui-datepicker' ), Plugin_Constants::VERSION , true );

            if ( ! isset( $_GET[ 'tab' ] ) || $_GET[ 'tab' ] == 'link_performance' ) {

                wp_enqueue_script( 'jquery-flot', $this->_constants->JS_ROOT_URL() . 'lib/flot/jquery.flot.min.js', array( 'jquery' ), Plugin_Constants::VERSION , true );
                wp_enqueue_script( 'jquery-flot-time', $this->_constants->JS_ROOT_URL() . 'lib/flot/jquery.flot.time.min.js', array( 'jquery' , 'jquery-flot' ), Plugin_Constants::VERSION , true );

            }

        } elseif ( $screen->id === 'edit-thirstylink' ) {

            wp_enqueue_style( 'ta_affiliate_link_list' , $this->_constants->CSS_ROOT_URL() . 'admin/ta-affiliate-link-list.css' , array() , Plugin_Constants::VERSION , 'all' );
            wp_enqueue_script( 'ta_affiliate_link_list', $this->_constants->JS_ROOT_URL() . 'app/ta-affiliate-link-list.js', array( 'jquery' ), Plugin_Constants::VERSION, true );
        }

        if ( get_option( 'ta_guided_tour_status' ) == 'open' && array_key_exists( $screen->id , $this->_guided_tour->get_screens() ) ) {

            wp_enqueue_style( 'ta-guided-tour_css' , $this->_constants->CSS_ROOT_URL() . 'admin/ta-guided-tour.css' , array( 'wp-pointer' ) , Plugin_Constants::VERSION , 'all' );
            wp_enqueue_script( 'ta-guided-tour_js' , $this->_constants->JS_ROOT_URL() . 'app/ta-guided-tour.js' , array( 'wp-pointer' , 'thickbox' ) , Plugin_Constants::VERSION , true );

            wp_localize_script( 'ta-guided-tour_js',
                'ta_guided_tour_params',
                array(
                    'actions'  => array( 'close_tour' => 'ta_close_guided_tour' ),
                    'nonces'   => array( 'close_tour' => wp_create_nonce( 'ta-close-guided-tour' ) ),
                    'screen'   => $this->_guided_tour->get_current_screen(),
                    'screenid' => $screen->id,
                    'height'   => 640,
                    'width'    => 640,
                    'texts'    => array(
                                     'btn_prev_tour'  => __( 'Previous', 'thirstyaffiliates' ),
                                     'btn_next_tour'  => __( 'Next', 'thirstyaffiliates' ),
                                     'btn_close_tour' => __( 'Close', 'thirstyaffiliates' ),
                                     'btn_start_tour' => __( 'Start Tour', 'thirstyaffiliates' )
                                 ),
                    'urls'     => array( 'ajax' => admin_url( 'admin-ajax.php' ) ),
                    'post'     => isset( $post ) && isset( $post->ID ) ? $post->ID : 0
                )
            );
        }

        if ( ( ! empty( $_GET['post_type'] ) && 'thirstylink' === $_GET['post_type'] ) || $screen->id === 'thirstylink' ) {
          wp_enqueue_style( 'ta-admin-styles' , $this->_constants->CSS_ROOT_URL() . 'admin/ta-admin.css' , array() , Plugin_Constants::VERSION , 'all' );
          wp_enqueue_script( 'ta-admin-scripts' , $this->_constants->JS_ROOT_URL() . 'app/ta-admin.js' , array( 'jquery', 'wp-pointer' ) , Plugin_Constants::VERSION , true );
        }

        if( isset($_GET['page']) && 'thirstyaffiliates_onboarding' === $_GET['page'] ) {
            wp_enqueue_style('ta-magnific-popup', $this->_constants->JS_ROOT_URL() . '/lib/magnific-popup/magnific-popup.min.css', array(), '1.1.0');

            wp_enqueue_script('ta-magnific-popup', $this->_constants->JS_ROOT_URL() . '/lib/magnific-popup/jquery.magnific-popup.min.js', array('jquery'), '1.1.0', true);

            wp_enqueue_style('ta-onboarding', $this->_constants->CSS_ROOT_URL() . 'admin/onboarding.css', array(), Plugin_Constants::VERSION);
            wp_enqueue_script('ta-onboarding', $this->_constants->JS_ROOT_URL() . 'app/ta-onboarding.js', array('jquery'), Plugin_Constants::VERSION, true);

            wp_localize_script('ta-onboarding', 'TaOnboardingL10n', array(
                'step' => isset($_GET['step']) ? (int) $_GET['step'] : 0,
                'ajax_url' => admin_url('admin-ajax.php'),
                'onboarding_url' => admin_url('admin.php?page=thirstyaffiliates-onboarding'),
                'is_pro_user' => Onboarding_Helper::is_pro_active(),
                'features' => Onboarding_Helper::get_features(),
                'save_features_nonce' => wp_create_nonce('ta_onboarding_save_features'),
                'save_new_link_nonce' => wp_create_nonce('ta_onboarding_save_new_link'),
                'save_new_category_nonce' => wp_create_nonce('ta_onboarding_save_new_category'),
                'get_category_nonce' => wp_create_nonce('ta_onboarding_get_category'),
                'install_correct_edition' => wp_create_nonce('ta_onboarding_install_correct_edition'),
                'install_license_edition' => wp_create_nonce('ta_install_license_edition'),
                'install_addons' => wp_create_nonce('ta_onboarding_install_addons'),
                'load_complete_step' => wp_create_nonce('ta_onboarding_load_complete_step'),
                'load_create_new_content' => wp_create_nonce('ta_onboarding_load_create_new_content'),
                'load_finish_step' => wp_create_nonce('ta_onboarding_load_finish_step'),
                'load_link_step_content' => wp_create_nonce('ta_onboarding_load_link_step_content'),
                're_render_links_list' => wp_create_nonce('ta_onboarding_re_render_links_list'),
                'set_content_nonce' => wp_create_nonce('ta_onboarding_set_content'),
                'unset_content_nonce' => wp_create_nonce('ta_onboarding_unset_content'),
                'import_links_nonce' => wp_create_nonce('ta_onboarding_import_links'),
                'unset_category_nonce' => wp_create_nonce('ta_onboarding_unset_category'),
                'mark_content_steps_skipped_nonce' => wp_create_nonce('ta_onboarding_mark_content_steps_skipped'),
                'mark_steps_complete_nonce' => wp_create_nonce('ta_onboarding_mark_steps_complete'),
                'deactivate_confirm' => __('Are you sure? You will no longer receive updates for ThirstyAffiliates if this License Key is deactivated.', 'thirstyaffiliates'),
                'activate_license_nonce' => wp_create_nonce('tap_activate_license'),
                'deactivate_license_nonce' => wp_create_nonce('tap_deactivate_license'),
                'an_error_occurred' => __('An error occurred', 'thirstyaffiliates'),
                'content_id' => Onboarding_Helper::get_link_id(),
                'has_imported_links' => Onboarding_Helper::get_has_imported_links(),
                'category_id' => Onboarding_Helper::get_category_id(),
                'link_name' => __('Link Name', 'thirstyaffiliates'),
                'course' => __('Course', 'thirstyaffiliates'),
                'page' => __('Page', 'thirstyaffiliates'),
                'may_take_couple_minutes' => __('This may take a couple of minutes', 'thirstyaffiliates'),
                'finish_nonce' => wp_create_nonce('ta_onboarding_finish'),
                'thirstylink_url' => admin_url('edit.php?post_type=thirstylink'),
                'error_installing_addon' => __('An error occurred when installing an add-on, please download and install the add-ons manually.', 'thirstyaffiliates'),
                'edition_url_param' => isset($_GET['edition']) ? sanitize_text_field(wp_unslash($_GET['edition'])) : '',
                'license_url_param' => isset($_GET['license']) ? sanitize_text_field(wp_unslash($_GET['license'])) : '',
                'images_url' => esc_url($this->_constants->IMAGES_ROOT_URL() . 'onboarding/'),
                'remove' => __('Remove', 'thirstyaffiliates'),
                'import_nonce' => wp_create_nonce( 'tap_upload_csv_form' ),
                'import_key'   => md5( uniqid() . get_current_user_id() ),
                'link_count' => Onboarding_Helper::get_links_count(),
                'loading_icon' => '<img src="'.esc_url(admin_url('images/wpspin_light.gif')).'" class="ta-icon-spinner" />'
            ));
        }

        $is_onboarding_page = isset( $_GET['page'] ) && 'thirstyaffiliates_onboarding' === $_GET['page'];
        $is_addon_page = preg_match( '/_page_thirstyaffiliates-addons$/', $screen->base );

        if ( $is_onboarding_page || $is_addon_page ) {
            wp_enqueue_style( 'ta-fontello-thirstyaffiliates',
                $this->_constants->PLUGIN_DIR_URL() . 'fonts/fontello/css/thirsty-affiliates.css',
                array(), Plugin_Constants::VERSION );
            wp_enqueue_style( 'ta-fontello-thirstyaffiliates-animation',
                $this->_constants->PLUGIN_DIR_URL() . 'fonts/fontello/css/animation.css',
                array(), Plugin_Constants::VERSION );
        }
    }

    /**
     * Load frontend js and css scripts.
     *
     * @since 3.0.0
     * @since 3.3.0 Add enable JS redirect TA global var.
     * @since 3.3.5 Add parameter for checking if statistics module is enabled or not.
     * @since 3.4.0 Add support for page builders.
     * @access public
     */
    public function load_frontend_scripts() {

        global $post, $wp;

        if ( apply_filters( 'ta_enqueue_tajs_script' , ( get_option( 'ta_enable_link_fixer' , 'yes' ) === 'yes' || get_option( 'ta_enable_stats_reporting_module' , 'yes' ) === 'yes' ) ) ) {

            // load main frontend script that holds the link fixer and stat record JS code
            wp_enqueue_script( 'ta_main_js' , $this->_constants->JS_ROOT_URL() . 'app/ta.js' , array( 'jquery' ) , Plugin_Constants::VERSION , true );
            wp_localize_script( 'ta_main_js' , 'thirsty_global_vars' , array(
                'home_url'                  => $this->_get_absolute_home_url( true ),
                'ajax_url'                  => admin_url( 'admin-ajax.php' ),
                'link_fixer_enabled'        => get_option( 'ta_enable_link_fixer' , 'yes' ),
                'link_prefix'               => $this->_helper_functions->get_thirstylink_link_prefix(),
                'link_prefixes'             => maybe_unserialize( get_option( 'ta_used_link_prefixes' ) ),
                'post_id'                   => isset( $post->ID ) ? $post->ID : 0,
                'enable_record_stats'       => get_option( 'ta_enable_stats_reporting_module' , 'yes' ),
                'enable_js_redirect'        => get_option( 'ta_enable_javascript_frontend_redirect' ),
                'disable_thirstylink_class' => get_option( 'ta_disable_thirsty_link_class' )
            ) );
        }

        // Page builder support.
        if ( $this->_helper_functions->is_page_builder_active() )
            $this->load_wp_editor_scripts();
    }

    /**
     * Load scripts for WP Editor.
     *
     * @since 3.4.0
     * @access public
     */
    public function load_wp_editor_scripts() {

        wp_enqueue_style( 'thirstyaffiliates-tinymce' , $this->_constants->CSS_ROOT_URL() . 'admin/ta-editor.css' , array( 'thickbox' ) , Plugin_Constants::VERSION , 'screen' );

        wp_enqueue_script( 'ta_editor_js', $this->_constants->JS_ROOT_URL() . 'app/ta-editor.js', array( 'jquery' , 'thickbox' ), Plugin_Constants::VERSION , true );
        wp_localize_script( 'ta_editor_js' , 'ta_editor_var' , apply_filters(  'ta_editor_linkpicker_jsvars' , array(
            'insertion_type'                   => $this->_helper_functions->get_option( 'ta_link_insertion_type' , 'link' ),
            'disable_qtag_buttons'             => get_option( 'ta_disable_text_editor_buttons' , 'no' ),
            'html_editor_enable_aff_link_btn'  => current_user_can( apply_filters( 'ta_enable_advance_link_picker' , 'edit_posts' ) ),
            'html_editor_enable_quick_add_btn' => current_user_can( apply_filters( 'ta_enable_quick_add_affiliate_link' , 'publish_posts' ) ),
            'html_editor_affiliate_link_btn'   => __( 'affiliate link' , 'thirstyaffiliates' ),
            'html_editor_quick_add_btn'        => __( 'quick add affiliate link' , 'thirstyaffiliates' ),
            'html_editor_affiliate_link_title' => __( 'Open the ThirstyAffiliates link picker' , 'thirstyaffiliates' ),
            'html_editor_quick_add_title'      => __( 'Open quick add affiliate link dialog' , 'thirstyaffiliates' ),
            'simple_search_placeholder'        => __( 'Type to search affiliate link' , 'thirstyaffiliates' )
        ) ) );
    }

    /**
     * Gutenberg backend scripts.
     *
     * @since 3.6
     * @access public
     */
    public function gutenberg_backend_scripts() {

        wp_enqueue_style( 'ta-gutenberg-support' , $this->_constants->JS_ROOT_URL() . 'app/gutenberg_support/dist/gutenberg-support.css' , Plugin_Constants::VERSION , 'screen' );

        wp_enqueue_script( 'ta-gutenberg-support' , $this->_constants->JS_ROOT_URL() . 'app/gutenberg_support/dist/gutenberg-support.js' , array( 'wp-blocks' , 'wp-element' , 'wp-components' , 'wp-i18n', 'lodash' ) , '1.0.0' , true );
    }

    /**
     * Get absolute home_url by recreating it and removing unneeded information.
     *
     * @since 3.2.4
     * @access public
     *
     * @param bool $no_scheme Toggle to check if need to include scheme or not.
     * @return string Recreated home_url.
     */
    private function _get_absolute_home_url( $no_scheme = false ) {

        $home_url = parse_url( home_url() );
        $scheme   = $no_scheme ? '//' : $home_url[ 'scheme' ] . '://';
        $path     = isset( $home_url[ 'path' ] ) ? $home_url[ 'path' ] : '';
        $port     = isset( $home_url[ 'port' ] ) ? ':' . $home_url[ 'port' ] : '';

        return apply_filters( 'ta_absolute_home_url', $scheme . $home_url[ 'host' ] . $port . $path );
    }

    public function support_widget() {

        if ( empty( $_GET['post_type'] ) || 'thirstylink' !== $_GET['post_type'] ) {
            return;
        }

        $thirstyaff_notifications = ThirstyAffiliates()->get_model( 'Notifications' );
        $notifications_count = $thirstyaff_notifications->get_count();

        printf(
            '<div id="caseproof-flyout">
                <div id="caseproof-flyout-items">
                    %1$s
                </div>
                <a href="#" id="caseproofFlyoutButton" class="caseproof-flyout-button caseproof-flyout-head">
                    %5$s
                    <div class="caseproof-flyout-label">%2$s</div>
                    <img src="%3$s" alt="%2$s" data-active="%4$s" />
                </a>
            </div>',
            $this->get_items_html(),
            esc_attr__( 'See Quick Links', 'thirstyaffiliates' ),
            esc_url( $this->_constants->IMAGES_ROOT_URL() . 'admin-flyout.svg' ),
            esc_url( $this->_constants->IMAGES_ROOT_URL() . 'admin-flyout.svg' ),
            $notifications_count > 0 ? '<span id="thirstyaffAdminNotificationsBadge" class="thirstyaff-notifications-count">' . esc_html( $notifications_count ) . '</span>' : ''
        );
    }

    /**
     * Generate menu items HTML.
     *
     * @since 1.5.7
     *
     * @return string Menu items HTML.
     */
    private function get_items_html() {

        $items      = array_reverse( $this->menu_items() );
        $items_html = '';

        foreach ( $items as $item_key => $item ) {
          $items_html .= sprintf(
            '<a id="%1$s" href="%2$s" target="_blank" rel="noopener noreferrer" class="caseproof-flyout-button caseproof-flyout-item caseproof-flyout-item-%3$d"%6$s%7$s>
              <div class="caseproof-flyout-label">%4$s</div>
              %5$s
            </a>',
            ! empty( $item['id'] ) ? esc_attr( $item['id'] ) : '',
            esc_url( $item['url'] ),
            (int) $item_key,
            wp_kses_post( $item['title'] ),
            $item['icon'],
            ! empty( $item['bgcolor'] ) ? ' style="background-color: ' . esc_attr( $item['bgcolor'] ) . '"' : '',
            ! empty( $item['hover_bgcolor'] ) ? ' onMouseOver="this.style.backgroundColor=\'' . esc_attr( $item['hover_bgcolor'] ) . '\'" onMouseOut="this.style.backgroundColor=\'' . esc_attr( $item['bgcolor'] ) . '\'"' : ''
          );
        }

        return $items_html;
    }

    /**
     * Menu items data.
     *
     * @since 1.5.7
     */
    private function menu_items() {

        $thirstyaff_notifications = ThirstyAffiliates()->get_model( 'Notifications' );
        $notifications_count = $thirstyaff_notifications->get_count();
        $notifications_count_text = $notifications_count > 0 ? sprintf( '(%s)', $notifications_count ) : '';

        $items = array(
            array(
                'title'         => esc_html__( 'Upgrade to ThirstyAffiliates Pro', 'thirstyaffiliates' ),
                'url'           => 'https://thirstyaffiliates.com/pricing?utm_source=plugin_admin&utm_medium=link&utm_campaign=in_plugin&utm_content=quick_links_widget',
                'icon'          => '<svg clip-rule="evenodd" fill-rule="evenodd" stroke-linejoin="round" stroke-miterlimit="2" viewBox="0 0 51 45" xmlns="http://www.w3.org/2000/svg"><g fill="#fff" fill-rule="nonzero" transform="translate(-15.5823 -17.1195)"><path d="m52.6 36.6c-.3-.8-1-1.3-1.8-1.3l-11.5.1-3.3-10.1c-.2-.8-1-1.3-1.8-1.3s-1.5.5-1.9 1.3l-3.3 9.9-11.5.1c-.8 0-1.5.5-1.8 1.2s0 1.6.6 2l8.9 7.5-4.2 12.8s0 .1-.1.1c0 0 0 .1-.1.1v.9s0 .1.1.1c0 .1 0 .1.1.2 0 0 0 .1.1.1l.3.3c.1.1.2.1.2.2h.1c.1 0 .1.1.2.1h.1c.1 0 .1 0 .2.1h.9c.1 0 .2 0 .2-.1h.1c.1 0 .2-.1.3-.1l10.2-8.1 9.6 8.2c.3.3.8.4 1.2.5.4-.1.7-.1 1.1-.3.6-.5 1-1.3.7-2.1l-4.1-12.6 9.3-7.6c.9-.6 1.2-1.4.9-2.2"/><path d="m40.1 25.9 5.1 2.1 2.1 5.1c.2.3.6.5.9.3.1-.1.2-.2.3-.3l2.1-5.1 5.1-2.1c.3-.2.5-.6.3-.9-.1-.1-.2-.2-.3-.3l-5.1-2.1-2.1-5.1c-.2-.3-.6-.5-.9-.3-.1.1-.2.2-.3.3l-2.1 5.1-5.1 2.1c-.3.2-.5.6-.3.9.1.1.2.2.3.3"/><path d="m66 39.7-4.2-1.7-1.7-4.1c-.1-.3-.5-.4-.7-.2-.1.1-.2.1-.2.2l-1.8 4.1-4.1 1.7c-.3.1-.4.5-.2.7.1.1.1.2.2.2l4.1 1.7 1.7 4.1c.1.3.5.4.7.2.1-.1.2-.1.2-.2l1.7-4.1 4.1-1.7c.3-.1.4-.5.2-.7.2 0 .1-.1 0-.2z"/></g></svg>',
                'bgcolor'       => '#FA7E23',
                'hover_bgcolor' => '#FF9000',
            ),
            array(
                'title' => esc_html__( 'Support & Docs', 'thirstyaffiliates' ),
                'url'   => 'https://thirstyaffiliates.com/support?utm_source=plugin_admin&utm_medium=link&utm_campaign=in_plugin&utm_content=quick_links_widget',
                'icon'  => '<svg clip-rule="evenodd" fill-rule="evenodd" stroke-linejoin="round" stroke-miterlimit="2" viewBox="0 0 42 37" xmlns="http://www.w3.org/2000/svg"><path d="m47.9 39.3v2.6c0 .5-.4 1-1 1h-4.6v4.6c0 .5-.4 1-1 1h-2.6c-.5 0-1-.4-1-1v-4.6h-4.6c-.5 0-1-.4-1-1v-2.6c0-.5.4-1 1-1h4.6v-4.6c0-.5.4-1 1-1h2.6c.5 0 1 .4 1 1v4.6h4.6c.5 0 1 .5 1 1m-24.4 13.1c-.3 0-.5-.2-.5-.5v-22.6c0-.3.2-.5.5-.5h33c.3 0 .5.2.5.5v22.6c0 .3-.2.5-.5.5zm12.6-28.8h7.9v1.3h-7.9zm20.9 1.3h-9.2v-1.3c0-2.2-1.8-3.9-3.9-3.9h-7.9c-2.2 0-3.9 1.8-3.9 3.9v1.3h-9.1c-2.2 0-3.9 1.8-3.9 3.9v23.6c0 2.2 1.8 3.9 3.9 3.9h34c2.2 0 3.9-1.8 3.9-3.9v-23.6c0-2.1-1.7-3.9-3.9-3.9" fill="#fff" fill-rule="nonzero" transform="translate(-19.1 -19.7)"/></svg>',
                'bgcolor' => '#008871',
                'hover_bgcolor' => '#38AD9C',
            ),
            array(
                'title' => esc_html__( 'Suggest a Feature', 'thirstyaffiliates' ),
                'url'   => 'https://thirstyaffiliates.com/contact?utm_source=plugin_admin&utm_medium=link&utm_campaign=in_plugin&utm_content=quick_links_widget',
                'icon'  => '<svg clip-rule="evenodd" fill-rule="evenodd" stroke-linejoin="round" stroke-miterlimit="2" viewBox="0 0 53 43" xmlns="http://www.w3.org/2000/svg"><g fill="#fff" fill-rule="nonzero" transform="translate(-13.5 -19.9)"><path d="m22.8 35.7c0-1.1-.9-2-2-2h-5.3c-1.1 0-2 .9-2 2s.9 2 2 2h5.3c1.1 0 2-.9 2-2"/><path d="m64.5 33.7h-5.3c-1.1 0-2 .9-2 2s.9 2 2 2h5.3c1.1 0 2-.9 2-2s-1-2-2-2"/><path d="m22.4 43.6-4.6 2.6c-1 .5-1.3 1.7-.8 2.7s1.7 1.3 2.7.8h.1l4.6-2.6c.9-.6 1.2-1.8.7-2.7-.6-1-1.8-1.3-2.7-.8"/><path d="m56.6 28.1c.3 0 .7-.1 1-.3l4.6-2.6c.9-.6 1.2-1.8.7-2.7s-1.7-1.2-2.7-.7l-4.6 2.6c-.9.5-1.3 1.8-.7 2.7.3.6 1 1 1.7 1"/><path d="m24.5 24.5-4.6-2.7c-.9-.6-2.2-.3-2.7.7-.6.9-.3 2.2.7 2.7h.1l4.6 2.6c.9.6 2.2.3 2.7-.7s.1-2.1-.8-2.6"/><path d="m49.6 23.5c-2.7-2.3-6.1-3.6-9.6-3.6-8.4 0-14.5 6.9-14.5 14.5 0 3.5 1.3 6.9 3.6 9.6 1.4 1.6 3.5 4.9 4.3 7.6h4c0-.4-.1-.8-.2-1.2-.5-1.5-1.9-5.3-5.1-9.1-1.7-1.9-2.6-4.4-2.6-7 0-6.1 4.9-10.6 10.6-10.6 5.8 0 10.6 4.7 10.6 10.6 0 2.6-.9 5-2.6 7-3.2 3.7-4.7 7.6-5.1 9-.1.4-.2.8-.2 1.2h4c.8-2.8 3-6 4.3-7.6 5.1-6 4.5-15.2-1.5-20.4"/><path d="m33.4 57.8c0 .3.1.5.2.7l2 3c.2.4.7.6 1.1.6h6.5c.4 0 .9-.2 1.1-.6l2-3c.1-.2.2-.5.2-.7v-3.6h-13.1z"/><path d="m62.2 46.2-4.6-2.6c-.9-.6-2.2-.3-2.7.7-.6.9-.3 2.2.7 2.7h.1l4.6 2.6c1 .5 2.2.2 2.7-.8.4-.9.1-2-.8-2.6"/><path d="m40 26.5c-4.4 0-7.9 3.6-7.9 7.9 0 .7.6 1.3 1.3 1.3s1.3-.6 1.3-1.3c0-2.9 2.4-5.3 5.3-5.3.7 0 1.3-.6 1.3-1.3s-.6-1.3-1.3-1.3"/></g></svg>',
                'bgcolor' => '#008871',
                'hover_bgcolor' => '#38AD9C',
            ),
            array(
              'id' => 'thirstyaffAdminNotifications',
              'title' => esc_html__( 'Notifications ', 'thirstyaffiliates' ) . '<span id="thirstyaffNotificationsCount"> ' . $notifications_count_text . ' </span>',
              'url'   => '#',
              'icon'  => '<svg width="22" height="14" viewBox="0 0 22 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M21.6944 6.5625C21.8981 6.85417 22 7.18229 22 7.54687V12.25C22 12.7361 21.8218 13.1493 21.4653 13.4896C21.1088 13.8299 20.6759 14 20.1667 14H1.83333C1.32407 14 0.891204 13.8299 0.534722 13.4896C0.178241 13.1493 0 12.7361 0 12.25V7.54687C0 7.18229 0.101852 6.85417 0.305556 6.5625L4.35417 0.765625C4.45602 0.644097 4.58333 0.522569 4.73611 0.401042C4.91435 0.279514 5.10532 0.182292 5.30903 0.109375C5.51273 0.0364583 5.7037 0 5.88194 0H16.1181C16.3981 0 16.6782 0.0850694 16.9583 0.255208C17.2639 0.401042 17.4931 0.571181 17.6458 0.765625L21.6944 6.5625ZM6.1875 2.33333L2.94097 7H7.63889L8.86111 9.33333H13.1389L14.3611 7H19.059L15.8125 2.33333H6.1875Z" fill="#ffffff"></path></svg>',
              'bgcolor' => '#008871',
              'hover_bgcolor' => '#38AD9C'
            )
        );

        return $items;
    }

    /**
     * Pre-footer promotion block displayed on all ThirstyAffiliates admin pages.
     *
     * @access public
     * @return void
     */
    public function promote_thirstyaffiliates() {
      if ( empty( $_GET['post_type'] ) || 'thirstylink' !== $_GET['post_type'] ) {
        return;
      }

      $is_pro_installed = is_plugin_active( 'thirstyaffiliates-pro/thirstyaffiliates-pro.php' );

      $links = array(
        array(
          'url'    => $is_pro_installed ? 'https://thirstyaffiliates.com/support/support-request-form/' : 'https://wordpress.org/plugins/thirstyaffiliates/',
          'text'   => __( 'Support', 'thirstyaffiliates' ),
          'target' => '_blank'
        ),
        array(
          'url'    => 'https://thirstyaffiliates.com/knowledge-base/',
          'text'   => __( 'Docs', 'thirstyaffiliates' ),
          'target' => '_blank'
        )
      );

      $title = __( 'Made with ♥ by the ThirstyAffiliates Team', 'thirstyaffiliates' );

      require_once $this->_constants->VIEWS_ROOT_PATH() . 'promotion.php';
    }

    /**
     * Execute plugin script loader.
     *
     * @since 3.0.0
     * @access public
     */
    public function run () {

        add_action( 'admin_enqueue_scripts' , array( $this , 'load_backend_scripts' ) , 10 , 1 );
        add_action( 'wp_enqueue_scripts' , array( $this , 'load_frontend_scripts' ) );

        // Add support for elementor.
        add_action( 'elementor/editor/before_enqueue_scripts' , array( $this , 'load_wp_editor_scripts' ) );

        // Gutenberg support
        add_action( 'enqueue_block_editor_assets' , array( $this , 'gutenberg_backend_scripts' ) );

        // Support widget
        add_action( 'admin_footer', array( $this, 'support_widget' ) );

        // Admin footer
        add_action( 'in_admin_footer', array( $this, 'promote_thirstyaffiliates' ) );

        // Generic admin notices
        add_action( 'wp_ajax_ta_dismiss_notice', array( $this, 'dismiss_notice' ) );
        add_action( 'wp_ajax_ta_dismiss_daily_notice', array( $this, 'dismiss_daily_notice' ) );
        add_action( 'wp_ajax_ta_dismiss_monthly_notice', array( $this, 'dismiss_monthly_notice' ) );
    }

    public function dismiss_notice() {
        if(check_ajax_referer('ta_dismiss_notice', false, false) && isset($_POST['notice']) && is_string($_POST['notice'])) {
          $notice = sanitize_key( $_POST['notice'] );
          update_option( "ta_dismiss_notice_$notice", true );
        }

        wp_send_json_success();
    }

    public function dismiss_daily_notice() {
        if(check_ajax_referer('ta_dismiss_notice', false, false) && isset($_POST['notice']) && is_string($_POST['notice'])) {
          $notice = sanitize_key($_POST['notice']);
          set_transient("ta_dismiss_notice_{$notice}", true, DAY_IN_SECONDS);
        }

        wp_send_json_success();
    }

  public function dismiss_monthly_notice() {
    if(check_ajax_referer('ta_dismiss_notice', false, false) && isset($_POST['notice']) && is_string($_POST['notice'])) {
      $notice = sanitize_key($_POST['notice']);
      set_transient("ta_dismiss_notice_{$notice}", true, DAY_IN_SECONDS * 30);
    }

    wp_send_json_success();
  }

}
