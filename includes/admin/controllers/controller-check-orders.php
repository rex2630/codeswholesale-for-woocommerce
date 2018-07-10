<?php
use CodesWholesale\Resource\Code;
use CodesWholesale\Resource\Order;
use CodesWholesale\Resource\Invoice;
use CodesWholesale\Util\Base64Writer;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!class_exists('CW_Controller_Check_orders')) :
    include_once(plugin_dir_path( __FILE__ ).'controller.php');

    /**
     * 
     */
    class CW_Controller_Check_orders extends CW_Controller
    {
        public $error;
        public $orders;
        public $from;
        public $to;
        
        public function __construct()
        {
            parent::__construct();
            
            add_action( 'wp_ajax_get_invoice_async', array($this, 'get_invoice_async'));
            add_action( 'wp_ajax_get_codes_by_order_async', array($this, 'get_codes_by_order_async'));
                       
        }
        
        public function init_view()
        {
            $this->init_account();
            
            include(plugin_dir_path( __FILE__ ).'../views/header.php');

            if (!CW()->get_codes_wholesale_client() instanceof \CodesWholesale\Client) {
                include_once(plugin_dir_path( __FILE__ ).'../views/view-blocked.php');
                return;
            }

            $this->from = ($_GET['from']) ? $_GET['from'] : date('Y-m-d');
            $this->to = ($_GET['to']) ? $_GET['to'] : date('Y-m-d');

            $dtFrom = \DateTime::createFromFormat('Y-m-d', $this->from);
            $dtTo = \DateTime::createFromFormat('Y-m-d', $this->to);

            if (false === $dtTo) {
                $dtTo = new \DateTime();
                $this->to = date('Y-m-d');
            }

            if (false === $dtFrom) {
                $this->from = date('Y-m-d');
            }

            $dtTo->add(new \DateInterval('P1D'));

            try {
                $this->orders = Order::getHistory($this->from, $dtTo->format('Y-m-d'));
            } catch (Exception $ex) {
                $this->error = $ex->getMessage();
            }

            include_once(plugin_dir_path( __FILE__ ).'../views/view-check-orders.php');
        }
        
        public function get_code($code) {
            return Code::get($code->getCodeId());
        }
        
        public function get_invoice_async()
        {
            $orderId = $_POST['id'];
            
            $this->createInvoiceFolder();
            $invoice = Invoice::get($orderId);

            Base64Writer::writeInvoice($invoice, $this->getInvoicePath() . '/' . $orderId);

            echo $this->getInvoiceHref($orderId, $invoice);

            wp_die();
        }

        public function get_codes_by_order_async()
        {
            $orderId = $_POST['id'];
            $codes = [];

            foreach (Order::getOrder($orderId)->getProducts() as $product) {
                foreach ($product->getCodes() as $code) {
                    $text = '';

                    if ($code->isPreOrder()) {
                        $text = "<b>Code has been pre-ordered!</b>" . " <br>";
                    }
                    if ($code->isText()) {
                        $text = "Text code to use: <b>" . $code->getCode() . "</b><br>";
                    }
                    if ($code->isImage()) {
                        Base64Writer::writeImageCode($code, "Cw_Attachments");
                        $path = wp_upload_dir()['baseurl'].'/../../wp-admin/Cw_Attachments/' . $code->getFileName();
                        $text = "Check in attachment file: <a target='_blank' href='".$path."'><b>" . $code->getFileName() . "</b></a><br>";
                    }

                    $codes[$product->getProductId()][] = $text;
                }
            }

            echo json_encode($codes);

            wp_die();
        }

        /**
         * @return string
         */
        private function getInvoicePath(): string
        {
            return wp_upload_dir()['basedir'] . '/' . 'invoice';
        }

        /**
         * @return string
         */
        private function getInvoiceHref(string $orderId, Invoice $invoice): string
        {
            return wp_upload_dir()['baseurl'] . '/invoice/' . $orderId .'/'. $invoice->getInvoiceNumber() . '.pdf';
        }

        /**
         * createInvoiceFolder
         */
        public function createInvoiceFolder()
        {
            if (!is_dir($this->getInvoicePath())) {
                $old = umask(0);
                mkdir($this->getInvoicePath(), 0777);
                umask($old);
            }
        }
    }

endif;

return new CW_Controller_Check_orders();