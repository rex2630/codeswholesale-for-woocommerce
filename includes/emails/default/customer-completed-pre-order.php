<?php

/**
 * CW_Email_Customer_Completed_Pre_Order_Default
 */
class CW_Email_Customer_Completed_Pre_Order_Default extends CW_DefaultEmailDataModel
{
    public function __construct() {
        $this->id       = "customer_completed_order";
        $this->title    = "Partially completed order";
        $this->heading  = "Your order is partially complete";
        $this->subject  = "Your {site_title} order from {order_date} is partially complete";
        $this->content  = "<p>Hello,</p><br>"
                . "<p>"
                . "Your recent order on {site_title} has been completed. "
                . "Your codes are shown below for your reference:"
                . "<h3>Order: {order_number}</h3>"
                . "[". CW_EmailsConst::CW_EMAIL_SHORTCODE_LOOP_KEYS. " "
                . "tittle = '<strong>{item_name}</strong>' "
                . "code = '{code}' "
                . "image='Check in attachment file: {file_name}' "
                . "preorder='This codes is Pre-Ordered'"
                . "]"
                . "</p>"
                . "<br><p>Best,</p>";
        
        $this->hint     = "{title}, {site_title}, {item_name}, {order_date}, {order_number}, {code}, {file_name}";
    }
}