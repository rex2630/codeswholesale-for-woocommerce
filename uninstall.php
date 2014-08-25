<?php
/**
 * CodesWholesale Uninstall
 *
 * Uninstalling CodesWholesale options.
 *
 * @author 		WooThemes
 * @category 	Core
 * @package 	WooCommerce/Uninstaller
 * @version     2.1.0
 */

if( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) 
	exit();

delete_option( "codeswholesale_params" );