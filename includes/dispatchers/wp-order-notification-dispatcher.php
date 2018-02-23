<?php

use CodesWholesaleFramework\Dispatcher\OrderNotificationDispatcher;

class WP_OrderNotificationDispatcher implements OrderNotificationDispatcher
{
    /**
     * @param WC_Order $order
     * @param $total_number_of_keys
     * @param $total_pre_orders
     */
    public function complete($order, $total_number_of_keys, $total_pre_orders)
    {
        $order->add_order_note(sprintf("Game keys sent (total: %s).", $total_number_of_keys));
    }
}
