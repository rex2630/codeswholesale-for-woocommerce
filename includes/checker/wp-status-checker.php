<?php

use CodesWholesaleFramework\Orders\Utils\StatusChangeInterface;

class WP_Status_Checker implements StatusChangeInterface{

    public function checkStatus($order_id){

        $is_full_filled = get_post_meta($order_id, CodesWholesaleConst::ORDER_FULL_FILLED_PARAM_NAME);
        if ($is_full_filled == CodesWholesaleOrderFullFilledStatus::FILLED) {
            return;
        }

        $order = new WC_Order($order_id);
        $items = $order->get_items();

        $observerArray = array(
            'order' => $order,
            'orderId' => $order_id,
            'orderedItems' => $items
        );

        return $observerArray;
    }
}

