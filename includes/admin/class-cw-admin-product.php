<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

use CodesWholesaleFramework\Provider\PriceProvider;
use CodesWholesale\Client;

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

            // ajax actions
            add_action( 'wp_ajax_get_calculated_price', array($this, 'get_calculated_price'));
        }

        /**
         *
         */
        public function get_calculated_price() {
            $priceProvider = new PriceProvider(new WP_DbManager());

            try {
                $price = $priceProvider->getCalculatedPrice( $_POST['spread_type'], $_POST['spread_value'], $_POST['stock_price'], $_POST['product_price_charmer'], $_POST['currency']);
            } catch (\Exception $ex) {
                $price = null;
            }

            // ajax return
            echo json_encode($price);

            wp_die();
        }

        /**
         *
         */
        public function output_custom_fields()
        {
            global $post;
            
            $options_array = CW()->get_options();
            
            $client = CW()->get_codes_wholesale_client();

            try {
                $external_products = $client->getProducts();

            } catch (\Error $e) {
                if (!CW()->get_codes_wholesale_client() instanceof Client) {
                    $error = new \Exception('Unauthorized!');
                } else {
                    $error = $e;
                }

                $external_products = [];
            }

            $global_spread_type = $options_array['spread_type'];
            $global_spread_value = $options_array['spread_value'];
            
            $product_stock_price = $this->get_custom_field($post->ID, CodesWholesaleConst::PRODUCT_STOCK_PRICE_PROP_NAME, 0);
            $product_spread_type = $this->get_custom_field($post->ID, CodesWholesaleConst::PRODUCT_SPREAD_TYPE_PROP_NAME, 0);
            $product_spread_value = $this->get_custom_field($post->ID, CodesWholesaleConst::PRODUCT_SPREAD_VALUE_PROP_NAME, 0);
            $product_calculate_price_method = $this->get_custom_field($post->ID, CodesWholesaleConst::PRODUCT_CALCULATE_PRICE_METHOD_PROP_NAME, (empty($global_spread_value) || strlen($global_spread_value) == 0)? 1 : 0);
            
            $product_item_options = array(__('---- CHOOSE ONE ----', 'woocommerce'));

            foreach ($external_products as $prod) {
                $product_item_options[$prod->getProductId()] = $prod->getName() . " - " . $prod->getPlatform() . " - " . $prod->getStockQuantity() . " - €" . number_format($prod->getLowestPrice(), 2, '.', '');
            }

            echo '<div class="options_group">';

            // Text Field
            // Select
            woocommerce_wp_select(
                array(
                    'id' => CodesWholesaleConst::PRODUCT_CODESWHOLESALE_ID_PROP_NAME,
                    'label' => __('Select a product from CodesWholesale', 'woocommerce'),
                    'options' => $product_item_options
                )
            );
 
            $calculate_price_methods = array(
                 __('Global profit margin', 'woocommerce'),
                 __('Custom profit margin', 'woocommerce'),
                 __('Custom price and stock', 'woocommerce')
            );

            $spread_types = array(
                __('Amount', 'woocommerce'),
                __('Percentage', 'woocommerce'),
            );
                                
            // Text Field //
            // Select //
            woocommerce_wp_select(
                array(
                    'id' => CodesWholesaleConst::PRODUCT_CALCULATE_PRICE_METHOD_PROP_NAME,
                    'label' => __('Price and stock settings:', 'woocommerce'),
                    'options' => $calculate_price_methods,
                    'value' => $product_calculate_price_method,
                    'description' => __('Choose how you want to set price for this product.', 'woocommerce')
                )
            );
            
            // Text Field //
            woocommerce_wp_text_input(
                array(
                    'id' => CodesWholesaleConst::PRODUCT_STOCK_PRICE_PROP_NAME,
                    'label' => __('CodesWholesale price (EUR):', 'woocommerce'),
                    'value' => $product_stock_price,
                    'class' => 'readonly',
                    'custom_attributes'=> array('readonly'=> 'readonly'),
                )
            );

            // Radio //
            woocommerce_wp_radio(
                array(
                    'id' => CodesWholesaleConst::PRODUCT_SPREAD_TYPE_PROP_NAME,
                    'label' => __('Profit margin type:', 'woocommerce'),
                    'options' => $spread_types,
                    'value' => $product_spread_type,
                )
            );
            
            // Text Field //
            woocommerce_wp_text_input(
                array(
                    'id' => CodesWholesaleConst::PRODUCT_SPREAD_VALUE_PROP_NAME,
                    'label' => __('Profit margin value:', 'woocommerce'),
                    'value' => $product_spread_value,
                    'type' => 'number',
                    'custom_attributes'=> array('min'=> '0')
                )
            );

            foreach ($external_products as $prod) {
                echo '<span style="display:none;" id="product-' . $prod->getProductId() . '" data-price="' . $prod->getLowestPrice() . '" data-stock="' . $prod->getStockQuantity() . '"></span>';
            }

            ?>

            <script type="text/javascript">
                var spreadType = 'input[name=<?php echo CodesWholesaleConst::PRODUCT_SPREAD_TYPE_PROP_NAME; ?>]';
                var spreadValue = '#<?php echo CodesWholesaleConst::PRODUCT_SPREAD_VALUE_PROP_NAME; ?>';

                jQuery(spreadType).change(function() {
                    setStepToSpreadValue(jQuery(this).val());
                });
                jQuery( document ).ready(function() {
                    setStepToSpreadValue(jQuery(spreadType+":checked").val());
                });

                function setStepToSpreadValue(selected) {
                    if("0" === selected) {
                        jQuery(spreadValue).attr( "step", "any" );
                    } else {
                        jQuery(spreadValue).attr( "step", "1" );
                    }      
                }
    
                var global_spread_value = '<?php echo $global_spread_value; ?>';
                 
                if (global_spread_value === 0 || !global_spread_value || global_spread_value === '') {
                    jQuery('#_regular_price').val('');
                    jQuery('#<?php echo CodesWholesaleConst::PRODUCT_CALCULATE_PRICE_METHOD_PROP_NAME; ?> option').first().attr("disabled", "disabled");
                }

                jQuery(document).ready(function () {
                    handleChangeCalculatePriceMethod();
                    
                    jQuery("#<?php echo CodesWholesaleConst::PRODUCT_CALCULATE_PRICE_METHOD_PROP_NAME; ?>").change(function () {
                        setProductPrice();
                        handleChangeCalculatePriceMethod();

                    });

                    jQuery("#<?php echo CodesWholesaleConst::PRODUCT_CODESWHOLESALE_ID_PROP_NAME; ?>").change(function () {
                        setProductPrice();
                    });
                    
                    jQuery("input[name=<?php echo CodesWholesaleConst::PRODUCT_SPREAD_TYPE_PROP_NAME; ?>]").change(function () {
                        setProductPrice();
                    });
                    
                    jQuery("#<?php echo CodesWholesaleConst::PRODUCT_SPREAD_VALUE_PROP_NAME; ?>").change(function () {
                        if(null === jQuery(this).val() || '' === jQuery(this).val() || parseFloat(jQuery(this).val()) < 0) {
                            jQuery(this).val('0');
                        }
                        
                        setProductPrice();
                    });
                    
                    function handleChangeCalculatePriceMethod() {
                        var selected_method = jQuery("#<?php echo CodesWholesaleConst::PRODUCT_CALCULATE_PRICE_METHOD_PROP_NAME; ?> option:selected").val();
                    
                        switch(selected_method) {
                            case '0':
                                setVisibilityProductSpreadFields(false);
                                break;
                            case '1':
                                setVisibilityProductSpreadFields(true);
                                break;
                            case '2':
                                setVisibilityProductSpreadFields(false);
                                break;    
                        }
                    }
                    
                    function setVisibilityProductSpreadFields(switchVisibility) {
                        if(switchVisibility) {
                            jQuery('.<?php echo CodesWholesaleConst::PRODUCT_SPREAD_TYPE_PROP_NAME; ?>_field').show();
                            jQuery('.<?php echo CodesWholesaleConst::PRODUCT_SPREAD_VALUE_PROP_NAME; ?>_field').show();
                        } else {
                            jQuery('.<?php echo CodesWholesaleConst::PRODUCT_SPREAD_TYPE_PROP_NAME; ?>_field').hide();
                            jQuery('.<?php echo CodesWholesaleConst::PRODUCT_SPREAD_VALUE_PROP_NAME; ?>_field').hide();
                        }   
                    }
                    
                    function setProductPrice() {
                        var selected_product = jQuery('#<?php echo CodesWholesaleConst::PRODUCT_CODESWHOLESALE_ID_PROP_NAME; ?> option:selected').val();
                        
                        if('0' === selected_product) return;
                        
                        var spread = getSpreadData();
                        var product = getExternalProductData(selected_product);

                        updateInternalProductStock(product);

                        jQuery.post(ajaxurl, {
                            'action': 'get_calculated_price',
                            'spread_type': spread.type,
                            'spread_value': spread.value,
                            'stock_price': product.price,
                            'product_price_charmer': '<?php echo $options_array['product_price_charmer'];?>',
                            'currency': '<?php echo $options_array['currency'];?>'
                            }, function(response) {
                                if(response && response !== 'null') {
                                    jQuery("#_regular_price").val(parseFloat(response).toFixed(2));
                                } else {
                                    alert('The price can not be converted');
                                }
                            }
                        );


                        jQuery("#<?php echo CodesWholesaleConst::PRODUCT_STOCK_PRICE_PROP_NAME; ?>").val(product.price.toFixed(2));
                        
                        if (!jQuery("#_virtual").prop('checked')) {
                            jQuery("#_virtual").click();
                        }                       
                    };
                    
                    
                    function updateInternalProductStock(product) {
                        var selected_method = jQuery("#<?php echo CodesWholesaleConst::PRODUCT_CALCULATE_PRICE_METHOD_PROP_NAME; ?> option:selected").val();
                    
                        switch(selected_method) {
                            case '0':
							case '1':
                                jQuery("#_stock").val(product.stock);
                                
                                if (product.stock > 0) {
                                    jQuery("#_stock_status").val("instock");
                                } else {
                                    jQuery("#_stock_status").val("outofstock");
                                }
                        
                                break;
                            case '2':
                                jQuery("#_stock").val(1); 
                                jQuery("#_stock_status").val("instock");
                                
                                break;
                        }
                        
                        if (!jQuery("#_manage_stock").prop('checked')) {
                            jQuery("#_manage_stock").click();
                        }
                    }
                    
                    function getSpreadData() {
                        var selected_method = jQuery("#<?php echo CodesWholesaleConst::PRODUCT_CALCULATE_PRICE_METHOD_PROP_NAME; ?> option:selected").val();
                        var spread_type = 0;
                        var spread_value = 0;
                        
                        switch(selected_method) {
                            case '0':
                                spread_type = '<?php echo $global_spread_type; ?>';
                                spread_value = '<?php echo $global_spread_value; ?>';
                                break;
                            case '1':                                
                                spread_type = jQuery("input[name=<?php echo CodesWholesaleConst::PRODUCT_SPREAD_TYPE_PROP_NAME; ?>]:checked").val();
                                spread_value = jQuery("#<?php echo CodesWholesaleConst::PRODUCT_SPREAD_VALUE_PROP_NAME; ?>").val();
                                break;
                        }
                        
                        return {
                            type : spread_type,
                            value : spread_value
                        };
                    }
                    
                    function getExternalProductData(selected_product) {
                        var price = jQuery("#product-" + selected_product).data("price");
                        var stock = jQuery("#product-" + selected_product).data("stock");
                        
                        return {
                            price : price,
                            stock : stock,
                        }; 
                    }
                   
                });
            </script>

            <?php
            echo '</div>';
        }

        /**
         * Save custom fields to db.
         *
         * @param $post_id
         */
        public function save_custom_fields($post_id)
        {
            update_post_meta($post_id, CodesWholesaleConst::PRODUCT_CODESWHOLESALE_ID_PROP_NAME, esc_attr($_POST[CodesWholesaleConst::PRODUCT_CODESWHOLESALE_ID_PROP_NAME]));
            update_post_meta($post_id, CodesWholesaleConst::PRODUCT_CALCULATE_PRICE_METHOD_PROP_NAME, esc_attr($_POST[CodesWholesaleConst::PRODUCT_CALCULATE_PRICE_METHOD_PROP_NAME]));
            update_post_meta($post_id, CodesWholesaleConst::PRODUCT_STOCK_PRICE_PROP_NAME, esc_attr($_POST[CodesWholesaleConst::PRODUCT_STOCK_PRICE_PROP_NAME]));
            update_post_meta($post_id, CodesWholesaleConst::PRODUCT_SPREAD_TYPE_PROP_NAME, esc_attr($_POST[CodesWholesaleConst::PRODUCT_SPREAD_TYPE_PROP_NAME]));
            update_post_meta($post_id, CodesWholesaleConst::PRODUCT_SPREAD_VALUE_PROP_NAME, esc_attr($_POST[CodesWholesaleConst::PRODUCT_SPREAD_VALUE_PROP_NAME]));   
        }

        private function get_custom_field($post_id, $field_name, $default)
        {
            $value = null;
            
            if($post_id) {
                $value = get_post_meta($post_id, $field_name, true);
            }
            
            if(empty($value) || null == $value) {
                return $default;
            }
            
            return $value;
        }


    }

endif;

return new CW_Admin_Product();