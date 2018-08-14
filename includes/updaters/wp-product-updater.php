<?php

use CodesWholesale\Resource\Product;
use CodesWholesaleFramework\Provider\PriceProvider;
use CodesWholesaleFramework\Model\ExternalProduct;
use CodesWholesaleFramework\Database\Factories\CodeswholesaleProductModelFactory;

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
    private $attributeUpdater;

    /**
     * @var CodeswholesaleProductModelFactory
     */
    private $codeswholesaleProductModelFactory;

    
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
        $this->attributeUpdater  = new WP_Attribute_Updater();
        $this->optionsArray = CW()->get_options();

        $this->codeswholesaleProductModelFactory = new CodeswholesaleProductModelFactory(new WP_DbManager());
    }

    public static function getInstance()
    {
        if(self::$instance === null) {
            self::$instance = new WP_Product_Updater();
        }
        return self::$instance;
    }

    /**
     * @param int $user_id
     * @param ExternalProduct $externalProduct
     * @return mixed
     * @throws Exception
     */
    public function createWooCommerceProduct(int $user_id, ExternalProduct $externalProduct)
    {
        $this->codeswholesaleProductModelFactory->create($externalProduct, $this->optionsArray[CodesWholesaleConst::PREFERRED_LANGUAGE_FOR_PRODUCT_OPTION_NAME]);

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
            throw new \Exception('Error');
        }

        update_post_meta( $post_id, CodesWholesaleConst::PRODUCT_CODESWHOLESALE_ID_PROP_NAME, esc_attr($externalProduct->getProduct()->getProductId()));
        update_post_meta( $post_id, CodesWholesaleConst::PRODUCT_CALCULATE_PRICE_METHOD_PROP_NAME, 0);
        update_post_meta( $post_id, CodesWholesaleConst::PRODUCT_SPREAD_TYPE_PROP_NAME, 0);
        update_post_meta( $post_id, CodesWholesaleConst::PRODUCT_SPREAD_VALUE_PROP_NAME, 0);

        $this->updateProductOptions($post_id, $externalProduct);

        $this->updateProductThumbnail($post_id, $externalProduct->getThumbnail());
        $this->updateProductGallery($post_id, $externalProduct->getPhotos());

        return $post_id;
    }

    /**
     * @param int $post_id
     * @param ExternalProduct $externalProduct
     */
    public function updateWooCommerceProduct(int $post_id, ExternalProduct $externalProduct)
    {
        $wpProduct = get_post($post_id);
        $cwProductModel = $this->codeswholesaleProductModelFactory->prepare($externalProduct, $this->optionsArray[CodesWholesaleConst::PREFERRED_LANGUAGE_FOR_PRODUCT_OPTION_NAME]);

        $post = array( 'ID' => $post_id, 'post_status' => 'publish');

        if (! $cwProductModel->isContentDiff($wpProduct->post_content)) {
            $post['post_content'] = $externalProduct->getDescription();
        }

        if (! $cwProductModel->isTitleDiff($wpProduct->post_title)) {
            $post['post_title'] = $externalProduct->getProduct()->getName();
        }

        wp_update_post( $post );

        $this->updateProductOptions($post_id, $externalProduct);

        $exist_thumb_title = get_the_title(get_post_thumbnail_id($post_id));

        if (! $cwProductModel->isThumbDiff($exist_thumb_title)) {
            $this->updateProductThumbnail($post_id, $externalProduct->getThumbnail(), $exist_thumb_title);
        }

        $exist_gallery = $this->getExistGallery($post_id);

        if (! $cwProductModel->isGalleryDiff($exist_gallery)) {
            $this->updateProductGallery($post_id, $externalProduct->getPhotos(), $exist_gallery);
        }

        $this->codeswholesaleProductModelFactory->update($externalProduct, $cwProductModel);
    }


    /**
     * @param $post_id
     * @param $externalProduct
     */
    private function updateProductOptions($post_id, $externalProduct) {


        update_post_meta( $post_id, '_virtual', 'yes' );
        update_post_meta( $post_id, '_manage_stock', "yes" );
        update_post_meta( $post_id, '_sku', $externalProduct->getProduct()->getIdentifier());
        update_post_meta( $post_id, '_backorders', "no" );

        $this->updateStockPrice($post_id, $externalProduct->getProduct()->getLowestPrice());
        $this->updateRegularPrice($post_id, $externalProduct->getProduct()->getLowestPrice());
        $this->updateStock($post_id, $externalProduct->getProduct()->getStockQuantity());

        $this->updateProductCategory($post_id, $externalProduct->getProduct());
        $this->updateProductTags($post_id, $externalProduct->getProduct());
        $this->updateProductAttributes($post_id, $externalProduct->getProduct());
    }

    /**
     * 
     * @param type $post_id
     * @param Product $product
     */
    public function updateProductAttributes($post_id, Product $product) {
        try {
            $global = $this->getProductGlobalAttributes($post_id, $product);
            $local = $this->geProductLocalAttributes($product);

            $attrs = array_merge($local, $global);

            update_post_meta($post_id, '_product_attributes', $attrs);
        } catch (Exception $ex) {
        }
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

    /**
     * @param Product $product
     * @return array
     */
    private function geProductLocalAttributes(Product $product) {
        $attributes =  $this->attributeUpdater->localAttributes($product);
        
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
     * @param $post_id
     * @param Product $product
     * @return array
     */
    private function getProductGlobalAttributes($post_id, Product $product) {

        $attributes =  $this->attributeUpdater->globalAttributes($product);
        
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
     * @param $post_id
     * @param Product $product
     */
    public function updateProductTags($post_id, Product $product) {
        try {
            $keywords = $product->getProductDescription()->getKeywords();

            if ($keywords) {
                wp_set_object_terms($post_id, $keywords, 'product_tag');
            } 
        } catch (Exception $ex) {
        }
    }

    /**
     * @param $post_id
     * @param Product $product
     */
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

    /**
     * @param $post_id
     * @param $category
     * @param $parent
     * @param string $description
     */
    public function setProductCategory($post_id, $category, $parent, $description = '') {
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
     * @param int $post_id
     * @param array $photos
     * @param array $exist_gallery
     */
    public function updateProductGallery(int $post_id, array $photos = [], $exist_gallery = []) {
        $ids = [];

        foreach($photos as $photo) {
            if(in_array($photo['name'], $exist_gallery)) {
                $ids[] = array_search($photo['name'], $exist_gallery);
            } else {
                $attach_id = $this->attachmentUpdater->setAttachment($post_id, $photo['url'], $photo['name']);

                if($attach_id) {
                    $ids[] = $attach_id;
                }
            }
        }

        add_post_meta($post_id, '_product_image_gallery', implode(',', $ids));
    }

    /**
     * @param $post_id
     * @param $thumb
     * @param null $exist_thumb
     */
    public function updateProductThumbnail($post_id, $thumb, $exist_thumb = null) {
        if (count($thumb) > 0 && $exist_thumb !== $thumb['name']) {
            $attach_id = $this->attachmentUpdater->setAttachment($post_id, $thumb['url'], $thumb['name']);
            set_post_thumbnail( $post_id, $attach_id );
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
       
        if ($product_calculate_price_method != 2) {
            update_post_meta( $post_id, '_stock', $quantity);
        
            if ($quantity == 0) {
                update_post_meta( $post_id, '_stock_status', 'outofstock');

            } else {
                update_post_meta( $post_id, '_stock_status', 'instock');
            } 
        }
    }

    /**
     *
     * @param type $post_id
     * @param type $field_name
     * @param type $default
     * @return type
     */
    private function get_custom_field($post_id, $field_name, $default)
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