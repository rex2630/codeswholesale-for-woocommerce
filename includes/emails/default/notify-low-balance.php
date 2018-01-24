<?php

/**
 * Class CW_Email_Notify_Low_Balance_Default
 */
class CW_Email_Notify_Low_Balance_Default extends CW_DefaultEmailDataModel
{
    public function __construct() {
        $this->id       = "notify_low_balance";
        $this->title    = "Low balance on CodesWholesale";
        $this->heading  = "Watch out. Your balance is too low.";
        $this->subject  = "Watch out. Your balance is too low.";
        $this->content  = "<p>Hello,</p> <br>"
                . "<p>"
                . "You have insufficient funds to order games via <a href='https://app.codeswholesale.com'>CodesWholesale.com</a>. <br /><br />"
                . "<strong>Your current balance</strong>: {current_balance} <br>"
                . "Please add funds to your account balance to continue with your purchases. <br /><br />"
                . "</p>"
                . "<br><p>Best,</p>";
        
        $this->hint     = "{title}, {current_balance}";
    }
}
?>