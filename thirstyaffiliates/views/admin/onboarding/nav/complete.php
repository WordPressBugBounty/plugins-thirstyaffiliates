<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<?php
$youtube_video_id = '';
$step = 6;

require $this->_constants->VIEWS_ROOT_PATH() . 'admin/onboarding/video.php';
?>
<div>
  <button type="button" id="ta-wizard-finish-onboarding" class="ta-wizard-button-blue"><span><?php esc_html_e('Finish', 'thirstyaffiliates'); ?></span></button>
</div>
