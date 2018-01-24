<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!class_exists('CW_Balance_Checker')) :

    class CW_Balance_Checker
    {

        public function __construct()
        {
            add_action('codeswholesale_buy_keys_completed', array($this, 'check_balance'));
        }


        public function check_balance()
        {
            $options = CW()->get_options();
            $account = CW()->get_codes_wholesale_client()->getAccount();
            $balance_value = $options[CodesWholesaleConst::NOTIFY_LOW_BALANCE_VALUE_OPTION_NAME];

            if ($balance_value >= doubleval($account->getCurrentBalance())) {
                $this->init_emails();
                do_action("codeswholesale_balance_to_low", $account);
            }
        }

        /**
         *
         */
        private function init_emails()
        {
            if (!isset(WC()->mailer()->emails["CW_Email_Notify_Low_Balance"])) {
                WC()->mailer()->emails["CW_Email_Notify_Low_Balance"] = include(CW()->plugin_path() . "/includes/emails/class/class-cw-email-notify-low-balance.php");
            }
        }
    }

endif;

new CW_Balance_Checker();