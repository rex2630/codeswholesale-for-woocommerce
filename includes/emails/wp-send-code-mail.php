<?php

use CodesWholesaleFramework\Mailer\SendCodeMailer;

class WP_Send_Code_Mail implements SendCodeMailer
{
    public function sendCodeMail($order, $attachments, $keys, $totalPreOrders)
    {
        WC()->mailer()->emails["CW_Email_Customer_Completed_Order"] = include(CW()->plugin_path() . "/includes/emails/class/class-cw-email-customer-completed-order.php");

        do_action("codeswholesale_send_keys_email", array('order' => $order, 'keys' => $keys, 'attachments' => $attachments, 'pre_orders_left' => $totalPreOrders));
    }
}