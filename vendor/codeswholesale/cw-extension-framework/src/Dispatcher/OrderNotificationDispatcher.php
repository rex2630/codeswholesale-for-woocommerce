<?php

namespace CodesWholesaleFramework\Dispatcher;

/**
 * Interface OrderNotificationDispatcher
 */
interface OrderNotificationDispatcher
{
    public function complete($order, $total_number_of_keys, $total_pre_orders);
}