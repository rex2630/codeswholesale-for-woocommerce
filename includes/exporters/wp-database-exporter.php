<?php
 use CodesWholesaleFramework\Orders\Utils\DataBaseExporter;

class WP_DataBase_Exporter implements DataBaseExporter {

    public function export($item, $orderDataArray, $item_key, $orderId)
    {
        wc_add_order_item_meta($item_key, CodesWholesaleConst::ORDER_ITEM_LINKS_PROP_NAME, json_encode($orderDataArray['links']), true);
        wc_add_order_item_meta($item_key, CodesWholesaleConst::ORDER_ITEM_ORDER_ID_PROP_NAME, $orderDataArray['cwOrderId'], true);
        wc_add_order_item_meta($item_key, CodesWholesaleConst::ORDER_ITEM_NUMBER_OF_PRE_ORDERS_PROP_NAME, $orderDataArray['preOrders'], true);
    }
}