<?php
use CodesWholesale\Resource\ImageType;
use CodesWholesale\Resource\StockAndPriceChange;
use CodesWholesaleFramework\Postback\UpdateProduct\UpdateProductInterface;
use CodesWholesaleFramework\Model\ExternalProduct;
use CodesWholesale\Resource\Product;

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

					$product = Product::get($cw_product->getProductId());

					$externalProduct = (new ExternalProduct())
						->setProduct($product)
						->updateInformations(CW()->get_options()[CodesWholesaleConst::PREFERRED_LANGUAGE_FOR_PRODUCT_OPTION_NAME])
					;

					WP_Product_Updater::getInstance()->createWooCommerceProduct(0, $externalProduct);

				} else {
					echo "Product allready in DB... \n";
					echo $cw_product->getName() . "\n";
				}
			
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
				
					$priceSpread = $this->calculateSpread($this->getSpreadParams(), $price);
                    
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
							echo esc_attr(round($priceSpread , 2)) . "\n";
							update_post_meta($post_product->ID, "_virtual", "yes");
						} else {
							update_post_meta($post_product->ID, "_regular_price", esc_attr(0));
							update_post_meta($post_product->ID, "_price", esc_attr(0));
						}
					}
					
					/* Create category */
					$wpProductUpdater = WP_Product_Updater::getInstance();
					$wpProductUpdater->updateProductCategory($post_product->ID, $cw_product);
					$wpProductUpdater->updateProductTags($post_product->ID, $cw_product);
					$wpProductUpdater->updateProductAttributes($post_product->ID, $cw_product);

					
					$cw_format_date = strtotime(str_replace( array('T',':00.000Z'), ' ', $cw_product->getReleaseDate() )); 

					wp_update_post(
						array (
							'ID'            => $post_product->ID, // ID of the post to update
							'post_date'     => $cw_product->getReleaseDate(),
							'post_date_gmt' => get_gmt_from_date( $cw_product->getReleaseDate() )
						)
					);

					update_post_meta( $post_product->ID, "_wc_pre_orders_availability_datetime", $cw_format_date );
					$availability_timestamp_in_utc = (int) get_post_meta( $post_product->ID, "_wc_pre_orders_availability_datetime", true );

					// if the availability date has passed
					if ( $availability_timestamp_in_utc > strtotime("now") ) {
							if ($cw_product->getStockQuantity() == 0) {
								update_post_meta( $post_product->ID, "_stock_status", "instock");
								update_post_meta( $post_product->ID, "_backorders", "yes");							
							}
					} else {
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