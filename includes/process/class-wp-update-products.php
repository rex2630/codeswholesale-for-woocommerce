<?php

use CodesWholesaleFramework\Postback\UpdateProduct\UpdateProductInterface;
use CodesWholesaleFramework\Model\ExternalProduct;
use CodesWholesale\Resource\Product;

/**
 * Class WP_Update_Products
 */
class WP_Update_Products implements UpdateProductInterface
{
    /**
     * @param $cwProductId
     * @param null $quantity
     * @param null $priceSpread
     * @throws Exception
     */
    public function updateProduct($cwProductId, $quantity = null, $priceSpread = null)
    {
        if (null === $quantity && null === $priceSpread) {
            $this->newProduct($cwProductId);
            return;
        }

        $wpProductUpdater = WP_Product_Updater::getInstance();
        $posts = CW()->get_related_wp_products($cwProductId);

        if ($posts) {

            try {
                foreach ($posts as $post) {
                    $wpProductUpdater->updateStockPrice($post->ID, $priceSpread);
                    $wpProductUpdater->updateRegularPrice($post->ID, $priceSpread);
                    $wpProductUpdater->updateStock($post->ID, $quantity);
                }
            } catch (\CodesWholesale\Resource\ResourceError $e) {
                die("Received product id: " . $cwProductId . " Error: " . $e->getMessage());
            } catch (\Exception $e) {
                die("Received product id: " . $cwProductId . " Error: " . $e->getMessage());
            }

        }
    }

    /**
     * Endpoint for API Client
     * 
     * @param $cwProductId
     */
    public function hideProduct($cwProductId)
    {

        if (1 == CW()->get_options()[CodesWholesaleConst::HIDE_PRODUCT_WHEN_DISABLED_OPTION_NAME]) {
            $posts = CW()->get_related_wp_products($cwProductId);

            foreach($posts as $post) {
                wp_update_post(array(
                    'ID'    =>  $post->ID,
                    'post_status'   =>  'draft'
                ));
            }
        }
    }

    /**
     * @param $cwProductId
     * @throws Exception
     */
    public function newProduct($cwProductId)
    {
        if (1 == CW()->get_options()[CodesWholesaleConst::AUTOMATICALLY_IMPORT_NEWLY_PRODUCT_OPTION_NAME]) {
            $product = Product::get($cwProductId);

            $externalProduct = (new ExternalProduct())
                ->setProduct($product)
                ->updateInformations(CW()->get_options()[CodesWholesaleConst::PREFERRED_LANGUAGE_FOR_PRODUCT_OPTION_NAME])
            ;

            $relatedInternalProducts = CW()->get_related_wp_products($externalProduct->getProduct()->getProductId());
                        
            if (0 === count($relatedInternalProducts)) {
                $this->createWooProduct($externalProduct);
            } elseif (0 < count($relatedInternalProducts)) {
                $this->updateWooProducts($externalProduct, $relatedInternalProducts);
            }
        }
    }

    /**
     * @param ExternalProduct $externalProduct
     */
    private function createWooProduct(ExternalProduct $externalProduct)
    {
        try {
            WP_Product_Updater::getInstance()->createWooCommerceProduct($this->getFirstAdminId(), $externalProduct);
        } catch (\Exception $ex) {
            die("Received product id: " . $externalProduct->getProduct()->getProductId() . " Error: " . $ex->getMessage());
        }
    }

    /**
     * @param ExternalProduct $externalProduct
     * @param $relatedInternalProducts
     */
    private function updateWooProducts(ExternalProduct $externalProduct, $relatedInternalProducts)
    {
        try {
            foreach ($relatedInternalProducts as $post) {
                WP_Product_Updater::getInstance()->updateWooCommerceProduct($post->ID, $externalProduct);
            }
        } catch (\Exception $ex) {
        }
    }

    public function getFirstAdminId()
    {
        global $wpdb;

        $result = $wpdb->get_results("SELECT ID FROM $wpdb->users ORDER BY ID");

        foreach ( $result as $user ) {
            $id = $user->ID;
            $level = (int) get_user_meta($id, 'wp_user_level', true);

            if($level >= 8){
                return $id;
            }
        }

        throw new \Exception('Not found admin');
    }
}

