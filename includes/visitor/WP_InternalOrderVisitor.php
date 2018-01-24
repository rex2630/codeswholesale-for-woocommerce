<?php

use CodesWholesaleFramework\Visitor\VisitorInterface;
use CodesWholesaleFramework\Visitor\VisitorAwareInterface;
use CodesWholesaleFramework\Model\InternalOrder;

/**
 * Class WP_InternalOrderVisitor
 */
class WP_InternalOrderVisitor implements VisitorInterface
{
    /**
     * @param VisitorAwareInterface|InternalOrder $visitee
     */
    public function visit(VisitorAwareInterface $visitee)
    {
        $is_full_filled = get_post_meta($visitee->getId(), CodesWholesaleConst::ORDER_FULL_FILLED_PARAM_NAME);

        if ($is_full_filled != CodesWholesaleOrderFullFilledStatus::FILLED) {
            $order = new WC_Order($visitee->getId());

            $visitee
                ->setOrder($order)
                ->setItems($order->get_items())
            ;
        }
    }
}