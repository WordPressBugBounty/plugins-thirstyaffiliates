<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); } ?>
<?php
use ThirstyAffiliates\Helpers\Onboarding_Helper;

?>
<div class="ta-onboarding">
  <div class="ta-onboarding-logo">
    <img src="<?php echo esc_url( $this->_constants->IMAGES_ROOT_URL() . 'TA.svg' ); ?>" alt="">
  </div>

  <h1><?php esc_html_e('Welcome to ThirstyAffiliates', 'thirstyaffiliates'); ?></h1>
  <p class="ta-onboarding-intro">
    <?php esc_html_e("We're thrilled you've chosen to team up with ThirstyAffiliates! Your decision is more than a smart move –  it's your launchpad to unparalleled affiliate marketing success. And we're here to support you, every click of the way!", 'thirstyaffiliates'); ?>
  </p>

  <p class="ta-onboarding-intro">
    <?php esc_html_e("Here at ThirstyAffiliates, we see affiliate links as more than just simple URLs. To us, they're valuable assets that can make or break your marketing efforts. That's why we've crafted a toolset that does more than just manage your links – it turns them into lean, mean, conversion machines!", 'thirstyaffiliates'); ?>
  </p>

  <p class="ta-onboarding-intro">
    <?php esc_html_e("But hang tight, because we're just scratching the surface. We've loaded our platform with all the bells and whistles you could dream of – advanced analytics, automated redirects, you name it! Trust us, if it makes your affiliate life easier and more profitable, it's in here.", 'thirstyaffiliates'); ?>
  </p>

  <p class="ta-onboarding-intro">
    <?php esc_html_e("So get ready! Your adventure in mastering affiliate marketing starts right here, right now! ", 'thirstyaffiliates'); ?>
  </p>


  <div class="ta-onboarding-get-started">
    <a href="<?php echo esc_url(admin_url('options.php?page=thirstyaffiliates_onboarding&step=1')); ?>"><?php esc_html_e("Let's Get Started", 'thirstyaffiliates'); ?><img src="<?php echo esc_url($this->_constants->IMAGES_ROOT_URL() . 'onboarding/long-arrow-right.svg'); ?>" alt=""></a>
  </div>

  <div class="ta-welcome-steps"></div>

  <?php if(!Onboarding_Helper::is_pro_active()): ?>

    <div class="pre-badge"><?php esc_html_e('Tap into the Strength of', 'thirstyaffiliates'); ?></div>

    <div class="ta-welcome-badge">
      <img class="ta-pro-logo-welcome" src="<?php echo $this->_constants->IMAGES_ROOT_URL() . 'onboarding/TA-Pro.svg'; ?>" alt="<?php esc_attr_e('Strength of ThirstyAffiliates Pro', 'thirstyaffiliates'); ?>">
    </div>

    <p><?php _e('Boost your marketing efforts with the EXCEPTIONAL features <br />included in our ThirstyAffiliates Premium plans.', 'thirstyaffiliates'); ?></p>

    <div class="ta-reasons">
        <div class="ta-reason">
          <div class="reason-image"><img src="<?php echo $this->_constants->IMAGES_ROOT_URL() . 'onboarding/link_management.png'; ?>" alt=""></div>
          <div class="reason-content">
            <div class="reason-title"><h3><?php esc_html_e('Effortless Link Management', 'thirstyaffiliates'); ?></h3></div>
            <div class="reason-desc">
              <p><?php esc_html_e("We've built a user-friendly platform designed to make managing your affiliate links as smooth as it gets. Our intuitive dashboard lets you edit, shorten, and neatly organize your links into categories – all without breaking a sweat.", 'thirstyaffiliates'); ?></p>
              <p><?php _e("What's more, we've equipped ThirstyAffiliates with real-time adjustment features. This means you can tweak your links instantly and jump on new opportunities as they come your way.", 'thirstyaffiliates'); ?></p>
            </div>
          </div>
        </div>
        <div class="ta-reason">
          <div class="reason-image reason-image-thirstypay"><img src="<?php echo $this->_constants->IMAGES_ROOT_URL() . 'onboarding/ThirstyPay-links.png'; ?>" alt=""></div>
          <div class="reason-content">
            <div class="reason-title"><h3><?php esc_html_e('Customized Checkout Links', 'thirstyaffiliates'); ?></h3></div>
            <div class="reason-desc">
              <p><?php esc_html_e("ThirstyPay™ is here for when you're ready to start making money from your own creative projects. From doubling up on your content's profits to marketing those quirky crafts you love making, ThirstyPay™ is ready to take your side hussle’s side hussle to the marketplace.", 'thirstyaffiliates'); ?></p>
              <p><?php _e("Create personalized payment links powered by Stripe's reliable payment processing platform. Share them with your network, and watch as followers become your paying customers.", 'thirstyaffiliates'); ?></p>
              <p><?php esc_html_e("*Upgrade to a ThirstyAffiliates Pro plan to avoid the 3% transaction fee on all sales.", 'thirstyaffiliates'); ?></p>
            </div>
          </div>
        </div>
        <div class="ta-reason">
          <div class="reason-image"><img src="<?php echo $this->_constants->IMAGES_ROOT_URL() . 'onboarding/product_displays.png'; ?>" alt=""></div>
          <div class="reason-content">
            <div class="reason-title"><h3><?php esc_html_e('Product Displays that Shine', 'thirstyaffiliates'); ?></h3></div>
            <div class="reason-desc">
              <p><?php esc_html_e("Spruce up your blog posts with masterfully crafted visual displays designed to highlight products and drive action.", 'thirstyaffiliates'); ?></p>
              <p><?php _e("Turn basic text links into mesmerizing showcases bursting with lively visuals, engaging description blurbs, and can't-miss call-to-actions.", 'thirstyaffiliates'); ?></p>
              <p><?php _e("With interactive elements and optimal page placement, capture your audience's interest right when their shopping spirit is high.", 'thirstyaffiliates'); ?></p>
            </div>
          </div>
        </div>
        <div class="ta-reason">
          <div class="reason-image"><img src="<?php echo $this->_constants->IMAGES_ROOT_URL() . 'onboarding/cloaking.png'; ?>" alt=""></div>
          <div class="reason-content">
            <div class="reason-title"><h3><?php esc_html_e('Secure Shortening & Cloaking', 'thirstyaffiliates'); ?></h3></div>
            <div class="reason-desc">
              <p><?php esc_html_e('Affiliate information is sensitive stuff. ThirstyAffiliates takes that seriously, providing top-notch link cloaking capabilities to protect your valuable info, all while supercharging your marketing strategies.', 'thirstyaffiliates'); ?></p>
              <p><?php esc_html_e("But we don't stop at security. We help you turn those long, confusing affiliate URLs into clean, concise links that people actually want to click on. They'll look better, feel more trustworthy, and you'll love sharing them even more.", 'thirstyaffiliates'); ?></p>
            </div>
          </div>
        </div>
        <div class="ta-reason">
          <div class="reason-image"><img src="<?php echo $this->_constants->IMAGES_ROOT_URL() . 'onboarding/uncloaking.png'; ?>" alt=""></div>
          <div class="reason-content">
            <div class="reason-title"><h3><?php esc_html_e('Compliance-Friendly Uncloaking', 'thirstyaffiliates'); ?></h3></div>
            <div class="reason-desc">
              <p><?php esc_html_e('Navigating the rules around affiliate links can be tricky, especially with platforms like Amazon that have strict requirements for original affiliate URL visibility.', 'thirstyaffiliates'); ?></p>
              <p><?php esc_html_e("That's where we step in. Our handy uncloaking feature lets you switch between cloaked links and meeting platform requirements with uncloaked ones.", 'thirstyaffiliates'); ?></p>
              <p><?php esc_html_e('In other words, you get to stay on the right side of platform guidelines without compromising the security of your information.', 'thirstyaffiliates'); ?></p>
            </div>
          </div>
        </div>
        <div class="ta-reason">
          <div class="reason-image"><img src="<?php echo $this->_constants->IMAGES_ROOT_URL() . 'onboarding/link_placement.png'; ?>" alt=""></div>
          <div class="reason-content">
            <div class="reason-title"><h3><?php esc_html_e('Automated Link Placement', 'thirstyaffiliates'); ?></h3></div>
            <div class="reason-desc">
              <p><?php esc_html_e("Forget manual link insertion. With ThirstyAffiliates' AutoLink Placement, you get to put your time and energy where it really counts: crafting your overall marketing strategy.", 'thirstyaffiliates'); ?></p>
              <p><?php esc_html_e("Once you've created a ThirstyAffiliate link, just pinpoint the target keywords you want to focus on. Our software takes it from there, replacing those keywords with your designated affiliate links all across your website, without you lifting another finger.", 'thirstyaffiliates'); ?></p>
            </div>
          </div>
        </div>
        <div class="ta-reason">
          <div class="reason-image"><img src="<?php echo $this->_constants->IMAGES_ROOT_URL() . 'onboarding/geolocation.png'; ?>" alt=""></div>
          <div class="reason-content">
            <div class="reason-title"><h3><?php esc_html_e('Localize & Monetize', 'thirstyaffiliates'); ?></h3></div>
            <div class="reason-desc">
              <p><?php esc_html_e('Turn global traffic into localized conversions. Our Geolocation redirect tool goes beyond rerouting clicks; it fine-tunes the user experience based on where your visitors are coming from', 'thirstyaffiliates'); ?></p>
              <p><?php esc_html_e('It\'s like having a local guide embedded in your website, directing traffic in a way that maximizes your earning potential while making things more relevant and accessible for your visitors.', 'thirstyaffiliates'); ?></p>
            </div>
          </div>
        </div>
        <div class="ta-reason">
          <div class="reason-image"><img src="<?php echo $this->_constants->IMAGES_ROOT_URL() . 'onboarding/link_health.png'; ?>" alt=""></div>
          <div class="reason-content">
            <div class="reason-title"><h3><?php esc_html_e('Reliable Link Checker', 'thirstyaffiliates'); ?></h3></div>
            <div class="reason-desc">
              <p><?php esc_html_e('Don\'t let broken links tarnish your site\'s reputation or mess with your SEO. Our Link Checker works around the clock, meticulously scanning your WordPress site for any link that\'s out of order.', 'thirstyaffiliates'); ?></p>
              <p><?php esc_html_e('But wait, it gets better. You can even validate links before publishing them, ensuring they\'re good to go from the start.', 'thirstyaffiliates'); ?></p>
              <p><?php esc_html_e('If a link does go rogue, you\'re not left in the dark. We\'ll shoot you an alert, so you can patch things up on the spot.', 'thirstyaffiliates'); ?></p>
            </div>
          </div>
        </div>
    </div>

    <div class="ta-onboarding-pricing">
      <h2>Complete Your Affiliate Marketing Toolkit</h2>
      <div class="ta-onboarding-pricing-guarantee">
        <img src="<?php echo $this->_constants->IMAGES_ROOT_URL() . 'onboarding/14days-badge.svg'; ?>" alt="">
        <p>Why go basic when you can go beyond? Upgrade today and gain access to an array of advanced features designed to transform your affiliate marketing strategies.</p>
      </div>
    </div>

    <div class="ta-onboarding-pricing-table">
      <div class="ta-onboarding-pricing-executive">
        <div class="ta-onboarding-pricing-content">
          <div class="ta-onboarding-price-title">ADVANCED</div>
          <div class="ta-onboarding-pricing-wrap">
            <div class="ta-onboarding-price-normally-wrap">
              <div class="ta-onboarding-price-normally">normally $499</div>
            </div>
            <div class="ta-onboarding-price-cost">
              <span class="ta-onb-price-currency">$</span>
              <span class="ta-onb-price-amount">199.60</span>
              <span class="ta-onb-price-term">/ year</span>
            </div>
            <div class="ta-onboarding-price-savings">$299.40 savings*</div>
          </div>
          <p class="ta-onboarding-price-desc">"For empire builders."</p>
          <div class="ta-onboarding-price-cta">
            <a href="https://thirstyaffiliates.com/register/advanced/" class="ta-onboarding-price-get-started">Get Started</a>
          </div>
          <ul class="ta-onboarding-price-features">
            <li class="ta-onboarding-price-feature"><strong>Use on up to 10 WordPress sites</strong></li>
              <li class="ta-onboarding-price-feature">Automatic Keyword Linking</li>
              <li class="ta-onboarding-price-feature">CSV Import/Export</li>
              <li class="ta-onboarding-price-feature">Geographic Redirects</li>
              <li class="ta-onboarding-price-feature">Amazon API Importing</li>
            <li class="ta-onboarding-price-feature">Priority support</li>
          </ul>
          <div class="ta-onboarding-price-all-features"><a href="https://thirstyaffiliates.com/pricing#ta-features-compare">See all features</a></div>
        </div>
      </div>


        <div class="ta-onboarding-pricing-marketer">
          <div class="ta-onboarding-pricing-content">
            <div class="ta-onboarding-price-popular">Most Popular</div>
            <div class="ta-onboarding-price-title">PLUS</div>
            <div class="ta-onboarding-pricing-wrap">
              <div class="ta-onboarding-price-normally-wrap">
                <div class="ta-onboarding-price-normally">normally $374</div>
              </div>
              <div class="ta-onboarding-price-cost">
                <span class="ta-onb-price-currency">$</span>
                <span class="ta-onb-price-amount">149.60</span>
                <span class="ta-onb-price-term">/ year</span>
              </div>
              <div class="ta-onboarding-price-savings">$224.40 savings*</div>
            </div>
            <p class="ta-onboarding-price-desc">"For multi site owners."</p>
            <div class="ta-onboarding-price-cta">
              <a href="https://thirstyaffiliates.com/register/plus/" class="ta-onboarding-price-get-started">Get Started</a>
            </div>
            <ul class="ta-onboarding-price-features">
              <li class="ta-onboarding-price-feature"><strong>Use on up to 5 WordPress sites</strong></li>
              <li class="ta-onboarding-price-feature">Automatic Keyword Linking</li>
              <li class="ta-onboarding-price-feature">CSV Import/Export</li>
              <li class="ta-onboarding-price-feature">Geographic Redirects</li>
              <li class="ta-onboarding-price-feature">Amazon API Importing</li>
              <li class="ta-onboarding-price-feature">1 year of support and updates</li>
            </ul>
            <div class="ta-onboarding-price-all-features"><a href="https://thirstyaffiliates.com/pricing#ta-features-compare">See all features</a></div>
          </div>
        </div>


        <div class="ta-onboarding-pricing-beginner">
          <div class="ta-onboarding-pricing-content">
            <div class="ta-onboarding-price-title">BASIC</div>
            <div class="ta-onboarding-pricing-wrap">
              <div class="ta-onboarding-price-normally-wrap">
                <div class="ta-onboarding-price-normally">normally $249</div>
              </div>
              <div class="ta-onboarding-price-cost">
                <span class="ta-onb-price-currency">$</span>
                <span class="ta-onb-price-amount">99.60</span>
                <span class="ta-onb-price-term">/ year</span>
              </div>
              <div class="ta-onboarding-price-savings">$149.40 savings*</div>
            </div>
            <p class="ta-onboarding-price-desc">"For single site owners."</p>
            <div class="ta-onboarding-price-cta">
              <a href="https://thirstyaffiliates.com/register/basic/" class="ta-onboarding-price-get-started">Get Started</a>
            </div>
            <ul class="ta-onboarding-price-features">
              <li class="ta-onboarding-price-feature">Use on 1 WordPress site</li>
              <li class="ta-onboarding-price-feature">Automatic Keyword Linking</li>
              <li class="ta-onboarding-price-feature">CSV Import/Export</li>
              <li class="ta-onboarding-price-feature">Geographic Redirects</li>
              <li class="ta-onboarding-price-feature">Amazon API Importing</li>
              <li class="ta-onboarding-price-feature">1 year of support and updates</li>
            </ul>
            <div class="ta-onboarding-price-all-features"><a href="https://thirstyaffiliates.com/pricing#ta-features-compare">See all features</a></div>
          </div>
        </div>

    </div>
  <?php endif; ?>
</div>
