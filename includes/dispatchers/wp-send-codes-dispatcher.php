<?php

use CodesWholesaleFramework\Provider\OrderDetailsProvider;

/**
 * Class WP_Send_Codes_Dispatcher
 */
class WP_Send_Codes_Dispatcher implements OrderDetailsProvider
{
    /**
     * {@inheritdoc}
     */
    public function provide($order_id): array
    {
        $order = new WC_Order($order_id);
        $items = $order->get_items();

        $observerParams = array(
            'order'        => $order,
            'orderedItems' => $items,
            'orderId'      => $order_id
        );

        return $observerParams;
    }
}
