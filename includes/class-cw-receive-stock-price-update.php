<?php

use CodesWholesaleFramework\Postback\UpdatePriceAndStock\UpdatePriceAndStockAction;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!class_exists('CW_Receive_Stock_Price_Update')) :

    /**
     *
     */
    class CW_Receive_Stock_Price_Update
    {
        /**
         *
         */
        public function __construct()
        {
            add_action('admin_post_update_price_and_stock', array($this, 'update_price_and_stock'));
            add_action('admin_post_nopriv_update_price_and_stock', array($this, 'update_price_and_stock'));
            //add_action('save_post', array($this, 'update_price_and_stock'));
            // add_action('transition_post_status', array($this, 'update_price_and_stock_after_publish'));
        }

        /**
         * Update price and stock.
         *
         * When CW send request or when post is updated.
         */
        public function update_price_and_stock()
        {
            $productId = null;
            $action = new UpdatePriceAndStockAction(new WP_Update_Price_And_Stock(), new WP_Spread_Retriever());
            $action->setConnection(CW()->get_codes_wholesale_client());
            $action->setProductId($productId);

            $action->process();
        }
    }

endif;

new CW_Receive_Stock_Price_Update();


