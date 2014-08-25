<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!class_exists('CW_Checkout')) :

    class CW_Checkout
    {

        public function __construct()
        {
            add_action('woocommerce_checkout_order_processed', array($this, 'add_codeswholesale_status'));
        }

        public function add_codeswholesale_status($order_id)
        {
            add_post_meta($order_id, "_codeswholesale_filled", 0);
        }
    }

endif;

new CW_Checkout();