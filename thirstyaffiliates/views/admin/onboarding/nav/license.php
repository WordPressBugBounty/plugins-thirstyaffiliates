<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<?php $li = get_site_transient('tap_license_info'); ?>

<?php if(!$li): ?>
  <div id="ta-wizard-license-nav-skip">
    <button type="button" class="ta-wizard-button-link ta-wizard-go-to-step" data-step="2" data-context="skip"><span><?php esc_html_e('Skip', 'thirstyaffiliates'); ?></span></button>
  </div>
<?php else: ?>
  <div id="ta-wizard-license-nav-continue">
    <button type="button" class="ta-wizard-button-blue ta-wizard-go-to-step" data-step="2" data-context="continue"><?php esc_html_e('Continue', 'thirstyaffiliates'); ?></button>
  </div>
<?php endif; ?>