<?php

use CodesWholesaleFramework\Dispatcher\OrderNotificationDispatcher;

class WP_OrderNotificationDispatcher implements OrderNotificationDispatcher
{
    /**
     * @param WC_Order $order
     * @param $total_number_of_keys
     */
    public function complete($order, $total_number_of_keys)
    {
        $order->add_order_note(sprintf("Game keys sent (total: %s).", $total_number_of_keys));
    }
}
