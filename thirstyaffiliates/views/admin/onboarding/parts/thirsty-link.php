<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<?php
use ThirstyAffiliates\Helpers\Onboarding_Helper;

$features_enabled = Onboarding_Helper::get_selected_features();
$import_export_selected = in_array('thirstyaffiliates-import-export', $features_enabled);
$has_imported_links = Onboarding_Helper::get_has_imported_links();
?>

<div id="ta-wizard-create-select-link">
  <h2 class="ta-wizard-step-title"><?php esc_html_e("Create Your ThirstyAffiliates Link", 'thirstyaffiliates'); ?></h2>
  <p class="ta-wizard-step-description"><?php esc_html_e("Whether you're a newcomer with a single affiliate link or a seasoned marketer with several, let's supercharge them for an electrifying start!", 'thirstyaffiliates'); ?></p>
  <p class="ta-wizard-step-description"><?php esc_html_e("Click the button below, and we'll take you step by step through the process of creating your first visually appealing, brand-boosting affiliate link. ", 'thirstyaffiliates'); ?></p>

  <?php if($import_export_selected): ?>
    <p class="ta-wizard-step-description"><?php _e("<i>Already have a long list of links you'd like to migrate over into ThirstyAffiliates?</i> Click \"Import Existing Links\" and have them automatically converted to thirsty links.", 'thirstyaffiliates'); ?></p>
  <?php endif; ?>

  <div class="ta-wizard-button-group">
    <button type="button" id="ta-wizard-create-new-link" class="ta-wizard-button-blue"><?php esc_html_e('Create Your First Link', 'thirstyaffiliates'); ?></button>

    <?php if($import_export_selected): ?>
      <button type="button" id="ta-wizard-import-links" class="ta-wizard-button-link"><span><?php esc_html_e('Import Existing Links', 'thirstyaffiliates'); ?></span></button>
    <?php endif; ?>
  </div>
</div>

<div id="ta-wizard-create-new-link-popup" class="ta-wizard-popup mfp-hide"></div>

<div id="ta-wizard-import-links-popup" class="ta-wizard-popup mfp-hide">
  <h2><?php esc_html_e('Import Your Links', 'thirstyaffiliates'); ?></h2>

  <?php if(Onboarding_Helper::is_pro_active()): ?>
    <p><?php esc_html_e('To convert your existing links into thirsty links, you\'ll need a CSV file that\'s compatible with the plugin.', 'thirstyaffiliates'); ?></p>
    <p><?php esc_html_e('For an example of a properly formatted ThirstyAffiliates CSV file, please click the "Download Sample CSV" button below.', 'thirstyaffiliates'); ?></p>

    <?php printf(
            __('<a class="ta-wizard-button-secondary" href="%s">Download Sample CSV</a>', 'thirstyaffiliates'),
            esc_url('https://thirstyaffiliates.com/wp-content/plugins/thirstyaffiliates-pro/sample.csv')
          );
    ?>

    <p>
      <?php printf(
            __('For more detailed instructions on how to configure your CSV file, check out the instructional video on the left-hand side of the page or refer to the <a href="%s" class="ta-wizard-kb-link" target="_blank">Importing and Exporting Links</a> article in our knowledge base.', 'thirstyaffiliates'),
            esc_url('https://thirstyaffiliates.com/knowledgebase/how-to-import-your-affiliate-links/')
          );
      ?>
    </p>

    <div class="ta-wizard-popup-field">
      <input type="file" id="ta-wizard-import-file" name="importedfile">
    </div>

    <div id="tap-import-progress" class="ta-hidden">
      <h3><?php esc_html_e( 'CSV Results', 'thirstyaffiliates-pro' ); ?></h3>

      <div class="tap-import-progress-information">
        <div class="tap-progress-spinner"></div>
        <div class="tap-progress-bar"></div>
      </div>

      <div class="tap-import-progress-data">
        <div class="tap-import-links-information">
          <div id="tap-import-successful-links"></div>
          <div id="tap-import-failed-links"></div>
        </div>
        <div class="tap-import-links-lists">
          <h4><?php esc_html_e( 'Successful Rows:', 'thirstyaffiliates-pro' ); ?></h4>
          <textarea id="tap-import-successful-rows" class="ta-wizard-import-rows"></textarea>

          <h4><?php esc_html_e( 'Failed Rows:', 'thirstyaffiliates-pro' ); ?></h4>
          <textarea id="tap-import-failed-rows" class="ta-wizard-import-rows"></textarea>
        </div>
      </div>
    </div>

    <div class="ta-wizard-popup-button-row">
      <button type="button" id="ta-wizard-import-links-save" class="ta-wizard-button-blue"><?php esc_html_e('Import', 'thirstyaffiliates'); ?></button>
    </div>
  <?php else: ?>
    <p><?php _e('<i>Oops!</i> ThirstyAffiliates Lite cannot access the Importer feature. Upgrade to ThirstyAffiliates Pro to unlock this awesome functionality and enjoy all its benefits.', 'thirstyaffiliates'); ?></p>

    <p><?php esc_html_e('Don\'t want to upgrade yet? Click the "Create New Link" button to get started with creating your first link.', 'thirstyaffiliates'); ?></p>

    <?php printf(
            __('<a class="ta-wizard-button-blue" href="%s">Upgrade to ThirstyAffiliates Pro Now</a>', 'thirstyaffiliates'),
            esc_url(Onboarding_Helper::get_upgrade_pricing_url())
          );
    ?>
  <?php endif; ?>
</div>

<?php if($has_imported_links): ?>
  <div id="ta-wizard-choose-link-results">
    <?php echo ThirstyAffiliates()->get_model('Onboarding')->render_links_list(); ?>
  </div>
<?php else: ?>
  <div id="ta-wizard-selected-content" class="ta-hidden">
    <h2 class="ta-wizard-step-title"><?php esc_html_e('Your Link', 'thirstyaffiliates'); ?></h2>
    <div class="ta-wizard-selected-content">
      <div>
        <div class="ta-wizard-selected-content-heading"></div>
        <div class="ta-wizard-selected-content-name"></div>
      </div>
      <div>
        <div class="ta-wizard-selected-content-expand-menu" data-id="ta-wizard-selected-content-menu">
          <img src="<?php echo esc_url($this->_constants->IMAGES_ROOT_URL() . 'onboarding/expand-menu.svg'); ?>" alt="">
        </div>
        <div id="ta-wizard-selected-content-menu" class="ta-wizard-selected-content-menu ta-hidden">
          <div class="ta-wizard-selected-content-delete"><?php esc_html_e('Remove', 'thirstyaffiliates'); ?></div>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>