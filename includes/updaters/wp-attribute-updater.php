<?php
use CodesWholesale\Resource\Product;

/**
 * Class WP_Attribute_Updater
 */
class WP_Attribute_Updater
{
    const ATTR_PLATFORM   = 'Platform';
    const ATTR_REGION     = 'Region';
    const ATTR_LANGUAGE   = 'Language';

    public function __construct() {
    }
    
    public function init() {
        $this->addAttribute(self::ATTR_PLATFORM);
        $this->addAttribute(self::ATTR_REGION);
        $this->addAttribute(self::ATTR_LANGUAGE);   
    }

    public function addAttribute($name) {
        $args = array(
                'name'         => $name,
                'type'         => 'select',
                'order_by'     => '',
                'has_archives' => 1,
        );

        $id = wc_create_attribute( $args ); 
        
        if ( is_wp_error( $id ) ) {
                return false;
        }

        return $id;
    }    
    
    public static function getSlug($name) {
        return 'pa_' . wc_sanitize_taxonomy_name($name);
    }
    
    public function insertAttributeTerm($value, $key) {
        if(is_array($value)) {
            foreach($value as $term) {
                wp_insert_term($term, wc_clean($key));
            }     
        } else {                
            wp_insert_term($value, wc_clean($key));

        }
    }
    
    public function localAttributes($extensionPacks, $eanCodes, $releases) {
        $attributes = [];
        
        $attributes['Extension packs'] = $extensionPacks;
        $attributes['Eans'] =  $eanCodes;

         if($releases) {
            $attributes['Releases'] = [];
                
            foreach($releases as $rel) {
                $attributes['Releases'][] = $rel->getTerritory() . ' - ' .  $rel->getStatus() . ' - ' . $rel->getDate();
            }
        }
        
        return $attributes;
    }
    public function globalAttributes($platform, $regions, $languages) {
        $attributes = [];

        $attributes[WP_Attribute_Updater::getSlug(WP_Attribute_Updater::ATTR_PLATFORM)] = $platform;
        
        if($regions) {
            $attributes[WP_Attribute_Updater::getSlug(WP_Attribute_Updater::ATTR_REGION)] = [];
                
            foreach($regions as $reg) {
                $attributes[WP_Attribute_Updater::getSlug(WP_Attribute_Updater::ATTR_REGION)][] = $reg;
            }
        }

        if($languages) {
            $attributes[WP_Attribute_Updater::getSlug(WP_Attribute_Updater::ATTR_LANGUAGE)] = [];
                
            foreach($languages as $lang) {
                $attributes[WP_Attribute_Updater::getSlug(WP_Attribute_Updater::ATTR_LANGUAGE)][] = $lang;
            }
        }
        
        return $attributes;  
    }
    
    public static function getInternalProductAttributes($post, $type) {
        $attr = [];
      
        $terms = get_the_terms( $post, self::getSlug($type));
        
        if($terms) {
          foreach ( $terms as $term ) {
              $attr[] = $term->name;
          }   
        }

        return $attr;
    }
}
