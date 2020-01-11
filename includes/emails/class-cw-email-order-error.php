<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!class_exists('CW_Email_Order_Error')) :

    if (!class_exists("WC_Email")) {
        include(WC()->plugin_path() . "/includes/abstracts/abstract-wc-email.php");
    }

    class CW_Email_Order_Error extends WC_Email
    {
        private $order;
        /**
         * Constructor
         */
        function __construct()
        {
            $this->id = 'order_error';
            $this->title = "CodesWholesale Order Error";
            $this->description = __('Order complete emails are sent to the customer when the order is marked complete and usual indicates that the order has been shipped.', 'woocommerce');

            $this->heading = "CodesWholesale Order Error";
            $this->subject = "CodesWholesale Order Error";

            $this->template_html = 'emails/order-error.php';
            $this->template_plain = 'emails/plain/order-error.php';

            // Triggers for this email
            add_action( 'codeswholesale_order_error', array( $this, 'trigger' ) );

            // Other settings
            $this->heading_downloadable = "CodesWholesale Order Error";
            $this->subject_downloadable = "CodesWholesale Order Error";

            parent::__construct();

            $this->recipient = get_option( 'admin_email' );
        }

        public function trigger($args) {

            $this->object = $args['error'];
            $this->title = $args['title'];
            $this->order = $args['order'];

            $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
        }

        /**
         * get_heading function.
         *
         * @access public
         * @return string
         */
        function get_heading()
        {
            return $this->title.", order #" . $this->order->id;
        }

        /**
         * get_subject function.
         *
         * @access public
         * @return string
         */
        function get_subject()
        {
            return "CodesWholesale something went bad order #" . $this->order->id;
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
                'error' => $this->object,
                'order' => $this->order,
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
                'error' => $this->object,
                'order' => $this->order,
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

new CW_Email_Order_Error();