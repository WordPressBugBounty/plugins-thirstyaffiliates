jQuery(function ($) {
  var $addonsContainer = $('#ta-addons-container');

  if ($addonsContainer.length) {
    if (window.List) {
      var list = new List($addonsContainer[0], {
        valueNames: ['ta-addon-name'],
        listClass: 'ta-addons'
      });

      $('#ta-addons-search').on('keyup', function () {
        list.search($(this).val());
      })
      .on('input', function () {
        // Used to detect click on HTML5 clear button
        if ($(this).val() === '') {
          list.search('');
        }
      });
    }

    if ($.fn.matchHeight) {
      $('.ta-addon .ta-addon-details').matchHeight({
        byRow: false
      });
    }

    var icons = {
      activate: '<i class="ta-icon ta-icon-toggle-on ta-flip-horizontal" aria-hidden="true"></i>',
      deactivate: '<i class="ta-icon ta-icon-toggle-on" aria-hidden="true"></i>',
      install: '<i class="ta-icon ta-icon-cloud-download" aria-hidden="true"></i>',
      spinner: '<i class="ta-icon ta-icon-spinner animate-spin" aria-hidden="true"></i>',
    };

    $(document).on('click', '.ta-addon-action button', function () {
      var $button = $(this),
        $addon = $button.closest('.ta-addon'),
        originalButtonHtml = $button.html(),
        originalButtonWidth = $button.width(),
        type = $button.data('type'),
        action,
        statusClass,
        statusText,
        buttonHtml,
        successText;

      if ($addon.hasClass('ta-addon-status-active')) {
        action = 'ta_addon_deactivate';
        statusClass = 'ta-addon-status-inactive';
        statusText = TAAddons.inactive;
        buttonHtml = icons.activate + TAAddons.activate;
      } else if ($addon.hasClass('ta-addon-status-inactive')) {
        action = 'ta_addon_activate';
        statusClass = 'ta-addon-status-active';
        statusText = TAAddons.active;
        buttonHtml = icons.deactivate + TAAddons.deactivate;
      } else if ($addon.hasClass('ta-addon-status-download')) {
        action = 'ta_addon_install';
        statusClass = 'ta-addon-status-active';
        statusText = TAAddons.active;
        buttonHtml = icons.deactivate + TAAddons.deactivate;
      } else {
        return;
      }

      $button.prop('disabled', true).html(icons.spinner).addClass('ta-loading').width(originalButtonWidth);

      var data = {
        action: action,
        _ajax_nonce: TAAddons.nonce,
        plugin: $button.data('plugin'),
        type: type
      };

      var handleError = function (message) {
        $addon.find('.ta-addon-actions').append($('<div class="ta-addon-message ta-addon-message-error">').text(message));
        $button.html(originalButtonHtml);
      };

      $.ajax({
        type: 'POST',
        url: TAAddons.ajax_url,
        dataType: 'json',
        data: data
      })
      .done(function (response) {
        if (!response || typeof response != 'object' || typeof response.success != 'boolean') {
          handleError(type === 'plugin' ? TAAddons.plugin_install_failed : TAAddons.install_failed);
        } else if (!response.success) {
          if (typeof response.data == 'object' && response.data[0] && response.data[0].code) {
            handleError(type === 'plugin' ? TAAddons.plugin_install_failed : TAAddons.install_failed);
          } else {
            handleError(response.data);
          }
        } else {
          if (action === 'ta_addon_install') {
            $button.data('plugin', response.data.basename);
            successText = response.data.message;

            if (!response.data.activated) {
              statusClass = 'ta-addon-status-inactive';
              statusText = TAAddons.inactive;
              buttonHtml = icons.activate + TAAddons.activate;
            }
          } else {
            successText = response.data;
          }

          $addon.find('.ta-addon-actions').append($('<div class="ta-addon-message ta-addon-message-success">').text(successText));

          $addon.removeClass('ta-addon-status-active ta-addon-status-inactive ta-addon-status-download')
                .addClass(statusClass);

          $addon.find('.ta-addon-status-label').text(statusText);

          $button.html(buttonHtml);
        }
      })
      .fail(function () {
        handleError(type === 'plugin' ? TAAddons.plugin_install_failed : TAAddons.install_failed);
      })
      .always(function () {
        $button.prop('disabled', false).removeClass('ta-loading').width('auto');

        // Automatically clear add-on messages after 3 seconds
        setTimeout(function() {
          $addon.find('.ta-addon-message').remove();
        }, 3000);
      });
    });
  }
});