<?php
use CodesWholesaleFramework\Postback\UpdatePriceAndStock\Utils\UpdatePriceAndStockInterface;

class WP_Update_Price_And_Stock implements UpdatePriceAndStockInterface
{
    public function updateProduct($cwProductId, $quantity, $priceSpread)
    {

        $options_array = CW()->get_options();
        $currency = $options_array['currency'];
        $importer = new Import_Currencies();
        $rate = $importer->getRateByCurrencyName($currency);
		$rateFL = $rate[0];
		

        $args = array(
            'post_type' => 'product',
            'meta_key' => CodesWholesaleConst::PRODUCT_CODESWHOLESALE_ID_PROP_NAME,
            'meta_value' => $cwProductId
        );

        $posts = get_posts($args);

        if ($posts) {

            try {

                foreach ($posts as $post) {

                    $product = WC()->product_factory->get_product($post->ID, array());

                    $product->set_stock($quantity);

                    update_post_meta($post->ID, '_price', round($priceSpread * $rateFL, 2));
                    update_post_meta($post->ID, '_regular_price', round($priceSpread * $rateFL, 2));
					

                    if ($quantity == 0) {

                        $product->set_stock_status('outofstock');

                    } else {

                        $product->set_stock_status('instock');
                    }
                }


            } catch (\CodesWholesale\Resource\ResourceError $e) {
                die("Received product id: " . $cwProductId . " Error: " . $e->getMessage());
            }

        }
    }
}