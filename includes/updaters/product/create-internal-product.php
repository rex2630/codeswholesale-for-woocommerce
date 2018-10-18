<?php
require_once( dirname(__FILE__) . '/action-abstract.php' );

use CodesWholesaleFramework\Model\ProductModel;

class CW_Create_Internal_Product extends CW_Product_Action_Abstract
{
    /**
     * 
     * @param ProductModel $productModel
     * @param int $user_id
     * @throws \Exception
     */
    public function create(ProductModel $productModel, int $user_id) 
    { 
        try {
            $this->codeswholesaleProductModelFactory->create($productModel, $this->optionsArray[CodesWholesaleConst::PREFERRED_LANGUAGE_FOR_PRODUCT_OPTION_NAME]);
                    
            $post_id = wp_insert_post( $this->getPostParams($productModel, $user_id) );
            
            if (! $post_id) {
                throw new \Exception('Can not create product');
            } 
        } catch (\Exception $ex) {
            throw $ex;
        }
        
        try {
            $this->addAdditionalInformation($productModel, $post_id);
            $this->updateProductOptions($productModel, $post_id); 
            $this->updateProductThumbnail($productModel, $post_id);
            $this->updateProductGallery($productModel, $post_id);

            return $post_id;
        
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
    
    /**
     * 
     * @param ProductModel $productModel
     * @param int $user_id
     * @return string
     */
    private function getPostParams(ProductModel $productModel, int $user_id) 
    {
        $post = array(
            'post_author' => $user_id,
            'post_content' => $productModel->getFactSheets(),
            'post_status' => "publish",
            'post_title' => $productModel->getName(),
            'post_parent' => '',
            'post_type' => "product",
        );
        
        return $post;
    }
    
    /**
     * 
     * @param ProductModel $productModel
     * @param type $post_id
     */
    private function addAdditionalInformation(ProductModel $productModel, $post_id) 
    {
        update_post_meta( $post_id, CodesWholesaleConst::PRODUCT_CODESWHOLESALE_ID_PROP_NAME, esc_attr($productModel->getProductId()));
        update_post_meta( $post_id, CodesWholesaleConst::PRODUCT_CALCULATE_PRICE_METHOD_PROP_NAME, 0);
        update_post_meta( $post_id, CodesWholesaleConst::PRODUCT_SPREAD_TYPE_PROP_NAME, 0);
        update_post_meta( $post_id, CodesWholesaleConst::PRODUCT_SPREAD_VALUE_PROP_NAME, 0);
    }
}

