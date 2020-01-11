<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!class_exists('CW_Email_Notify_Low_Balance')) :

    if (!class_exists("WC_Email")) {
        include(WC()->plugin_path() . "/includes/abstracts/abstract-wc-email.php");
    }

    class CW_Email_Notify_Low_Balance extends WC_Email
    {

        /**
         * Constructor
         */
        function __construct()
        {
            $this->id = 'notify_low_balance';
            $this->title = "CodesWholesale low balance";
            $this->description = __('Order complete emails are sent to the customer when the order is marked complete and usual indicates that the order has been shipped.', 'woocommerce');

            $this->heading = "CodesWholesale low balance";
            $this->subject = "CodesWholesale low balance";

            $this->template_html = 'emails/notify-low-balance.php';
            $this->template_plain = 'emails/plain/notify-low-balance.php';

            // Triggers for this email
            add_action( 'codeswholesale_balance_to_low', array( $this, 'trigger' ) );

            // Other settings
            $this->heading_downloadable = "CodesWholesale low balance";
            $this->subject_downloadable = "CodesWholesale low balance";

            parent::__construct();

            $this->recipient = get_option( 'admin_email' );
        }

        public function trigger($account) {
            $this->object = $account;
            $this->send($this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
        }

        /**
         * get_heading function.
         *
         * @access public
         * @return string
         */
        function get_heading()
        {
            return "Watch out. Your balance is too low.";
        }

        /**
         * get_subject function.
         *
         * @access public
         * @return string
         */
        function get_subject()
        {
            return "Watch out. Your balance is too low.";
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
                'account' => $this->object,
                'email_heading' => $this->get_heading(),
                'sent_to_admin' => false,
                'plain_text' => true
            ));

            return ob_get_clean();
        }

        /**
         * Initialise Settings Form Fields
         *
         * @access public
         * @return void
         */
        function init_form_fields()
        {
            $this->form_fields = array(
                'enabled' => array(
                    'title' => __('Enable/Disable', 'woocommerce'),
                    'type' => 'checkbox',
                    'label' => __('Enable this email notification', 'woocommerce'),
                    'default' => 'yes'
                ),
                'subject' => array(
                    'title' => __('Subject', 'woocommerce'),
                    'type' => 'text',
                    'description' => sprintf(__('Defaults to <code>%s</code>', 'woocommerce'), $this->subject),
                    'placeholder' => '',
                    'default' => ''
                ),
                'heading' => array(
                    'title' => __('Email Heading', 'woocommerce'),
                    'type' => 'text',
                    'description' => sprintf(__('Defaults to <code>%s</code>', 'woocommerce'), $this->heading),
                    'placeholder' => '',
                    'default' => ''
                ),
                'subject_downloadable' => array(
                    'title' => __('Subject (downloadable)', 'woocommerce'),
                    'type' => 'text',
                    'description' => sprintf(__('Defaults to <code>%s</code>', 'woocommerce'), $this->subject_downloadable),
                    'placeholder' => '',
                    'default' => ''
                ),
                'heading_downloadable' => array(
                    'title' => __('Email Heading (downloadable)', 'woocommerce'),
                    'type' => 'text',
                    'description' => sprintf(__('Defaults to <code>%s</code>', 'woocommerce'), $this->heading_downloadable),
                    'placeholder' => '',
                    'default' => ''
                ),
                'email_type' => array(
                    'title' => __('Email type', 'woocommerce'),
                    'type' => 'select',
                    'description' => __('Choose which format of email to send.', 'woocommerce'),
                    'default' => 'html',
                    'class' => 'email_type',
                    'options' => array(
                        'plain' => __('Plain text', 'woocommerce'),
                        'html' => __('HTML', 'woocommerce'),
                        'multipart' => __('Multipart', 'woocommerce'),
                    )
                )
            );
        }
    }

endif;

new CW_Email_Notify_Low_Balance();