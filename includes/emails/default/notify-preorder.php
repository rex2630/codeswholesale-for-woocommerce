<?php

/**
 * Class CW_Email_Notify_Preorder_Default
 */
class CW_Email_Notify_Preorder_Default extends CW_DefaultEmailDataModel
{
    public function __construct() {
        $this->id       = "notify_preorder";
        $this->title    = "CodesWholesale pre-ordered codes";
        $this->heading  = "Pre-order received  #{order_id}";
        $this->subject  = "Pre-order received  #{order_id}";
        $this->content  = "<p>Hello,</p><br>"
                . "<p>Your order has been received and is now being processed. </p>"
                . "<p>You have purchased {count} pre-ordered codes(s) for {item_name}</p>"
                . "<br><p>Best,</p>";
        
        $this->hint     = "{title}, {order_id}, {count}";
    }
}
?>