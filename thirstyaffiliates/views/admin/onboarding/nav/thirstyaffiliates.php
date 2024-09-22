<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<?php
$youtube_video_id = '';
$step = 3;

require $this->_constants->VIEWS_ROOT_PATH() . 'admin/onboarding/video.php';
?>
<div id="ta-wizard-link-nav-skip">
  <button type="button" class="ta-wizard-button-link ta-wizard-go-to-step" data-step="5" data-context="skip"><span><?php esc_html_e('Skip', 'thirstyaffiliates'); ?></span></button>
</div>
<div id="ta-wizard-link-nav-continue" class="ta-hidden">
  <button type="button" class="ta-wizard-button-blue ta-wizard-go-to-step" data-step="4" data-context="continue"><?php esc_html_e('Continue', 'thirstyaffiliates'); ?></button>
</div>
