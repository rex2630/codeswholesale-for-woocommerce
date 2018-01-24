<?php

use CodesWholesaleFramework\Orders\Codes\OrderCreatorAction;
use CodesWholesaleFramework\Provider\InternalOrderProvider;

if (!defined('ABSPATH')) exit;

if (!class_exists('CW_Buy_Keys')) :

    class CW_Buy_Keys
    {
        /**
         *
         * Bind to complete event
         *
         */
        public function __construct()
        {
            add_action('woocommerce_order_status_completed', array($this, 'buy_keys_for_order'));
        }
        /**
         *
         * Send key when payment is completed
         *
         * @param $orderId
         */
        public function buy_keys_for_order($orderId)
        {
            $action = new OrderCreatorAction(
                new WP_InternalOrderVisitor(),
                new WP_DataBase_Exporter(),
                new WP_Order_Event_Dispatcher(),
                new WP_Order_Item_Retriever(),
                new WP_Admin_Error(),
                new WP_Admin_General_Error(),
                new WP_Validate_Purchase(),
                CW()->get_codes_wholesale_client()
            );

            $action->setInternalOrder(InternalOrderProvider::generateById($orderId));
            $action->process();
        }
    }

endif;

new CW_Buy_Keys();