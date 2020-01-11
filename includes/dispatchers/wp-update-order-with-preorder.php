<?php

use CodesWholesaleFramework\Postback\ReceivePreOrders\Utils\UpdateOrderWithPreOrdersInterface;

class WP_Update_Order_With_PreOrder implements UpdateOrderWithPreOrdersInterface
{

    public function update($newKeys, $textComment)
    {
        $order = new WC_Order($newKeys['item']['order']->id);

        WC()->mailer()->emails["CW_Email_Customer_Completed_Order"] = include(CW()->plugin_path() . "/includes/emails/class-cw-email-customer-completed-order.php");

        wc_update_order_item_meta($newKeys['item']['item']['id'], CodesWholesaleConst::ORDER_ITEM_LINKS_PROP_NAME, json_encode(array_merge($newKeys['linksToAdd'], array_values($newKeys['links']))));

        wc_update_order_item_meta($newKeys['item']['item']['id'], CodesWholesaleConst::ORDER_ITEM_NUMBER_OF_PRE_ORDERS_PROP_NAME, $newKeys['preOrdersLeft']);

        $keys[] = array(
            'item' => $newKeys['item']['item'],
            'codes' => $newKeys['codes']
        );

        do_action("codeswholesale_send_keys_email", array('order' => $order, 'keys' => $keys, 'attachments' => $newKeys['item']['attachments'], 'pre_orders_left' => $newKeys['item']['number_of_preorders']));

        $newKeys['item']['order']->add_order_note(sprintf("Pre-ordered game keys sent (total: %s, left: %s).", $newKeys['item']['numberOfKeysSent'], $newKeys['item']['preOrdersLeft']));

        $this->clean_temp($newKeys['attachments']);
    }

    private function clean_temp($attachments)
    {
        foreach ($attachments as $attachment) {
            if (file_exists($attachment)) {
                unlink($attachment);
            }
        }
    }

}
