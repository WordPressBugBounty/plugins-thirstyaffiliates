jQuery( document ).ready( function($){

    // Functions
    thirstySettings = {

        /**
         * Event function that toggles if custom link prefix option is to be shown or not.
         *
         * @since 3.0.0
         */
        customLinkPrefix : function() {

            $settingsBlock.on( 'change' , '#ta_link_prefix' , function() {

                var linkPrefix           = $(this).val(),
                    $customLinkPrefixRow = $settingsBlock.find( '.ta_link_prefix_custom-row' );

                if ( linkPrefix == 'custom' ) {

                    $customLinkPrefixRow.show();
                    $customLinkPrefixRow.find( '#ta_link_prefix_custom' ).prop( 'disabled' , false );

                } else {

                    $customLinkPrefixRow.hide();
                    $customLinkPrefixRow.find( '#ta_link_prefix_custom' ).prop( 'disabled' , true );

                }

            } );

            $settingsBlock.find( '#ta_link_prefix' ).trigger( 'change' );

        },

        /**
         * Event function that checks on submit if the selected link prefix is valid or not.
         *
         * @since 3.0.0
         */
        validLinkPrefix : function() {

            if ( $settingsBlock.find( '#ta_link_prefix' ).length > 0 ) {

                $settingsBlock.on( 'click' , '#submit' , function() {

                    var linkPrefix           = $settingsBlock.find( '#ta_link_prefix' ).val(),
                        $customLinkPrefixRow = $settingsBlock.find( '.ta_link_prefix_custom-row' );

                    if ( linkPrefix == 'custom' && $.trim( $customLinkPrefixRow.find( '#ta_link_prefix_custom' ).val() ) === '' ) {

                            alert( ta_settings_var.i18n_custom_link_prefix_valid_val );

                            $( 'html, body' ).animate( {
                                scrollTop : $customLinkPrefixRow.find( '#ta_link_prefix_custom' ).offset().top - 50
                            } , 500 );

                            $customLinkPrefixRow.find( '#ta_link_prefix_custom' ).focus();

                            return false;

                    }

                } );

            }

        },

        /**
         * Event function that show/hides the category select field for toggle options that have the 'category' option.
         *
         * @since 3.2.0
         */
        toggleCat : function() {

            $settingsBlock.on( 'change' , 'select.toggle-cat' , function() {

                var $cat_select     = $settingsBlock.find( 'select.toggle-cat-' + $(this).prop( 'id' ) ),
                    $cat_select_row = $cat_select.closest( 'tr' ),
                    option_value    = $(this).val();

                if ( $cat_select.length < 1 || $cat_select_row.length < 1 )
                    return;

                if ( option_value == 'category' ) {

                    $cat_select.prop( 'disabled' , false );
                    $cat_select.prop( 'required' , true );
                    $cat_select.selectize({
                        plugins   : [ 'remove_button' , 'drag_drop' ]
                    });
                    $cat_select_row.show();

                } else {

                    $cat_select.prop( 'disabled' , true );

                    if ( $cat_select[0].selectize ) {
                        $cat_select[0].selectize.destroy();
                    }

                    $cat_select.prop( 'required' , false );
                    $cat_select_row.hide();
                }

            } );

            $settingsBlock.find( 'select.toggle-cat' ).trigger( 'change' );
        },

        /**
         * Initialize block bots settings as a selectized textarea.
         *
         * @since 3.3.2
         */
        blockBotsSettings : function() {

            $settingsBlock.find( '#ta_blocked_bots' ).selectize({
                plugins   : [ 'restore_on_backspace' , 'remove_button' , 'drag_drop' ],
                delimeter : ',',
                persist   : false,
                create    : function(input) {
                    return {
                        value : input,
                        text  : input
                    }
                }
            });
        },

        copyToClipboard: function () {
            $( '.ta-copy-to-clipboard' ).each( function ( i, el ) {
                var $el = $( el ),
                    copy_text = 'Copy to Clipboard',
                    copied_text = 'Copied!',
                    copy_error_text = 'Oops, Copy Failed!',
                    clipboard = new ClipboardJS( el );

                if ( $.fn.tipTip ) {
                    var $content = $( '<div>' ).css( { width: '90px', textAlign: 'center' } ).text( copy_text );

                    $el.tipTip( {
                        content: $content,
                        defaultPosition: 'top',
                        exit: function () {
                            setTimeout( function () {
                                $content.text( copy_text );
                            }, 200 );
                        }
                    } );

                    clipboard
                        .on( 'success', function () {
                            $content.text( copied_text );
                        } )
                        .on( 'error', function () {
                            $content.text( copy_error_text );
                        } );
                }
            } );
        },

        currencySelect: function () {
            $('#ta_thirstypay_default_currency').selectize();
        },

        customerPortal: function () {
            var $configure_button = $( '#ta-stripe-configure-customer-portal' );

            $configure_button.on( 'click', function () {
                var $sub_box = $( '#ta-customer-portal-sub-box' ),
                    show = $sub_box.is( ':hidden' );

                if ( show ) {
                    $configure_button.find( 'i' ).removeClass( 'dashicons-arrow-right-alt2' ).addClass( 'dashicons-arrow-down-alt2' );
                    $sub_box.slideDown();
                } else {
                    $configure_button.find( 'i' ).removeClass( 'dashicons-arrow-down-alt2' ).addClass( 'dashicons-arrow-right-alt2' );
                    $sub_box.slideUp();
                }
            } );

            $( '#ta-portal-customer-update, #ta-portal-subscription-cancel' ).on( 'change', function () {
                $( this ).closest( 'td' ).find( '.ta-portal-sub-options' )[this.checked ? 'show' : 'hide']();
            } );

            if ( window.location.href.indexOf( '&configure_customer_portal=1' ) !== -1 ) {
                if ( window.history.replaceState ) {
                    window.history.replaceState( null, null, window.location.href.replace( '&configure_customer_portal=1', '' ) );
                }

                if ( $configure_button.length ) {
                    $configure_button.find( 'i' ).removeClass( 'dashicons-arrow-right-alt2' ).addClass( 'dashicons-arrow-down-alt2' );
                    $( '#ta-customer-portal-sub-box' ).show();

                    window.scrollTo( {
                        top: $configure_button.offset().top - 82,
                        behavior: 'smooth'
                    } );
                }
            }
        }

    };

    var $settingsBlock = $( '.ta-settings.wrap' );

    // initialize custom link prefix settings display
    thirstySettings.customLinkPrefix();
    thirstySettings.validLinkPrefix();
    thirstySettings.toggleCat();
    thirstySettings.blockBotsSettings();
    thirstySettings.copyToClipboard();
    thirstySettings.currencySelect();
    thirstySettings.customerPortal();

});
