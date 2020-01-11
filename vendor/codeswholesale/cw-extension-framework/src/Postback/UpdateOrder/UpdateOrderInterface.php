<?php

namespace CodesWholesaleFramework\Postback\UpdateOrder;

/**
 * Interface UpdateOrderInterface
 */
interface UpdateOrderInterface
{
    /**
     * @param string $code
     *
     * @return mixed
     */
    public function preOrderAssigned(string $code);
}