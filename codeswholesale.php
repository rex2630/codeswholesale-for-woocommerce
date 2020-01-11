<?php
/**
 * Plugin Name: CodesWholesale for WooCommerce
 * Plugin URI: http://docs.codeshowlesale.com
 * Depends: WooCommerce
 * Description: Integration with CodesWholesale API. PHP >= 7.0
 * Version: 2.6.6
 * Author: DevTeam devteam@codeswholesale.com
 * Author URI: http://docs.codeswholesale.com
 * License: GPL2
 */

use CodesWholesaleFramework\Connection\Connection;
use GuzzleHttp\Exception\ClientException;

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

defined('ABSPATH') or die("No script kiddies please!");

final class CodesWholesaleOrderFullFilledStatus
{
    const FILLED = 1;
    const TO_FILL = 0;
}

final class CodesWholesaleConst
{
    const OPTIONS_NAME = "cw_options";
    const SETTINGS_CODESWHOLESALE_PARAMS_NAME       = "codeswholesale_params";
    
    const ORDER_ITEM_LINKS_PROP_NAME                = "_codeswholesale_links";
    const ORDER_ITEM_ORDER_ID_PROP_NAME             = "_codeswholesale_order_id";
    const ORDER_ITEM_NUMBER_OF_PRE_ORDERS_PROP_NAME = "_codeswholesale_number_of_pre_order";
    const ORDER_FULL_FILLED_PARAM_NAME              = "_codeswholesale_filled";

    const PRODUCT_CODESWHOLESALE_ID_PROP_NAME       = "_codeswholesale_product_id";
    const PRODUCT_STOCK_PRICE_PROP_NAME             = "_codeswholesale_product_stock_price";
    const PRODUCT_SPREAD_TYPE_PROP_NAME             = "_codeswholesale_product_spread_type";
    const PRODUCT_SPREAD_VALUE_PROP_NAME            = "_codeswholesale_product_spread_value";
    const PRODUCT_CALCULATE_PRICE_METHOD_PROP_NAME  = "_codeswholesale_product_calculate_price_method";
    
    const HIDE_PRODUCT_WHEN_DISABLED_OPTION_NAME            = "_codeswholesale_product_hide_when_disabled";  
    const AUTOMATICALLY_IMPORT_NEWLY_PRODUCT_OPTION_NAME    = "_codeswholesale_product_automatically_import_newly";
    const AUTOMATICALLY_COMPLETE_ORDER_OPTION_NAME          = "_codeswholesale_auto_complete";
    const NOTIFY_LOW_BALANCE_VALUE_OPTION_NAME              = "_codeswholesale_notify_balance_value";
    const PREFERRED_LANGUAGE_FOR_PRODUCT_OPTION_NAME        = "_codeswholesale_preferred_language_for_product";
    const RISK_SCORE_PROP_NAME                              = "_codeswholesale_risk_score";
    const DOUBLE_CHECK_PRICE_PROP_NAME                      = "_codeswholesale_double_check_price";
    const ALLOW_PRE_ORDER_PROP_NAME                         = "_codeswholesale_allow_pre_order";

    const TEST_SIGNATURE = "b4cded07-e13e-4021-8b9f-a3cee994109b";

    /**
     * Format money with euro.
     *
     * @param $money
     * @return string
     */
    static public function format_money($money)
    {
        return "€" . number_format($money, 2, '.', '');
    }
}

final class CodesWholesaleAutoCompleteOrder
{
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
    private $codes_wholesale_client;

    /**
     * Plugin version
     *
     * @var string
     */
    private $version = "2.6.6";

    /**
     * @var array
     */
    private $plugin_options;

    /**
     *
     */
    public function __construct()
    {
        register_activation_hook(__FILE__, array('CodesWholesale', 'codeswholesale_install'));
        register_deactivation_hook(__FILE__, array('CodesWholesale', 'codeswholesale_uninstall'));
        
        // Auto-load classes on demand
        if (function_exists("__autoload")) {
            spl_autoload_register("__autoload");
        }

        spl_autoload_register(array($this, 'autoload'));

        $this->define_constants();

        $this->includes();

        self::codeswholesale_activate();

        $this->configure_cw_client();
    }

