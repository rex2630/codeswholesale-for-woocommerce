<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

use CodesWholesaleFramework\Provider\CurrencyProvider;
use CodesWholesale\Client;

if (!class_exists('CW_Controller_Settings')) :
    include_once(plugin_dir_path( __FILE__ ).'controller.php');

    /**
     * 
     */
    class CW_Controller_Settings extends CW_Controller
    {
        /**
         * @var array
         */
        private $admin_options = array(

            'environment' =>

                array(
                    'label' => 'Environment',
                    'renderer' => 'render_environment',
                    'class' => 'cst-label'
                ),

            'api_client_id' =>

                array(
                    'label' => 'API client ID',
                    'description' => 'Generate client ID under API tab on CodesWholesale.',
                    'renderer' => 'render_options_text',
                    'class' => 'cst-label'
                ),

            'api_client_secret' =>

                array(
                    'label' => 'API client secret',
                    'description' => 'Generate client secret under API tab on CodesWholesale.',
                    'renderer' => 'render_options_text',
                    'class' => 'cst-label'
                ),
            'api_client_singature' =>

                array(
                    'label' => 'API client signature',
                    'description' => 'Get client signature under API tab on CodesWholesale.',
                    'renderer' => 'render_options_text',
                    'class' => 'cst-label'
                ),

            'spread_type' =>

                array(
                    'label' => 'Profit margin type',
                    'description' => 'Select your profit margin type',
                    'renderer' => 'render_spread_type',
                    'class' => 'cst-input cst-label'
                ),


            'spread_value' =>

                array(
                    'label' => 'Profit margin value',
                    'description' => 'Enter a value depending on the margin type chosen.  The value will be added to CodesWholesale price and will apply to all products.',
                    'renderer' => 'render_options_number_field',
                    'class' => 'cst-label'
                ),

            'currency' =>
                array(
                    'label' => 'Currency',
                    'description' => 'Select a currency that corresponds to your store.',
                    'renderer' => 'render_currency_select',
                    'class' => 'cst-select cst-label'
                ),
            
            CodesWholesaleConst::AUTOMATICALLY_COMPLETE_ORDER_OPTION_NAME =>

                array(
                    'label' => 'Auto-complete orders',
                    'description' => 'Get orders automatically completed upon payment. This option only works with IPN payments (eg. PayPal).',
                    'renderer' => 'render_orders_checkbox',
                    'class' => 'cst-label'
                ),

            CodesWholesaleConst::ALLOW_PRE_ORDER_PROP_NAME =>

                array(
                    'label' => 'Pre-order products',
                    'description' => 'Enable or disable purchasing pre-order products.',
                    'renderer' => 'render_orders_checkbox',
                    'class' => 'cst-label'
                ),

            CodesWholesaleConst::AUTOMATICALLY_IMPORT_NEWLY_PRODUCT_OPTION_NAME =>

                array(
                    'label' => 'Automatic product import',
                    'description' => 'Import newly added products automatically.',
                    'renderer' => 'render_orders_checkbox',
                    'class' => 'cst-label'
                ),

            CodesWholesaleConst::NOTIFY_LOW_BALANCE_VALUE_OPTION_NAME =>

                array(
                    'label' => 'Low-balance notification',
                    'description' => 'If your account balance on CodesWholesale reaches below the value entered, you will receive an email notification.',
                    'renderer' => 'render_options_number_field',
                    'class' => 'cst-input cst-label'
                ),

            CodesWholesaleConst::RISK_SCORE_PROP_NAME =>

                array(
                    'label' => 'Risk score value',
                    'description' => 'The order will be suspended if the client’s risk score is equal or higher than the value entered. The recommended value is 1.5.',
                    'renderer' => 'render_options_number_field',
                    'class' => 'cst-label'
                ),

            CodesWholesaleConst::DOUBLE_CHECK_PRICE_PROP_NAME =>

                array(
                    'label' => 'Double-check price',
                    'description' => 'Compare your stock price with that of CodesWholesale.',
                    'renderer' => 'render_orders_checkbox',
                    'class' => 'cst-label'
                ),


            CodesWholesaleConst::HIDE_PRODUCT_WHEN_DISABLED_OPTION_NAME =>
                
                array(
                    'label' => 'Hide products',
                    'description' => 'Hide products that are disabled on CodesWholesale.',
                    'renderer' => 'render_orders_checkbox',
                    'class' => 'cst-label'
                ),

            CodesWholesaleConst::PREFERRED_LANGUAGE_FOR_PRODUCT_OPTION_NAME =>
                array(
                    'label' => 'Product description language',
                    'description' => 'Select a language of product descriptions.',
                    'renderer' => 'render_preferred_language_select',
                    'class' => 'cst-select cst-label'
                ),

            'product_price_charmer' =>

                  array(
                    'label' => 'Charm pricing',
                    'description' => 'Make all your prices more attractive to your customers. See the examples below:'.
                        '<ul>
                          <li><p class="description">10.01 - 10.29 range &#10140; 10.29</p></li>
                          <li><p class="description">10.30 to 10.49 range &#10140; 10.49</p></li>
                          <li><p class="description">10.50 to 10.79 range &#10140; 10.79</p></li>
                          <li><p class="description">10.80 to 10.99 range &#10140; 10.99</p></li>
                          <li><p class="description">10.00 &#10140; 9.99</p></li>
                        </ul>',
                    'renderer' => 'render_orders_checkbox',
                    'class' => 'cst-label'
                ),
        );

        /**
         * @var CurrencyProvider
         */
        private $currencyProvider;

        public function __construct()
        {
            parent::__construct();

            $this->currencyProvider = new CurrencyProvider(new WP_DbManager());

            /**
             * For admin only
             */
            if (is_admin()) {
                // General plugin setup
                add_action('admin_init', array($this, 'admin_settings_construct'));
                
                // ajax actions
                add_action( 'wp_ajax_get_currency_rate', array($this, 'get_currency_rate'));
            }
        }
        
        public function get_currency_rate() 
        {
            $id = $_POST['id'];

            try {
                $result = $this->currencyProvider->getRate($id);
            } catch (Exception $ex) {
                $result = $ex->getMessage();
            }

            echo json_encode($result);

            wp_die();
        }

        public function init_view() {
            $this->init_account();
            
            include(plugin_dir_path( __FILE__ ).'../views/header.php');
            
            include_once(plugin_dir_path( __FILE__ ).'../views/view-settings.php');
        }
                
        public function updateCwOptions($options)
        {
            session_start();
            $_SESSION['cw_options'] = $options;
            (new \CodesWholesaleFramework\Database\Repositories\AccessTokenRepository(new WP_DbManager()))->deleteToken();

            if (1 == $options['environment'] && 0 == $_REQUEST['cw_options']['environment']) {
                if (CW()->isClientCorrect()) {
                    ApiClient::sendActivity(ApiClient::API_DISCONNECTED);
                }
            }
        }
        
        /**
         *
         */
        public function admin_settings_construct()
        {
            register_setting('cw-settings-group', 'cw_options');
            add_settings_section('cw-settings-section', '', array($this, 'section_one_callback'), 'cw_options_page_slug');

            add_action('update_option_cw_options', array($this, 'updateCwOptions'));

            $options = $this->get_options();

            foreach ($this->admin_options as $option_key => $option) {

                add_settings_field($option_key, $option['label'], array($this, $option['renderer']), 'cw_options_page_slug', 'cw-settings-section',
                    array(
                        'name' => $option_key,
                        'options' => $options,
                        'class' => $option['class']
                    ));
            }
        }

        public function checkEnvironment()
        {
            if (array_key_exists('cw_options', $_SESSION)) {
                if ($this->isChangedTokenOnLive()) {
                    if (true === CW()->isClientCorrect()) {
                        ApiClient::sendActivity(ApiClient::CONNECTED_TO_WOOCOMMERCE);
                    }
                }
            }
        }

        public function detectedChangeEnvironment() {
            $options = $this->get_options();
            $sessionOptions = $_SESSION['cw_options'];

            return $sessionOptions['environment'] !== $options['environment'];
        }

        public function clearSettingsSession() {
            unset($_SESSION['cw_options']);
        }
		
        public function isChangedPriceSettings() {
            $options        = $this->get_options();
            $sessionOptions = $_SESSION['cw_options'];
            $changed        = false;
            
            if($sessionOptions) {
                if($sessionOptions['spread_type'] != $options['spread_type']) {
                    $changed = true;
                }

                if($sessionOptions['spread_value'] != $options['spread_value']) {
                    $changed = true;
                }

                if($sessionOptions['product_price_charmer'] != $options['product_price_charmer']) {;
                    $changed = true;
                }

                if($sessionOptions['currency'] != $options['currency']) {
                    $changed = true;
                }  
            }

            return $changed;
        }
		
        protected function isChangedTokenOnLive()
        {
            $options = $this->get_options();
            $sessionOptions = $_SESSION['cw_options'];

            $changedToLive = 0 == $sessionOptions['environment'] && 1 == $options['environment'];
            $changedClientId = $sessionOptions['api_client_id'] !== $options['api_client_id'];
            $changedClientSecret = $sessionOptions['api_client_secret'] !== $options['api_client_secret'];

            return $changedToLive || (1 == $options['environment'] && ($changedClientId || $changedClientSecret));
        }

        /*
         * Render a text field
         *
         * @access public
         * @param array $args
         * @return void
         */
        public function render_options_text($args = array())
        {
            printf(
                '<input type="text" id="%s" name="cw_options[%s]" value="%s" pattern="[^\s]+" title="White spaces are not allowed, please double check the values." /><p class="description cst-desc">%s</p>',
                $args['name'],
                $args['name'],
                $args['options'][$args['name']],
                $this->admin_options[$args['name']]['description']
            );
        }
        /*
         * Render a text field
         *
         * @access public
         * @param array $args
         * @return void
         */
        public function render_options_number_field($args = array())
        {
            printf(
                '<input type="number" step="any" min="0" id="%s" name="cw_options[%s]" value="%s" /><p class="description cst-desc">%s</p>',
                $args['name'],
                $args['name'],
                $args['options'][$args['name']],
                $this->admin_options[$args['name']]['description']
            );
        }
        /**
         * @param array $args
         */
        public function render_orders_checkbox($args = array())
        {
            printf(
                '<input type="checkbox" id="%s" name="cw_options[%s]" value="1" %s /><p class="description cst-desc">%s</p>',
                $args['name'],
                $args['name'],
                (isset($args['options'][$args['name']]) && $args['options'][$args['name']]) == 1 ? "checked" : "",
                $this->admin_options[$args['name']]['description']
            );
        }

        public function render_currency_select($args = array())
        {
            ?>
            <select id="currency" name="cw_options[<?php echo $args['name'] ?>]">
                <?php foreach ($this->currencyProvider->getAllCurrencies($args['options']['currency']) as $currency): ?>
                    <option  value="<?php echo $currency->getCurrency(); ?>"<?php if ($args['options']['currency'] == $currency->getCurrency()) {  echo ' selected="selected"'; } ?>>
                        <?php echo $currency->getCurrency() . ' - ' . $currency->getCurrencyName(); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php
        }
        
        public function render_preferred_language_select($args = array())
        {
            try {
                $regions = CW()->get_codes_wholesale_client()->getTerritories();  
                $values = [];
                
                foreach ($regions as $item) {
                    $values[$item->getTerritory()] = $item->getTerritory();
                }
            } catch (\Error $ex) {
                $selected = $args['options'][CodesWholesaleConst::PREFERRED_LANGUAGE_FOR_PRODUCT_OPTION_NAME];
                $values[$selected] = $selected;
            }   
            ?>
            <select id="currency" name="cw_options[<?php echo $args['name'] ?>]">
                <?php foreach ($values as $key => $value): ?>
                    <option value="<?php echo $key; ?>"<?php if ($args['options'][CodesWholesaleConst::PREFERRED_LANGUAGE_FOR_PRODUCT_OPTION_NAME] == $key) { ?> selected="selected" <?php } ?>>
                        <?php echo $value; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <p class="description cst-desc"><?php echo $this->admin_options[$args['name']]['description'] ?></p>
            <?php  
        }

        /*
         * Render a text field
         *
         * @access public
         * @param array $args
         * @return void
         */
        public function render_environment($args = array())
        {
            ?>
            <label title="Sandbox">
                <input type="radio" name="cw_options[<?php echo $args['name'] ?>]" value="0"
                       class="cw_env_type" <?php if ($args['options'][$args['name']] == 0) { ?> checked <?php } ?>>
                <span class="input-desc">Sandbox</span>
            </label> <br/> <br/>
            <label title="Live" style="padding-top:10px;">
                <input type="radio" name="cw_options[<?php echo $args['name'] ?>]" value="1"
                       class="cw_env_type" <?php if ($args['options'][$args['name']] == 1) { ?> checked <?php } ?>>
                <span class="input-desc">Live</span>
            </label>
            <?php
        }
        
        /*
         * Render spread type
         *
         * @access public
         * @param array $args
         * @return void
         */
        public function render_spread_type($args = array())
        {
            ?>
            <label title="Flat">
                <input type="radio" name="cw_options[<?php echo $args['name'] ?>]" value="0"
                       class="" <?php if ($args['options'][$args['name']] == 0) { ?> checked <?php } ?>>
                <span class="input-desc"><?php _e('Amount', 'woocommerce') ?></span>
            </label> <br/> <br/>
            <label title="Percent" style="padding-top:10px;">
                <input type="radio" name="cw_options[<?php echo $args['name'] ?>]" value="1"
                       class="" <?php if ($args['options'][$args['name']] == 1) { ?> checked <?php } ?>>
                <span class="input-desc"><?php _e('Percentage', 'woocommerce') ?></span>
            </label>
            <?php
        }
    }
    
endif;

return new CW_Controller_Settings();
