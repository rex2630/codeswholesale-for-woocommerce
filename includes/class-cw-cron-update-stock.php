<?php

use CodesWholesale\Resource\Product;
use CodesWholesale\Resource\ImageType;
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
			
			$cw_products_names_array = array();

			$cw_product_id = array();

			$cw_product_ids = array();
			
			foreach ($cw_products as $cw_product) {
				$cw_products_names_array[] =  $cw_product->getIdentifier();
			}

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
				$cw_product_ids[] = get_post_meta($product->ID, CodesWholesaleConst::PRODUCT_CODESWHOLESALE_ID_PROP_NAME, true);
				$products_ids[$cw_product_id] = $product;
            }
			
			$cw_products_not_set = array_diff($cw_products_names_array, $cw_product_ids);
			$cw_products_no_more = array_diff($cw_product_ids, $cw_products_names_array);

			sort($cw_products);
			
			foreach ($cw_products as $cw_product) {
				echo $cw_product->getName() . "\n";
				if (!isset($products_ids[$cw_product->getProductId()])) {
				
					echo "Adding Product... \n";

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

				}

				echo "Updateing Product... \n";

				$post_product = $products_ids[$cw_product->getProductId()];

				if ($post_product == null) {
					$post_product->ID = $post_id;
				}
				
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

				$category_id = array();

				$Platform = get_term_by('name', 'Platform', 'product_cat');
				$Region = get_term_by('name', 'Region', 'product_cat');
				$Language = get_term_by('name', 'Language', 'product_cat');

				$getRegions = $cw_product->getRegions();
				$getLanguages = $cw_product->getLanguages();

				$Platforms = get_term_by('name', str_replace(' ', '-', $cw_product->getPlatform()), 'product_cat');

				
				if ( empty( $Platform ) ) {
					wp_insert_term( 'Platform', 'product_cat', array( 'slug' => 'platform'));
				}

				if ( empty( $Platforms ) ) {
					wp_insert_term( $cw_product->getPlatform(), 'product_cat', array( 'slug' => $Platforms, 'parent'=> term_exists( 'Platform', 'product_cat' )['term_id'] ));
				}					
				$category_id[0] = $Platforms->term_id;
				
				if ( empty( $Region ) ) {
					wp_insert_term( 'Region', 'product_cat', array( 'slug' => 'region'));
				}

				foreach ($getRegions as $getRegion) {
					$Regions  = get_term_by('slug', str_replace(' ', '-', $getRegion), 'product_cat');
					if ( empty( $Regions ) ) {
						wp_insert_term(  $getRegion, 'product_cat', array( 'slug' => $getRegion, 'parent'=> term_exists( 'Region', 'product_cat' )['term_id'] ));
						$Regions  = get_term_by('slug', str_replace(' ', '-', $getRegion), 'product_cat');
					}
					$category_id[1] = $Regions->term_id;
					echo $getRegion;
				}
				
				if ( empty( $Language ) ) {
					wp_insert_term( 'Language', 'product_cat', array( 'slug' => 'language'));
				}

				foreach ($getLanguages as $getLanguage) {
					$Languages = get_term_by('slug', str_replace(' ', '-', $getLanguage), 'product_cat');
					if ( empty( $Languages ) ) {
						wp_insert_term(  $getLanguage, 'product_cat', array( 'slug' => $getLanguage, 'parent'=> term_exists( 'Language', 'product_cat' )['term_id'] ));
						$Languages = get_term_by('slug', str_replace(' ', '-', $getLanguage), 'product_cat');
					}
					$category_id[2] = $Languages->term_id;
					echo  $getLanguage;
				}

				$term_lists = wp_get_post_terms( $post_product->ID, 'product_cat', array( 'fields' => 'ids' ) );
				
				//echo  $cw_product->getName() . "\n";					
				$category_ids_ = array_merge($category_id, $term_lists);
				
				wp_set_post_terms($post_product->ID, array_unique($category_ids_), "product_cat");
				update_post_meta($post_product->ID, "product_cat", array_unique($category_ids_));
				
				update_post_meta($post_product->ID, "post_title", htmlspecialchars(esc_attr($cw_product->getName())));

				// Returns Array of Term Names for "my_taxonomy".
				$term_list = wp_get_post_terms( $post_product->ID, 'product_cat', array( 'fields' => 'names' ) );
				print_r( $term_list );

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

				if ( !has_post_thumbnail($post_product->ID) ) {
					$cw_img_url = $cw_product->getImageUrl(ImageType::MEDIUM);
					//update_post_meta( $post_product->ID, "_cw_image_url", esc_attr($cw_img));
					
					if( !strpos($cw_img_url, "no-image") ) {													
						// Add Featured Image to Post
						$upload_dir = wp_upload_dir(); // Set upload folder
						$image_data = file_get_contents($cw_img_url); // Get image data
						$filename   = basename($cw_product->getProductId() . ".png"); // Create image file name

						// Check folder permission and define file location
						if( wp_mkdir_p( $upload_dir['path'] ) ) {
							$file = $upload_dir['path'] . '/' . $filename;
						} else {
							$file = $upload_dir['basedir'] . '/' . $filename;
						}

						// Create the image  file on the server
						file_put_contents( $file, $image_data );

						// Check image file type
						$wp_filetype = wp_check_filetype( $filename, null );

						// Set attachment data
						$attachment = array(
							'post_mime_type' => $wp_filetype['type'],
							'post_title'     => sanitize_file_name( $cw_product->getName() ),
							'post_content'   => '',
							'post_author'   => '1',
							'post_status'    => 'publish'
						);

						// Create the attachment
						$attach_id = wp_insert_attachment( $attachment, $file, $post_product->ID );

						// Include image.php
						require_once(ABSPATH . 'wp-admin/includes/image.php');

						// Define attachment metadata
						$attach_data = wp_generate_attachment_metadata( $attach_id, $file );

						// Assign metadata to attachment
						wp_update_attachment_metadata( $attach_id, $attach_data );

						// And finally assign featured image to post
						set_post_thumbnail( $post_product->ID, $attach_id );
						
						echo $cw_img_url . "\n";
					}
				}	
			}
			
			echo "Stock updated. \n";
        }
    }

endif;

new CW_Cron_Update_Stock();
