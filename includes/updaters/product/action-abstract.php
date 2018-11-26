<?php

use CodesWholesaleFramework\Model\ProductModel;
use CodesWholesaleFramework\Provider\PriceProvider;
use CodesWholesaleFramework\Database\Factories\CodeswholesaleProductModelFactory;

abstract class CW_Product_Action_Abstract 
{
    /**
     * @var WP_Attachment_Updater
     */
    protected $attachmentUpdater;
    
    /**
     * @var WP_Category_Updater
     */
    protected $categoryUpdater;
    
    /**
     * @var WP_Attribute_Updater
     */
    protected $attributeUpdater;
 
    /**
     * @var array|mixed|void
     */
    protected $optionsArray;
    
    /**
     * @var CodeswholesaleProductModelFactory
     */
    protected $codeswholesaleProductModelFactory;
    
    public function __construct()
    {
        $this->attachmentUpdater = new WP_Attachment_Updater();
        $this->categoryUpdater  = new WP_Category_Updater();
        $this->attributeUpdater = new WP_Attribute_Updater();
        $this->optionsArray = CW()->get_options();
        $this->codeswholesaleProductModelFactory = new CodeswholesaleProductModelFactory(new WP_DbManager());
    }
     
   
    /**
     * Update front price based on stock price
     * 
     * @param type $post_id
     * @param type $stock_price
     */
    public function updateRegularPrice($post_id, $stock_price)
    {
        $product_calculate_price_method = $this->get_custom_field($post_id, CodesWholesaleConst::PRODUCT_CALCULATE_PRICE_METHOD_PROP_NAME, 0);
       
        switch($product_calculate_price_method) {
            case 0:
                $spread_type  = $this->optionsArray['spread_type'];
                $spread_value = $this->optionsArray['spread_value'];
               break;
           case 1:
                $spread_type  = $this->get_custom_field($post_id, CodesWholesaleConst::PRODUCT_SPREAD_TYPE_PROP_NAME, 0);
                $spread_value = $this->get_custom_field($post_id, CodesWholesaleConst::PRODUCT_SPREAD_VALUE_PROP_NAME, 0);
               break;
           default:
               return;
        }

        $currency = $this->optionsArray['currency'];
        $product_price_charmer = $this->optionsArray['product_price_charmer'];
		 
        $priceProvider = new PriceProvider(new WP_DbManager());
        $price = $priceProvider->getCalculatedPrice($spread_type, $spread_value, $stock_price, $product_price_charmer, $currency);

        update_post_meta($post_id, '_regular_price', round($price, 2));
        update_post_meta($post_id, '_price', round($price, 2));
    }
    
    /**
     * Update stock (price form codeswholesale API) price in EUR
     * 
     * @param $post_id
     * @param $price
     */
    public function updateStockPrice($post_id, $price)
    {
        update_post_meta($post_id, CodesWholesaleConst::PRODUCT_STOCK_PRICE_PROP_NAME, round($price, 2));
    }

    /**
     * Update stock quantity
     * 
     * @param $post_id
     * @param $quantity
     */
    public function updateStock($post_id, $quantity)
    {        
        $product_calculate_price_method = $this->get_custom_field($post_id, CodesWholesaleConst::PRODUCT_CALCULATE_PRICE_METHOD_PROP_NAME, 0);
        
        if ($product_calculate_price_method === 2) {
            return;   
        }
        
        update_post_meta( $post_id, '_stock', $quantity);

        if ($quantity == 0) {
            $backordes = $this->get_custom_field($post_id, '_backorders', 0);
            $stockStatus = $this->get_custom_field($post_id, '_stock_status', 0);
            
            if ($backordes &&  $backordes === 'yes' && $stockStatus !== 'instock') {
                return;
            }

            update_post_meta( $post_id, '_stock_status', 'outofstock');
        } else {
            update_post_meta( $post_id, '_stock_status', 'instock');
        } 
    }
    
