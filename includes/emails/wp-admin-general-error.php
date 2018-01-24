<?php

use CodesWholesaleFramework\Errors\ErrorHandler;

class WP_Admin_General_Error implements ErrorHandler
{

    public function sendAdminErrorMail($order, $title, $e)
    {
        $this->init_emails();
        do_action("codeswholesale_order_error", array('error' => $e, "title" => $title, "order" => $order));
    }

    private function init_emails()
    {
        WC()->mailer()->emails["CW_Email_Order_Error"] = include(CW()->plugin_path() . "/includes/emails/class/class-cw-email-order-error.php");
    }

    public function handleError($order, string $title, \Exception $e)
    {
        // TODO: Implement handleError() method.
    }
}