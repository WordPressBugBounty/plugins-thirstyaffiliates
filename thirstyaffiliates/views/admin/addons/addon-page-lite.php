<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<div id="ta-admin-addons" class="wrap thirstyaffiliates-blur-wrap">
  <div class="thirstyaffiliates-blur">
    <h2><?php esc_html_e('ThirstyAffiliates Add-ons', 'thirstyaffiliates'); ?><a href="#" class="add-new-h2 ta-addons-refresh"><?php esc_html_e('Refresh Add-ons', 'thirstyaffiliates'); ?></a><input type="search" id="ta-addons-search" placeholder="<?php esc_attr_e('Search add-ons', 'thirstyaffiliates'); ?>" disabled></h2>

    <p>
      <?php
        printf(
          esc_html__('Improve your links with our premium add-ons. Missing an add-on that you think you should be able to see? Click the <a href="#">Refresh Add-ons</a> button above.', 'thirstyaffiliates')
        );
      ?>
    </p>

    <h4><?php esc_html_e('Available Add-ons', 'thirstyaffiliates'); ?></h4>

    <div id="ta-addons-container">
      <div class="ta-addons ta-clearfix">
        <div class="ta-addon ta-addon-status-inactive">
          <div class="ta-addon-details">
            <img src="https://ta-add-on-icons.s3.amazonaws.com/400x400/ta-display-icon_400x400.png" alt="<?php esc_attr_e('Thirsty Affiliates Product Displays', 'thirstyaffiliates'); ?>">
            <h5 class="ta-addon-name"><?php esc_html_e('Product Displays', 'thirstyaffiliates'); ?></h5>
            <p><?php esc_html_e('Add beautifully styled product displays to your site.', 'thirstyaffiliates'); ?></p>
          </div>
          <div class="ta-addon-actions ta-clearfix">
            <div class="ta-addon-status">
              <strong><?php esc_html_e('Status: ', 'thirstyaffiliates'); ?><span class="ta-addon-status-label"><?php esc_html_e('Inactive', 'thirstyaffiliates'); ?></span></strong>
            </div>
            <div class="ta-addon-action">
              <button type="button" disabled>
                <i class="ta-icon ta-icon-toggle-on ta-flip-horizontal"></i>
                <?php esc_html_e('Activate', 'thirstyaffiliates'); ?>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php include_once $this->_constants->VIEWS_ROOT_PATH() . "/admin/upgrade/dialog.php"; ?>
</div>