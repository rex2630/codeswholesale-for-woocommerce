<?php

namespace CodesWholesaleFramework\Visitor;

/**
 * Interface VisitorAwareInterface
 */
interface VisitorAwareInterface
{
    public function accept(VisitorInterface $visitor);
}