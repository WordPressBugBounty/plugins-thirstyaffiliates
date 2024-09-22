<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<div id="ta-wizard-create-select-category">
  <h2 class="ta-wizard-step-title"><?php esc_html_e("Time to Get Organized!", 'thirstyaffiliates'); ?></h2>
  <p class="ta-wizard-step-description"><?php esc_html_e("Sort your links into categories for a super sleek system ready to roll. No more link chaos – just quick access and a breeze to manage. No matter how many links you pile up!", 'thirstyaffiliates'); ?></p>

  <div class="ta-wizard-button-group">
    <button type="button" id="ta-wizard-create-new-category" class="ta-wizard-button-blue"><?php esc_html_e('Create New Category', 'thirstyaffiliates'); ?></button>
  </div>
</div>

<div id="ta-wizard-selected-category" class="ta-hidden">
  <h2 class="ta-wizard-step-title"><?php esc_html_e('Your Category', 'thirstyaffiliates'); ?></h2>
  <div class="ta-wizard-selected-content ta-wizard-selected-content-full-scape">
    <div class="ta-wizard-selected-content-column">
      <div class="ta-wizard-selected-content-heading"><?php esc_html_e('Category Name', 'thirstyaffiliates'); ?></div>
      <div class="ta-wizard-selected-content-name" id="ta-selected-category-name"></div>
    </div>
    <hr>
    <div class="ta-wizard-selected-content-column">
      <div class="ta-wizard-selected-content-heading"><?php esc_html_e('Category Slug', 'thirstyaffiliates'); ?></div>
      <div class="ta-wizard-selected-content-name"  id="ta-selected-category-slug"></div>
    </div>
    <hr>
    <div class="ta-wizard-selected-content-column">
      <div class="ta-wizard-selected-content-heading"><?php esc_html_e('Count','thirstyaffiliates'); ?></div>
      <div class="ta-wizard-selected-content-name"  id="ta-selected-category-count"></div>
    </div>
      <div class="ta-wizard-selected-content-expand-menu" data-id="ta-wizard-selected-category-menu">
        <img src="<?php echo esc_url($this->_constants->IMAGES_ROOT_URL() . 'onboarding/expand-menu.svg'); ?>" alt="">
      </div>
      <div id="ta-wizard-selected-category-menu" class="ta-wizard-selected-content-menu ta-hidden">
        <div id="ta-wizard-selected-category-delete"><?php esc_html_e('Remove', 'thirstyaffiliates'); ?></div>
      </div>
  </div>
</div>

<div id="ta-wizard-create-new-category-popup" class="ta-wizard-popup ta-wizard-popup-create-category mfp-hide">
  <form id="ta-wizard-create-new-category-form">
    <h2><?php esc_html_e('Create Category', 'thirstyaffiliates'); ?></h2>

    <div class="ta-wizard-popup-field">
      <label for="ta-wizard-create-category-name"><?php esc_html_e('Category Name', 'thirstyaffiliates'); ?></label>
      <input type="text" id="ta-wizard-create-category-name" placeholder="<?php esc_attr_e('Enter the name of your category.', 'thirstyaffiliates'); ?>">
    </div>

    <div class="ta-wizard-popup-button-row">
      <button type="button" id="ta-wizard-create-new-category-save" class="ta-wizard-button-blue"><?php esc_html_e('Save', 'thirstyaffiliates'); ?></button>
    </div>
  </form>
</div>