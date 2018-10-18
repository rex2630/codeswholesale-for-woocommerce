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
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => CodesWholesaleConst::PRODUCT_CODESWHOLESALE_ID_PROP_NAME,
                    'value'   => array('', '0', null),
                    'compare' => 'NOT IN',
                ),
                array(
                    'relation' => 'OR',
                    array(
                        'key' => CodesWholesaleConst::PRODUCT_CALCULATE_PRICE_METHOD_PROP_NAME,
                        'value' => 0
                    ),
                    array(
                        'key' => CodesWholesaleConst::PRODUCT_CALCULATE_PRICE_METHOD_PROP_NAME,
                        'value' => 1
                    )
                )
            ),
        );

        $posts = get_posts($args);

        if ($posts) {

            foreach ($posts as $post) {
                $stock_price = get_post_meta($post->ID, CodesWholesaleConst::PRODUCT_STOCK_PRICE_PROP_NAME, true);

                $wpProductUpdater->updateProduct->updateRegularPrice($post->ID, $stock_price);
            }
        }
    }
}

$updateProductsPrice = new UpdateProductsPrice();

try {
    $optionsArray = CW()->get_options();

    $updateProductsPrice ->execute();
} catch (\Exception $ex) {
}