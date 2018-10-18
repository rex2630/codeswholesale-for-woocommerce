<?php

use CodesWholesale\Resource\Product;
use CodesWholesale\Resource\FullProduct;
use CodesWholesaleFramework\Model\ProductModel;
use CodesWholesaleFramework\Factories\ProductModelFactory;
use CodesWholesaleFramework\Postback\UpdateProduct\UpdateProductInterface;

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

        if (!$posts) {
            return;
        }
        
        try {
            foreach ($posts as $post) {
                $wpProductUpdater->updateProduct->updateStockPrice($post->ID, $priceSpread);
                $wpProductUpdater->updateProduct->updateRegularPrice($post->ID, $priceSpread);
                $wpProductUpdater->updateProduct->updateStock($post->ID, $quantity);
            }
        } catch (\CodesWholesale\Resource\ResourceError $e) {
            die("Received product id: " . $cwProductId . " Error: " . $e->getMessage());
        } catch (\Exception $e) {
            die("Received product id: " . $cwProductId . " Error: " . $e->getMessage());
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

            $producModel = ProductModelFactory::resolveProduct($product, CW()->get_options()[CodesWholesaleConst::PREFERRED_LANGUAGE_FOR_PRODUCT_OPTION_NAME]);

            $relatedInternalProducts = CW()->get_related_wp_products($producModel->getProductId());
                        
            if (0 === count($relatedInternalProducts)) {
                $this->createWooProduct($producModel);
            } elseif (0 < count($relatedInternalProducts)) {
                $this->updateWooProducts($producModel, $relatedInternalProducts);
            }
        }
    }
    
    /**
     * 
     * @param FullProduct[]
     */
    public function fullProducts(array $fullProducts) {
        try {
            (new PostbackImportProduct())->execute($fullProducts);
        } catch (\Exception $ex) {
            die("Postback import error: " . $ex->getMessage());
        }
    }

    /**
     * @param ProductModel $producModel
     */
    private function createWooProduct(ProductModel $producModel)
    {
        try {
            WP_Product_Updater::getInstance()->createProduct->create($producModel, $this->getFirstAdminId());
        } catch (\Exception $ex) {
            die("Received product id: " . $producModel->getProductId() . " Error: " . $ex->getMessage());
        }
    }

    /**
     * @param ProductModel $producModel
     * @param $relatedInternalProducts
     */
    private function updateWooProducts(ProductModel $producModel, $relatedInternalProducts)
    {
        try {
            foreach ($relatedInternalProducts as $post) {
                WP_Product_Updater::getInstance()->updateProduct->update($producModel, $post->ID);
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

