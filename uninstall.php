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

delete_option( "cw_options" );
delete_option( "codeswholesale_params" );
delete_option( "_codeswholesale_auto_complete" );
delete_option( "_codeswholesale_notify_balance_value" );
