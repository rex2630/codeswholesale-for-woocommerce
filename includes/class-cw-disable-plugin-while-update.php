<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 2014-12-17
 * Time: 09:49
 */
if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!class_exists('CW_Disable_Plugin_While_Update')) :

    class CW_Disable_Plugin_While_Update
    {
        public function __construct()
        {
            if (!function_exists('is_plugin_inactive')) {
                require_once(ABSPATH . '/wp-admin/includes/plugin.php');
            }

            if (is_plugin_inactive('woocommerce/woocommerce.php')) {
                deactivate_plugins(plugin_basename('codeswholesale-woocommerce/codeswholesale.php'));
            }

        }
    }
endif;

new CW_Disable_Plugin_While_Update();