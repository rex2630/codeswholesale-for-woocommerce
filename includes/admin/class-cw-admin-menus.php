<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!class_exists('CW_Admin_Menus')) :

    include_once('controllers/controller-settings.php');
    include_once('controllers/imports/controller-import-products.php');
    include_once('controllers/imports/controller-postback-import-products.php');
    include_once('controllers/controller-check-orders.php');
    
    /**
     * CW_Admin_Menus Class
     */
    class CW_Admin_Menus
    {
        protected $view;
        
        /**
         * Hook in tabs.
         */
        public function __construct()
        {
            /**
             * For admin only
             */
            if (is_admin()) {
                // General plugin setup
                add_action('admin_menu', array($this, 'add_admin_menu'));
            }
            
            $this->view  = new stdClass();
            $this->view->import = new CW_Controller_Import_products();
            $this->view->postback_import = new CW_Controller_Postback_Import_products();
        }

        /**
         * Add menu items
         */
        public function add_admin_menu()
        {
            add_menu_page('CodesWholesale', 'CodesWholesale', 'manage_options', 'codeswholesale', array($this, 'set_up_admin_page'), 'dashicons-admin-codeswholesale', 30);
            add_submenu_page( 'codeswholesale', 'Settings', 'Settings', 'manage_options', 'codeswholesale', array($this, 'set_up_admin_page'));
            add_submenu_page( 'codeswholesale', 'Order History', 'Order History', 'manage_options', 'cw-check-orders', array($this, 'check_orders'));
            // add_submenu_page( 'codeswholesale', 'Import', 'Import', 'manage_options', 'cw-import-products', array($this, 'import_products'));
            add_submenu_page( 'codeswholesale', 'Import', 'Import', 'manage_options', 'cw-postback-import-products', array($this, 'postback_import_products'));
            add_submenu_page( 'codeswholesale', 'Emails', 'Emails', 'manage_options', 'edit.php?post_type=codeswholesale_email');
        }
        
        /**
         * Set up admin form menu
         */
        public function set_up_admin_page()
        {
            $view = new CW_Controller_Settings();
            $view ->init_view();
        }
        
        /**
         * 
         */
        public function import_products() 
        {
            $this->view->import->initView();
        }

        /**
         *
         */
        public function postback_import_products()
        {
            $this->view->postback_import->initView();;
        }

        /**
         *
         */
        public function check_orders()
        {
            $view = new CW_Controller_Check_orders();
            $view ->init_view();
        }
    }

endif;

return new CW_Admin_Menus();