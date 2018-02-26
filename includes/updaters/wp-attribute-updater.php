<?php


/**
 * Class WP_Attribute_Updater
 */
class WP_Attribute_Updater
{
    const ATTR_EXTENSION_PACK   = 'Extension packs';
    const ATTR_RELEASES         = 'Releases';
    const ATTR_EANS             = 'Eans';

    public function __construct() {
        $this->addAttribute(self::ATTR_EXTENSION_PACK);
        $this->addAttribute(self::ATTR_RELEASES);
        $this->addAttribute(self::ATTR_EANS);
    }

    public function addAttribute($name) {
        $args = array(
                'name'         => $name,
                'type'         => 'text',
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
}