    public static function codeswholesale_install()
    {
        if ( !is_plugin_active('woocommerce/woocommerce.php')) {
            // Deactivate the plugin
            deactivate_plugins(FILE);

            $error_message = __('This plugin requires <a href="http://wordpress.org/extend/plugins/woocommerce/">WooCommerce</a> to be active!', 'woocommerce');
            die($error_message);
        } 
		
        $options = CW()->get_options();

        if (1 == $options['environment'] && CW()->isClientCorrect()) {
            ApiClient::sendActivity(ApiClient::CONNECTED_TO_WOOCOMMERCE);
        }
    }
    
    public static function codeswholesale_uninstall()
    {
        $options = CW()->get_options();

        if (1 == $options['environment'] && CW()->isClientCorrect()) {
            ApiClient::sendActivity(ApiClient::API_DISCONNECTED);
        }
    }
    
    public static function codeswholesale_activate()
    {
        foreach (self::getRepositories() as $repository) {
            $repository->createTable();
        }
    }

    /**
     * @return \CodesWholesaleFramework\Database\Interfaces\RepositoryInterface[]
     */
    private static function getRepositories(): array
    {
        $db = new WP_DbManager();

        return [
            new \CodesWholesaleFramework\Database\Repositories\ImportPropertyRepository($db),
            new \CodesWholesaleFramework\Database\Repositories\AccessTokenRepository($db),
            new \CodesWholesaleFramework\Database\Repositories\RefreshTokenRepository($db),
            new \CodesWholesaleFramework\Database\Repositories\CodeswholesaleProductRepository($db),
            new \CodesWholesaleFramework\Database\Repositories\CurrencyControlRepository($db),
            new \CodesWholesaleFramework\Database\Repositories\PostbackImportRepository($db),
            new \CodesWholesaleFramework\Database\Repositories\PostbackImportDetailsRepository($db),
        ];
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

    /**
     *
     */
    private function includes()
    {
        include_once('includes/cw-core-functions.php');
        include_once('vendor/autoload.php');

        include_once('includes/class-cw-install.php');
        include_once('includes/class-cw-disable-plugin-while-update.php');

        // support the process
        include_once('includes/process/class-cw-buy-keys.php');
        include_once('includes/process/class-cw-send-keys.php');
        include_once('includes/process/class-cw-balance-checker.php');
        include_once('includes/process/class-wp-update-products.php');
        include_once('includes/process/class-wp-update-orders.php');

        //Validation
        include_once('includes/validate/wp-validate-purchase.php');

        //Visitor
        include_once('includes/visitor/WP_InternalOrderVisitor.php');

        //Checkers
        include_once('includes/checker/wp-configuration-checker.php');

        //Client
        include_once('includes/client/wp-api-client.php');

        //Exec
        include_once('includes/exec/wp-exec-manager.php');

        //Exporters
        include_once('includes/exporters/wp-database-exporter.php');

        //Imports
        include_once('includes/import/postback-impot-products.php');
        
        //Updaters
        include_once('includes/updaters/wp-attachment-updater.php');
        include_once('includes/updaters/wp-attribute-updater.php');
        include_once('includes/updaters/wp-category-updater.php');
        include_once('includes/updaters/wp-product-updater.php');
        
        include_once('includes/updaters/product/create-internal-product.php');
        include_once('includes/updaters/product/update-internal-product.php');
        
        // Retrievers
        include_once('includes/retrievers/wp-links-retriever.php');
        include_once('includes/retrievers/wp-order-item-retriever.php');
         
        // Dispatchers
        include_once('includes/dispatchers/wp-order-event-dispatcher.php');
        include_once('includes/dispatchers/wp-send-codes-dispatcher.php');
        include_once('includes/dispatchers/wp-order-notification-dispatcher.php');

        // Managers
        include_once('includes/managers/wp-file-manager.php');
        include_once('includes/managers/wp-db-manager.php');

        //WooCommerce
        include_once('includes/woocommerce/class-cw-woocommerce-order.php');
        include_once('includes/woocommerce/class-cw-checkout.php');
        
        //E-mails
        include_once('includes/emails/class-cw-emails.php');
        
        if (is_admin()) {
            include_once('includes/admin/class-cw-admin.php');
        }
    }


    /**
     * Define WC Constants
     */
    private function define_constants()
    {
        define('CW_PLUGIN_FILE', __FILE__);
        define('CW_VERSION', $this->version);
    }

    /**
     * Main CodesWholesale Instance
     *
     * Ensures only one instance of CodesWholesale is loaded or can be loaded.
     *
     * @since 1.0
     * @static
     * @see CW()
     * @return CodesWholesale
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
    public function plugin_path()
    {
        return untrailingslashit(plugin_dir_path(__FILE__));
    }

    /**
     * Get the template path.
     *
     * @return string
     */
    public function template_path()
    {
        return apply_filters('CW_TEMPLATE_PATH', 'codeswholesale-woocommerce/');
    }

    /**
     * Get the plugin url.
     *
     * @return string
     */
    public function plugin_url()
    {
        return untrailingslashit(plugins_url('/', __FILE__));
    }

    /**
     *
     */
    private function configure_cw_client()
    {
        global $wpdb;

        $options = get_option(CodesWholesaleConst::OPTIONS_NAME);

        if ($options) {

            if (Connection::hasConnection()) {
                return Connection::getConnection(array());
            }

            $options = array(
                'environment' => $options['environment'],
                'client_id' => empty($options['api_client_id']) ? '0' : $options['api_client_id'] ,
                'client_secret' => empty($options['api_client_secret']) ? '0' : $options['api_client_secret'],
                'client_headers' => 'Codeswholesale-WooCommerce/' . $this->version,
                'db' => new PDO('mysql:host=' . $wpdb->dbhost . ';dbname=' . $wpdb->dbname, $wpdb->dbuser, $wpdb->dbpassword),
                'prefix' => sprintf('%s%s', $wpdb->prefix, \CodesWholesaleFramework\Database\Interfaces\RepositoryInterface::CW_PREFIX),
                'signature' => empty($options['api_client_singature']) ? CodesWholesaleConst::TEST_SIGNATURE : $options['api_client_singature']
            );

            try {
                return $this->codes_wholesale_client = Connection::getConnection($options);
            } catch (ClientException $e) {
            }
        }
    }


    /**
     * @return \CodesWholesale\Client
     */
    public function get_codes_wholesale_client()
    {
        return $this->codes_wholesale_client;
    }

    /**
     *
     */
    public function refresh_codes_wholesale_client()
    {
        $_SESSION["php-oauth-client"] = array();
        $this->configure_cw_client();
    }

    /**
     * Return options for CW plugin
     */
    public function get_options()
    {
        if (count($this->plugin_options) == 0) {
            $this->plugin_options = get_option(CodesWholesaleConst::OPTIONS_NAME);
        }

        return $this->plugin_options;
    }
    
    public function get_related_wp_products($cwProductId, $postStatus = 'any') {
        $args = array(
            'posts_per_page' => -1,
            'post_type' => 'product',
            'post_status' =>  $postStatus,
            'meta_key' => CodesWholesaleConst::PRODUCT_CODESWHOLESALE_ID_PROP_NAME,
            'meta_value' => $cwProductId
        );
        
        return get_posts($args);
    }

    public function isClientCorrect(): bool
    {
        try {
            CW()->get_codes_wholesale_client()->getAccount();
            return true;
        } catch (\Error $e) {
            return false;
        }
    }

    /**
     * @param string $orderId
     *
     * @return string
     */
    public function getWooCommerceOrderIdByExternalId(string $orderId): string
    {
        global $wpdb;

        $query = sprintf("SELECT * FROM %swoocommerce_order_itemmeta WHERE meta_key = '%s' AND meta_value = '%s'",
            $wpdb->prefix,
            CodesWholesaleConst::ORDER_ITEM_ORDER_ID_PROP_NAME,
            $orderId
        );

        $result = $wpdb->get_row($query);

        if (null === $result) {
            return '';
        }

        $product = new WC_Order_Item_Product($result->order_item_id);

        return $product->get_order()->get_id();
    }
}

/**
 * Returns the main instance of CW to prevent the need to use globals.
 *
 * @since  1.0
 * @return CodesWholesale
 */

function CW()
{
    return CodesWholesale::instance();
}

CW();


