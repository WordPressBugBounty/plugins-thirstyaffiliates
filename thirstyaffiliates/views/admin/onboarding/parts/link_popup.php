<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<h2><?php esc_html_e('Create Link', 'thirstyaffiliates'); ?></h2>

<div id="ta-wizard-create-link-fields">
  <div class="ta-wizard-popup-field">
    <label for="ta-wizard-create-link-target-url"><?php esc_html_e('Destination URL', 'thirstyaffiliates'); ?></label>
    <input type="text" id="ta-wizard-create-link-target-url" placeholder="<?php esc_attr_e('Enter the URL where your link should redirect to.', 'thirstyaffiliates'); ?>">
  </div>

  <div class="ta-wizard-popup-field">
    <label for="ta-wizard-create-link-thirsty-link"><?php esc_html_e('SLUG', 'thirstyaffiliates'); ?></label>
    <input type="text" id="ta-wizard-create-link-thirsty-link" placeholder="<?php esc_attr_e('Enter the slug for your link.', 'thirstyaffiliates'); ?>">
  </div>

  <div class="ta-wizard-popup-field">
    <label for="ta-wizard-create-link-redirection"><?php esc_html_e('Redirection', 'thirstyaffiliates'); ?></label>
    <select id="ta-wizard-create-link-redirection">
      <option value="302"><?php esc_html_e('302 (Temporary)', 'thirstyaffiliates'); ?></option>
      <option value="301"><?php esc_html_e('301 (Permanent)', 'thirstyaffiliates'); ?></option>
      <option value="307"><?php esc_html_e('307 Temporary (alternative)', 'thirstyaffiliates'); ?></option>
    </select>
  </div>
</div>

<div class="ta-wizard-popup-button-row">
  <button type="button" id="ta-wizard-create-new-link-save" class="ta-wizard-button-blue"><?php esc_html_e('Save', 'thirstyaffiliates'); ?></button>
  <a target="_blank" class="ta-wizard-popuphelp" href="<?php echo admin_url('edit.php?post_type=thirstylink'); ?>">
    <?php
      printf(
        /* translators: %1$s: open underline tag, %2$s: close underline tag */
        esc_html__('More advanced options are available on the %1$sThirstyAffiliates%2$s page', 'thirstyaffiliates'),
        '<u>',
        '</u>'
      );
    ?>
  </a>
</div>