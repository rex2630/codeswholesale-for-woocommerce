<?php

/**
 * Class CW_Email_Order_Error_Default
 */
class CW_Email_Order_Error_Default extends CW_DefaultEmailDataModel
{
    public function __construct() {
        $this->id       = "order_error";
        $this->title    = "CodesWholesale Order Error";
        $this->heading  = "{title}, order #{order_id}";
        $this->subject  = "CodesWholesale something went bad order #{order_id}";
        $this->content  = "<p>Hello,</p><br>"
                . "<p>"
                . "Something is wrong - we have encountered some issues during purchases. Please check the details below: <br><br>"
                . "<strong>Error class</strong>: {error_class} <br>"
                . "<strong>Message</strong>: {error_message} <br> <br>"
                . "<strong>Stack trace</strong>:  <br>"
                . "<pre><small>{stack_trace}</small></pre>"
                . "</p>"
                . "<br><p>Best,</p>";
        
        $this->hint     = "{title}, {order_id}, {error_class}, {error_message}, {stack_trace}";
    }
}
?>