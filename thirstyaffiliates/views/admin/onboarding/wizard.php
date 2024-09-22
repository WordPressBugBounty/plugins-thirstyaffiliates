<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<?php 
use ThirstyAffiliates\Helpers\Onboarding_Helper;
?>
<div class="ta-wizard">
  <div class="ta-wizard-inner">
    <div class="ta-onboarding-logo">
      <img src="<?php echo esc_url( $this->_constants->IMAGES_ROOT_URL() . 'TA.svg' ); ?>" alt="">
    </div>
    <div class="ta-wizard-steps">
      <?php
        $onboarding_steps_completed = Onboarding_Helper::get_steps_completed();
        $next_applicable_step = $onboarding_steps_completed + 1;

        foreach($steps as $key => $step) {
          printf('<div class="ta-wizard-step ta-wizard-step-%s">', $key + 1);
          echo '<div class="ta-wizard-progress-steps">';

          foreach($steps as $progress_key => $progress_step) {
            $link_step = $progress_step['step'];

            $skipped_steps = Onboarding_Helper::get_skipped_steps();
            $css_class = '';

            if($progress_key == $key){
               $css_class .= ' ta-wizard-current-step';
            }

            if(in_array($link_step, $skipped_steps) && $progress_key != $key){
              $css_class .= ' ta-wizard-current-step-skipped';
            }

            printf(
              '<div class="ta_onboarding_step_%s ta-wizard-progress-step%s"><span></span><a href="%s">%s</a></div>',
              $link_step,
              $css_class,
              admin_url('options.php?page=thirstyaffiliates_onboarding&step='.(int)$link_step),
              esc_html($progress_step['title'])
            );

          }

          echo '</div>';
          if(file_exists($step['content'])){
            require $step['content'];
          }
          echo '</div>';
        }
      ?>
    </div>
  </div>
  <div class="ta-wizard-nav">
    <?php
      foreach($steps as $key => $step) {
        printf(
          '<div class="ta-wizard-nav-step ta-wizard-nav-step-%1$s">',
          $key + 1
        );

        if(file_exists($step['nav'])) {
          require $step['nav'];
        }

        echo '</div>';
      }
    ?>
  </div>
</div>
