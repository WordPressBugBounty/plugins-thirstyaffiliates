<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<div id="ta-wizard-links-list-container">
  <?php
    $total_links = count($pretty_links);
    $links_per_page = 15;
    $total_pages = ceil($total_links / $links_per_page);

    $offset = ($current_page - 1) * $links_per_page;

    $paged_links = array_slice($pretty_links, $offset, $links_per_page);
  ?>

  <?php if(count($paged_links)): ?>
    <div id="ta-wizard-selected-content" class="ta-wizard-created-links">
      <div>
        <h2 class="ta-wizard-step-title"><?php esc_html_e('Your Affiliate Links', 'thirstyaffiliates'); ?></h2>
      </div>

      <?php foreach($paged_links as $link): ?>
        <div class="ta-wizard-selected-content" id="ta-wizard-selected-content-<?php echo esc_attr($link->ID); ?>">
          <div>
            <div class="ta-wizard-selected-content-heading"><?php esc_html_e('Link Name', 'thirstyaffiliates'); ?></div>
            <div class="ta-wizard-selected-content-name"><?php echo esc_html($link->post_title); ?></div>
          </div>
          <div>
            <div class="ta-wizard-selected-content-expand-menu" data-id="ta-wizard-selected-content-menu-<?php echo esc_attr($link->ID); ?>">
              <img src="<?php echo esc_url($this->_constants->IMAGES_ROOT_URL() . 'onboarding/expand-menu.svg'); ?>">
            </div>
            <div id="ta-wizard-selected-content-menu-<?php echo esc_attr($link->ID); ?>" class="ta-wizard-selected-content-menu ta-hidden">
              <div class="ta-wizard-selected-content-delete" data-link-id="<?php echo esc_attr($link->ID); ?>"><?php esc_html_e('Remove', 'thirstyaffiliates'); ?></div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="ta-wizard-links-pagination">
      <?php if($total_pages > 1): ?>
        <?php for($i = 1; $i <= $total_pages; $i++): ?>
          <?php $active_class = ($i === $current_page) ? 'active' : ''; ?>

          <a href="#" class="ta-wizard-links-pagination-page <?php echo $active_class; ?>" data-page="<?php echo esc_attr($i); ?>"><?php echo esc_html($i); ?></a>
        <?php endfor; ?>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</div>