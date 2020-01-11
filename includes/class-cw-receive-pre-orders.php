<?php

use CodesWholesaleFramework\Postback\ReceivePreOrders\ReceivePreOrdersAction;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!class_exists('CW_Receive_Pre_Orders')) :

    /**
     *
     */
    class CW_Receive_Pre_Orders
    {
        public function __construct()
        {
            add_action('admin_post_receive_pre_orders', array($this, 'receive_pre_orders'));
            add_action('admin_post_nopriv_receive_pre_orders', array($this, 'receive_pre_orders'));
        }

        public function receive_pre_orders()
        {
            $action = new ReceivePreOrdersAction(new WP_Item_Retriever(), new WP_Event_Dispatcher());
            $action->setConnection(CW()->get_codes_wholesale_client());
            $action->process();
        }
    }
endif;

new CW_Receive_Pre_Orders();


