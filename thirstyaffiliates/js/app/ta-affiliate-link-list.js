jQuery(function ($) {
    var url_params = new URLSearchParams(window.location.search.substring(1));

    if (url_params.get('post_type') === 'thirstylink' && url_params.get('thirstypay') === '1') {
        $('#posts-filter').addClass('thirstypay').append('<input type="hidden" name="thirstypay" value="1">');
    }

    var $customer_portal_notice_popup = $('#ta-customer-portal-notice-popup');

    if($customer_portal_notice_popup.length && $.magnificPopup) {
        $.magnificPopup.open({
            items: {
                src: $customer_portal_notice_popup,
                type: 'inline'
            },
            mainClass: 'mfp-ta',
            closeOnBgClick: false,
            callbacks: {
                close: function () {
                    $.ajax({
                        method: 'POST',
                        url: ajaxurl,
                        data: {
                            action: 'ta_dismiss_customer_portal_notice',
                            _ajax_nonce: $customer_portal_notice_popup.data('nonce')
                        }
                    });
                }
            }
        });

        $('#ta-customer-portal-notice-close').on('click', function () {
            $.magnificPopup.close();
        });
    }
});
