<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly


if (!class_exists('CW_Admin_Product')) :
    /**
     * CW_Admin_Product Class
     */
    class CW_Admin_Product
    {
        /**
         * Hook into product.
         */
        public function __construct()
        {
            // Display Fields
            add_action('woocommerce_product_options_general_product_data', array($this, 'output_custom_fields'));
            add_action('woocommerce_process_product_meta', array($this, 'save_custom_fields'));
        }

        public function output_custom_fields()
        {

            $options = array();

            $prods = CW()->getCodesWholesaleClient()->getProducts();

            $options[] = "---- CHOOSE ONE ----";

            foreach ($prods as $prod) {
                $options[$prod->getProductId()] = $prod->getName() . " - " .
                    $prod->getPlatform() . " - €". number_format($prod->getDefaultPrice(), 2, '.', '');
            }

            echo '<div class="options_group">';

            // Text Field
            // Select
            woocommerce_wp_select(
                array(
                    'id' => CodesWholesaleConst::PRODUCT_CODESWHOLESALE_ID_PROP_NAME,
                    'label' => __('Select CodesWholesale product', 'woocommerce'),
                    'options' => $options
                )
            );


            echo '</div>';

        }

        /**
         * Save custom fields to db.
         *
         * @param $post_id
         */
        function save_custom_fields($post_id)
        {
            $woocommerce_select = $_POST[CodesWholesaleConst::PRODUCT_CODESWHOLESALE_ID_PROP_NAME];
            if (!empty($woocommerce_select)) {
                update_post_meta($post_id, CodesWholesaleConst::PRODUCT_CODESWHOLESALE_ID_PROP_NAME, esc_attr($woocommerce_select));
            }
        }

    }

endif;

return new CW_Admin_Product();