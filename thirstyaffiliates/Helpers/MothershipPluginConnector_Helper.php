<?php
namespace ThirstyAffiliates\Helpers;

use ThirstyAffiliates\GroundLevel\Mothership\AbstractPluginConnection;
use ThirstyAffiliates_Pro\Helpers\Plugin_Constants as ProPlugin_Constants;

class MothershipPluginConnector_Helper extends AbstractPluginConnection
{
    /**
     * Constructor.
     */
    public function __construct()
    {
      $this->pluginId     = 'thirstyaffiliates';
      $this->pluginPrefix = 'TAP_';
    }

    /**
     * Gets the license activation status option.
     *
     * @return boolean The license activation status.
     */
    public function getLicenseActivationStatus(): bool
    {
      if ( Onboarding_Helper::is_pro_active() && method_exists(ThirstyAffiliates_Pro(), 'get_model') ) {
        $tap_update = ThirstyAffiliates_Pro()->get_model('Update');
        if ( is_object($tap_update) && method_exists($tap_update, 'is_activated') ) {
          return $tap_update->is_activated();
        }
      }
      return false;
    }

    /**
     * Updates the license activation status option.
     *
     * @param boolean $status The status to update.
     */
    public function updateLicenseActivationStatus(bool $status): void
    {
      if ( Onboarding_Helper::is_pro_active() ) {
        update_option(ProPlugin_Constants::OPTION_LICENSE_ACTIVATED, $status);
      }
     
    }

    /**
     * Gets the license key option.
     *
     * @return string The license key.
     */
    public function getLicenseKey(): string
    {
      if ( Onboarding_Helper::is_pro_active() ) {
        return get_option( ProPlugin_Constants::OPTION_LICENSE_KEY );
      }
      return '';
    }

    /**
     * Updates the license key option.
     *
     * @param string $licenseKey The license key to update.
     */
    public function updateLicenseKey(string $licenseKey): void
    {   
      if ( Onboarding_Helper::is_pro_active() ) {
        update_option(ProPlugin_Constants::OPTION_LICENSE_KEY, $licenseKey);
      }
    }

    /**
     * Gets the domain option.
     *
     * @return string The domain.
     */
    public function getDomain(): string
    {
      return ThirstyAffiliates()->helpers[ 'Helper_Functions' ]->get_site_domain();
    }
}
