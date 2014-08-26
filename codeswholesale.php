<?php
/**
 * Plugin Name: CodesWholesale for WooCommerce
 * Plugin URI: http://docs.codeshowlesale.com
 * Description: Integration with CodesWholesale API.
 * Version: 1.0
 * Author: DevTeam devteam@codeswholesale.com
 * Author URI: http://docs.codeswholesale.com
 * License: GPL2
 */

defined('ABSPATH') or die("No script kiddies please!");

if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    die('no WooCommerce plugin found');
}

final class CodesWholesaleOrderFullFilledStatus {
    const FILLED  = 1;
    const TO_FILL = 0;
}

final class CodesWholesaleConst {

    const ORDER_ITEM_LINKS_PROP_NAME               = "_codeswholesale_links";
    const PRODUCT_CODESWHOLESALE_ID_PROP_NAME      = "_codeswholesale_product_id";
    const ORDER_FULL_FILLED_PARAM_NAME             = "_codeswholesale_filled";
    const AUTOMATICALLY_COMPLETE_ORDER_OPTION_NAME = "_codeswholesale_auto_complete";
    const SETTINGS_CODESWHOLESALE_PARAMS_NAME      = "codeswholesale_params";

}

final class CodesWholesaleAutoCompleteOrder {
    const COMPLETE = 1;
    const NOT_COMPLETE = 0;
}


final class CodesWholesale
{

    /**
     *
     * @var CodesWholesale
     */
    protected static $_instance = null;

    /**
     * CodesWholesale API client
     *
     * @var CodesWholesale\Client
     */
    private $codesWholesaleClient;

    /**
     * Plugin version
     *
     * @var string
     */
    private $version = "1.0";

    /**
     *
     */
    public function __construct()
    {
		// Auto-load classes on demand
		if ( function_exists( "__autoload" ) ) {
			spl_autoload_register( "__autoload" );
		}

		spl_autoload_register( array( $this, 'autoload' ) );

        $this->define_constants();

        $this->includes();

        $this->configure_cw_client();

    }

    /**
     * Auto-load WC classes on demand to reduce memory consumption.
     *
     * @param mixed $class
     * @return void
     */
    public function autoload($class)
    {
        $path = null;
        $class = strtolower($class);
        $file = 'class-' . str_replace('_', '-', $class) . '.php';

        if (strpos($class, 'cw_admin') === 0) {
            $path = $this->plugin_path() . '/includes/admin/';
        }

        if ($path && is_readable($path . $file)) {
            include_once($path . $file);
            return;
        }

        // Fallback
        if (strpos($class, 'cw_') === 0) {
            $path = $this->plugin_path() . '/includes/';
        }

        if ($path && is_readable($path . $file)) {
            include_once($path . $file);
            return;
        }
    }

    private function includes()
    {
        include_once( 'includes/cw-core-functions.php');
        include_once( 'vendor/autoload.php' );
        include_once( 'includes/class-cw-install.php' );
        include_once( 'includes/class-cw-checkout.php');
        include_once( 'includes/class-cw-sendkeys.php');

        include_once( 'includes/abstracts/class-cw-cron-job.php');
        include_once( 'includes/class-cw-cron-update-stock.php');
        include_once( 'includes/class-cw-cron-check-filled-orders.php');

        if (is_admin()) {

            include_once('includes/admin/class-cw-admin.php');
        }


    }


    /**
     * Define WC Constants
     */
    private function define_constants() {
        define( 'CW_PLUGIN_FILE', __FILE__       );
        define( 'CW_VERSION'    , $this->version );
    }

    /**
     * Main CodesWholesale Instance
     *
     * Ensures only one instance of CodesWholesale is loaded or can be loaded.
     *
     * @since 1.0
     * @static
     * @see CW()
     * @return CodesWholesaleWooCommerce - Main instance
     */
    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Get the plugin path.
     *
     * @return string
     */
    public function plugin_path() {
        return untrailingslashit( plugin_dir_path( __FILE__ ) );
    }

    /**
     * Get the template path.
     *
     * @return string
     */
    public function template_path() {
        return apply_filters( 'CW_TEMPLATE_PATH', 'codeswholesale-woocommerce/' );
    }

    /**
     * Get the plugin url.
     *
     * @return string
     */
    public function plugin_url() {
        return untrailingslashit( plugins_url( '/', __FILE__ ) );
    }

    /**
     *
     */
    private function configure_cw_client()
    {
        $json = json_decode(get_option(CodesWholesaleConst::SETTINGS_CODESWHOLESALE_PARAMS_NAME));
        if($json) {
            $params = get_object_vars($json);
            $params['cw.token_storage'] = new \fkooman\OAuth\Client\SessionStorage();
            $clientBuilder = new \CodesWholesale\ClientBuilder($params);
            $this->codesWholesaleClient = $clientBuilder->build();
        }
    }

    /**
     * @return \CodesWholesale\Client
     */
    public function getCodesWholesaleClient() {
        return $this->codesWholesaleClient;
    }

    /**
     *
     */
    public function refreshCodesWholesaleClient()
    {
        $_SESSION["php-oauth-client"]= array();
        $this->configure_cw_client();
    }
}

/**
 * Returns the main instance of WC to prevent the need to use globals.
 *
 * @since  1.0
 * @return CodesWholesale
 */
function CW()
{
    return CodesWholesale::instance();
}

CW();