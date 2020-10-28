<?php
require_once( dirname(__FILE__) . '/action-abstract.php' );

use CodesWholesaleFramework\Model\ProductModel;

class CW_Update_Internal_Product extends CW_Product_Action_Abstract
{
    public function update(ProductModel $productModel, int $post_id) 
    { 
        $wpProduct = get_post($post_id); 
          
        $post = array( 'ID' => $post_id, 'post_status' => 'publish');
          
        $cwProductModel = $this->codeswholesaleProductModelFactory->prepare($productModel, $this->optionsArray[CodesWholesaleConst::PREFERRED_LANGUAGE_FOR_PRODUCT_OPTION_NAME]);

        if (! $cwProductModel->isContentDiff($wpProduct->post_content)) {
            $post['post_content'] = $productModel->getFactSheets();
        }

        if (! $cwProductModel->isTitleDiff($wpProduct->post_title)) {
            $post['post_title'] = $productModel->getName();
        }

        wp_update_post( $post );
        
        $this->updateProductOptions($productModel, $post_id);

        $exist_thumb_title = get_the_title(get_post_thumbnail_id($post_id));

        if (! $cwProductModel->isThumbDiff($exist_thumb_title)) {
            $this->updateProductThumbnail($productModel, $post_id, $exist_thumb_title);
        }

        $exist_gallery = $this->getExistGallery($post_id);

        if (! $cwProductModel->isGalleryDiff($exist_gallery)) {
            $this->updateProductGallery($productModel, $post_id, $exist_gallery);
        }

        $this->codeswholesaleProductModelFactory->update($productModel, $cwProductModel);
    }
    
    /**
     * @param $post_id
     * @return array
     */
    private function getExistGallery($post_id) {
        $gallery_attach_ids = explode(',', $this->get_custom_field($post_id, '_product_image_gallery', ''));
        $names = [];

        foreach($gallery_attach_ids as $ids) {
            $title = get_the_title($ids);
            if($title) {
                $names[$ids] = $title;
            }
        }

        return $names;
    }
}

