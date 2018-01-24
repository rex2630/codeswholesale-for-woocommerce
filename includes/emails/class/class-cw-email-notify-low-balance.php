<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!class_exists("CW_Email_Abstract")) {
    include("class-cw-email-abstract.php");
}

if (!class_exists('CW_Email_Notify_Low_Balance')) :

    class CW_Email_Notify_Low_Balance extends CW_Email_Abstract
    {

        /**
         * Constructor
         */
        function __construct()
        {
            $this->wp_email = CW_EmailsConst::getCustomEmail(CW_EmailsConst::CW_EMAIL_TYPE_NOTIFY_LOW_BALANCE);
            
            $this->id           = $this->get_cw_id();
            $this->title        = $this->get_cw_title();
            $this->heading      = $this->get_cw_heading();
            $this->subject      = $this->get_cw_subject();
            $this->cw_content   = $this->get_cw_content();
            
            $this->description = __('Order complete emails are sent to the customer when the order is marked complete and usual indicates that the order has been shipped.', 'woocommerce');

            $this->template_html = 'emails/notify-low-balance.php';
            $this->template_plain = 'emails/plain/notify-low-balance.php';

            // Triggers for this email
            add_action( 'codeswholesale_balance_to_low', array( $this, 'trigger' ) );

            parent::__construct();

            $this->recipient = get_option( 'admin_email' );
        }

        public function trigger($account) {
            $this->object = $account;
                                   
            $this->find[] = '{title}';
            $this->replace[] = $this->title;
            
            $this->send($this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
        }

        /**
         * get_content_html function.
         *
         * @access public
         * @return string
         */
        function get_content_html()
        {
            $this->cw_content =  str_replace("{current_balance}", CodesWholesaleConst::format_money($this->object->getCurrentBalance()), $this->cw_content);
            
            ob_start();
            cw_get_template($this->template_html, array(
                'content' =>$this->cw_content,
                'account' => $this->object,
                'email_heading' => $this->get_heading(),
                'sent_to_admin' => false,
                'plain_text' => false
            ));

            return ob_get_clean();
        }

        /**
         * get_content_plain function.
         *
         * @access public
         * @return string
         */
        function get_content_plain()
        {
            ob_start();

            cw_get_template($this->template_plain, array(
                'content' =>$this->cw_content,
                'account' => $this->object,
                'email_heading' => $this->get_heading(),
                'sent_to_admin' => false,
                'plain_text' => true
            ));

            return ob_get_clean();
        }
    }

endif;

new CW_Email_Notify_Low_Balance();