<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<?php
return; 
$youtube_video_hash = md5($youtube_video_id);
?>

<div class="ta-wizard-onboarding-video-wrapper ta-wizard-onboarding-video-<?php echo esc_attr($step); ?>" id="wrapper_<?php echo $youtube_video_hash; ?>" >
   <div  class="ta-wizard-onboarding-video-expand" id="expand_<?php echo $youtube_video_hash; ?>" data-id="<?php echo $youtube_video_hash; ?>">
  <img src="<?php echo $this->_constants->IMAGES_ROOT_URL() . 'onboarding/expand.png'; ?>" class="ta-animation-shaking" />
</div>
  <div class="ta-video-wrapper" id="inner_<?php echo $youtube_video_hash; ?>">

    <div class='ta-video-holder' id="holder_<?php echo $youtube_video_hash; ?>">
         <a href='#' class='ta-video-play-button' id="ta_play_<?php echo $youtube_video_hash; ?>" data-hash="<?php echo $youtube_video_hash; ?>"  data-holder-id="holder_<?php echo $youtube_video_hash; ?>" data-id='<?php echo esc_attr($youtube_video_id); ?>'></a>
    </div>
  </div>
  <div class="ta-wizard-onboarding-video-collapse" data-id="<?php echo $youtube_video_hash; ?>">
    <img src="<?php echo $this->_constants->IMAGES_ROOT_URL() . 'onboarding/collapse.png'; ?>" />
  </div>
</div>