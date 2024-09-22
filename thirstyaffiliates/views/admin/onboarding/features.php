<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<?php
use ThirstyAffiliates\Helpers\Onboarding_Helper;

$features = Onboarding_Helper::get_selected_features();
$features_addons_selectable = Onboarding_Helper::features_addons_selectable_list();
?>
<h2 class="ta-wizard-step-title"><?php esc_html_e('Gear Up with ThirstyAffiliates Goodies', 'thirstyaffiliates'); ?></h2>
<p class="ta-wizard-step-description"><?php esc_html_e('We\'ve got a whole bunch of cool link tools ready for you to dive into. Just choose the ones you want to enable right away, then we’ll handle the setup. ', 'thirstyaffiliates'); ?></p>
<div class="ta-wizard-features">
  <div class="ta-wizard-feature">
    <div>
      <h3><?php esc_html_e('Link Tracking', 'thirstyaffiliates'); ?></h3>
      <p><?php esc_html_e('Monitor clicks, engagement, conversions, and more, enabling data-driven choices that magnify your marketing strategies.', 'thirstyaffiliates'); ?></p>
    </div>
    <div class="ta-wizard-feature-right">
      <input type="checkbox" class="ta-wizard-feature-input" value="thirstyaffiliates-link-tracking" <?php checked(in_array('thirstyaffiliates-link-tracking', $features, true)); ?>>
      <img class="ta-wizard-feature-checked" src="<?php echo esc_url($this->_constants->IMAGES_ROOT_URL() . 'onboarding/checkbox-checked.svg'); ?>" alt="">
      <img class="ta-wizard-feature-unchecked" src="<?php echo esc_url($this->_constants->IMAGES_ROOT_URL() . 'onboarding/checkbox-unchecked.svg'); ?>" alt="">
    </div>
  </div>  
  <div class="ta-wizard-feature">
    <div>
      <h3><?php esc_html_e('Uncloak Links', 'thirstyaffiliates'); ?></h3>
      <p><?php esc_html_e('Adjust cloaked URLs to reveal their true destination, ensuring compliance with stricter affiliate program standards, such as Amazon Associates.', 'thirstyaffiliates'); ?></p>
    </div>
    <div class="ta-wizard-feature-right">
      <input type="checkbox" class="ta-wizard-feature-input" value="thirstyaffiliates-uncloak-links" <?php checked(in_array('thirstyaffiliates-uncloak-links', $features, true)); ?>>
      <img class="ta-wizard-feature-checked" src="<?php echo esc_url($this->_constants->IMAGES_ROOT_URL() . 'onboarding/checkbox-checked.svg'); ?>" alt="">
      <img class="ta-wizard-feature-unchecked" src="<?php echo esc_url($this->_constants->IMAGES_ROOT_URL() . 'onboarding/checkbox-unchecked.svg'); ?>" alt="">
    </div>
  </div>
  <div class="ta-wizard-feature">
    <div>
      <h3><?php esc_html_e('AutoLinker', 'thirstyaffiliates'); ?></h3>
      <p><?php esc_html_e('Effortlessly connect keywords and phrases to your affiliate links, driving more visitors to essential pages and giving your click-through rates a boost.', 'thirstyaffiliates'); ?></p>
    </div>
    <div class="ta-wizard-feature-right">
      <input type="checkbox" class="ta-wizard-feature-input" value="thirstyaffiliates-keyword-replacements" <?php checked(in_array('thirstyaffiliates-keyword-replacements', $features, true)); ?>>
      <img class="ta-wizard-feature-checked" src="<?php echo esc_url($this->_constants->IMAGES_ROOT_URL() . 'onboarding/checkbox-checked.svg'); ?>" alt="">
      <img class="ta-wizard-feature-unchecked" src="<?php echo esc_url($this->_constants->IMAGES_ROOT_URL() . 'onboarding/checkbox-unchecked.svg'); ?>" alt="">
    </div>
  </div>
  <div class="ta-wizard-feature">
    <div>
      <h3><?php esc_html_e('Event Notifications', 'thirstyaffiliates'); ?></h3>
      <p><?php esc_html_e('Receive real-time notifications when your links hit specific click milestones, staying in the loop on your link performance.', 'thirstyaffiliates'); ?></p>
    </div>
    <div class="ta-wizard-feature-right">
      <input type="checkbox" class="ta-wizard-feature-input" value="thirstyaffiliates-event-notifications" <?php checked(in_array('thirstyaffiliates-event-notifications', $features, true)); ?>>
      <img class="ta-wizard-feature-checked" src="<?php echo esc_url($this->_constants->IMAGES_ROOT_URL() . 'onboarding/checkbox-checked.svg'); ?>" alt="">
      <img class="ta-wizard-feature-unchecked" src="<?php echo esc_url($this->_constants->IMAGES_ROOT_URL() . 'onboarding/checkbox-unchecked.svg'); ?>" alt="">
    </div>
  </div>
  <div class="ta-wizard-feature">
    <div>
      <h3><?php esc_html_e('Amazon Import', 'thirstyaffiliates'); ?></h3>
      <p><?php esc_html_e('Automatically add Amazon product links to your affiliate stash, eliminating the need for manual copying and pasting. ', 'thirstyaffiliates'); ?></p>
    </div>
    <div class="ta-wizard-feature-right">
      <input type="checkbox" class="ta-wizard-feature-input" value="thirstyaffiliates-amazon-import" <?php checked(in_array('thirstyaffiliates-amazon-import', $features, true)); ?>>
      <img class="ta-wizard-feature-checked" src="<?php echo esc_url($this->_constants->IMAGES_ROOT_URL() . 'onboarding/checkbox-checked.svg'); ?>" alt="">
      <img class="ta-wizard-feature-unchecked" src="<?php echo esc_url($this->_constants->IMAGES_ROOT_URL() . 'onboarding/checkbox-unchecked.svg'); ?>" alt="">
    </div>
  </div>
  <div class="ta-wizard-feature">
    <div>
      <h3><?php esc_html_e('Link Health', 'thirstyaffiliates'); ?></h3>
      <p><?php esc_html_e('Stay on top of broken links with instant notifications, so you can take immediate action, enhance user experience, and maintain a healthy website. ', 'thirstyaffiliates'); ?></p>
    </div>
    <div class="ta-wizard-feature-right">
      <input type="checkbox" class="ta-wizard-feature-input" value="thirstyaffiliates-link-health" <?php checked(in_array('thirstyaffiliates-link-health', $features, true)); ?>>
      <img class="ta-wizard-feature-checked" src="<?php echo esc_url($this->_constants->IMAGES_ROOT_URL() . 'onboarding/checkbox-checked.svg'); ?>" alt="">
      <img class="ta-wizard-feature-unchecked" src="<?php echo esc_url($this->_constants->IMAGES_ROOT_URL() . 'onboarding/checkbox-unchecked.svg'); ?>" alt="">
    </div>
  </div>

  <div class="ta-wizard-feature">
    <div>
      <h3><?php esc_html_e('Import/Export Links', 'thirstyaffiliates'); ?></h3>
      <p><?php esc_html_e('Move your affiliate links in and out with a simple click, making link management a breeze and saving you valuable time and effort.', 'thirstyaffiliates'); ?></p>
    </div>
    <div class="ta-wizard-feature-right">
      <input type="checkbox" class="ta-wizard-feature-input" value="thirstyaffiliates-import-export" <?php checked(in_array('thirstyaffiliates-import-export', $features, true)); ?>>
      <img class="ta-wizard-feature-checked" src="<?php echo esc_url($this->_constants->IMAGES_ROOT_URL() . 'onboarding/checkbox-checked.svg'); ?>" alt="">
      <img class="ta-wizard-feature-unchecked" src="<?php echo esc_url($this->_constants->IMAGES_ROOT_URL() . 'onboarding/checkbox-unchecked.svg'); ?>" alt="">
    </div>
  </div>

  <div class="ta-wizard-feature">
    <div>
      <h3><?php esc_html_e('Geographic Redirects', 'thirstyaffiliates'); ?></h3>
      <p><?php esc_html_e('Direct users to different destinations based on their geographical location, enhancing user experience and engagement.', 'thirstyaffiliates'); ?></p>
    </div>
    <div class="ta-wizard-feature-right">
      <input type="checkbox" class="ta-wizard-feature-input" value="thirstyaffiliates-geographic-redirects" <?php checked(in_array('thirstyaffiliates-geographic-redirects', $features, true)); ?>>
      <img class="ta-wizard-feature-checked" src="<?php echo esc_url($this->_constants->IMAGES_ROOT_URL() . 'onboarding/checkbox-checked.svg'); ?>" alt="">
      <img class="ta-wizard-feature-unchecked" src="<?php echo esc_url($this->_constants->IMAGES_ROOT_URL() . 'onboarding/checkbox-unchecked.svg'); ?>" alt="">
    </div>
  </div>
  
  <div class="ta-wizard-feature">
    <div>
      <h3><?php esc_html_e('Product Displays', 'thirstyaffiliates'); ?></h3>
      <p><?php esc_html_e('Transform basic text links into vibrant displays with stunning visuals, intriguing descriptions, and compelling CTAs.', 'thirstyaffiliates'); ?></p>
    </div>
    <div class="ta-wizard-feature-right">
    <?php if($features_addons_selectable['thirstyaffiliates-product-displays']): ?>
      <input type="checkbox" class="ta-wizard-feature-input ta-wizard-plugin" value="thirstyaffiliates-product-displays" <?php checked(in_array('thirstyaffiliates-product-displays', $features, true)); ?>>
      <img class="ta-wizard-feature-checked" src="<?php echo esc_url($this->_constants->IMAGES_ROOT_URL() . 'onboarding/checkbox-checked.svg'); ?>" alt="">
      <img class="ta-wizard-feature-unchecked" src="<?php echo esc_url($this->_constants->IMAGES_ROOT_URL() . 'onboarding/checkbox-unchecked.svg'); ?>" alt="">
    <?php else: ?>
      <input type="hidden" class="ta-wizard-feature-input-active" value="thirstyaffiliates-product-displays">
      <img src="<?php echo esc_url($this->_constants->IMAGES_ROOT_URL() . 'onboarding/checkbox-disabled.svg'); ?>" alt="">
    <?php endif; ?>
    </div>
  </div>

  <div class="ta-wizard-feature">
    <div>
      <h3><?php esc_html_e('MonsterInsights', 'thirstyaffiliates'); ?></h3>
      <p><?php esc_html_e('Collect comprehensive data using Google Analytics for deeper insights into link performance, user behavior, and conversions.', 'thirstyaffiliates'); ?></p>
    </div>
    <div class="ta-wizard-feature-right">
    <?php if($features_addons_selectable['monsterinsights']): ?>
      <input type="checkbox" class="ta-wizard-feature-input ta-wizard-plugin" value="monsterinsights" <?php checked(in_array('monsterinsights', $features, true)); ?>>
      <img class="ta-wizard-feature-checked" src="<?php echo esc_url($this->_constants->IMAGES_ROOT_URL() . 'onboarding/checkbox-checked.svg'); ?>" alt="">
      <img class="ta-wizard-feature-unchecked" src="<?php echo esc_url($this->_constants->IMAGES_ROOT_URL() . 'onboarding/checkbox-unchecked.svg'); ?>" alt="">
    <?php else: ?>
      <input type="hidden" class="ta-wizard-feature-input-active" value="monsterinsights">
      <img src="<?php echo esc_url($this->_constants->IMAGES_ROOT_URL() . 'onboarding/checkbox-disabled.svg'); ?>" alt="">
    <?php endif; ?>
    </div>
  </div>

</div>
<p class="ta-wizard-plugins-to-install">
  <?php
    printf(
    __('If your subscription level allows, the following plugins will be installed automatically: %s', 'thirstyaffiliates'),
      '<span></span> <br /><br /><strong>Want a feature your membership level doesn’t support? No worries! You’ll get the chance to upgrade later in the onboarding wizard.</strong>'
    );
  ?>
</p>
