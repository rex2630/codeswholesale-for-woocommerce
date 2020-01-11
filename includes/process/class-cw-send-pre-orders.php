<?php
use CodesWholesaleFramework\Postback\ReceivePreOrders\UpdateOrderWithPreOrdersAction;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!class_exists('CW_Send_Pre_Orders')) :

    class CW_Send_Pre_Orders
    {

        public function __construct()
        {
            add_action('codeswholesale_pre_orders_added', array($this, 'send_keys_for_pre_orders'));
        }

        /**
         * Send key when payment is completed.
         *
         * @param $args
         */
        public function send_keys_for_pre_orders($args)
        {
            $action = new UpdateOrderWithPreOrdersAction(new WP_Update_Order_With_PreOrder());
            $action->setKeys($args[0]);
            $action->process();
        }
    }

endif;

new CW_Send_Pre_Orders();