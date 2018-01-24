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
		wp_enqueue_style( 'codeswholesale_admin_menu_styles', CW()->plugin_url() . '/assets/css/menu.css', array(), CW_VERSION );

        do_action( 'codeswholesale_admin_css' );
	}



}

endif;

return new CW_Admin_Assets();
