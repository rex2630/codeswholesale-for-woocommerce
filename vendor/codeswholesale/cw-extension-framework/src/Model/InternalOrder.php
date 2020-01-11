<?php

namespace CodesWholesaleFramework\Model;

use CodesWholesaleFramework\Visitor\VisitorAwareInterface;
use CodesWholesaleFramework\Visitor\VisitorInterface;

/**
 * Class InternalOrder
 */
class InternalOrder implements VisitorAwareInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var array
     */
    protected $items;

    /**
     * @var mixed
     */
    protected $order;

    /**
     * InternalOrder constructor.
     *
     * @param int $id
     */
    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function accept(VisitorInterface $visitor)
    {
        $visitor->visit($this);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        return $this->items ? $this->items : [];
    }

    /**
     * @param array $items
     *
     * @return InternalOrder
     */
    public function setItems(array $items): InternalOrder
    {
        $this->items = $items;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param mixed $order
     *
     * @return InternalOrder
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }
}