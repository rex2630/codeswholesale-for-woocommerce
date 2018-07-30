<?php

use CodesWholesale\CodesWholesale;
use CodesWholesaleFramework\Connection\Connection;
use CodesWholesale\CodesWholesaleClientConfig;
use CodesWholesale\ClientBuilder;
use CodesWholesale\CodesWholesaleApi;
use CodesWholesale\Storage\TokenDatabaseStorage;
use CodesWholesale\Storage\TokenSessionStorage;

class ApiClient
{
    const
        API_DISCONNECTED = 'API_DISCONNECTED',
        CONNECTED_TO_WOOCOMMERCE = 'CONNECTED_TO_WOOCOMMERCE',
        CONNECTED_TO_PRESTASHOP = 'CONNECTED_TO_PRESTASHOP',
        CONNECTED_TO_OPENCART = 'CONNECTED_TO_OPENCART',
        CONNECTED_TO_SHOPIFY = 'CONNECTED_TO_SHOPIFY',
        CONNECTED_TO_MAGENTO = 'CONNECTED_TO_MAGENTO'
    ;
    /**
     * @var ApiClient
     */
    protected static $instance;

    /**
     * @var CodesWholesaleApi
     */
    protected $client;

    /**
     * @return ApiClient
     * @throws \CodesWholesale\Exceptions\ClientConfigException
     */
    public static function getInstance(): ApiClient
    {
        global $wpdb;

        if (null === self::$instance) {
            self::$instance = new self();

            $defaultOptions = get_option(CodesWholesaleConst::OPTIONS_NAME);

            $options = [
                'environment' => $defaultOptions['environment'],
                'client_id' => empty($defaultOptions['api_client_id']) ? '0' : $defaultOptions['api_client_id'] ,
                'client_secret' => empty($defaultOptions['api_client_secret']) ? '0' : $defaultOptions['api_client_secret'],
                'client_headers' => 'Codeswholesale-WooCommerce/2.3',
                'db' => new PDO('mysql:host=' . $wpdb->dbhost . ';dbname=' . $wpdb->dbname, $wpdb->dbuser, $wpdb->dbpassword),
                'prefix' => sprintf('%s%s', $wpdb->prefix, \CodesWholesaleFramework\Database\Interfaces\RepositoryInterface::CW_PREFIX),
            ];

            $parameters = [
                'cw.endpoint_uri' => $options['environment'] == 0 ? CodesWholesale::SANDBOX_ENDPOINT : CodesWholesale::LIVE_ENDPOINT,
                'cw.client_id' => $options['environment'] == 0 ? Connection::SANDBOX_CLIENT_ID : $options['client_id'],
                'cw.client_secret' => $options['environment'] == 0 ? Connection::SANDBOX_CLIENT_SECRET : $options['client_secret'],
                'cw.token_storage' => isset($options['db']) && $options['db'] instanceof \PDO ? new TokenDatabaseStorage($options['db'], $options['prefix']) : new TokenSessionStorage(),
                'cw.client.headers' => [
                    'User-Agent' => $options['client_headers'],
                ]
            ];

            $config = new CodesWholesaleClientConfig($parameters);

            self::$instance->client = new CodesWholesaleApi(ClientBuilder::CONFIGURATION_ID, $config);
        }

        return self::$instance;
    }

    /**
     * @return CodesWholesaleApi
     */
    public function getClient(): CodesWholesaleApi
    {
        return $this->client;
    }

    /**
     * @param string $code
     */
    public static function sendActivity(string $code)
    {
        self::getInstance()->getClient()->post( '/v2/activities', [
            'json' => ['activity' => $code]
        ]);
    }
}