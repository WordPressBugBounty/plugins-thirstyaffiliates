jQuery(document).ready(function($) {

  $('#thirstyaffAdminNotifications').on('click', function(e) {
    e.preventDefault();
    $('#thirstyaff-notifications').toggleClass('visible');
    $('#caseproofFlyoutButton').trigger('click');
  });
  $('#thirstyaffNotificationsClose').on('click', function(e) {
    e.preventDefault();
    $('#thirstyaff-notifications').removeClass('visible');
  });

  var viewDismissed = $('#viewDismissed');
  var viewActive = $('#viewActive');
  var dismissedMessages = $('.dismissed-messages');
  var activeMessages = $('.active-messages');

  viewDismissed.on('click', function(event) {
    event.preventDefault();
    dismissedMessages.show();
    activeMessages.hide();
    viewActive.show();
    viewDismissed.hide();
  });
  viewActive.on('click', function(event) {
    event.preventDefault();
    dismissedMessages.hide();
    activeMessages.show();
    viewActive.hide();
    viewDismissed.show();
  });

  $('body').on('click', '.thirstyaff-notice-dismiss', function(event) {

    event.preventDefault();

    var $this = $(this);
    var messageId = $this.data('message-id');
    var message = $('#thirstyaff-notifications-message-' + messageId);
    var countEl = $('#thirstyaffNotificationsCount');
    var mainCountEl = $('#thirstyaffAdminNotificationsBadge');
    var trayCountEl = $('#thirstyaffNotificationsCountTray');
    var count = parseInt(mainCountEl.html());
    var adminMenuCount = $('#thirstyaffAdminMenuUnreadCount');

    var data = {
      action: 'thirstyaff_notification_dismiss',
      nonce: ThirstyAffAdminNotifications.nonce,
      id: messageId,
    };

    $this.prop('disabled', 'disabled');
    message.fadeOut();

    $.post( ThirstyAffAdminNotifications.ajax_url, data, function( res ) {

      if ( ! res.success ) {
        console.debug( res );
      } else {
        message.prependTo(dismissedMessages);
        message.show();
        count--;

        if ( count < 0 ) {
          count = 0;
          countEl.hide();
          mainCountEl.hide();
          trayCountEl.hide();
          adminMenuCount.closest('.awaiting-mod').remove();
        } else if ( 0 == count ) {
          countEl.hide();
          mainCountEl.hide();
          trayCountEl.hide();
          $('.thirstyaff-notifications-none').show();
          $('.dismiss-all').hide();
          adminMenuCount.closest('.awaiting-mod').remove();
        } else if ( count < 10 ) {
          countEl.addClass('single-digit');
          countEl.html('(' + count + ')');
          mainCountEl.html(count);
          trayCountEl.html(count);
          adminMenuCount.html(count);
        } else {
          countEl.html('(' + count + ')');
          mainCountEl.html(count);
          trayCountEl.html(count);
          adminMenuCount.html(count);
        }
      }

    } ).fail( function( xhr, textStatus, e ) {

      console.debug( xhr.responseText );
      message.show('Message could not be dismissed.');
    } );
  });

  $('body').on('click', '.dismiss-all' ,function(event) {

    event.preventDefault();

    var $this = $(this);
    var countEl = $('#thirstyaffNotificationsCount');
    var count = parseInt(countEl.html());
    var mainCountEl = $('#thirstyaffAdminNotificationsBadge');
    var adminMenuCount = $('#thirstyaffAdminMenuUnreadCount');
    var trayCountEl = $('#thirstyaffNotificationsCountTray');

    var data = {
      action: 'thirstyaff_notification_dismiss',
      nonce: ThirstyAffAdminNotifications.nonce,
      id: 'all',
    };

    $this.prop('disabled', 'disabled');

    $.post( ThirstyAffAdminNotifications.ajax_url, data, function( res ) {

      if ( ! res.success ) {
        console.debug( res );
      } else {
        countEl.hide();
        mainCountEl.hide();
        trayCountEl.hide();
        adminMenuCount.closest('.awaiting-mod').remove();
        $('.thirstyaff-notifications-none').show();
        $('.dismiss-all').hide();

        $.each($('.active-messages .thirstyaff-notifications-message'), function(i, el) {
          $(el).appendTo(dismissedMessages);
        });
      }

    } ).fail( function( xhr, textStatus, e ) {

      console.debug( xhr.responseText );
      message.show('Messages could not be dismissed.');
    } );
  });
});