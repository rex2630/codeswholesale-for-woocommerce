<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!class_exists('CW_SendKeys')) :

    class CW_SendKeys
    {

        public function __construct()
        {
            add_action('woocommerce_order_status_completed', array($this, 'send_keys_for_order'));
        }

        /**
         *
         * Send key when payment is completed
         *
         * @param $order_id
         * @return mixed
         */
        public function send_keys_for_order($order_id)
        {
            WC()->mailer()->emails["CW_Email_Customer_Completed_Order"] = include("emails/class-cw-email-customer-completed-order.php");
            include('emails/class-cw-email-customer-completed-order.php');


            $order = new WC_Order($order_id);
            $attachments = array();
            $keys = array();

            $items = $order->get_items();

            foreach ($items as $item_key => $item) {

                $product_id = $item["product_id"];
                $qty = $item["qty"];
                $cw_product_id = get_post_meta($product_id, CodesWholesaleConst::PRODUCT_CODESWHOLESALE_ID_PROP_NAME, true);

                $cw_product = \CodesWholesale\Resource\Product::get($cw_product_id);
                $links = array();

                $codes = \CodesWholesale\Resource\Order::createBatchOrder($cw_product, array('quantity' => $qty));

                foreach ($codes as $code) {

                    if($code->isImage()) {
                        $attachments[] = \CodesWholesale\Util\CodeImageWriter::write($code, CW()->plugin_path() . "/temp");
                    }

                    $links[] = $code->getHref();
                }

                $keys[] = array(
                    'item' => $item,
                    'codes' => $codes
                );

                wc_add_order_item_meta($item_key, CodesWholesaleConst::ORDER_ITEM_LINKS_PROP_NAME, json_encode($links), true);
            }

            update_post_meta($order_id, CodesWholesaleConst::ORDER_FULL_FILLED_PARAM_NAME, CodesWholesaleOrderFullFilledStatus::FILLED);

            $email = new CW_Email_Customer_Completed_Order($order);
            $email->send_keys($keys, $attachments);

            foreach($attachments as $attachment) {
                unlink($attachment);
            }
        }
    }

endif;

new CW_SendKeys();