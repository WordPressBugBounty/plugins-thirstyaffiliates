<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<?php
use ThirstyAffiliates\Helpers\Onboarding_Helper;
?>
<h2 class="ta-wizard-finished"><?php esc_html_e("That’s It! You're Ready to Roll", 'thirstyaffiliates'); ?></h2>
<div id="ta-wizard-completed">
  <div id="ta-wizard-content-section"><?php echo Onboarding_Helper::get_completed_step_urls_html(); ?></div>

  <h2 class="ta-wizard-step-title"><?php esc_html_e('Your next step...', 'thirstyaffiliates'); ?></h2>
  <div class="ta-wizard-selected-content ta-wizard-selected-content-full-scape">
    <div class="ta-wizard-selected-content-column">
        <div class="ta-wizard-selected-content-image-box">
          <div class="ta-wizard-selected-content-image-thumbnail">
            <img src="<?php echo $this->_constants->IMAGES_ROOT_URL(); ?>onboarding/subscribe-to-blog-ta-wizard.png" alt="<?php esc_html_e('Subscribe to the ThirstyAffiliates Blog','thirstyaffiliates'); ?>" />
          </div>
          <div class="ta-wizard-selected-content-image-description">
             <a href="https://thirstyaffiliates.com/blog/" target="_blank">
              <h4 class="ta-image-title"><?php esc_html_e('Subscribe to the ThirstyAffiliates Blog','thirstyaffiliates'); ?></h4>
              <p class="ta-image-desc"><?php esc_html_e('Stay up to date on all the smart moves, handy hacks, and inside scoops on amplifying your affiliate link earnings.','thirstyaffiliates'); ?></p>
            </a>
          </div>
        </div>
    </div>
  </div>

  <div class="ta-wizard-selected-content ta-wizard-selected-content-full-scape">
    <div class="ta-wizard-selected-content-column">
      <div class="ta-wizard-selected-content-image-box">
        <div class="ta-wizard-selected-content-image-thumbnail">
          <img src="<?php echo $this->_constants->IMAGES_ROOT_URL(); ?>onboarding/thirstypay-wizard.png" alt="<?php esc_html_e('NEW! Start Selling with ThirstyPay™','thirstyaffiliates'); ?>" />
        </div>
        <div class="ta-wizard-selected-content-image-description">
          <a href="https://thirstyaffiliates.com/knowledgebase/getting-started-with-thirstypay-links" target="_blank">
            <h4 class="ta-image-title"><?php esc_html_e('NEW! Start Selling with ThirstyPay™','thirstyaffiliates'); ?></h4>
            <p class="ta-image-desc"><?php _e('<i>Selling your own creations?</i> Create custom payment links for easy peasy, personalized transactions that put money in your pocket with every click!','thirstyaffiliates'); ?></p>
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="ta-wizard-selected-content ta-wizard-selected-content-full-scape">
    <div class="ta-wizard-selected-content-column">
        <div class="ta-wizard-selected-content-image-box">
          <div class="ta-wizard-selected-content-image-thumbnail">
            <img src="<?php echo $this->_constants->IMAGES_ROOT_URL(); ?>onboarding/how-to-use-thirstyaffiliates.jpg" alt="<?php esc_html_e('Bookmark Our Support Docs','thirstyaffiliates'); ?>" />
          </div>
          <div class="ta-wizard-selected-content-image-description">
            <a href="https://thirstyaffiliates.com/knowledge-base" target="_blank">
              <h4 class="ta-image-title"><?php esc_html_e('Bookmark Our Support Docs','thirstyaffiliates'); ?></h4>
              <p class="ta-image-desc"><?php esc_html_e('Keep our Support Docs in your digital back pocket! They\'re packed with handy tips whenever you have a question or need a quick fix.','thirstyaffiliates'); ?></p>
            </a>
          </div>
        </div>
    </div>
  </div>
</div>
