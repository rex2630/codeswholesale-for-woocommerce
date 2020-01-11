<?php

use CodesWholesaleFramework\Postback\Retriever\ItemRetrieverInterface;

class WP_Item_Retriever implements ItemRetrieverInterface
{


    public function retrieveItem($order_id)
    {

        global $wpdb;

        $meta_key = CodesWholesaleConst::ORDER_ITEM_ORDER_ID_PROP_NAME;

        $result = $wpdb->get_results(
            "SELECT *
                    FROM   {$wpdb->prefix}woocommerce_order_itemmeta oim, {$wpdb->prefix}woocommerce_order_items oi
                    WHERE  oim.meta_key='{$meta_key}'
                    AND    oim.meta_value='{$order_id}'
                    AND    oi.order_item_id=oim.order_item_id");

        if (count($result) > 0) {
            $item = array(
                'id' => $result[0]->order_item_id,
                'name' => $result[0]->order_item_name,
                'type' => $result[0]->order_item_type,
                'order_id' => $result[0]->order_id,
            );
        }
            $order = new WC_Order($item['order_id']);
            $items = $order->get_items();

            foreach ($items as $item_key => $ordered_item) {

                $links = json_decode(wc_get_order_item_meta($item_key, CodesWholesaleConst::ORDER_ITEM_LINKS_PROP_NAME));
                $number_of_preorders = json_decode(wc_get_order_item_meta($item_key, CodesWholesaleConst::ORDER_ITEM_NUMBER_OF_PRE_ORDERS_PROP_NAME));
            }

            $observer = array(
                'order' => $order,
                'item' => $item,
                'links' => $links,
                'number_of_preorders' => $number_of_preorders
            );
            return $observer;
        }
}