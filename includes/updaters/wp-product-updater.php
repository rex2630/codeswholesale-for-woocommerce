<?php

use CodesWholesale\Resource\Product;
use CodesWholesaleFramework\Provider\PriceProvider;
use CodesWholesaleFramework\Model\ExternalProduct;

/**
 * Class WP_Product_Updater
 */
class WP_Product_Updater
{
    /**
     * @var WP_Product_Updater
     */
    private static $instance;

    /**
     * @var WP_Attachment_Updater
     */
    private $attachmentUpdater;
    
    /**
     * @var WP_Category_Updater
     */
    private $categoryUpdater;
    
    /**
     * @var WP_Attribute_Updater
     */
    private $attributUpdater;
    
    /**
     * @var array|mixed|void
     */
    private $optionsArray;

    /**
     * WP_Product_Updater constructor.
     */
    private function __construct()
    {
        $this->attachmentUpdater = new WP_Attachment_Updater();
        $this->categoryUpdater  = new WP_Category_Updater();
        $this->attributUpdater  = new WP_Attribute_Updater();
        $this->optionsArray = CW()->get_options();
    }

    public static function getInstance()
    {
        if(self::$instance === null) {
            self::$instance = new WP_Product_Updater();
        }
        return self::$instance;
    }

    public function createWooCommerceProduct(int $user_id, ExternalProduct $externalProduct)
    {
        $post = array(
            'post_author' => $user_id,
            'post_content' => $externalProduct->getDescription(),
            'post_status' => "publish",
            'post_title' => wc_clean($externalProduct->getProduct()->getName()),
            'post_parent' => '',
            'post_type' => "product",
        );
        
        $post_id = wp_insert_post( $post );
        
        if (! $post_id) {
            throw new Exception('Error');
        }
        
        update_post_meta( $post_id, CodesWholesaleConst::PRODUCT_CODESWHOLESALE_ID_PROP_NAME, esc_attr($externalProduct->getProduct()->getProductId()));
        update_post_meta( $post_id, CodesWholesaleConst::PRODUCT_CALCULATE_PRICE_METHOD_PROP_NAME, 0);
        update_post_meta( $post_id, CodesWholesaleConst::PRODUCT_SPREAD_TYPE_PROP_NAME, 0);
        update_post_meta( $post_id, CodesWholesaleConst::PRODUCT_SPREAD_VALUE_PROP_NAME, 0);   
        
        update_post_meta( $post_id, '_virtual', 'yes' );
        update_post_meta( $post_id, '_manage_stock', "yes" );
        update_post_meta( $post_id, '_sku', $externalProduct->getProduct()->getIdentifier());
        update_post_meta( $post_id, '_backorders', "no" );
        
        $this->updateStockPrice($post_id, $externalProduct->getProduct()->getLowestPrice());
        $this->updateRegularPrice($post_id, $externalProduct->getProduct()->getLowestPrice());
        $this->updateStock($post_id, $externalProduct->getProduct()->getStockQuantity());
        
        $this->updateProductDescriptions($post_id, $externalProduct);
        
        return $post_id;
    }
    
    public function updateWooCommerceProduct(int $post_id, ExternalProduct $externalProduct)
    {
        $post = array(
            'ID' => $post_id,
            'post_status' => 'publish',
            'post_content' => $externalProduct->getDescription(),
            'post_title' => $externalProduct->getProduct()->getName(),
        );

        wp_update_post( $post );
        update_post_meta($post_id, '_sku', $externalProduct->getProduct()->getIdentifier());
        
        $this->updateStockPrice($post_id, $externalProduct->getProduct()->getLowestPrice());
        $this->updateRegularPrice($post_id, $externalProduct->getProduct()->getLowestPrice());
        $this->updateStock($post_id, $externalProduct->getProduct()->getStockQuantity());
        
        $this->updateProductDescriptions($post_id, $externalProduct);
    }

    private function updateProductDescriptions($post_id, $externalProduct) {
        $this->updateProductCategory($post_id, $externalProduct->getProduct());
        $this->updateProductTags($post_id, $externalProduct->getProduct());
        $this->updateProductAttributes($post_id, $externalProduct->getProduct());
        $this->updateProductGallery($post_id, $externalProduct->getProduct());
        $this->updateProductThumbnail($post_id, $externalProduct->getProduct()->getImageUrl('MEDIUM'));
    }

    /**
     * 
     * @param type $post_id
     * @param Product $product
     */
    public function updateProductAttributes($post_id, Product $product) {
        try {
            $global = $this->getProductGloablAttributes($post_id, $product);
            $local = $this->geProductLocalAttributes($product);

            $attrs = array_merge($local, $global);

            update_post_meta($post_id, '_product_attributes', $attrs);
        } catch (Exception $ex) {
        }
    }
    
