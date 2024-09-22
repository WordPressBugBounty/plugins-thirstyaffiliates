<div class="thirstyaffiliates-popup">
  <div class="thirstyaffiliates-popup-wrap">
    <div class="thirstyaffiliates-popup-content">
      <div class="thirstyaffiliates-popup-logo">
        <img src="<?php echo $this->_constants->IMAGES_ROOT_URL() . 'admin-review-notice-logo.png'; ?>" alt="">
      </div>
      <h2>Make Money from Content You Already Have</h2>
      <?php if ( ! empty( $section_title ) ) : ?>
        <h4>ThirstyAffiliates Lite cannot access <?php echo $section_title; ?>.</h4>
      <?php endif; ?>
      <p>Once you upgrade to ThirstyAffiliates Pro, you'll have access to these pro features:</p>
      <ul class="features">
        <li>Advanced Link Insertion Types</li>
        <li>Automatic Keyword Linking</li>
        <li>Geolocation Link Redirects</li>
        <li>REST API Compatibility</li>
        <li>Support &amp; Updates</li>
        <li>Link Categories</li>
        <li>Automatic 404 Checker</li>
        <li>Advanced Statistics Reports</li>
        <li>Import and Export Links</li>
        <li>Product Displays</li>
      </ul>
    </div>
    <div class="thirstyaffiliates-popup-cta">
      <a href="<?php echo $upgrade_link; ?>" id="ta_cta_upgrade_link" class="thirstyaffiliates-cta-button">Upgrade to ThirstyAffiliates Pro Now</a>
    </div>
  </div>
</div>
