<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!class_exists('CW_Email_Order_Error')) :

    class CW_Email_Order_Error extends CW_Email_Abstract
    {
        private $order;
        /**
         * Constructor
         */
        function __construct()
        {
            $this->wp_email = CW_EmailsConst::getCustomEmail(CW_EmailsConst::CW_EMAIL_TYPE_ORDER_ERROR);
            
            $this->id           = $this->get_cw_id();
            $this->title        = $this->get_cw_title();
            $this->heading      = $this->get_cw_heading();
            $this->subject      = $this->get_cw_subject();
            $this->cw_content   = $this->get_cw_content();
            
            
            $this->description = __('Order complete emails are sent to the customer when the order is marked complete and usual indicates that the order has been shipped.', 'woocommerce');

            $this->template_html = 'emails/order-error.php';
            $this->template_plain = 'emails/plain/order-error.php';

            // Triggers for this email
            add_action( 'codeswholesale_order_error', array( $this, 'trigger' ) );

            parent::__construct();

            $this->recipient = get_option( 'admin_email' );
        }

        public function trigger($args) {

            $this->object = $args['error'];
            $this->title = $args['title'];
            $this->order = $args['order'];

            $this->find[]    = '{order_id}';
            $this->replace[] = $$this->order->id;
            
            $this->find[]    = '{title}';
            $this->replace[] = $this->get_cw_title();
            
            $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
        }
        
        /**
         * get_content_html function.
         *
         * @access public
         * @return string
         */
        function get_content_html()
        {
            $this->cw_content =  str_replace("{error_class}", get_class($this->object), $this->cw_content);
            $this->cw_content =  str_replace("{error_message}",$this->object->getMessage(), $this->cw_content);
            $this->cw_content =  str_replace("{stack_trace}", $this->object->getTraceAsString(), $this->cw_content);
            
            ob_start();

            cw_get_template($this->template_html, array(
                'content' =>$this->cw_content,
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