    /**
     * 
     * @param type $post_id
     * @param Product $product
     */
    private function geProductLocalAttributes(Product $product) {
        $attributes =  $this->attributUpdater->localAttributes($product);
        
        $product_attributes_data = array();
         
         foreach ($attributes as $key => $value) // Loop round each attribute
         {
            if(is_array($value)) {
                $value = implode("|", $value);
            }
            
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
     * 
     * @param type $post_id
     * @param Product $product
     */
    private function getProductGloablAttributes($post_id, Product $product) {

        $attributes =  $this->attributUpdater->globalAttributes($product);
        
        $product_attributes_data = array();
        
        foreach ($attributes as $key => $value) // Loop round each attribute
        {
            $this->attributUpdater->insertAttributeTerm($value, $key);

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
        
    public function updateProductTags($post_id, Product $product) {
        try {
            $keywords = $product->getProductDescription()->getKeywords();

            if ($keywords) {
                wp_set_object_terms($post_id, $keywords, 'product_tag');
            } 
        } catch (Exception $ex) {
        }
    }
    
    public function updateProductCategory($post_id, Product $product) {
        try {
            $developer = $product->getProductDescription()->getDeveloperName();

            $developer_description = $product->getProductDescription()->getDeveloperHomepage();

            if($developer_description) {
                $developer_description = 'Developer homepage: ' . $developer_description;
            }

            $this->setProductCategory($post_id, $developer,  WP_Category_Updater::CATEGORY_SLUG_DEVELOPER, $developer_description);

            $category = $product->getProductDescription()->getCategories();

            $this->setProductCategory($post_id, $category,  WP_Category_Updater::CATEGORY_SLUG_CATEGORY);

            $pegi = $product->getProductDescription()->getPegiRating();

            $this->setProductCategory($post_id, $pegi,  WP_Category_Updater::CATEGORY_SLUG_PEGI);  
        } catch (Exception $ex) {
        }
    }
    
    public function setProductCategory($post_id, $category, $parent, $description = '') {
        if(is_array($category)) {
            foreach($category as $cat) {
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
    
    public function updateProductGallery($post_id, Product $product) {
        try {
            $photos = $product->getProductDescription()->getPhotos();
            
            $default     = [];
            $preferred   = [];
            
            /** @var \CodesWholesale\Resource\Photo $photo */
            foreach($photos as $photo) {
                if('SCREEN_SHOT_LARGE' == $photo->getType()) {
                    if("" == $photo->getTerritory() || $this->optionsArray[CodesWholesaleConst::PREFERRED_LANGUAGE_FOR_PRODUCT_OPTION_NAME] == $photo->getTerritory()) {
                        $preferred[] = $photo->getUrl();
                    }
                    if("" == $photo->getTerritory() || 'uk' == $photo->getTerritory()) {
                        $default[] = $photo->getUrl();
                    }    
                } 
            }
            
            $urls = empty($preferred) ? $default : $preferred;
            
            $this-> setProductGallery($post_id, $urls);
        } catch (Exception $ex) {
        }
    }
    
    public function setProductGallery(int $post_id, Array $urls = []) {
        $ids = [];
        
        foreach($urls as $url) {
            $photo_data = explode("/",$url);
            $count = count ($photo_data);
            $attach_id = $this->attachmentUpdater->setAttachment($post_id, $url, $photo_data[$count-2]);
            
            if($attach_id) {
                $ids[] = $attach_id;
            }
        }
        
        add_post_meta($post_id, '_product_image_gallery', implode(',', $ids));  
    }
    /**
     * 
     * @param type $post_id
     * @param type $url
     */
    public function updateProductThumbnail($post_id, $url) {        
        try{
            $photo_data = explode("/",$url);
            $count = count ($photo_data);

            if(is_array($photo_data) && $photo_data[$count-2]) {
                $attach_id = $this->attachmentUpdater->setAttachment($post_id, $url, $photo_data[$count-2]);
                set_post_thumbnail( $post_id, $attach_id );
            }

        } catch (Exception $ex) {
            // log error
        }
    }
    
    /**
     * Update front price based on stock price
     * 
     * @param type $post_id
     * @param type $stock_price
     */
    public function updateRegularPrice($post_id, $stock_price)
    {
        $currency = $this->optionsArray['currency'];
        $spread_type = $this->optionsArray['spread_type'];
        $spread_value = $this->optionsArray['spread_value'];
        $product_price_charmer = $this->optionsArray['product_price_charmer'];

        $priceProvider = new PriceProvider();
        $price = $priceProvider->getCalculatedPrice($spread_type, $spread_value, $stock_price, $product_price_charmer, $currency);

        update_post_meta($post_id, '_regular_price', round($price, 2));
        update_post_meta($post_id, '_price', round($price, 2));
    }

    /**
     * Update stock (price form codeswholesale API) price in EUR
     * 
     * @param type $post_id
     * @param type $price
     */
    public function updateStockPrice($post_id, $price)
    {
        update_post_meta($post_id, CodesWholesaleConst::PRODUCT_STOCK_PRICE_PROP_NAME, round($price, 2));
    }

    /**
     * Update stock quantity
     * 
     * @param type $post_id
     * @param type $quantity
     */
    public function updateStock($post_id, $quantity)
    {
        update_post_meta( $post_id, '_stock', $quantity);
        
        if ($quantity == 0) {
            update_post_meta( $post_id, '_stock_status', 'outofstock');

        } else {
            update_post_meta( $post_id, '_stock_status', 'instock');
        }
    }
}