<?php

use CodesWholesale\Resource\Product;
use CodesWholesale\Resource\FullProduct;
use CodesWholesale\Resource\StockAndPriceChange;
use CodesWholesaleFramework\Model\ProductModel;
use CodesWholesaleFramework\Factories\ProductModelFactory;
use CodesWholesaleFramework\Postback\UpdateProduct\UpdateProductInterface;

if (!class_exists('CW_Update_Stock')) :

    class CW_Cron_Update_Stock extends CW_Cron_Job
    {
        
        public function __construct()
        {
			parent::__construct("codeswholesale_update_stock_action");
			            
		}
		
		public function calculateSpread(array $spreadParams, $price)
		{
			if ($spreadParams['cwSpreadType'] == 0) {
	
				$priceSpread = $price + $spreadParams['cwSpread'];
	
			} else if ($spreadParams['cwSpreadType'] == 1) {
	
				$result = $price / 100 * $spreadParams['cwSpread'] + $price;
				$priceSpread = round($result, 2);
			}
	
			return $priceSpread;
		}

		public function getSpreadParams() {

			$options = CW()->instance()->get_options();
			$spread_type = $options['spread_type'];
			$spread_value = $options['spread_value'];
	
			$spread_params = array(
				'cwSpreadType' => $spread_type,
				'cwSpread' => $spread_value
			);
	
			return $spread_params;
		}

        public function cron_job()
        {
            $products_ids = array();

			$cw_products = CW()->get_codes_wholesale_client()->getProducts();

			$products = get_posts(array(
                'post_type' => 'product',
                'meta_query' => array(
                    array(
                        'key' => CodesWholesaleConst::PRODUCT_CODESWHOLESALE_ID_PROP_NAME,
                        'value' => '',
                        'compare' => '!='
                    )
                ),
                'numberposts' => -1
            ));
			
			foreach ($products as $product) {
                $cw_product_id = get_post_meta($product->ID, CodesWholesaleConst::PRODUCT_CODESWHOLESALE_ID_PROP_NAME, true);
                $products_ids[$cw_product_id] = $product;
            }
						
			$args = get_posts(array(
				'post_type' => 'product',
				'meta_query' => array(
					array(
						'key' => CodesWholesaleConst::PRODUCT_CODESWHOLESALE_ID_PROP_NAME,
						'compare' => '!=',
						'value' => ''
					)
				),
				'numberposts' => -1
			));

			foreach ($args as $post) { 
				$produits[] = get_post_meta($post->ID, CodesWholesaleConst::PRODUCT_CODESWHOLESALE_ID_PROP_NAME, true);
			}

			foreach ($cw_products as $cw_product) {
				$cw_produits[] = $cw_product->getProductId();
			}

			$produits_diff = array_diff($cw_produits, $produits);

			foreach ($cw_products as $cw_product) {
				if (in_array($cw_product->getProductId(), $produits_diff)) {

					echo "Adding Product... \n";
					echo $cw_product->getName() . "\n";

					if ( $cw_product->getReleaseDate() == '' ) {
						$post = array(
							'post_author' => '1',
							'post_status' => "publish",
							'post_name' => esc_attr($cw_product->getIdentifier()),
							'post_title' => esc_attr($cw_product->getName()),
							'post_type' => "product",
							'post_date'     => "1950-01-01T00:00:00.000Z",
							'post_date_gmt' => get_gmt_from_date( "1950-01-01T00:00:00.000Z" )
						);
					} else {
						$post = array(
							'post_author' => '1',
							'post_status' => "publish",
							'post_name' => esc_attr($cw_product->getIdentifier()),
							'post_title' => esc_attr($cw_product->getName()),
							'post_type' => "product",
							'post_date'     => $cw_product->getReleaseDate(),
							'post_date_gmt' => get_gmt_from_date( $cw_product->getReleaseDate() )
						);
					}

					//Create post
					$post_id = wp_insert_post( $post, $wp_error );
					
					//Add Additional Information
					wp_set_object_terms($post_id, 'simple', 'product_type');
					update_post_meta( $post_id, '_visibility', 'visible' );
					update_post_meta( $post_id, '_stock_status', 'outofstock');
					update_post_meta( $post_id, 'total_sales', '0');
					update_post_meta( $post_id, '_downloadable', 'no');
					update_post_meta( $post_id, '_virtual', 'yes');
					update_post_meta( $post_id, '_featured', "no" );
					update_post_meta( $post_id, '_sku', htmlspecialchars_decode($cw_product->getIdentifier()));
					update_post_meta( $post_id, '_product_attributes', array());
					update_post_meta( $post_id, '_manage_stock', "yes" );
					update_post_meta( $post_id, "_stock", esc_attr($cw_product->getStockQuantity())); 
					update_post_meta( $post_id, '_codeswholesale_product_id', $cw_product->getProductId() );
					update_post_meta( $post_id, '_codeswholesale_product_spread_type', "0" );
					update_post_meta( $post_id, '_codeswholesale_product_is_in_sale', "0" );

				} else {
					echo "Updateing Product... \n";
				}
			
                if (isset($products_ids[$cw_product->getProductId()])) {

					echo $cw_product->getName() . "\n";	

                    $post_product = $products_ids[$cw_product->getProductId()];
                    
                    $price = $cw_product->getDefaultPrice();
				
					$priceSpread = $this->calculateSpread($this->getSpreadParams(), $price);
                    
                    $product_spread_type =  get_post_meta($post_product->ID, "_codeswholesale_product_spread_type", true);
					
					$product_is_in_sale =  get_post_meta($post_product->ID, "_codeswholesale_product_is_in_sale", true);
																			
					if ($cw_product->getStockQuantity() > 0) {
						update_post_meta($post_product->ID, "_stock_status", "instock");
						update_post_meta($post_product->ID, '_manage_stock', "yes" );
						update_post_meta($post_product->ID, "_stock", esc_attr($cw_product->getStockQuantity())); 
					}
						
					if ($product_spread_type == 0) {
						if ($cw_product->getDefaultPrice() > 0) {
							if ($product_is_in_sale == 0) {
								update_post_meta($post_product->ID, "_regular_price", esc_attr(round($priceSpread , 2)));
								update_post_meta($post_product->ID, "_price", esc_attr(round($priceSpread , 2)));
							} else {
								update_post_meta($post_product->ID, "_sale_price", esc_attr(round($priceSpread , 2)));
								update_post_meta($post_product->ID, "_price", esc_attr(round($priceSpread , 2)));
							}
							update_post_meta($post_product->ID, "_virtual", "yes");
						} else {
							update_post_meta($post_product->ID, "_regular_price", esc_attr(0));
							update_post_meta($post_product->ID, "_price", esc_attr(0));
						}
					}
				
				}

				$cw_format_date = strtotime(str_replace( array('T',':00.000Z'), ' ', $cw_product->getReleaseDate() )); 
				update_post_meta( $post_product->ID, "_wc_pre_orders_availability_datetime", $cw_format_date );
				$availability_timestamp_in_utc = (int) get_post_meta( $post_product->ID, "_wc_pre_orders_availability_datetime", true );

				// if the availability date has passed
				if ( $availability_timestamp_in_utc > strtotime("now") ) {
						update_post_meta($post_product->ID, "_wc_pre_orders_enabled", "yes");
						update_post_meta($post_product->ID, "_wc_pre_orders_when_to_charge", "upfront");
						if ($cw_product->getStockQuantity() == 0) {
							update_post_meta($post_product->ID, "_stock", esc_attr(0));
							update_post_meta( $post_product->ID, "_stock_status", "onbackorder");
							update_post_meta( $post_product->ID, "_backorders", "notify");								
						}
				} else {
					update_post_meta($post_product->ID, "_wc_pre_orders_enabled", "no");
					update_post_meta($post_product->ID, "_wc_pre_orders_when_to_charge", "");
					update_post_meta( $post_product->ID, "_backorders", "no");	
					if ($cw_product->getStockQuantity() == 0) {
						update_post_meta($post_product->ID, "_stock_status", "outofstock");
						update_post_meta($post_product->ID, "_stock", esc_attr(0));
					}
					if ( $cw_product->getReleaseDate() == '' ) {
						wp_update_post(
							array (
								'ID'            => $post_product->ID, // ID of the post to update
								'post_status' 	=> "publish",
								'post_date'     => "1950-01-01T00:00:00.000Z",
								'post_date_gmt' => get_gmt_from_date( "1950-01-01T00:00:00.000Z" )
							)
						);
					} else {
						wp_update_post(
							array (
								'ID'            => $post_product->ID, // ID of the post to update
								'post_status' 	=> "publish",
								'post_date'     => $cw_product->getReleaseDate(),
								'post_date_gmt' => get_gmt_from_date( $cw_product->getReleaseDate() )
							)
						);
					}
				}

			}

			foreach ($cw_products as $cw_product) {
				/* Create category */
				$producModel = ProductModelFactory::resolveProduct($cw_product, CW()->get_options()[CodesWholesaleConst::PREFERRED_LANGUAGE_FOR_PRODUCT_OPTION_NAME]);
				WP_Product_Updater::getInstance()->updateProduct->update($producModel, $post_product->ID);

				echo $cw_product->getName() . "\n";	
			}
			
			echo "Stock updated. \n";
        }
    }

endif;

new CW_Cron_Update_Stock();