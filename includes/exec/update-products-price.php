<?php

require_once( dirname(__FILE__) . '/../../../../../wp-load.php' );
require_once( dirname(__FILE__) . '/../../codeswholesale.php' );


class UpdateProductsPrice
{


    /**
     * execute
     */
    public function execute()
    {
        $wpProductUpdater = WP_Product_Updater::getInstance();

        $args = array(
            'posts_per_page' => -1,
            'post_type' => 'product',
            'meta_key' => CodesWholesaleConst::PRODUCT_CALCULATE_PRICE_METHOD_PROP_NAME,
            'meta_value' => 0
        );

        $posts = get_posts($args);

        if ($posts) {

            foreach ($posts as $post) {
                $stock_price = get_post_meta($post->ID, CodesWholesaleConst::PRODUCT_STOCK_PRICE_PROP_NAME, true);

                $wpProductUpdater->updateRegularPrice($post->ID, $stock_price);
            }
        }
    }
}

$updateProductsPrice = new UpdateProductsPrice();

$updateProductsPrice ->execute();