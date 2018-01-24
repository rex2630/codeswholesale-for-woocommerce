<?php

use CodesWholesaleFramework\Dispatcher\EventDispatcherInternalOrder;
use CodesWholesaleFramework\Model\InternalOrder;

/**
 * Class WP_Order_Event_Dispatcher
 */
class WP_Order_Event_Dispatcher implements EventDispatcherInternalOrder
{
    /**
     * @param InternalOrder $internalOrder
     */
    public function dispatch(InternalOrder $internalOrder)
    {
        update_post_meta($internalOrder->getId(), CodesWholesaleConst::ORDER_FULL_FILLED_PARAM_NAME, CodesWholesaleOrderFullFilledStatus::FILLED);
        do_action("codeswholesale_buy_keys_completed", $internalOrder->getId());
    }
}