    /**
     * 
     * @param ProductModel $productModel
     * @param type $post_id
     * @return type
     */
    protected function getProductGlobalAttributes(ProductModel $productModel, $post_id) 
    {
        $platform   = $productModel->getPlatform();
        $regions    = $productModel->getRegions();
        $languages  = $productModel->getLanguages();
        
        $attributes =  $this->attributeUpdater->globalAttributes($platform, $regions, $languages);
        
        $product_attributes_data = array();
        
        foreach ($attributes as $key => $value) // Loop round each attribute
        {
            $this->attributeUpdater->insertAttributeTerm($value, $key);

            wp_set_object_terms( $post_id, $value, wc_clean($key ));
            
            if($value) {
               $product_attributes_data[sanitize_title($key)] = array( // Set this attributes array to a key to using the prefix 'pa'
                   'name' => wc_clean($key),
                   'value' => $value,
                   'is_visible' =>  true,
                   'is_variation' => true,
                   'is_taxonomy' => true
               ); 
           }
        }
        
        return $product_attributes_data;
    }
    
    /**
     * 
     * @param ProductModel $productModel
     * @return type
     */
    protected function getProductLocalAttributes(ProductModel $productModel) 
    {
        $extensionPacks = $productModel->getExtensionsPacks();
        $eanCodes       = $productModel->getEans();
        $releases       = $productModel->getReleases();
                
        $attributes =  $this->attributeUpdater->localAttributes($extensionPacks, $eanCodes, $releases);
        
        $product_attributes_data = array();
         
         foreach ($attributes as $key => $value) // Loop round each attribute
         {
            $value = is_array($value) ? implode("|", $value) : $value;

            if($value) {
                $product_attributes_data[sanitize_title($key)] = array( // Set this attributes array to a key to using the prefix 'pa'
                    'name' => wc_clean($key),
                    'value' => $value,
                    'is_visible' =>  true,
                    'is_variation' => false,
                    'is_taxonomy' => false
                ); 
            }
         }
         
        return $product_attributes_data;
    }
    
    /**
     * @param $post_id
     * @param $category
     * @param $parent
     * @param string $description
     */
    protected function setProductCategory($post_id, $category, $parent, $description = '') 
    {
        if(is_array($category)) {
            foreach($category as $cat) {
                if(! $cat) {
                    continue;
                }

                $id = $this->categoryUpdater->getTermIdForce($cat,$parent, $description);
                wp_set_post_terms( $post_id, $id, WP_Category_Updater::TAXONOMY_SLUG, true );
            }
        } else {
            if($category) {
                $id = $this->categoryUpdater->getTermIdForce($category, $parent, $description);
                wp_set_post_terms( $post_id, $id, WP_Category_Updater::TAXONOMY_SLUG, true );  
            }
        }
    }
    
    /**
     * 
     * @param ProductModel $productModel
     * @param type $post_id
     */
    protected function setProductDeveloper(ProductModel $productModel, $post_id) 
    {
        try {
            $developer = $productModel->getDeveloperName();

            $developer_description = $productModel->getDeveloperHomepage();

            if($developer_description) {
                $developer_description = 'Developer homepage: ' . $developer_description;
            }
            
            $this->setProductCategory($post_id, $developer,  WP_Category_Updater::CATEGORY_SLUG_DEVELOPER, $developer_description);
        } catch (\Exception $ex) {
        }
    }
    
    /**
     * 
     * @param ProductModel $productModel
     * @param type $post_id
     */
    protected function setProductCategories(ProductModel $productModel, $post_id) 
    {
        try {
            $category = $productModel->getCategory();

            $this->setProductCategory($post_id, $category,  WP_Category_Updater::CATEGORY_SLUG_CATEGORY);
        } catch (\Exception $ex) {
        }
    }
    
    /**
     * 
     * @param ProductModel $productModel
     * @param type $post_id
     */
    protected function setProductPegiRatin(ProductModel $productModel, $post_id) 
    {
        try {
            $pegi = $productModel->getPegiRating();

            $this->setProductCategory($post_id, $pegi,  WP_Category_Updater::CATEGORY_SLUG_PEGI);
        } catch (\Exception $ex) {
        }
    }
    
