<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!class_exists("CW_Email_Abstract")) {
    include("class-cw-email-abstract.php");
}

if (!class_exists('CW_Email_Notify_Import_Finished')) :

    class CW_Email_Notify_Import_Finished extends CW_Email_Abstract
    {

        /**
         * Constructor
         */
        function __construct()
        {
            $this->wp_email = CW_EmailsConst::getCustomEmail(CW_EmailsConst::CW_EMAIL_TYPE_NOTIFY_IMPORT_FINISHED);
            
            $this->id           = $this->get_cw_id();
            $this->title        = $this->get_cw_title();
            $this->heading      = $this->get_cw_heading();
            $this->subject      = $this->get_cw_subject();
            $this->cw_content   = $this->get_cw_content();
            
            $this->description = __('Import finished emails are sent after import products from CodesWholesale.', 'woocommerce');

            $this->template_html = 'emails/notify-import-finished.php';
            $this->template_plain = 'emails/plain/notify-import-finished.php';

            // Triggers for this email
            add_action( 'codeswholesale_import_finished', array( $this, 'trigger' ) );

            parent::__construct();

            $this->recipient = get_option( 'admin_email' );
        }

        public function trigger($args) {
            $this->attachments = $args['attachments'];
            $this->object = $args['import'];
                                   
            $this->find[] = '{title}';
            $this->replace[] = $this->title;
            
            $this->send($this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
        }

        /**
         *
         * @return mixed
         */
        function get_attachments()
        {
            return apply_filters('woocommerce_email_attachments', $this->attachments, $this->id, $this->object);
        }

        /**
         * get_content_html function.
         *
         * @access public
         * @return string
         */
        function get_content_html()
        {
            ob_start();
         
            cw_get_template($this->template_html, array(
                'content' =>$this->cw_content,
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
                'email_heading' => $this->get_heading(),
                'sent_to_admin' => false,
                'plain_text' => true
            ));

            return ob_get_clean();
        }
    }

endif;

new CW_Email_Notify_Import_Finished();