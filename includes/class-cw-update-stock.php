<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!class_exists('CW_Update_Stock')) :

    class CW_Update_Stock
    {
        /**
         *
         */
        public function __construct()
        {
            add_filter('cron_schedules', array($this, 'cron_add'));

            register_activation_hook(CW_PLUGIN_FILE, array($this, 'schedule_update'));
            register_deactivation_hook(CW_PLUGIN_FILE, array($this, 'remove_schedule'));

            add_action("codeswholesale_update_stock_action", array($this, 'update_stock_data'));
        }

        /*
         *
         * public static function deactivate() {
         *  6
         *     wp_clear_scheduled_hook('my_hourly_event');
         *   7
         *  } // end activate
         */
        public function schedule_update()
        {
            wp_schedule_event(time(), "each_three", "codeswholesale_update_stock_action");
        }

        /**
         *
         */
        public function remove_schedule()
        {
            wp_clear_scheduled_hook('codeswholesale_update_stock_action');
        }

        /**
         * @param $schedules
         * @return mixed
         */
        public function cron_add($schedules)
        {
            // Adds once weekly to the existing schedules.
            $schedules['each_three'] = array(
                'interval' => 10,
                'display' => "Each Three"
            );

            return $schedules;
        }

        /**
         *
         */
        public function update_stock_data()
        {
            $products = get_posts(array(
                'post_type' => 'product',
                'meta_query' => array(
                    array(
                        'key' => CodesWholesaleConst::PRODUCT_CODESWHOLESALE_ID_PROP_NAME,
                        'value' => '',
                        'compare' => '!='
                    )
                ),
                'numberposts' => -1
            ));

            $products_ids = array();

            foreach ($products as $product) {
                $cw_product_id = get_post_meta($product->ID, CodesWholesaleConst::PRODUCT_CODESWHOLESALE_ID_PROP_NAME, true);
                $products_ids[$cw_product_id] = $product;
            }

            $cw_products = CW()->getCodesWholesaleClient()->getProducts();
            
            foreach ($cw_products as $cw_product) {

                if (isset($products_ids[$cw_product->getProductId()])) {

                    $post_product = $products_ids[$cw_product->getProductId()];

                    if ($cw_product->getStockQuantity() > 0) {
                        update_post_meta($post_product->ID, "_stock_status", "instock");
                    } else {
                        update_post_meta($post_product->ID, "_stock_status", "outofstock");
                    }

                }
            }

        }
    }

endif;

new CW_Update_Stock();