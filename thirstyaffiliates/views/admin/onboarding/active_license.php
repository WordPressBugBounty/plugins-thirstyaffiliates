<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<div id="ta-license-container" class="ta-wizard-license-container">
  <div class="ta-wizard-license">
    <div class="ta-wizard-license-notice">
      <img src="<?php echo esc_url($this->_constants->IMAGES_ROOT_URL() . 'onboarding/green-check.svg'); ?>" alt="">
      <?php
        $expires_at = null;

        if(isset($li['license_key']['expires_at'])) {
          $expires_at = date_create($li['license_key']['expires_at']);
        }

        if($expires_at instanceof DateTime) {
          $timestamp = $expires_at->getTimestamp();
          $date = wp_date('F j, Y', $timestamp);

          echo esc_html(
            sprintf(
              __('License activated until %s', 'thirstyaffiliates'),
              $date
            )
          );
        }
        else {
          esc_html_e('License activated', 'thirstyaffiliates');
        }
      ?>
    </div>
    <div class="ta-wizard-license-details">
      <div>
        <div class="ta-wizard-license-label">
          <?php esc_html_e('Account email', 'thirstyaffiliates'); ?>
        </div>
        <div class="ta-wizard-license-value">
          <?php echo esc_html(!empty($li['user']['email']) ? $li['user']['email'] : __('Unknown', 'thirstyaffiliates')); ?>
        </div>
      </div>
      <div>
        <div class="ta-wizard-license-label">
          <?php esc_html_e('Product', 'thirstyaffiliates'); ?>
        </div>
        <div class="ta-wizard-license-value">
          <?php echo esc_html($li['product_name']); ?>
        </div>
      </div>      
      <div>
        <div class="ta-wizard-license-label">
          <?php esc_html_e('License Key', 'thirstyaffiliates'); ?>
        </div>
        <div class="ta-wizard-license-value">
          ********-****-****-****-<?php echo esc_html( substr( $li['license_key']['license'], - 12 ) ); ?>
        </div>
      </div>
      <div>
        <div class="ta-wizard-license-label">
          <?php esc_html_e('Activations', 'thirstyaffiliates'); ?>
        </div>
        <div class="ta-wizard-license-value">
          <?php
            printf(
              // translators: %1$d: activation count, %2$d: max activations
              esc_html__('%1$d of %2$d sites have been activated with this license key', 'thirstyaffiliates'),
              esc_html($li['activation_count']),
              esc_html(ucwords($li['max_activations']))
            );
          ?>
        </div>
      </div>
    </div>
    <div class="ta-wizard-license-manage">
      <a href="https://thirstyaffiliates.com/account/" target="_blank"><?php esc_html_e('Manage activations', 'thirstyaffiliates'); ?></a>
    </div>
    <div class="ta-wizard-license-deactivate">
      <button type="button" id="ta-deactivate-license-key" class="ta-wizard-button-secondary"><?php esc_html_e('Deactivate License', 'thirstyaffiliates'); ?></button>
    </div>
  </div>
</div>
