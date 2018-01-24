<?php

use CodesWholesaleFramework\Orders\Utils\CodesProcessor;
use CodesWholesaleFramework\Model\InternalOrder;

/**
 * Class WP_Validate_Purchase
 */
class WP_Validate_Purchase implements CodesProcessor
{
    public function process(InternalOrder $internalOrder, $numberOfPreOrders, $error, $item)
    {
        if (!$error) {
            $internalOrder->getOrder()->add_order_note(sprintf("Pre-ordered keys %d, for product: %s", $numberOfPreOrders, $item['name']));

            return array('orderId' => $internalOrder->getId());
        } else {

            $internalOrder->getOrder()->add_order_note("Game keys weren't sent due to script errors: " . $error->getMessage());
            return false;

        }
    }
}