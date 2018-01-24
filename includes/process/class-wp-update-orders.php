<?php

use CodesWholesaleFramework\Postback\UpdateOrder\UpdateOrderInterface;
use CodesWholesale\Resource\Code;
use CodesWholesale\Util\Base64Writer;
use CodesWholesaleFramework\Mailer\SendCodeMailer;
use CodesWholesaleFramework\Dispatcher\OrderNotificationDispatcher;

/**
 * Class WP_Update_Orders
 */
class WP_Update_Orders implements UpdateOrderInterface
{

    /**
     * @var SendCodeMailer
     */
    private $sendCodeMailer;

    /**
     * @var OrderNotificationDispatcher
     */
    private $orderNotificationDispatcher;

    /**
     * WP_Update_Orders constructor.
     */
    public function __construct()
    {
        $this->sendCodeMailer = new WP_Send_Code_Mail();
        $this->orderNotificationDispatcher = new WP_OrderNotificationDispatcher();
    }

    /**
     * {@inheritdoc}
     */
    public function preOrderAssigned(string $codeId)
    {
        if (1 != CW()->get_options()[CodesWholesaleConst::ALLOW_PRE_ORDER_PROP_NAME]) {
            return;
        }

        $orderItemId = $this->findOrderItemIdByCode($codeId);
        $orderItem = new WC_Order_Item_Product($orderItemId);
        $order = $orderItem->get_order();
        $keys = [];

        /** @var Code $code */
        $code = Code::get($codeId);
        $codes = [$code];
        $preOrder = 0;
        $totalNumberOfKeys = 1;
        $attachments = [];

        if ($code->isImage()) {

            /** @var Code $code */
            $attachments[] = Base64Writer::writeImageCode($code, 'Cw_Attachments');
        }

        if ($code->isPreOrder()) {
            $preOrder = 1;
        }

        $keys[] = array(
            'item' => $orderItem,
            'codes' => $codes
        );

        $this->sendCodeMailer->sendCodeMail($order, $attachments, $keys, $preOrder);
        $this->orderNotificationDispatcher->complete($order, $totalNumberOfKeys);
        $this->updateTotalPreOrders($orderItem);

        $this->cleanAttach($attachments);

    }

    /**
     * @param $attachments
     */
    private function cleanAttach($attachments)
    {
        foreach ($attachments as $attachment) {
            if (file_exists($attachment)) {

                unlink($attachment);
            }
        }
    }

    private function updateTotalPreOrders(WC_Order_Item_Product $product)
    {
        $preOrders = wc_get_order_item_meta($product->get_id(), CodesWholesaleConst::ORDER_ITEM_NUMBER_OF_PRE_ORDERS_PROP_NAME, true);

        if (null !== $preOrders && (int) $preOrders > 0) {
            $preOrders = (int) $preOrders - 1;
            wc_update_order_item_meta($product->get_id(), CodesWholesaleConst::ORDER_ITEM_NUMBER_OF_PRE_ORDERS_PROP_NAME, $preOrders);
        }

    }

    private function findOrderItemIdByCode(string $code): int
    {
        global $wpdb;

        $query = sprintf("SELECT * FROM %swoocommerce_order_itemmeta WHERE meta_key = '%s' AND meta_value LIKE '%s'",
            $wpdb->prefix,
            CodesWholesaleConst::ORDER_ITEM_LINKS_PROP_NAME,
            '%' . $code . '%'
        );

        $result = $wpdb->get_row($query);

        if (null === $result) {
            throw new \Exception("Order item does not exist");
        }

        return $result->order_item_id;
    }
}