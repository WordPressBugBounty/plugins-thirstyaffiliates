<?php

namespace ThirstyAffiliates\Models;

use ThirstyAffiliates\Abstracts\Abstract_Main_Plugin_Class;

use ThirstyAffiliates\Interfaces\Model_Interface;
use ThirstyAffiliates\Interfaces\Activatable_Interface;
use ThirstyAffiliates\Interfaces\Initiable_Interface;

use ThirstyAffiliates\Helpers\Plugin_Constants;
use ThirstyAffiliates\Helpers\Helper_Functions;
use ThirstyAffiliates\Models\Notifications;
use ThirstyAffiliates\Helpers\MothershipPluginConnector_Helper;

use ThirstyAffiliates\GroundLevel\Container\Concerns\HasStaticContainer;
use ThirstyAffiliates\GroundLevel\Container\Container;
use ThirstyAffiliates\GroundLevel\Container\Contracts\StaticContainerAwareness;
use ThirstyAffiliates\GroundLevel\InProductNotifications\Service as IPNService;
use ThirstyAffiliates\GroundLevel\Mothership\Service as MoshService;
use ThirstyAffiliates\GroundLevel\Support\Models\Hook;

/**
 * Model that houses the logic of GrdLvl
 */
class GrdLvl implements Model_Interface, Initiable_Interface, StaticContainerAwareness 
{
  use HasStaticContainer;

  /*
  |--------------------------------------------------------------------------
  | Class Properties
  |--------------------------------------------------------------------------
  */

  /**
   * Property that holds the single main instance of GrdLvl.
   *
   * @since 3.0.0
   * @access private
   * @var GrdLvl
   */
  private static $_instance;

  /**
   * Model that houses all the plugin constants.
   *
   * @since 3.0.0
   * @access private
   * @var Plugin_Constants
   */
  private $_constants;

  /**
   * Property that houses all the helper functions of the plugin.
   *
   * @since 3.0.0
   * @access private
   * @var Helper_Functions
   */
  private $_helper_functions;

  /*
  |--------------------------------------------------------------------------
  | Class Methods
  |--------------------------------------------------------------------------
  */

  /**
   * Class constructor.
   *
   * @since 3.0.0
   * @access public
   *
   * @param Abstract_Main_Plugin_Class $main_plugin      Main plugin object.
   * @param Plugin_Constants           $constants        Plugin constants object.
   * @param Helper_Functions           $helper_functions Helper functions object.
   */
  public function __construct( Abstract_Main_Plugin_Class $main_plugin , Plugin_Constants $constants , Helper_Functions $helper_functions ) {

      $this->_constants           = $constants;
      $this->_helper_functions    = $helper_functions;

      $main_plugin->add_to_all_plugin_models( $this );

  }

  /**
   * Ensure that only one instance of this class is loaded or can be loaded ( Singleton Pattern ).
   *
   * @since 3.0.0
   * @access public
   *
   * @param Abstract_Main_Plugin_Class $main_plugin      Main plugin object.
   * @param Plugin_Constants           $constants        Plugin constants object.
   * @param Helper_Functions           $helper_functions Helper functions object.
   * @return GrdLvl
   */
  public static function get_instance( Abstract_Main_Plugin_Class $main_plugin , Plugin_Constants $constants , Helper_Functions $helper_functions ) {

      if ( !self::$_instance instanceof self )
          self::$_instance = new self( $main_plugin , $constants , $helper_functions );

      return self::$_instance;

  }

  /**
   * Execute class.
   *
   * @access public
   * @implements \ThirstyAffiliates\Interfaces\Model_Interface
   */
  public function run() {
  
  }

  /*
  |--------------------------------------------------------------------------
  | Fulfill Implemented Interface Contracts
  |--------------------------------------------------------------------------
  */

  /**
   * Execute codes that needs to run on plugin initialization.
   *
   * @since 3.0.0
   * @access public
   * @implements \ThirstyAffiliates\Interfaces\Initiable_Interface
   */
  public function initialize() {
    /**
     * Currently we're loading a container, mothership, and ipn services in order
     * to power IPN functionality. We don't need the container or mothership
     * for anything other than IPN so we can skip the whole load if notifications
     * are disabled or unavailable for the user.
     *
     * Later we'll want to move this condition to be only around the {@see self::init_ipn()}
     * load method.
     */
    if (Notifications::has_access() || wp_doing_cron()) {
      self::setContainer(new Container());

      /**
       * @todo: Later we'll want to "properly" bootstrap a container via a
       * plugin bootstrap via GrdLvl package.
       */

      self::init_mothership();
      self::init_ipn();
    }
  }

  /**
   * Initializes and configures the IPN Service.
   */
  private static function init_ipn(): void
  { 

    $edition = defined( 'TAP_EDITION' ) ? TAP_EDITION : 'thirstyaffiliates-lite';
    
    // Set IPN Service parameters.
    self::$container->addParameter(IPNService::PRODUCT_SLUG, $edition);
    self::$container->addParameter(IPNService::PREFIX, 'tap');
    self::$container->addParameter(
      IPNService::RENDER_HOOK,
      'ta_admin_header_actions'
    );
    self::$container->addParameter(
        IPNService::THEME,
        [
          'primaryColor'       => '#3789ac',
          'primaryColorDarker' => '#ff7e00',
        ]
    );

    self::$container->addService(
        IPNService::class,
        static function (Container $container): IPNService {
          return new IPNService($container);
        },
        true
    );
  }

  /**
   * Initializes the Mothership Service.
   */
  private static function init_mothership(): void
  {
    self::$container->addService(
      MoshService::class,
      static function (Container $container): MoshService {
          return new MoshService(
            $container,
            new MothershipPluginConnector_Helper()
          );
      },
      true
    );
  }
}
