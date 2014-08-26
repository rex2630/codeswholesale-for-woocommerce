<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!class_exists('CW_Update_Stock')) :

    class CW_Cron_Update_Stock extends CW_Cron_Job
    {
        /**
         *
         */
        public function __construct()
        {
            parent::__construct("codeswholesale_update_stock_action");
        }

        /**
         *
         */
        public function cron_job()
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

            echo "Stock updated. \n";
        }
    }

endif;

new CW_Cron_Update_Stock();