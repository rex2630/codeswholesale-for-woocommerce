<?php

use CodesWholesale\Resource\Order;
use CodesWholesale\Resource\Invoice;
use CodesWholesale\Util\Base64Writer;
use CodesWholesale\Resource\ProductResponse;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!class_exists('CW_Woocommerce_order')) :


    class CW_Woocommerce_order
    {

        public function __construct()
        {
            add_action( 'woocommerce_order_actions', array($this, 'wc_add_order_meta_box_action') );
            
            add_action( 'woocommerce_order_action_wc_get_cw_invoice_order_action', array($this, 'wc_get_cw_invoice_order_process') );
            add_action( 'woocommerce_order_action_wc_get_cw_codes_order_action', array($this, 'wc_get_cw_codes_order_process') );
        }

        function wc_add_order_meta_box_action( $actions ) {
            global $theorder;

            if ( ! $theorder->has_status( 'completed' ) ) {
                return $actions;
            }

            $actions['wc_get_cw_invoice_order_action'] = __( 'Get codeswholesale invoice', 'woocommerce' );
            $actions['wc_get_cw_codes_order_action'] = __( 'Get codeswholesale codes', 'woocommerce' );
            
            return $actions;
        }

        /**
         * @param WC_Order $order
         */
        function wc_get_cw_invoice_order_process( $order ) {
            /** @var WC_Product $product */
            $product = array_shift($order->get_items());

            $orderId = wc_get_order_item_meta($product->get_id(), CodesWholesaleConst::ORDER_ITEM_ORDER_ID_PROP_NAME, true);

            if ($orderId) {
                $invoice = Invoice::get($orderId);

                $temp = tmpfile();
                $uri = stream_get_meta_data($temp)['uri'];
                file_put_contents($uri, base64_decode($invoice->getBody()));
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="'.$invoice->getInvoiceNumber().'.pdf"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($uri));
                readfile($uri);
                fclose($temp);
                exit;
            }
        }

        /**
         * @param WC_Order $order
         */
        function wc_get_cw_codes_order_process( $order ) {
            /** @var WC_Product $product */
            $product = array_shift($order->get_items());

            $orderId = wc_get_order_item_meta($product->get_id(), CodesWholesaleConst::ORDER_ITEM_ORDER_ID_PROP_NAME, true);

            $text = '';

            /** @var ProductResponse $product */
            foreach (Order::getOrder($orderId)->getProducts() as $product) {
                foreach ($product->getCodes() as $code) {
                    //$text .= '<p>'.$product->getProductId() . ':</p>';

                    if ($code->isPreOrder()) {
                        $text .= "<p><b>Code has been pre-ordered!</b>" . " </p>";
                    }
                    if ($code->isText()) {
                        $text .= "<p>Text code to use:</p><p> <b>" . $code->getCode() . "</b></p>";
                    }
                    if ($code->isImage()) {
                        Base64Writer::writeImageCode($code, "Cw_Attachments");
                        $path = wp_upload_dir()['baseurl'].'/../../wp-admin/Cw_Attachments/' . $code->getFileName();
                        $text .= "<p>Check in attachment file:</p><p> <a target='_blank' href='".$path."'><b>" . $code->getFileName() . "</b></a></p>";
                    }
                }
            }

            $order->add_order_note($text);
        }
    }

endif;

new CW_Woocommerce_order();