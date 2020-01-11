<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!class_exists('CW_Email_Customer_Completed_Order')) :

    if (!class_exists("WC_Email")) {
        include(WC()->plugin_path() . "/includes/abstracts/abstract-wc-email.php");
    }

    class CW_Email_Customer_Completed_Order extends WC_Email
    {

        private $keys;
        private $attachments;
        private $pre_orders_left;

        /**
         * Constructor
         */
        function __construct()
        {
            $this->id = 'customer_completed_order';
            $this->title = __('Completed order', 'woocommerce');
            $this->description = __('Order complete emails are sent to the customer when the order is marked complete and usual indicates that the order has been shipped.', 'woocommerce');

            $this->heading = __('Your order is complete', 'woocommerce');
            $this->subject = __('Your {site_title} order from {order_date} is complete', 'woocommerce');

            $this->template_html = 'emails/customer-completed-order.php';
            $this->template_plain = 'emails/plain/customer-completed-order.php';

            // Other settings
            $this->heading_downloadable = $this->get_option('heading_downloadable', __('Your order is complete - download your files', 'woocommerce'));
            $this->subject_downloadable = $this->get_option('subject_downloadable', __('Your {site_title} order from {order_date} is complete - download your files', 'woocommerce'));


            // Triggers for this email
            add_action('codeswholesale_send_keys_email', array($this, 'send_keys'));

            parent::__construct();
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
         * @param $sendTo
         */
        public function send_keys($args)
        {
            $order = $this->object = $args['order'];
            $keys = $this->title = $args['keys'];
            $attachments = $this->order = $args['attachments'];
            $this->pre_orders_left = $args['pre_orders_left'];

            $this->object = $order;

            $this->recipient = $this->object->billing_email;

            if ($this->pre_orders_left > 0) {
                $this->heading = __('Your order is partially complete', 'woocommerce');
                $this->subject = __('Your {site_title} order from {order_date} is partially complete', 'woocommerce');
            }

            $this->find[] = '{order_date}';
            $this->replace[] = date_i18n(wc_date_format(), strtotime($this->object->order_date));

            $this->find[] = '{order_number}';
            $this->replace[] = $this->object->get_order_number();

            $this->keys = $keys;
            $this->attachments = $attachments;
            $this->send($this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments());
        }

        /**
         * get_heading function.
         *
         * @access public
         * @return string
         */
        function get_heading()
        {
            if (!empty($this->object) && $this->object->has_downloadable_item())
                return apply_filters('woocommerce_email_heading_customer_completed_order', $this->format_string($this->heading_downloadable), $this->object);
            else
                return apply_filters('woocommerce_email_heading_customer_completed_order', $this->format_string($this->heading), $this->object);
        }

        /**
         * get_subject function.
         *
         * @access public
         * @return string
         */
        function get_subject()
        {
            if (!empty($this->object) && $this->object->has_downloadable_item())
                return apply_filters('woocommerce_email_subject_customer_completed_order', $this->format_string($this->subject_downloadable), $this->object);
            else
                return apply_filters('woocommerce_email_subject_customer_completed_order', $this->format_string($this->subject), $this->object);
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
                'order' => $this->object,
                'keys' => $this->keys,
                'pre_orders_left' => $this->pre_orders_left,
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
                'order' => $this->object,
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

new CW_Email_Customer_Completed_Order();