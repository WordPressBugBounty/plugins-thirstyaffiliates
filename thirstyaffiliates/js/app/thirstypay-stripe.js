jQuery(function ($) {
    var $product_select = $('#ta-stripe-product-select');

    $product_select.select2({
        theme: 'ta',
        placeholder: ta_thirstypay_stripe_params.find_or_add_product,
        allowClear: true,
        width: '100%',
        ajax: {
            url: ta_thirstypay_stripe_params.ajax_url,
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    action: 'ta_search_stripe_prices',
                    _ajax_nonce: ta_thirstypay_stripe_params.search_stripe_prices_nonce,
                    search: params.term
                }
            },
            processResults: function (data) {
                return {
                    results: data && data.success ? data.data : []
                };
            }
        },
        templateResult: function (price) {
            if (price.id === 'add') {
                return $('<strong>').text(price.text);
            }

            return price.text;
        }
    });

    $product_select.on('select2:select', function (e) {
        if (e && e.params && e.params.data) {
            if (e.params.data.id === 'add') {
                $.magnificPopup.open({
                    items: {
                        src: '#ta-stripe-add-product-popup',
                        type: 'inline'
                    },
                    mainClass: 'mfp-ta',
                    closeOnBgClick: false
                });
            } else if (e.params.data.html) {
                $(e.params.data.html).appendTo($('#ta-stripe-line-items'));
                $('#ta-stripe-product-selector').hide();
                update_line_items();
            }

            $product_select.val(null).trigger('change');
        }
    });

    var $line_items = $('#ta-stripe-line-items'),
        $save_payment_details = $('input[name="ta_stripe_save_payment_details"]'),
        $recurring_only = $('.ta-stripe-recurring-only');

    $line_items.on('click', '.ta-stripe-line-item-x', function () {
        $(this).closest('.ta-stripe-line-item-box').remove();
        $('#ta-stripe-product-selector').show();
        update_line_items();
    });

    function update_line_items() {
        var line_items = [],
            recurring = false;

        $('.ta-stripe-line-item-box').each(function () {
            var line_item = $(this).data('line-item');

            if (line_item) {
                line_items.push(line_item);

                if (line_item.price && line_item.price.type === 'recurring') {
                    recurring = true;
                }
            }
        });

        $('textarea[name="ta_stripe_line_items"]').val(
            line_items.length ? JSON.stringify(line_items) : ''
        );

        $save_payment_details.prop({ checked: recurring, disabled: recurring });
        $recurring_only[recurring ? 'show' : 'hide']();
    }

    $('#ta_stripe_shipping_address_allowed_countries').select2({
        theme: 'ta',
        placeholder: ta_thirstypay_stripe_params.select_shipping_countries,
        allowClear: true,
        width: '100%'
    });

    $('#ta_stripe_billing_address_collection, #ta_stripe_include_free_trial').on('change', function () {
        $(this).closest('.ta-stripe-thirstypay-link-option')
            .find('> .ta-stripe-thirstypay-link-sub-options')[this.checked ? 'show' : 'hide']();
    });

    $('input[name="ta_stripe_shipping_address_collection"]').on('change', function () {
        var enabled = $('input[name="ta_stripe_shipping_address_collection"]:checked').val() === '1';

        $('#ta_stripe_shipping_address_allowed_countries')
            .closest('.ta-stripe-thirstypay-link-sub-option')[enabled ? 'show' : 'hide']();
    });

    var recurring = false;

    $line_items.find('.ta-stripe-line-item-box').each(function () {
        var line_item = $(this).data('line-item');

        if (line_item && line_item.price && line_item.price.type === 'recurring') {
            recurring = true;
        }
    });

    if (recurring) {
        $save_payment_details.prop({ checked: true, disabled: true });
    }

    $recurring_only[recurring ? 'show' : 'hide']();

    var $currency_select = $('#ta_stripe_add_product_currency');

    $currency_select.select2({
        theme: 'ta',
        width: '100%',
        dropdownParent: $currency_select.closest('.ta-stripe-add-product-field')
    });

    $('#ta_stripe_add_product_billing_period').on('change', function () {
        $('#ta-stripe-add-product-recurring-custom')[$(this).val() === 'custom' ? 'show' : 'hide']();
    });

    $('#ta-stripe-add-product-cancel').on('click', function () {
        $.magnificPopup.close();
    });

    var $product_type = $('input[name="ta_stripe_recurring"]');

    $product_type.on('change', function () {
        $('#ta-stripe-add-product-recurring-options')[$product_type.filter(':checked').val() === 'recurring' ? 'show' : 'hide']();
    });

    var adding = false,
        $popup = $('#ta-stripe-add-product-popup');

    $('#ta-stripe-add-product-save').on('click', function () {
        if (adding) {
            return;
        }

        adding = true;

        var data = get_product_form_data();

        if(!validate_product_form_data(data)) {
            adding = false;
            return;
        }

        var $button = $(this),
            button_width = $button.width(),
            button_html = $button.html();

        $button.width(button_width).html('<span class="ta-loading"></span>');
        $popup.find('.notice').remove();

        $.ajax({
            method: 'POST',
            url: ta_thirstypay_stripe_params.ajax_url,
            dataType: 'json',
            data: {
                action: 'ta_stripe_add_product',
                _ajax_nonce: ta_thirstypay_stripe_params.add_product_nonce,
                data: JSON.stringify(data)
            }
        })
        .done(function (response) {
            if (response && typeof response.success === 'boolean') {
                if (response.success) {
                    $(response.data).appendTo($('#ta-stripe-line-items'));
                    $('#ta-stripe-product-selector').hide();
                    update_line_items();
                    $.magnificPopup.close();

                    $('#ta_stripe_add_product_name, #ta_stripe_add_product_price').val('');
                } else {
                    product_form_error(response.data);
                }
            } else {
                product_form_error('Request failed');
            }
        })
        .fail(function () {
            product_form_error('Request failed');
        })
        .always(function () {
            $button.html(button_html).width('auto');
            adding = false;
        });
    });

    function product_form_error(message) {
        $popup.find('.ta-stripe-add-product-title').after(
            $('<div class="notice notice-error">').append(
                $('<p>').text(message)
            )
        );
    }

    function get_product_form_data() {
        var data = {},
            fields = [
                'name', 'price', 'currency', 'billing_period', 'interval_count', 'interval', 'tax_behavior'
            ];

        $.each(fields, function (i, field) {
            data[field] = $('#ta_stripe_add_product_' + field).val()
        });

        data.type = $('input[name="ta_stripe_recurring"]:checked').val();

        return data;
    }

    function validate_product_form_data(data) {
        $('.ta-stripe-field-has-error').removeClass('ta-stripe-field-has-error');
        $('.ta-stripe-field-error').remove();

        var required = ['name', 'price'],
            valid = true;

        if (data.type === 'recurring' && data.billing_period === 'custom') {
            required.push('interval_count');
        }

        $.each(required, function (i, field) {
            var $field = $('#ta_stripe_add_product_' + field),
                value = $field.val();

            if(typeof value !== 'string' || !value.length) {
                $field.addClass('ta-stripe-field-has-error').closest('.ta-stripe-add-product-field').append(
                    $('<div class="ta-stripe-field-error">').text(ta_thirstypay_stripe_params.this_field_is_required)
                );

                valid = false;
            }
        });

        return valid;
    }
});