    /**
     * 
     * @param ProductModel $productModel
     * @param type $post_id
     */
    protected function updateProductCategory(ProductModel $productModel, $post_id) 
    {
        $this->setProductDeveloper($productModel, $post_id);
        $this->setProductCategories($productModel, $post_id);
        $this->setProductPegiRatin($productModel, $post_id);
    }
    
    /**
     * 
     * @param ProductModel $productModel
     * @param type $post_id
     */
    protected function updateProductTags(ProductModel $productModel, $post_id) 
    {
        try {
            $keywords = $productModel->getKeywords();

            if ($keywords) {
                wp_set_object_terms($post_id, $keywords, 'product_tag');
            }  
        } catch (\Exception $ex) {
        }
    }
    
    /**
     * 
     * @param ProductModel $productModel
     * @param type $post_id
     */
    protected function updateProductAttributes(ProductModel $productModel, $post_id) 
    {
        try {
            $global = $this->getProductGlobalAttributes($productModel, $post_id);
            $local = $this->getProductLocalAttributes($productModel, $post_id);

            $attrs = array_merge($local, $global);

            update_post_meta($post_id, '_product_attributes', $attrs);
        } catch (\Exception $ex) {
        }
    }
    
    /**
     * 
     * @param ProductModel $productModel
     * @param type $post_id
     */
    protected function updateProductOptions(ProductModel $productModel, $post_id) 
    {
        $backordes = $this->get_custom_field($post_id, '_backorders', 0);
        
        update_post_meta( $post_id, '_virtual', 'yes' );
        update_post_meta( $post_id, '_manage_stock', "yes" );
        update_post_meta( $post_id, '_sku', $productModel->getIdentifier());
        
        if(!$backordes) {
            update_post_meta( $post_id, '_backorders', "no" );
        }
        
        $this->updateStockPrice($post_id, $productModel->getPrice());
        $this->updateRegularPrice($post_id, $productModel->getPrice());
        $this->updateStock($post_id, $productModel->getQuantity());

        $this->updateProductCategory($productModel, $post_id);
        $this->updateProductTags($productModel, $post_id);
        $this->updateProductAttributes($productModel, $post_id);
    }
    
    /**
     * 
     * @param ProductModel $productModel
     * @param type $post_id
     * @param type $exist_thumb
     */
    protected function updateProductThumbnail(ProductModel $productModel, $post_id, $exist_thumb = null) 
    {
        $thumb = $productModel->getImage();
        
        if (count($thumb) > 0 && $exist_thumb !== $thumb['name']) {
            try {
                $attach_id = $this->attachmentUpdater->setAttachment($post_id, $thumb['url'], $thumb['name']);
                set_post_thumbnail( $post_id, $attach_id );
            } catch (\Exception $ex) {
            }
        }
    }
    
    /**
     * 
     * @param ProductModel $productModel
     * @param type $post_id
     * @param type $exist_gallery
     */
    protected function updateProductGallery(ProductModel $productModel, $post_id, $exist_gallery = []) 
    {
        $ids = [];
        $photos = $productModel->getPhotos();
        
        foreach($photos as $photo) {
            if(in_array($photo['name'], $exist_gallery)) {
                $ids[] = array_search($photo['name'], $exist_gallery);
            } else {
                try {
                    $attach_id = $this->attachmentUpdater->setAttachment($post_id, $photo['url'], $photo['name']);

                    if($attach_id) {
                            $ids[] = $attach_id;
                    }
                } catch (\Exception $ex) {
                }
            }
        }

        add_post_meta($post_id, '_product_image_gallery', implode(',', $ids));  
    }
        
    /**
     *
     * @param type $post_id
     * @param type $field_name
     * @param type $default
     * @return type
     */
    protected function get_custom_field($post_id, $field_name, $default)
    {
        $value = null;

        if($post_id) {
            $value = get_post_meta($post_id, $field_name, true);
        }

        if(empty($value) || null == $value) {
            return $default;
        }

        return $value;
    }
}
