<?php

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
            add_filter('woocommerce_payment_complete_order_status', array($this, 'automatic_complete_order'));
        }

        /**
         * @param $order_id
         */
        public function add_codeswholesale_status($order_id)
        {
            add_post_meta($order_id, CodesWholesaleConst::ORDER_FULL_FILLED_PARAM_NAME, CodesWholesaleOrderFullFilledStatus::TO_FILL);
        }

        /**
         * @param $order_id
         * @return string
         */
        public function automatic_complete_order($order_id)
        {
            if(get_option(CodesWholesaleConst::AUTOMATICALLY_COMPLETE_ORDER_OPTION_NAME) == CodesWholesaleAutoCompleteOrder::COMPLETE) {
                $order = new WC_Order($order_id);
                return 'completed';
            }
            return 'processing';
        }
    }

endif;

new CW_Checkout();