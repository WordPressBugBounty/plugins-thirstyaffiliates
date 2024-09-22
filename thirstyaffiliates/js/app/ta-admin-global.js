jQuery(function ($) {
  $(document).on('click', '.ta-notice-dismiss-permanently button.notice-dismiss', function () {
    $.ajax({
      url: TAAdminGlobal.ajax_url,
      method: 'POST',
      data: {
        action: 'ta_dismiss_notice',
        _ajax_nonce: TAAdminGlobal.dismiss_notice_nonce,
        notice: $(this).closest('.notice').data('notice')
      }
    })
  });

  $(document).on('click', '.ta-notice-dismiss-daily button.notice-dismiss', function() {
    $.ajax({
      url: TAAdminGlobal.ajax_url,
      method: 'POST',
      data: {
        action: 'ta_dismiss_daily_notice',
        _ajax_nonce: TAAdminGlobal.dismiss_notice_nonce,
        notice: $(this).closest('.notice').data('notice')
      }
    });
  });

  $(document).on('click', '.ta-notice-dismiss-monthly button.notice-dismiss', function() {
    $.ajax({
      url: TAAdminGlobal.ajax_url,
      method: 'POST',
      data: {
        action: 'ta_dismiss_monthly_notice',
        _ajax_nonce: TAAdminGlobal.dismiss_notice_nonce,
        notice: $(this).closest('.notice').data('notice')
      }
    });
  });
});
