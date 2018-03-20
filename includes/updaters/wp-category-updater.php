<?php


/**
 * Class WP_Category_Updater
 */
class WP_Category_Updater
{
    const TAXONOMY_SLUG = 'product_cat';
    
    const CATEGORY_SLUG_PLATFORM = 'platform';
    const CATEGORY_NAME_PLATFORM = 'Platform';
    
    const CATEGORY_SLUG_DEVELOPER = 'developer';
    const CATEGORY_NAME_DEVELOPER = 'Developer';
    
    const CATEGORY_SLUG_PEGI = 'pegi';
    const CATEGORY_NAME_PEGI = 'PEGI';
    
    const CATEGORY_SLUG_CATEGORY = 'genre';
    const CATEGORY_NAME_CATEGORY = 'GENRE';
    
     
    public function getTermIdForce($term_slug, $parent_slug, $description = '') {
        $term   = term_exists( $this->toSlug($term_slug), self::TAXONOMY_SLUG );
        
        if ($term['term_id']) {
           return $term['term_id'];
        } else {
            $parent = term_exists( $this->toSlug($parent_slug), self::TAXONOMY_SLUG );
            
            if (! $parent['term_id']) {
                return $this->insertTerm($term_slug, $this->insertTerm($parent_slug));
            } else {
                return $this->insertTerm($term_slug, $parent['term_id'], $description);
            }
        }
    }
    
    public function insertTerm($slug, $parent_id = null, $description = '') {        
        $name = $this->getTermNameBySlug($slug);
        
        $args = [
            'description'=> $description,
            'slug' => $this->toSlug($slug),
            'parent'=> $parent_id  // get numeric term id
        ];
        
        $data = wp_insert_term( $name, self::TAXONOMY_SLUG, $args );
        
        return $data['term_id'];
    }
    
    public function getTermNameBySlug($slug) {
        $name = '';
        
        switch($slug) {
            case self::CATEGORY_SLUG_PLATFORM:
                $name = self::CATEGORY_NAME_PLATFORM;
                break;
            case self::CATEGORY_SLUG_DEVELOPER:
                $name = self::CATEGORY_NAME_DEVELOPER;
                break;
            case self::CATEGORY_SLUG_PEGI:
                $name = self::CATEGORY_NAME_PEGI;
                break;
            case self::CATEGORY_SLUG_CATEGORY:
                $name = self::CATEGORY_NAME_CATEGORY;
                break;
            default:
                $name = $slug;
        }
        
        return $name;
    }
    
    public function toSlug($text) {
        $slug = trim($text);
        $slug = strtolower($slug);
        $slug = str_replace(' ', '_', $slug);
        
        return $slug;
    }
}
