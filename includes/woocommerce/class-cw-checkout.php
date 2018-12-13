<?php

use CodesWholesale\Resource\Security;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!class_exists('CW_Checkout')) :

    /**
     *
     */
    class CW_Checkout
    {

        /**
         *
         */
        public function __construct()
        {
            add_action('woocommerce_checkout_order_processed', array($this, 'add_codeswholesale_status'));
            add_filter('woocommerce_payment_complete_order_status', array($this, 'automatic_complete_order'), 10, 3);
        }

        /**
         * @param $order_id
         */
        public function add_codeswholesale_status($order_id)
        {
            add_post_meta($order_id, CodesWholesaleConst::ORDER_FULL_FILLED_PARAM_NAME, CodesWholesaleOrderFullFilledStatus::TO_FILL);
        }

        /**
         * @param $status
         * @param int $order_id
         * @param bool|WC_Order $order
         *
         * @return string
         */
        public function automatic_complete_order($status, $order_id = 0, $order = false )
        {
            $options_array = CW()->get_options();

            if($order && $options_array[CodesWholesaleConst::AUTOMATICALLY_COMPLETE_ORDER_OPTION_NAME] == CodesWholesaleAutoCompleteOrder::COMPLETE) {
                $securityInformation = Security::check(
                    $order->get_billing_email(),
                    $order->get_customer_user_agent(),
                    $order->get_billing_email(),
                    $order->get_customer_ip_address()
                );

                $riskScore = $securityInformation->getRiskScore();

                $order->add_order_note("Client risk score: <strong>".$riskScore."</strong>");

                if ($riskScore > $options_array[CodesWholesaleConst::RISK_SCORE_PROP_NAME]) {
                    $order->add_order_note("Client risk score: <strong>".$riskScore."</strong> is higher then your risk score value <strong>". $options_array[CodesWholesaleConst::RISK_SCORE_PROP_NAME]."</strong>.");

                    return 'processing';
                }
                return 'completed';
            }
            return 'processing';
        }
    }

endif;

new CW_Checkout();