<?php

use CodesWholesaleFramework\Postback\RegisterHandlers;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!class_exists('CW_Install')) :

    class CW_Install
    {
        public static $default = array(

            'environment' => 0,
            'api_client_id' => 'ff72ce315d1259e822f47d87d02d261e',
            'api_client_secret' => '$2a$10$E2jVWDADFA5gh6zlRVcrlOOX01Q/HJoT6hXuDMJxek.YEo.lkO2T6',
            CodesWholesaleConst::AUTOMATICALLY_COMPLETE_ORDER_OPTION_NAME => 0,
            CodesWholesaleConst::RISK_SCORE_PROP_NAME => 2,
            CodesWholesaleConst::NOTIFY_LOW_BALANCE_VALUE_OPTION_NAME => 100,
            CodesWholesaleConst::PREFERRED_LANGUAGE_FOR_PRODUCT_OPTION_NAME => 'english',
            'spread_type' => 1,
            'spread_value' => 50,
            'currency' => 'EUR',
            'currency_value' => 1
        );

        public function __construct()
        {
            register_activation_hook(CW_PLUGIN_FILE, array($this, 'install'));
            add_action('woocommerce_email', array($this, 'unhook_those_pesky_emails'));

            add_action('admin_post_codeswholesale_notifications', array($this, 'codeswholesale_notifications'));
            add_action('admin_post_nopriv_codeswholesale_notifications', array($this, 'codeswholesale_notifications'));
        }

        public function install()
        {
            $this->create_options();
        }

        private function create_options()
        {
            add_option(CodesWholesaleConst::OPTIONS_NAME, CW_Install::$default);
        }

        public function unhook_those_pesky_emails($email_class)
        {
            remove_action('woocommerce_order_status_completed_notification', array(&$email_class->emails['WC_Email_Customer_Completed_Order'], 'trigger'));
        }

        public function codeswholesale_notifications()
        {
            $options = get_option(CodesWholesaleConst::OPTIONS_NAME);

            $action = new RegisterHandlers(CW()->get_codes_wholesale_client(), $options['environment']);

            $action->setProductUpdater(new WP_Update_Products());
            $action->setOrderUpdater(new WP_Update_Orders());

            $action->process();
        }
    }

endif;

new CW_Install();