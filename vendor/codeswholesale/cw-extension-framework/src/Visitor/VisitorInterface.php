<?php

namespace CodesWholesaleFramework\Visitor;

/**
 * Interface VisitorInterface
 */
interface VisitorInterface
{
    /**
     * @param VisitorAwareInterface $visitee
     *
     * @return void
     */
    public function visit(VisitorAwareInterface $visitee);
}