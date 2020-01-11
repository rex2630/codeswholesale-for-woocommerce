<?php

use CodesWholesaleFramework\Postback\ReceivePreOrders\EventDispatcher;

class WP_Event_Dispatcher implements EventDispatcher
{
    public function dispatchEvent(array $data)
    {
        do_action('codeswholesale_pre_orders_added', $data);
    }
}