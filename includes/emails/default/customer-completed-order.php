<?php

/**
 * CW_Email_Customer_Completed_Order_Default
 */
class CW_Email_Customer_Completed_Order_Default extends CW_DefaultEmailDataModel
{
    public function __construct() {
        $this->id       = "customer_completed_order";
        $this->title    = "Completed order";
        $this->heading  = "[". CW_EmailsConst::CW_EMAIL_SHORTCODE_HEADING. " "
                . "code='Your order is complete' "
                . "image='Your order is complete - download your files' "
                . "]";
        $this->subject  = "[". CW_EmailsConst::CW_EMAIL_SHORTCODE_SUBJECT. " "
                . "code='Your {site_title} order from {order_date} is complete - download your files' "
                . "image='Your {site_title} order from {order_date} is complete - download your files' "
                . "]";
        $this->content  = "<p>Hello,</p><br>"
                . "<p>"
                . "Your recent order on {site_title} has been completed. "
                . "Your codes are shown below for your reference:"
                . "<h3>Order: {order_number}</h3>"
                . "[". CW_EmailsConst::CW_EMAIL_SHORTCODE_LOOP_KEYS. " "
                . "tittle = '<strong>{item_name}</strong>' "
                . "code = '{code}' "
                . "image='Check in attachment file: {file_name}' "
                . "preorder='This key is Pre-Ordered'"
                . "]"
                . "</p>"
                . "<br><p>Best,</p>";
        
        $this->hint     = "{title}, {site_title}, {item_name}, {order_date}, {order_number}, {code}, {file_name}";
    }
}