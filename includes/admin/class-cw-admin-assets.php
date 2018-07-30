<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'CW_Admin_Assets' ) ) :

/**
 * WC_Admin_Assets Class
 */
class CW_Admin_Assets {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
	}

	/**
	 * Enqueue styles
	 */
	public function admin_styles() {
       //     wp_enqueue_style( 'codeswholesale_admin_menu_styles', CW()->plugin_url() . '/assets/css/menu.css', array(), CW_VERSION );
            wp_enqueue_style( 'cw-style', CW()->plugin_url() . '/vendor/codeswholesale/cw-extension-framework/src/Assets/css/style.css', array(), CW_VERSION );
            wp_enqueue_style( 'cw-custom-style', CW()->plugin_url() . '/assets/style.css', array(), CW_VERSION );
            wp_enqueue_style( 'font-awesome', 'https://use.fontawesome.com/releases/v5.1.0/css/all.css' );
        
            do_action( 'codeswholesale_admin_css' );
	}



}

endif;

return new CW_Admin_Assets();
