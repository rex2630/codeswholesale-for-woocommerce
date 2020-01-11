<?php
use CodesWholesale\Resource\ImageType;
use CodesWholesale\Resource\StockAndPriceChange;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!class_exists('CW_Update_Stock')) :

    class CW_Cron_Update_Stock extends CW_Cron_Job
    {
        
        /**
         *
         */
        public function __construct()
        {
            parent::__construct("codeswholesale_update_stock_action");
            
        }

        /**
         *
         */
        public function cron_job()
        {
            $products_ids = array();

            $cw_products = CW()->get_codes_wholesale_client()->getProducts();
			
			foreach ($cw_products as $cw_product) {
				$args = get_posts(array(
					'post_type' => 'product',
					'meta_query' => array(
						array(
							'key' => CodesWholesaleConst::PRODUCT_CODESWHOLESALE_ID_PROP_NAME,
							'value' => $cw_product->getProductId(),
							'compare' => '='
						)
					),
					'numberposts' => -1
				));

				if (count($args) == 0) {
					echo "Adding Product... \n";
					echo $cw_product->getName() . "\n";
					$post = array(
						'post_author' => '1',
						'post_content' => '',
						'post_status' => "publish",
						'post_name' => esc_attr($cw_product->getIdentifier()),
						'post_title' => esc_attr($cw_product->getName()),
						'post_parent' => '',
						'post_type' => "product",
					);

					//Create post
					$post_id = wp_insert_post( $post, $wp_error );
					if($post_id){
						$attach_id = get_post_meta($product->parent_id, "_thumbnail_id", true);
						add_post_meta($post_id, '_thumbnail_id', $attach_id);
					}

					wp_set_object_terms($post_id, 'simple', 'product_type');

					update_post_meta( $post_id, '_visibility', 'visible' );
					update_post_meta( $post_id, '_stock_status', 'outofstock');
					update_post_meta( $post_id, 'total_sales', '0');
					update_post_meta( $post_id, '_downloadable', 'no');
					update_post_meta( $post_id, '_virtual', 'yes');
					update_post_meta( $post_id, '_featured', "no" );
					update_post_meta( $post_id, '_sku', htmlspecialchars_decode($cw_product->getIdentifier()));
					update_post_meta( $post_id, '_product_attributes', array());
					update_post_meta( $post_id, '_sale_price_dates_from', "" );
					update_post_meta( $post_id, '_sale_price_dates_to', "" );
					update_post_meta( $post_id, '_sold_individually', "" );
					update_post_meta( $post_id, '_manage_stock', "yes" );
					update_post_meta( $post_id, "_stock", esc_attr($cw_product->getStockQuantity())); 
					update_post_meta( $post_id, '_backorders', "no" );
					update_post_meta( $post_id, '_codeswholesale_product_id', $cw_product->getProductId() );
					update_post_meta( $post_id, '_codeswholesale_product_spread_type', "0" );
					update_post_meta( $post_id, '_codeswholesale_product_is_in_sale', "0" );

					// grant permission to any newly added files on any existing orders for this product
					// do_action( 'woocommerce_process_product_file_download_paths', $post_id, 0, $downdloadArray );
					update_post_meta( $post_id, '_downloadable_files', '');
					update_post_meta( $post_id, '_download_limit', '');
					update_post_meta( $post_id, '_download_expiry', '');
					update_post_meta( $post_id, '_download_type', '');
					update_post_meta( $post_id, '_product_image_gallery', '');
				} else {
					echo "Product allready in DB... \n";
					echo $cw_product->getName() . "\n";
				}
			
				/*if (in_array( $cw_product->getIdentifier(), $cw_products_no_more )) {
						echo "products_no_mores \n";
						echo $cw_product->getIdentifier() . "\n";
				}*/
			
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
                $products_ids[$cw_product_id] = $product;
            }
			
			foreach ($cw_products as $cw_product) {
				
                if (isset($products_ids[$cw_product->getProductId()])) {

                    $post_product = $products_ids[$cw_product->getProductId()];
                    
                    $price = $cw_product->getDefaultPrice();
                    
					// $priceSpread = $this->spreadCalculator->calculateSpread($this->spreadParams->getSpreadParams(), $price);
					
					$priceSpread = $cw_product->getPrice();
                    
                    $product_spread_type =  get_post_meta($post_product->ID, "_codeswholesale_product_spread_type", true);
					
					$product_is_in_sale =  get_post_meta($post_product->ID, "_codeswholesale_product_is_in_sale", true);
										
					echo $cw_product->getName() . "\n";					
										
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
					
					if ( $cw_product->getPlatform() == "GOG.com") {
						$category_id[0] = 117 ;
					} else if ( $cw_product->getPlatform() == "Steam") {
						$category_id[0] = 114 ;
					} else if ( $cw_product->getPlatform() == "Xbox Live") {
						$category_id[0] = 105 ;
					} else if ( $cw_product->getPlatform() == "Rockstar Social Club") {
						$category_id[0] = 109 ;
					} else if ( $cw_product->getPlatform() == "PSN") {
						$category_id[0] = 107 ;
					} else if ( $cw_product->getPlatform() == "Origin") {
						$category_id[0] = 100 ;
					} else if ( $cw_product->getPlatform() == "Official website") {
						$category_id[0] = 115 ;
					} else if ( $cw_product->getPlatform() == "iTunes") {
						$category_id[0] = 110 ;
					} else if ( $cw_product->getPlatform() == "Battle.net") {
						$category_id[0] = 116 ;
					} else if ( $cw_product->getPlatform() == "ubi.com") {
						$category_id[0] = 392 ;
					} else if ( $cw_product->getPlatform() == "None") {
						$category_id[0] = 115 ;
					} else if ( $cw_product->getPlatform() == "Uplay") {
						$category_id[0] = 102 ;
					}
					
					if ( in_array("WORLDWIDE", $cw_product->getRegions())) {
						$category_id[1] = 543 ;
					} else if ( in_array("US", $cw_product->getRegions())) {
						$category_id[1] = 108 ;
					} else if ( in_array("PL", $cw_product->getRegions())) {
						$category_id[1] = 104 ;
					} else if ( in_array("EU", $cw_product->getRegions())) {
						$category_id[1] = 99 ;
					} else if ( in_array("ASIA,EU", $cw_product->getRegions())) {
						$category_id[1] = 391 ;
					} else if ( in_array("RU", $cw_product->getRegions())) {
						$category_id[1] = 467 ;
					} else if ( in_array("EMEA", $cw_product->getRegions())) {
						$category_id[1] = 542 ;
						wp_remove_object_terms( $post_product->ID, array(99,467,391,104,108,543), 'product_cat' );
					}					
										
					if ( in_array("Multilanguage", $cw_product->getLanguages())) {
						$category_id[2] = 250 ;
					} if ( in_array("en", $cw_product->getLanguages())) {
						$category_id[3] = 251 ;
					} if ( in_array("es", $cw_product->getLanguages())) {
						$category_id[4] = 469 ;
					} if ( in_array("fr", $cw_product->getLanguages())) {
						$category_id[5] = 470 ;
					} if ( in_array("pl", $cw_product->getLanguages())) {
						$category_id[6] = 471 ;
					} if ( in_array("ru", $cw_product->getLanguages())) {
						$category_id[7] = 472 ;
					} if ( in_array("de", $cw_product->getLanguages())) {
						$category_id[8] = 468 ;
					}
					
					$term_lists = wp_get_post_terms( $post_product->ID, 'product_cat', array( 'fields' => 'ids' ) );
					
					//echo  $cw_product->getName() . "\n";					
					$category_id_ = array_merge($category_id, $term_lists);
					
					//print_r(array_unique($category_id_));
					
					wp_set_post_terms($post_product->ID, array_unique($category_id_), "product_cat");
					update_post_meta($post_product->ID, "product_cat", array_unique($category_id_));
					
					update_post_meta($post_product->ID, "post_title", htmlspecialchars(esc_attr($cw_product->getName())));
					
					/*echo $cw_product->getPlatform() . "\n";
					print_r($cw_product->getRegions()) . "\n";
					print_r($cw_product->getLanguages()) . "\n";
					print_r($category_id) . "\n";*/

					$cw_format_date = strtotime(str_replace( array('T',':00.000Z'), ' ', $cw_product->getReleaseDate() )); 
					update_post_meta( $post_product->ID, "_wc_pre_orders_availability_datetime", $cw_format_date );
					$availability_timestamp_in_utc = (int) get_post_meta( $post_product->ID, "_wc_pre_orders_availability_datetime", true );

					// if the availability date has passed
					if ( $availability_timestamp_in_utc > strtotime("now") ) {
							update_post_meta($post_product->ID, "_wc_pre_orders_enabled", "yes");
							update_post_meta($post_product->ID, "_wc_pre_orders_when_to_charge", "upfront");
							if ($cw_product->getStockQuantity() == 0) {
								update_post_meta( $post_product->ID, "_stock_status", "instock");
								update_post_meta( $post_product->ID, "_backorders", "yes");								
							}
					} else {
							update_post_meta($post_product->ID, "_wc_pre_orders_enabled", "no");
							update_post_meta($post_product->ID, "_wc_pre_orders_when_to_charge", "");
							update_post_meta( $post_product->ID, "_backorders", "no");	
							if ($cw_product->getStockQuantity() == 0) {
								update_post_meta($post_product->ID, "_stock_status", "outofstock");
								update_post_meta($post_product->ID, "_stock", esc_attr(0));
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
				
            }
			
			echo "Stock updated. \n";
        }
    }

endif;

new CW_Cron_Update_Stock();