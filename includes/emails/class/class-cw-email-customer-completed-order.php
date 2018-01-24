<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!class_exists("CW_Email_Abstract")) {
    include("class-cw-email-abstract.php");
}

if (!class_exists('CW_Email_Customer_Completed_Order')) :

    class CW_Email_Customer_Completed_Order extends CW_Email_Abstract
    {

        private $keys;
        private $attachments;
        private $pre_orders_left;
        private $wp_email_pre_order;
        
        /**
         * Constructor
         */
        function __construct()
        {
            $this->wp_email = CW_EmailsConst::getCustomEmail(CW_EmailsConst::CW_EMAIL_TYPE_ORDER_COMPLETED);
            $this->wp_email_pre_order = CW_EmailsConst::getCustomEmail(CW_EmailsConst::CW_EMAIL_TYPE_PRE_ORDER_COMPLETED);
            
            $this->id           = $this->get_cw_id();
            $this->title        = $this->get_cw_title();
            $this->heading      = $this->get_cw_heading();
            $this->subject      = $this->get_cw_subject();
            $this->cw_content   = $this->get_cw_content();
           
            $this->description = __('Order complete emails are sent to the customer when the order is marked complete and usual indicates that the order has been shipped.', 'woocommerce');

            $this->template_html = 'emails/customer-completed-order.php';
            $this->template_plain = 'emails/plain/customer-completed-order.php';

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
        
        function overwriteObject() {
            $this->id           = $this->get_cw_id($this->wp_email_pre_order);
            $this->title        = $this->get_cw_title($this->wp_email_pre_order);
            $this->heading      = $this->get_cw_heading($this->wp_email_pre_order);
            $this->subject      = $this->get_cw_subject($this->wp_email_pre_order);
            $this->cw_content   = $this->get_cw_content($this->wp_email_pre_order);
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
            
            if($this->pre_orders_left > 0) {
                $this->overwriteObject();
            }
            
            $this->recipient = $this->object->billing_email;

            $this->find[] = '{order_date}';
            $this->replace[] = date_i18n(wc_date_format(), strtotime($this->object->order_date));
            $this->find[] = '{order_number}';
            $this->replace[] = $this->object->get_order_number();
            $this->find[] = '{title}';
            $this->replace[] = $this->title;

            $this->keys = $keys;
            $this->attachments = $attachments;

            $this->send($this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments());
        }

        
        public function getContentKeyLoop($atts) {
            $html = "";


            foreach ($this->keys as $key) :
                if("" != $html) {
                     $html .='<br/>';
                } 
                
                $html .= $atts['tittle'].'<br/>';
                $html = str_replace("{item_name}", $key['item']['name'], $html);

                /** @var \CodesWholesale\Resource\Code $code */
                foreach ($key['codes'] as $code) :
                    if($code->isText())
                    {
                         $html .= $atts['code'] . '<br />';
                    }

                    else if($code->isPreOrder()) {

                         $html .= $atts['preorder'] . '<br />';

                    }

                    else if ($code->isImage())
                    {
                         $html .= $atts['image'] . '<br />';
                    }

                    try {
                        $html = str_replace("{code}", $code->getCode(), $html);
                    } catch (\Exception $e) {
                    }

                    try {
                        $html = str_replace("{file_name}", $code->getFileName(), $html);
                    } catch (\Exception $e) {
                    }
                endforeach;
            endforeach;  

            return $html;
        }
        /**
         * get_content_html function.
         *
         * @access public
         * @return string
         */
        function get_content_html()
        {
            $shortcode = $this->get_cw_shortcode($this->cw_content, CW_EmailsConst::CW_EMAIL_SHORTCODE_LOOP_KEYS);

            if(null != $shortcode) {
                $atts = $this->getShortCodeAttributes($shortcode, CW_EmailsConst::CW_EMAIL_SHORTCODE_LOOP_KEYS);
                $html = $this->getContentKeyLoop($atts);
                
                $this->cw_content =  str_replace($shortcode, $html, $this->cw_content);
            }
            
            $this->cw_content =  str_replace("{order_number}", $this->object->get_order_number(), $this->cw_content);
            $this->cw_content =  str_replace("{site_title}", $this->get_blogname(), $this->cw_content);
            
            
            ob_start();
            
            cw_get_template($this->template_html, array(
                'content' =>$this->cw_content,
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

        public function get_heading() 
        {
            $content =$this->heading;
            $shortcode  = $this->get_cw_shortcode( $content,  CW_EmailsConst::CW_EMAIL_SHORTCODE_HEADING );

            if ( null !== $shortcode ) {
                return apply_filters('woocommerce_email_heading_'.$this->id, $this->format_string($this->getShortCodeTextByEmailParams($shortcode, CW_EmailsConst::CW_EMAIL_SHORTCODE_HEADING)), $this->object);

            } else {
                return apply_filters('woocommerce_email_heading_'.$this->id, $this->format_string($content), $this->object);
            }

        }

        public function get_subject() 
        {
            $content    = $this->subject;
            $shortcode  = $this->get_cw_shortcode( $content,  CW_EmailsConst::CW_EMAIL_SHORTCODE_SUBJECT );
            
            if (  null !== $shortcode ) {
                return apply_filters('woocommerce_email_subject_'.$this->id, $this->format_string($this->getShortCodeTextByEmailParams($shortcode, CW_EmailsConst::CW_EMAIL_SHORTCODE_SUBJECT)), $this->object);

            } else {
                return apply_filters('woocommerce_email_subject_'.$this->id, $this->format_string($content), $this->object);
            }
        }

        protected function getShortCodeTextByEmailParams ($content, $name) 
        {
            $atts = $this->getShortCodeAttributes($content, $name);

            if (!empty($this->object) && $this->object->has_downloadable_item()){
                $text = $atts['image'];
            } else {

                $text = $atts['code'];
            }

            return $text;
        }

        protected function getShortCodeAttributes(string $content , string $name) {
            $content = str_replace("[".$name, "", "$content");
            $content = str_replace("]", "", "$content");

            return shortcode_parse_atts($content);
        }
    }

endif;

new CW_Email_Customer_Completed_Order();