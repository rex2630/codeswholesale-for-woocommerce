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
            WC()->mailer()->emails["CW_Email_Notify_Low_Balance"]       = include("emails/class-cw-email-notify-low-balance.php");
            WC()->mailer()->emails["CW_Email_Customer_Completed_Order"] = include("emails/class-cw-email-customer-completed-order.php");
            WC()->mailer()->emails["CW_Email_Order_Error"]              = include("emails/class-cw-email-order-error.php");

            $order = new WC_Order($order_id);
            $attachments = array();
            $keys = array();
            $error = null;
            $balance_value = get_option(CodesWholesaleConst::NOTIFY_LOW_BALANCE_VALUE_OPTION_NAME);

            $items = $order->get_items();

            foreach ($items as $item_key => $item) {

                $product_id = $item["product_id"];
                $qty = $item["qty"];
                $cw_product_id = get_post_meta($product_id, CodesWholesaleConst::PRODUCT_CODESWHOLESALE_ID_PROP_NAME, true);

                $links = array();

                try {

                    $cw_product = \CodesWholesale\Resource\Product::get($cw_product_id);
                    $codes = \CodesWholesale\Resource\Order::createBatchOrder($cw_product, array('quantity' => $qty));

                    foreach ($codes as $code) {

                        if ($code->isImage()) {
                            $attachments[] = \CodesWholesale\Util\CodeImageWriter::write($code, CW()->plugin_path() . "/temp");
                        }

                        $links[] = $code->getHref();
                    }

                    $keys[] = array(
                        'item' => $item,
                        'codes' => $codes
                    );

                    wc_add_order_item_meta($item_key, CodesWholesaleConst::ORDER_ITEM_LINKS_PROP_NAME, json_encode($links), true);

                } catch (\CodesWholesale\Resource\ResourceError $e) {
                    $this->support_resource_error($e, $order);
                    $error = $e;
                    break;
                } catch (Exception $e) {
                    $this->support_error($e, $order);
                    $error = $e;
                    break;
                }
            }

            if (!$error) {

                $account = CW()->getCodesWholesaleClient()->getAccount();

                if ($balance_value > 10) {
                    do_action("codeswholesale_balance_to_low", $account);
                }

                update_post_meta($order_id, CodesWholesaleConst::ORDER_FULL_FILLED_PARAM_NAME, CodesWholesaleOrderFullFilledStatus::FILLED);

                $email = new CW_Email_Customer_Completed_Order($order);
                $email->send_keys($keys, $attachments);

                $order->add_order_note("Game keys sent - done.");

            } else {

                $order->add_order_note("Game keys weren't sent due to script errors: " . $error->getMessage());

            }

            foreach ($attachments as $attachment) {
                if (file_exists($attachment)) {
                    unlink($attachment);
                }
            }
        }

        public function support_resource_error($e, $order)
        {
            if ($e->isInvalidToken()) {
                do_action("codeswholesale_order_error", array('error' => $e, "title" => "Invalid token", "order" => $order));
            } else
                // handle scenario when account's balance is not enough to make order
                if ($e->getStatus() == 400 && $e->getErrorCode() == 10002) {
                    do_action("codeswholesale_order_error", array('error' => $e, "title" => "Balance too low", "order" => $order));
                } else
                    // handle scenario when code details where not found
                    if ($e->getStatus() == 404 && $e->getErrorCode() == 50002) {
                        do_action("codeswholesale_order_error", array('error' => $e, "title" => "Code not found", "order" => $order));
                    } else
                        // handle scenario when product was not found in price list
                        if ($e->getStatus() == 404 && $e->getErrorCode() == 20001) {
                            do_action("codeswholesale_order_error", array('error' => $e, "title" => "Product not found", "order" => $order));
                        } else
                            // handle when quantity was less then 1
                            if ($e->getStatus() == 400 && $e->getErrorCode() == 40002) {
                                do_action("codeswholesale_order_error", array('error' => $e, "title" => "Quantity less then 1", "order" => $order));
                            } else {
                                $this->support_error($e, $order);
                            }
        }

        public function support_error($e, $order)
        {
            do_action("codeswholesale_order_error", array('error' => $e, "title" => "Error occurred"));
        }
    }

endif;

new CW_SendKeys();