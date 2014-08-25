<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!class_exists('CW_Install')) :

    class CW_Install
    {
        public static $default_client_id = "ff72ce315d1259e822f47d87d02d261e";
        public static $default_client_secret = '$2a$10$E2jVWDADFA5gh6zlRVcrlOOX01Q/HJoT6hXuDMJxek.YEo.lkO2T6';
        public static $default_env = \CodesWholesale\CodesWholesale::SANDBOX_ENDPOINT; // sandbox

        /**
         *
         */
        public function __construct()
        {
            register_activation_hook(CW_PLUGIN_FILE, array($this, 'install'));
            add_action( 'woocommerce_email', array($this, 'unhook_those_pesky_emails' ));
        }

        /**
         *
         */
        public function install()
        {
            $this->create_options();
        }

        /**
         *
         */
        private function create_options()
        {
            $params = array(
                'cw.client_id' => static::$default_client_id,
                'cw.client_secret' => static::$default_client_secret,
                'cw.endpoint_uri' => static::$default_env
            );

            add_option(CodesWholesaleConst::SETTINGS_CODESWHOLESALE_PARAMS_NAME, json_encode($params));
        }

        public function unhook_those_pesky_emails( $email_class) {
            remove_action('woocommerce_order_status_completed_notification', array(&$email_class->emails['WC_Email_Customer_Completed_Order'], 'trigger'));
        }
    }

endif;

new CW_Install();