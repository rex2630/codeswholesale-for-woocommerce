<?php

namespace CodesWholesaleFramework\Provider;

/**
 * Interface OrderDetailsProvider
 */
interface OrderDetailsProvider
{
    public function provide($orderId): array;
}