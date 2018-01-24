<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class CW_EmailCustomPostType {
    
    public function __construct() {        
         add_action( 'init', array( $this, 'init_email_custom_post_type' ) ); 
    }
    
    /**
     * Include any classes we need within emails.
     */
    public function init_email_custom_post_type() {
        // Post type
        $labels = $this->getCustomPostTypeLabels();
        $args   = $this->getCustomPostTypeArgs( $labels);

        register_post_type( CW_EmailsConst::CW_EMAIL_POST_TYPE, $args );
        
        // Taxonomy
        $tax_labels = $this->getTaxonomyLabels();
        $tax_args = $this->getTaxonomyArgs($tax_labels);
        
        register_taxonomy( CW_EmailsConst::CW_EMAIL_TAXONOMY_TYPE, CW_EmailsConst::CW_EMAIL_POST_TYPE, $tax_args );
        
        
        new WDS_Taxonomy_Radio( CW_EmailsConst::CW_EMAIL_TAXONOMY_TYPE );
            

        foreach (CW_EmailsConst::getDefaultEmailsKeys() as $key) { 
             $class = $key."_Default";
             $this->insertTermToCustomTaxonomy(new $class(), $key);
         }
         
        (new WP_CustomPostEmailsGenerator)->init();
    }
    
    private function insertTermToCustomTaxonomy(CW_DefaultEmailDataInterface $defaultEmailDataModel, string $key) {
        // Insert element to term
        wp_insert_term(
            $defaultEmailDataModel->getTitle(), // the term 
            CW_EmailsConst::CW_EMAIL_TAXONOMY_TYPE, // the taxonomy
            array(
              'description'=> $defaultEmailDataModel->getTitle(),
              'slug' => $key,
            )
          );  
         
    }
    
    private function getCustomPostTypeLabels() {
      	$labels = array(
            'name'                  => _x( 'Emails', 'Post Type General Name', 'woocommerce' ),
            'singular_name'         => _x( 'Email', 'Post Type Singular Name', 'woocommerce' ),
            'menu_name'             => __( 'Emails', 'woocommerce' ),
            'name_admin_bar'        => __( 'Email', 'woocommerce' ),
            'edit_item'             => __( 'Edit email', 'woocommerce' ),
	);
        
        return $labels;
    }
    
    private function getCustomPostTypeArgs($labels) {
        $args = array(
            'label'                 => __( 'Post Type', 'woocommerce' ),
            'description'           => __( 'Post Type Description', 'woocommerce' ),
            'labels'                => $labels,
            'supports'              => array( ),
            'taxonomies'            => array( CW_EmailsConst::CW_EMAIL_TAXONOMY_TYPE ),
            'hierarchical'          => false,
            'public'                => false,
            'show_ui'               => true,
            'show_in_menu'          => false,
            'menu_position'         => 5,
            'show_in_admin_bar'     => false,
            'show_in_nav_menus'     => false,
            'can_export'            => false,
            'has_archive'           => false,		
            'exclude_from_search'   => false,
            'publicly_queryable'    => false,
            'capability_type'       => 'post',
            'capabilities' => array(
                'create_posts' => 'do_not_allow', // false < WP 4.5, credit @Ewout
            ),
            'map_meta_cap' => true,
            'supports' => array( 
                'title', 
                'editor', 
                //'excerpt', 
                //'thumbnail', 
                'custom-fields', 
              )
	);
        
        return $args;
        
    }
    
    private function  getTaxonomyLabels() {
        $labels = array(
            'name'              => _x( 'Email Types', 'taxonomy general name', 'woocommerce' ),
            'singular_name'     => _x( 'Email Type', 'taxonomy singular name', 'woocommerce' ),
            'search_items'      => __( 'Search Types', 'woocommerce' ),
            'all_items'         => __( 'All Email Type', 'woocommerce' ),
            'menu_name'         => __( 'Email Type', 'woocommerce' ),
	);
        
        return $labels;
    }
    
    private function  getTaxonomyArgs($labels) {
        $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'public' => false,
            'capabilities' => array(
                'assign_terms'  => true,
                'manage_terms'  => false,
                'delete_terms'  => false,
                'edit_terms'    => false,
            ),
        );
        
        return $args;
    }
}

return new CW_EmailCustomPostType();