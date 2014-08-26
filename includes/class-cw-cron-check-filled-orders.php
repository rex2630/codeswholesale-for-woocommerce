<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!class_exists('CW_Cron_Check_Filled_Orders')) :

    class CW_Cron_Check_Filled_Orders extends CW_Cron_Job
    {
        /**
         * Set up settings.
         */
        public function __construct()
        {
            parent::__construct("codeswholesale_check_filled_orders");
        }

        /**
         * Do the cron.
         *
         */
        public function cron_job()
        {

            $customer_orders = get_posts(array(

                'post_type' => 'shop_order',

                'meta_query' => array(
                    array(
                        'key' => CodesWholesaleConst::ORDER_FULL_FILLED_PARAM_NAME,
                        'value' => '0',
                        'compare' => '='
                    )
                ),

                'tax_query' => array(
                    array(
                        'taxonomy' => 'shop_order_status',
                        'field' => 'slug',
                        'terms' => 'completed')
                ),

                'numberposts' => -1
            ));

            foreach ($customer_orders as $k => $v) {
                $a = new CW_SendKeys();
                $a->send_keys_for_order($customer_orders[$k]->ID);
            }

            echo "Orders checked. \n";
        }
    }

endif;

new CW_Cron_Check_Filled_Orders();