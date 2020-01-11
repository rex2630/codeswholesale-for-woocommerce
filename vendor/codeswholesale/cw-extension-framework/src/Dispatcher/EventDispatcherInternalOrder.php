<?php

namespace CodesWholesaleFramework\Dispatcher;

use CodesWholesaleFramework\Model\InternalOrder;

/**
 * Interface EventDispatcherInternalOrder
 */
interface EventDispatcherInternalOrder
{
    /**
     * @param InternalOrder $internalOrder
     */
    public function dispatch(InternalOrder $internalOrder);
}