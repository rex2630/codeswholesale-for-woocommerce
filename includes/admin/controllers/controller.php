<?php
include_once(plugin_dir_path( __FILE__ ).'../generators/wp-form-element-generator.php');

abstract class CW_Controller {

        public $form_element_generator;
                
        public  function __construct() {
            $this->form_element_generator = new WP_Form_Element_Generator();
        }        
        /**
         *
         */
        public function section_one_callback()
        {
            // section one description
        }

        /*
         * Get plugin options set by user
         *
         * @access public
         * @return array
         */
        public function get_options()
        {
            return CW()->instance()->get_options();
        }
        
        /**
         * 
         * @return type
         */
        public function plugin_img() {
            return plugins_url().'/codeswholesale-for-woocommerce/assets/images/codeswholesale.png';
        }
}

