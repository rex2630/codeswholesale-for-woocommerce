<?php

use CodesWholesaleFramework\Orders\Codes\SendCodesAction;

if (!defined('ABSPATH')) exit;

if (!class_exists('CW_Send_Keys')) :

    class CW_Send_Keys
    {

        public function __construct()
        {
            add_action('codeswholesale_buy_keys_completed', array($this, 'send_keys_for_order'));
        }

        /**
         * Send key when payment is completed
         *
         * @param $order_id
         */
        public function send_keys_for_order($order_id)
        {
            $action = new SendCodesAction(
                new WP_Send_Codes_Dispatcher(),
                new WP_Send_Code_Mail(),
                new WP_OrderNotificationDispatcher(),
                new WP_Links_Retriever()
            );

            $action->setOrderDetails($order_id);
            $action->process();
        }
    }

endif;

new CW_Send_Keys();