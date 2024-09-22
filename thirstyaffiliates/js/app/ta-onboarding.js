var TA_Onboarding = (function($) {
  var onboarding;
  var working = false;
  var selected_content = null;
  var upgrade_wait_started;

  onboarding = {
    init: function () {
      if(!TaOnboardingL10n.step) {
        return; // Skip JS on Welcome page
      }

      if(TaOnboardingL10n.step > 1) {
        onboarding.go_to_step(TaOnboardingL10n.step);
      }


      $('body').on('click','.ta-wizard-onboarding-video-collapse', function (e) {
        e.preventDefault();
        $('#inner_' + $(this).data('id')).hide();
        $('#wrapper_' + $(this).data('id')).removeClass('active');
        $('#expand_' + $(this).data('id')).show();
      });

      $('body').on('click','.ta-wizard-onboarding-video-expand', function (e) {
        e.preventDefault();
        $(this).hide();
        $('#wrapper_' + $(this).data('id')).show();
        $('#wrapper_' + $(this).data('id')).addClass('active');
        $('#inner_' + $(this).data('id')).show();
        $('#ta_play_' + $(this).data('id')).trigger('click');
      });

      $('body').on('click','.ta-video-play-button', function (e) {
        e.preventDefault();
        var taPlayBtn = $(this);
        onboarding.load_video(taPlayBtn, 1);
      });

      $('.ta-wizard-go-to-step').on('click', function () {
        var current_step = TaOnboardingL10n.step;
        var context = $(this).data('context');
        onboarding.go_to_step($(this).data('step'));

        if(current_step == 3 || current_step == 4){
          if(context == 'skip'){
            $('.ta_onboarding_step_3').addClass('ta-wizard-current-step-skipped');
            $('.ta_onboarding_step_4').addClass('ta-wizard-current-step-skipped');
            $.ajax({
              method: 'POST',
              url: TaOnboardingL10n.ajax_url,
              dataType: 'json',
              data: {
                action: 'ta_onboarding_mark_content_steps_skipped',
                _ajax_nonce: TaOnboardingL10n.mark_content_steps_skipped_nonce,
                data: JSON.stringify({})
              }
            });
            return;
          }else{
            $('.ta_onboarding_step_3').removeClass('ta-wizard-current-step-skipped');
            $('.ta_onboarding_step_4').removeClass('ta-wizard-current-step-skipped');
          }
        }

        onboarding.mark_steps_complete(current_step);

      });


      $(window).on('resize', function(){

         if( $( window ).width() > 1440 ){
            $('.ta-wizard-onboarding-video-expand').each(function(){
              var _this = $(this);
              var obj_id = $(this).data('id');
              $('#expand_' + obj_id).trigger('click');
            });
         }
      });

      $(window).trigger('resize');

      $(window).on('popstate', function (e) {
        var state = e.originalEvent.state;

        if(state && state.step) {
          onboarding.display_step(state.step);
        }
      });

      $('#ta-wizard-activate-license-key').on('click', onboarding.activate_license_key);

      $('.ta-wizard-feature').on('click', function () {
        onboarding.toggle_feature($(this));
      });

      onboarding.show_features_to_install();

      $('#ta-wizard-save-features').on('click', onboarding.save_features);

      $('body').on('click', 'a.ta-wizard-remove-selected-link', function(e) {
        e.preventDefault();
        $(this).closest('li').remove();
      })

      onboarding.setup_popups();

      $('body').on('click', '#ta-wizard-create-new-link-save', onboarding.create_new_link);
      $('body').on('click', '#ta-wizard-import-links-save', onboarding.import_links);

      $('.ta-wizard-selected-content-expand-menu').on('click', function (e) {
        e.stopPropagation();
        var element_id = $(this).data('id');
        $('#'+element_id).show();

        $(document.body).one('click', function () {
          $('#' + element_id).hide();
        });
      });

      $('#ta-wizard-selected-category-delete').on('click', function () {
        $('#ta-wizard-selected-category').hide();
        $('#ta-wizard-category-nav-continue').hide();
        $('#ta-wizard-category-nav-skip, #ta-wizard-create-select-category').show();

        var data = {
          category_id: TaOnboardingL10n.category_id
        };

        $.ajax({
          method: 'POST',
          url: TaOnboardingL10n.ajax_url,
          dataType: 'json',
          data: {
            action: 'ta_onboarding_unset_category',
            _ajax_nonce: TaOnboardingL10n.unset_category_nonce,
            data: JSON.stringify(data)
          }
        });
      });

      $('#ta-wizard-create-category-links').on('keyup', onboarding.debounce(onboarding.search_links, 250));

      $('#ta-wizard-choose-link-save').on('click', onboarding.select_existing_content);

      $('#ta-wizard-create-new-category-save').on('click', onboarding.create_new_category);

      if( TaOnboardingL10n.step == 3 ) {
        if( TaOnboardingL10n.content_id > 0 && TaOnboardingL10n.has_imported_links == 0 ) {
          onboarding.select_existing_content();
        }

        if(TaOnboardingL10n.has_imported_links == 1) {
          $('#ta-wizard-link-nav-skip').hide();
          $('#ta-wizard-link-nav-continue').show();
        }
      }

      $(document.body).on('click', '#ta-deactivate-license-key', onboarding.deactivate_license);

      if( TaOnboardingL10n.step == 1 ){
          $('#ta-wizard-license-wrapper').removeClass('ta-hidden');
      }

      if( TaOnboardingL10n.step == 4 ){
        if( TaOnboardingL10n.category_id > 0 ){
          onboarding.fillin_category_data();
        }else{
          $('#ta-wizard-create-select-category').show();
        }
      }

      $('#ta-wizard-finish-onboarding').on('click', onboarding.finish);
    },

    load_video: function (o_this) {
      var video_id = o_this.data('id');

      if(o_this.hasClass('iframe_loaded')){
        return;
      }
      var video_holder_id = o_this.data('holder-id');
      var video_hash = o_this.data('hash');
      var iframe_id = 'ta_iframe' + video_hash;

      $('#'+ video_holder_id).html('<iframe id="'+iframe_id+'" width="100%" height="100%" src="https://www.youtube.com/embed/'+video_id+'?rel=0&autoplay=0&mute=1&enablejsapi=1" frameborder="0" allowfullscreen></iframe>')
      o_this.addClass('iframe_loaded');
    },

    mark_steps_complete: function (current_step) {
      $.ajax({
          method: 'POST',
          url: TaOnboardingL10n.ajax_url,
          dataType: 'json',
          data: {
            action: 'ta_onboarding_mark_steps_complete',
            _ajax_nonce: TaOnboardingL10n.mark_steps_complete_nonce,
             data: JSON.stringify({step:current_step})
           }
      });
    },

    toggle_feature: function ($feature) {
      var $checkbox = $feature.find('input[type="checkbox"]');

      $checkbox.prop('checked', !$checkbox.prop('checked')).triggerHandler('change');
      onboarding.show_features_to_install();
    },

    show_features_to_install: function () {
      var plugins_to_install = [];
      var $plugins_to_install = $('.ta-wizard-plugins-to-install');

      $('.ta-wizard-plugin:checked').each(function () {
        var value = $(this).val();

        if(value && TaOnboardingL10n.features.addons[value]) {
          plugins_to_install.push(TaOnboardingL10n.features.addons[value]);
        }
      });

      $plugins_to_install.find('span').text(plugins_to_install.join(', '));
      $plugins_to_install[plugins_to_install.length ? 'show' : 'hide']();
    },

    save_features: function () {
      if(working) {
        return;
      }

      working = true;

      var $button = $(this),
        button_html = $button.html(),
        button_width = $button.width();

      $button.width(button_width).html(TaOnboardingL10n.loading_icon);

      var features = [];

      $('.ta-wizard-feature-input:checked').each(function () {
        var value = $(this).val();

        if(value && (TaOnboardingL10n.features.features[value] || TaOnboardingL10n.features.addons[value])) {
          features.push(value);
        }
      });

      $.ajax({
        method: 'POST',
        url: TaOnboardingL10n.ajax_url,
        dataType: 'json',
        data: {
          action: 'ta_onboarding_save_features',
          _ajax_nonce: TaOnboardingL10n.save_features_nonce,
          data: JSON.stringify(features)
        }
      })
      .done(function (response) {
        if(response && typeof response.success === 'boolean') {
          if(response.success) {
            onboarding.go_to_step(3);
          }
          else {
            onboarding.save_features_error(response.data);
          }
        }
        else {
          onboarding.save_features_error('Invalid response');
        }
      })
      .fail(function () {
        onboarding.save_features_error('Request failed');
      })
      .always(function () {
        $button.html(button_html).width('auto');
        working = false;
      });
    },

    save_features_error: function (message) {
      alert(message || TaOnboardingL10n.an_error_occurred);
    },

    go_to_step: function (step) {
      TaOnboardingL10n.step = step;
      onboarding.display_step(step);

      var url = new URL(window.location);
      url.searchParams.set('step', step);
      window.history.pushState({ step: step }, '', url);

      if( step == 3 ) {
        onboarding.load_link_step_content();
      }

      if( step == 4 ){
        if( TaOnboardingL10n.category_id > 0 ){
          onboarding.fillin_category_data();
        }else{
          $('#ta-wizard-create-select-category').show();
        }
      }

      if( step == 5 ){
        onboarding.load_finish_step();
      }

      if( step == 6 ){
        onboarding.load_complete_step();
      }

      if($('.ta-wizard-onboarding-video-'+step).length){
        var taPlayBtn =  $('.ta-video-play-button', $('.ta-wizard-onboarding-video-'+step) );
        onboarding.load_video(taPlayBtn);
      }
    },

    load_finish_step: function () {
      var edition = TaOnboardingL10n.edition_url_param;
      var license = TaOnboardingL10n.license_url_param;

      if(upgrade_wait_started && (Date.now() - upgrade_wait_started > 45000)) {
        edition = null;
      }

      $.ajax({
        method: 'POST',
        url: TaOnboardingL10n.ajax_url,
        dataType: 'json',
        data: {
          action: 'ta_onboarding_load_finish_step',
          _ajax_nonce: TaOnboardingL10n.load_finish_step,
          data: JSON.stringify({
            step: 6,
            edition: edition,
            license: license
          })
        }
      })
      .done(function (response) {
        if(response && typeof response.success === 'boolean') {
          if(response.success) {
            $('#ta-wizard-finish-step-container').html(response.data.html);

            if($('#ta-upgrade-wait-edition').length) {
              if(!upgrade_wait_started) {
                upgrade_wait_started = Date.now();
              }

              setTimeout(function () {
                onboarding.load_finish_step();
              }, 10000);

              return;
            }

            if($('#ta-finishing-setup-redirect').length) {
              setTimeout(function(){
                onboarding.mark_steps_complete(5);
                onboarding.go_to_step(6);
              }, 1500);
            }

            if($('#ta_wizard_finalize_setup').length) {
              if($('#ta_wizard_install_correct_edition').length) {
                onboarding.install_correct_edition();
              } else {
                if($('#start_addon_slug_installable').length) {
                  onboarding.install_addons($('#start_addon_slug_installable').val());
                }
                else {
                  $('#ta-wizard-finish-step-container').find('.ta-wizard-step-description').hide();
                }
              }
            }
          }
        }
      })
      .fail(function () {

      })
      .always(function () {

      });
    },

    load_complete_step: function () {
      $.ajax({
        method: 'POST',
        url: TaOnboardingL10n.ajax_url,
        dataType: 'json',
        data: {
          action: 'ta_onboarding_load_complete_step',
          _ajax_nonce: TaOnboardingL10n.load_complete_step,
           data: JSON.stringify({step:6})
        }
      })
      .done(function (response) {
        if(response && typeof response.success === 'boolean') {
          if(response.success) {
            var completed_step_urls = response.data.html;
            $('#ta-wizard-content-section').html(completed_step_urls);
          }
        }
      })
      .fail(function () {

      })
      .always(function () {

      });
    },

    display_step: function (step) {
      $('.ta-wizard-step').hide();
      $('.ta-wizard-step-' + step).show();
      $('.ta-wizard-nav-step').hide();
      $('.ta-wizard-nav-step-' + step).css('display', 'flex');
    },

    setup_popups: function () {
      if(!$.magnificPopup) {
        return;
      }

      $('#ta-wizard-create-new-category').on('click', function () {
        $.magnificPopup.open({
          mainClass: 'ta-wizard-mfp',
          closeOnBgClick: false,
          items: {
            src: '#ta-wizard-create-new-category-popup',
            type: 'inline'
          }
        });
      });

      $('#ta-wizard-choose-content').on('click', function () {
        $.magnificPopup.open({
          mainClass: 'ta-wizard-mfp',
          items: {
            src: '#ta-wizard-choose-link-popup',
            type: 'inline'
          }
        });
      });
    },

    load_link_step_content: function() {
      var search_params = new URLSearchParams(window.location.href);

      $.ajax({
        method: 'POST',
        url: TaOnboardingL10n.ajax_url,
        dataType: 'json',
        data: {
          action: 'ta_onboarding_load_link_step_content',
          _ajax_nonce: TaOnboardingL10n.load_link_step_content,
          data: JSON.stringify({step:3, link_page: search_params.get('link_page')})
        }
      })
      .done(function (response) {
        if(response && typeof response.success === 'boolean') {
          if(response.success) {
            $('#ta-wizard-link-step-container').html(response.data.html);

            onboarding.register_link_step_listeners();

            if( TaOnboardingL10n.content_id > 0 && TaOnboardingL10n.has_imported_links == 0 ) {
              onboarding.select_existing_content();
            }

            if(TaOnboardingL10n.has_imported_links == 1) {
              if(TaOnboardingL10n.link_count > 0) {
                $('#ta-wizard-link-nav-skip').hide();
                $('#ta-wizard-link-nav-continue').show();
              } else {
                $('#ta-wizard-link-nav-skip').show();
                $('#ta-wizard-link-nav-continue').hide();
              }
            }
          }
        }
      })
      .fail(function () {

      })
      .always(function () {

      });
    },

    register_link_step_listeners: function() {
      // Unbind previous event listeners.
      $('#ta-wizard-create-new-link').off('click');
      $('#ta-wizard-import-links').off('click');
      $('.ta-wizard-selected-content-expand-menu').off('click');
      $('.ta-wizard-selected-content-delete').off('click');
      $('.ta-wizard-links-pagination-page').off('click');

      // Register new event listeners.
      $('#ta-wizard-create-new-link').on('click', function () {
        var o_this = $(this);
        o_this.attr('disabled','disabled');
        $.ajax({
          method: 'POST',
          url: TaOnboardingL10n.ajax_url,
          dataType: 'json',
          data: {
            action: 'ta_onboarding_load_create_new_content',
            _ajax_nonce: TaOnboardingL10n.load_create_new_content,
            data: JSON.stringify({step:3})
          }
        })
        .done(function (response) {
          o_this.removeAttr('disabled');
          if(response && typeof response.success === 'boolean') {
            if(response.success) {
              $('#ta-wizard-create-new-link-popup').html(response.data.html);
              $.magnificPopup.open({
                mainClass: 'ta-wizard-mfp',
                items: {
                  src: '#ta-wizard-create-new-link-popup',
                  type: 'inline'
                }
              });
            }
          }
        })
        .fail(function () {
          o_this.removeAttr('disabled');
        })
        .always(function () {
          o_this.removeAttr('disabled');
        });
      });

      $('#ta-wizard-import-links').on('click', function() {
        $.magnificPopup.open({
          mainClass: 'ta-wizard-mfp',
          closeOnBgClick: false,
          items: {
            src: '#ta-wizard-import-links-popup',
            type: 'inline'
          },
          callbacks: {
            close: function() {
              if(!TaOnboardingL10n.is_pro_user) {
                return;
              }

              if(TaOnboardingL10n.has_imported_links == 0) {
                window.location.reload();
              } else {
                onboarding.re_render_links_list();
              }
            }
          }
        });
      });

      $('.ta-wizard-selected-content-expand-menu').on('click', function (e) {
        e.stopPropagation();
        var element_id = $(this).data('id');
        $('#'+element_id).show();

        $(document.body).one('click', function () {
          $('#' + element_id).hide();
        });
      });

      $('.ta-wizard-selected-content-delete').on('click', onboarding.select_content_remove);

      $('.ta-wizard-selected-content-delete').on('click', function () {
        selected_content = null;

        if(TaOnboardingL10n.has_imported_links == 0) {
          var $selected_content = $('#ta-wizard-selected-content');
        } else {
          var $selected_content = $('#ta-wizard-selected-content-' + $(this).data('link-id'));
        }

        $selected_content.find('.ta-wizard-selected-content-heading').text('');
        $selected_content.find('.ta-wizard-selected-content-name').text('');
        $selected_content.hide();

        if(TaOnboardingL10n.has_imported_links == 0) {
          $('#ta-wizard-link-nav-continue').hide();
          $('#ta-wizard-create-select-link, #ta-wizard-link-nav-skip').show();
        }
      });

      $('.ta-wizard-links-pagination-page').on('click', function(e) {
        e.preventDefault();
        onboarding.re_render_links_list($(this).data('page'));
      });
    },

    re_render_links_list: function(page_id = 0) {
      $('#ta-wizard-links-list-container .ta-icon-spinner').show();

      $.ajax({
        method: 'POST',
        url: TaOnboardingL10n.ajax_url,
        dataType: 'json',
        data: {
          action: 'ta_onboarding_re_render_links_list',
          _ajax_nonce: TaOnboardingL10n.re_render_links_list,
          data: JSON.stringify({step:3, page: page_id})
        }
      })
      .done(function(response) {
        if(response && typeof response.success === 'boolean') {
          if(response.success) {
            $('#ta-wizard-links-list-container').html(response.data.html);

            onboarding.register_link_step_listeners();

            if( TaOnboardingL10n.content_id > 0 && TaOnboardingL10n.has_imported_links == 0 ) {
              onboarding.select_existing_content();
            }
          }
        }
      })
    },

    clear_import_data: function() {
      // Clear any inputs from previous import.
      $('#ta-wizard-import-file').val('');
      $('.ta-wizard-import-rows').val('');
      $('#ta-wizard-import-created-count').text('0');
      $('#ta-wizard-import-updated-count').text('0');
      $('#ta-wizard-import-failed-create-count').text('0');
      $('#ta-wizard-import-failed-update-count').text('0');
      $('#ta-wizard-import-failed-rows').empty();

      $('#ta-wizard-import-links-popup-info').hide();
      $('#ta-wizard-import-failed-create').hide();
      $('#ta-wizard-import-failed-update').hide();
      $('#ta-wizard-import-failed-rows-container').hide();
    },

    create_new_link: function () {
      $('#ta-wizard-create-new-link-popup').find('.ta-wizard-popup-field-error').removeClass('ta-wizard-popup-field-error');

      var $target_url = $('#ta-wizard-create-link-target-url');
      var $slug = $('#ta-wizard-create-link-thirsty-link');
      var $redirection = $('#ta-wizard-create-link-redirection');

      var data = {
        target_url: $target_url.val(),
        slug: $slug.val(),
        redirection: $redirection.val()
      };

      if(!data.target_url) {
        $target_url.closest('.ta-wizard-popup-field').addClass('ta-wizard-popup-field-error');
        return;
      }

      if(!data.slug) {
        $slug.closest('.ta-wizard-popup-field').addClass('ta-wizard-popup-field-error');
        return;
      }

      if(!data.redirection) {
        $redirection.closest('.ta-wizard-popup-field').addClass('ta-wizard-popup-field-error');
        return;
      }

      if(working) {
        return;
      }

      working = true;

      var $button = $(this),
        button_html = $button.html(),
        button_width = $button.width();

      $button.width(button_width).html(TaOnboardingL10n.loading_icon);

      $.ajax({
        method: 'POST',
        url: TaOnboardingL10n.ajax_url,
        dataType: 'json',
        data: {
          action: 'ta_onboarding_save_new_link',
          _ajax_nonce: TaOnboardingL10n.save_new_link_nonce,
          data: JSON.stringify(data)
        }
      })
      .done(function (response) {
        if(response && typeof response.success === 'boolean') {
          if(response.success) {
            selected_content = response.data.link;

            $('#ta-wizard-link-nav-continue').show();

            if(TaOnboardingL10n.has_imported_links == 1) {
              $('#ta-wizard-link-nav-skip').hide();

              onboarding.re_render_links_list();
            } else {
              $('#ta-wizard-create-select-link, #ta-wizard-link-nav-skip').hide();

              var $selected_content = $('#ta-wizard-selected-content');
              $selected_content.find('.ta-wizard-selected-content-heading').text(response.data.heading);
              $selected_content.find('.ta-wizard-selected-content-name').text(response.data.link.name);
              $selected_content.show();
            }

            if($.magnificPopup) {
              $.magnificPopup.close();
            }

            TaOnboardingL10n.content_id = response.data.link.link_cpt_id;
          }
          else {
            onboarding.wizard_ta_ajax_error(response.data);
          }
        }
        else {
          onboarding.wizard_ta_ajax_error('Invalid response');
        }
      })
      .fail(function () {
        onboarding.wizard_ta_ajax_error('Request failed');
      })
      .always(function () {
        $button.html(button_html).width('auto');
        working = false;
      });
    },

    import_links: function() {
      // Make sure we aren't running this when there's no file uploaded.
      if($('#ta-wizard-import-file').val() === '') {
        return;
      }

      var tap_offset = 0;
      var tap_total = 0;
      var tap_total_successful_links = 0;
      var tap_total_failed_links = 0;
      var tap_override_links = false;
      var tap_skip_escape = false;

      var form_data = new FormData();
      var file_input = $('#ta-wizard-import-file')[0];
      var $import_info = $('#ta-wizard-import-links-popup-info');

      form_data.append('import', file_input.files[0]);
      form_data.append('action', 'ta_onboarding_import_links');
      form_data.append('_ajax_nonce', TaOnboardingL10n.import_links_nonce);
      form_data.append('_wpnonce', TaOnboardingL10n.import_nonce);
      form_data.append('import_key', TaOnboardingL10n.import_key);
      form_data.append( 'override_links', tap_override_links );
      form_data.append( 'skip_escape', tap_skip_escape );
      form_data.append( 'onboarding', 1 );

      if(working) {
        return;
      }

      working = true;

      onboarding.clear_import_data();

      $import_info.show();

      var $button = $(this),
        button_html = $button.html(),
        button_width = $button.width();

      $button.width(button_width).html(TaOnboardingL10n.loading_icon);

      var tap_import_batch = function () {
            form_data.set( 'offset', tap_offset );
            $( '#tap-import-progress' ).show();

            $.ajax( {
                type: 'POST',
                url: TaOnboardingL10n.ajax_url,
                data: form_data,
                contentType: false,
                processData: false,
            } ).done( function ( res ) {
                if ( ! res.success ) {
                    $( '#tap-import-progress' ).hide();
                    alert( res.message );
                    return;
                }

                tap_offset = res.offset;
                tap_total = res.total;
                var tap_import_percent = Math.round( ( tap_offset / tap_total ) * 100 );
                tap_total_successful_links += res.total_successful_links;
                tap_total_failed_links += res.total_failed_links;

                $( '#tap-import-progress .tap-progress-bar' ).css( 'width', tap_import_percent + '%' ).text( tap_import_percent + '%' );

                $( '#tap-import-progress #tap-import-successful-links' ).text( wp.i18n.sprintf(
                    wp.i18n.__( '%1$s links imported successfully.', 'thirstyaffiliates-pro' ),
                    tap_total_successful_links
                ) );

                $( '#tap-import-progress #tap-import-failed-links' ).text( wp.i18n.sprintf(
                    wp.i18n.__( '%1$s links failed to import.', 'thirstyaffiliates-pro' ),
                    tap_total_failed_links
                ) );

                $.each( res.imported_links, function ( _, link ) {
                    var tap_import_message;
                    var tap_import_list_id;

                    if ( link.was_successful ) {
                        tap_import_message = wp.i18n.sprintf(
                            wp.i18n.__( 'Link: %1$s (%2$s) was imported successfully.', 'thirstyaffiliates-pro' ) + '\n',
                            link.name,
                            link.slug
                        );

                        tap_import_list_id = '#tap-import-successful-rows';
                    } else {
                        tap_import_message = wp.i18n.sprintf(
                            wp.i18n.__( 'Link: %1$s (%2$s) failed to import (Error: %3$s).', 'thirstyaffiliates-pro' ) + '\n',
                            link.name,
                            link.slug,
                            link.error_message
                        );

                        tap_import_list_id = '#tap-import-failed-rows'
                    }

                    $( tap_import_list_id ).val( function ( _, current_list ) {
                        return current_list + tap_import_message;
                    } );
                } );

                if ( tap_offset < tap_total ) {
                    // Import the next batch.
                    tap_import_batch();
                } else {
                    working = false;
                    // Import is complete.
                    $( '#tap-import-progress .tap-progress-bar' ).css( 'width', '100%' ).text( res.message );

                    setTimeout( function () {
                        $( '#tap-import-progress .tap-import-progress-information' ).fadeOut();
                    }, 3000 );

                    $button.html(button_html).width('auto');
                }
            } );
        }

        tap_import_batch();
    },

    create_new_category: function () {
      $('#ta-wizard-create-new-category-popup').find('.ta-wizard-popup-field-error').removeClass('ta-wizard-popup-field-error');

      var $name = $('#ta-wizard-create-category-name');
      var $links = $('#ta-wizard-selected-links li');
      var link_ids = [];

      if($links && $links.length > 0) {
        $links.each(function() {
          link_ids.push($(this).data('id'));
        });
      }

      var data = {
        name: $name.val(),
        link_ids: link_ids
      };

      if(!data.name) {
        $name.closest('.ta-wizard-popup-field').addClass('ta-wizard-popup-field-error');
        return;
      }

      if(working) {
        return;
      }

      working = true;

      var $button = $(this),
        button_html = $button.html(),
        button_width = $button.width();

      $button.width(button_width).html(TaOnboardingL10n.loading_icon);

      $.ajax({
        method: 'POST',
        url: TaOnboardingL10n.ajax_url,
        dataType: 'json',
        data: {
          action: 'ta_onboarding_save_new_category',
          _ajax_nonce: TaOnboardingL10n.save_new_category_nonce,
          data: JSON.stringify(data)
        }
      })
      .done(function (response) {
        if(response && typeof response.success === 'boolean') {
          if(response.success) {
            selected_content = response.data.term;
            TaOnboardingL10n.category_id = response.data.term.term_id;

            $('#ta-wizard-create-select-category, #ta-wizard-category-nav-skip').hide();
            $('#ta-wizard-category-nav-continue').show();

            var $selected_content = $('#ta-wizard-selected-category');
            $selected_content.find('#ta-selected-category-name').text(response.data.term.name);
            $selected_content.find('#ta-selected-category-slug').text(response.data.term.slug);
            $selected_content.find('#ta-selected-category-count').text(response.data.term.count);
            $selected_content.show();

            $('#ta-wizard-create-category-name').val('');
            $('#ta-wizard-selected-links').empty();

            if($.magnificPopup) {
              $.magnificPopup.close();
            }
          }
          else {
            onboarding.wizard_ta_ajax_error(response.data);
          }
        }
        else {
          onboarding.wizard_ta_ajax_error('Invalid response');
        }
      })
      .fail(function () {
        onboarding.wizard_ta_ajax_error('Request failed');
      })
      .always(function () {
        $button.html(button_html).width('auto');
        working = false;
      });
    },


    fillin_category_data: function () {

      var data = {
        category_id: TaOnboardingL10n.category_id,
      };

      if(!data.category_id) {
        return;
      }

      if(working) {
        return;
      }

      working = true;

      $.ajax({
        method: 'POST',
        url: TaOnboardingL10n.ajax_url,
        dataType: 'json',
        data: {
          action: 'ta_onboarding_get_category',
          _ajax_nonce: TaOnboardingL10n.get_category_nonce,
          data: JSON.stringify(data)
        }
      })
      .done(function (response) {
        if(response && typeof response.success === 'boolean') {
          if(response.success) {
            selected_content = response.data.term;

            $('#ta-wizard-create-select-category, #ta-wizard-category-nav-skip').hide();
            $('#ta-wizard-category-nav-continue').show();

            var $selected_content = $('#ta-wizard-selected-category');
            $selected_content.find('#ta-selected-category-name').text(response.data.term.name);
            $selected_content.find('#ta-selected-category-slug').text(response.data.term.slug);
            $selected_content.find('#ta-selected-category-count').text(response.data.term.count);
            $selected_content.show();
          }
        }
      })
      .fail(function () {
        onboarding.wizard_ta_ajax_error('Request failed');
      })
      .always(function () {
        working = false;
      });
    },

    install_correct_edition: function () {
      if(working) {
        return;
      }

      working = true;

      $.ajax({
        method: 'POST',
        url: TaOnboardingL10n.ajax_url,
        dataType: 'json',
        data: {
          action: 'ta_onboarding_install_correct_edition',
          _ajax_nonce: TaOnboardingL10n.install_correct_edition,
        }
      })
      .done(function (response) {
        if(response && typeof response.success === 'boolean') {
          if(response.success) {
            window.location.reload();
          } else {
            alert(response.data);
          }
        } else {
          onboarding.wizard_ta_ajax_error('Invalid response');
        }
      })
      .fail(function () {
        onboarding.wizard_ta_ajax_error('Request failed');
      })
      .always(function () {
        working = false;
      });
    },

    install_addons: function (addon_slug) {
      var data = {
        addon_slug: addon_slug,
      };

      if(!data.addon_slug) {
        return;
      }

      $.ajax({
        method: 'POST',
        url: TaOnboardingL10n.ajax_url,
        dataType: 'json',
        data: {
          action: 'ta_onboarding_install_addons',
          _ajax_nonce: TaOnboardingL10n.install_addons,
          data: JSON.stringify(data)
        }
      })
      .done(function (response) {
        if(response && typeof response.success === 'boolean') {
          if(response.success) {
            var _addon_slug = response.data.addon_slug;
            var status = response.data.status;
            var message = response.data.message;
            var o_div = jQuery('#ta-finish-step-addon-' + _addon_slug);
            var o_spinner = jQuery('#ta-wizard-finish-step-' + _addon_slug);

            if(o_div.length && 1 === status) {
              o_div.find('.ta-wizard-feature-activatedx').addClass('ta-wizard-feature-activated');
              o_spinner.hide();
            }

            if(0 === status) {
              o_spinner.hide();
              o_div.find('.ta-wizard-addon-text').addClass('error').html(message);
            }

            var next_addon = response.data.next_addon;

            if(next_addon !== '') {
              onboarding.install_addons(next_addon);
            }
            else {
              setTimeout(function(){
                onboarding.mark_steps_complete(5);
                onboarding.go_to_step(6);
              }, 1500);
            }
          }
          else {
            onboarding.install_addons_error(typeof response.data === 'string' ? response.data : null);
          }
        } else {
          onboarding.install_addons_error('Invalid response');
        }
      })
      .fail(function () {
        onboarding.install_addons_error('Request failed');
      });
    },

    install_addons_error: function (message) {
      $('#ta-wizard-finish-step-container .ta-wizard-step-description').text(TaOnboardingL10n.error_installing_addon);
      $('#ta-wizard-finish-step-container .animate-spin').hide();
      onboarding.wizard_ta_ajax_error(message);
    },

    wizard_ta_ajax_error: function (message) {
      alert(message || TaOnboardingL10n.an_error_occurred);
    },

    debounce: function (func, wait, immediate) {
      var timeout;

      return function() {
        var context = this,
          args = arguments;

        var later = function() {
          timeout = null;

          if (!immediate) {
            func.apply(context, args);
          }
        };

        var callNow = immediate && !timeout;

        clearTimeout(timeout);
        timeout = setTimeout(later, wait);

        if (callNow) {
          func.apply(context, args);
        }
      };
    },

    search_links: function() {
      var $search = $(this);
      var $suggestions_list = $('#ta-wizard-links-suggestions-list');
      var added_suggestions = [];

      $('#ta-wizard-search-spinner').hide();

      if($search.val().length < 2) {
        $suggestions_list.hide();
        return;
      }

      $('#ta-wizard-search-spinner').show();

      $.ajax({
        method: 'GET',
        url: TaOnboardingL10n.ajax_url,
        dataType: 'json',
        data: {
          action: 'ta_search_for_links',
          term: $search.val()
        }
      })
      .done(function(res) {
        $suggestions_list.show();
        $('#ta-wizard-search-spinner').hide();

        if(!res.length) {
          return;
        }

        $suggestions_list.empty();

        var suggestions = res.map(function(link) {
          return { title_with_slug: link.title + ' (' + link.slug + ')', title: link.title, id: link.id };
        });

        suggestions.forEach(function(suggestion) {
          if(!added_suggestions.includes(suggestion.id)) {
            $suggestions_list.append('<li class="ta-link-suggestion" tabindex="0" data-id="' + suggestion.id + '" data-title="' + suggestion.title + '">' + suggestion.title_with_slug + '</li>');
            added_suggestions.push(suggestion.id);
          }
        });

        $('.ta-link-suggestion').on('keydown', function(e) {
          if(e.keyCode === 13) {
            e.preventDefault();
            $(this).trigger('click');
          }
        });

        $('.ta-link-suggestion').on('click', function() {
          $search.val('');
          $suggestions_list.hide();

          var link_id = $(this).data('id');
          var link_title = $(this).data('title');

          $('#ta-wizard-selected-links').append(
            `<li class="ta-wizard-selected-link" data-id="${link_id}">
              ${link_title}
              <span class="ta-group-remove-link">
                <a href="" class="ta-wizard-remove-selected-link"><i class="ta-icon ta-icon-cancel-circled ta-18"></i></a>
              </span>
            </li>`);
        });
      })
    },

    select_content_remove: function () {
      if(TaOnboardingL10n.has_imported_links == 0) {
        var $link_id = TaOnboardingL10n.content_id;
      } else {
        var $link_id = $(this).data('link-id');
      }

      var data = {
        content_id: $link_id,
        imported_links: TaOnboardingL10n.has_imported_links
      };

      $.ajax({
        method: 'POST',
        url: TaOnboardingL10n.ajax_url,
        dataType: 'json',
        data: {
          action: 'ta_onboarding_unset_content',
          _ajax_nonce: TaOnboardingL10n.unset_content_nonce,
          data: JSON.stringify(data)
        }
      })
      .done(function(response) {
        if(response && typeof response.success === 'boolean') {
          if(response.data !== undefined && response.data.count !== undefined && TaOnboardingL10n.has_imported_links) {
            if(response.data.count <= 0) {
              $('#ta-wizard-link-nav-continue').hide();
              $('#ta-wizard-create-select-link, #ta-wizard-link-nav-skip').show();
            }
          }
        }
        onboarding.re_render_links_list();
      });

      TaOnboardingL10n.content_id = 0;
    },

    select_existing_content: function () {
      if(working){
        return;
      }

      working = true;

      var data = {
        content_id: TaOnboardingL10n.content_id
      };

      $.ajax({
        method: 'POST',
        url: TaOnboardingL10n.ajax_url,
        dataType: 'json',
        data: {
          action: 'ta_onboarding_set_content',
          _ajax_nonce: TaOnboardingL10n.set_content_nonce,
          data: JSON.stringify(data)
        }
      })
      .done(function (response) {
        if($.magnificPopup) {
          $.magnificPopup.close();
        }

        if(response && typeof response.success === 'boolean') {
          $('#ta-wizard-create-select-link, #ta-wizard-link-nav-skip').hide();
          $('#ta-wizard-link-nav-continue').show();

          var $selected_content = $('#ta-wizard-selected-content');
          $selected_content.find('.ta-wizard-selected-content-heading').text(TaOnboardingL10n.link_name);
          $selected_content.find('.ta-wizard-selected-content-name').text(response.data.link_data.post_title);
          $selected_content.show();
        }
      })
      .fail(function () {
        alert('Request failed');
      })
      .always(function () {
        working = false;
      });
    },

    activate_license_key: function () {
      var $button = $(this),
        button_width = $button.width(),
        button_html = $button.html(),
        key = $('#ta-wizard-license-key').val();

      if (working || !key) {
        return;
      }

      working = true;
      $button.width(button_width).html(TaOnboardingL10n.loading_icon);
      $('#ta-wizard-activate-license-container').find('> .notice').remove();

      $.ajax({
        url: TaOnboardingL10n.ajax_url,
        method: 'POST',
        dataType: 'json',
        data: {
          action: 'ta_activate_license',
          _ajax_nonce: TaOnboardingL10n.activate_license_nonce,
          key: key,
          onboarding: 1
        }
      })
      .done(function (response) {
        if (!response || typeof response != 'object' || typeof response.success != 'boolean') {
          onboarding.activate_license_error('Request failed');
        } else if (!response.success) {
          onboarding.activate_license_error(response.data);
        } else if (response.data === true) {
          window.location.reload();
        } else {
          $('#ta-wizard-activate-license-container').html(response.data);
          $('#ta-wizard-license-nav-skip').addClass('ta-hidden');
          $('#ta-wizard-license-nav-continue').removeClass('ta-hidden');
        }
      })
      .fail(function () {
        onboarding.activate_license_error('Request failed');
      })
      .always(function () {
        working = false;
        $button.html(button_html).width('auto');
      });
    },

    activate_license_error: function (message) {
      $('#ta-wizard-activate-license-container').prepend(
        $('<div class="notice notice-error">').append(
          $('<p>').html(message)
        )
      );
    },

    deactivate_license: function () {
      var $button = $(this),
        button_width = $button.width(),
        button_html = $button.html();

      if (working || !confirm(TaOnboardingL10n.deactivate_confirm)) {
        return;
      }

      working = true;
      $button.width(button_width).html(TaOnboardingL10n.loading_icon);
      $('#ta-license-container').find('> .notice').remove();

      $.ajax({
        url: TaOnboardingL10n.ajax_url,
        method: 'POST',
        dataType: 'json',
        data: {
          action: 'tap_mothership_deactivate_license',
          _ajax_nonce: TaOnboardingL10n.deactivate_license_nonce
        }
      })
      .done(function (response) {
        if (!response || typeof response != 'object' || typeof response.success != 'boolean') {
          onboarding.deactivate_license_error('Request failed');
        } else if (!response.success) {
          onboarding.deactivate_license_error(response.data);
        } else {
          window.location.reload();
        }
      })
      .fail(function () {
        onboarding.deactivate_license_error('Request failed');
      })
      .always(function () {
        working = false;
        $button.html(button_html).width('auto');
      });
    },

    deactivate_license_error: function (message) {
      $('#ta-license-container').prepend(
        $('<div class="notice notice-error">').append(
          $('<p>').html(message)
        )
      );
    },

    finish: function () {
      var $button = $(this);

      if (working) {
        return;
      }

      working = true;
      $button.width($button.width()).html(TaOnboardingL10n.loading_icon);

      $.ajax({
        url: TaOnboardingL10n.ajax_url,
        method: 'POST',
        dataType: 'json',
        data: {
          action: 'ta_onboarding_finish',
          _ajax_nonce: TaOnboardingL10n.finish_nonce
        }
      })
      .always(function () {
        window.location = TaOnboardingL10n.thirstylink_url;
      });
    }
  };

  $(onboarding.init);

  return onboarding;
})(jQuery);