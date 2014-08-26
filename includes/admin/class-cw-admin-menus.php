<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!class_exists('CW_Admin_Menus')) :
    /**
     * CW_Admin_Menus Class
     */
    class CW_Admin_Menus
    {

        /**
         * Hook in tabs.
         */
        public function __construct()
        {
            // Add menus
            add_action('admin_menu', array($this, 'admin_menu'), 8);
        }

        /**
         * Add menu items
         */
        public function admin_menu()
        {
            add_menu_page('Codes Wholesale', 'Codes Wholesale', 'manage_options', 'codeswholesale', array($this, 'settings_page'), 'dashicons-admin-codeswholesale', 30);
            // add_submenu_page( 'codeswholesale', 'Check orders', 'Check orders', 'manage_options', 'cw-check-orders', array($this, 'check_orders'));
        }

        /**
         *
         */
        public function check_orders()
        {
            $term =  get_term_by( 'slug', 'completed', 'shop_order_status' );

            $customer_orders = get_posts(array(

                'post_type' => 'shop_order',

                'meta_key'    => '_codeswholesale_filled',
                'meta_value'  => 0,

                'tax_query' => array(
                    array(
                        'taxonomy' => 'shop_order_status',
                        'field' => 'term_id',
                        'terms' => $term->term_id)
                ),

                'numberposts' => -1

            ));

            foreach($customer_orders as $k => $v)
            {
                $order = new WC_Order( $customer_orders[ $k ]->ID );

                $a = new CW_SendKeys();
                $a->send_keys_for_order($customer_orders[ $k ]->ID);

                echo $order->order_id;
                echo 'Order by '.$order->billing_first_name.' '.$order->billing_last_name . "<br />";
                echo $order->needs_payment(). "<br />";
            }
        }

        /**
         * Init the settings page
         */
        public function settings_page()
        {

            include("class-cw-admin-settings-vo.php");

            if (isset($_POST["cw_env_type"])) {
                $this->updateSettings();
            }

            if(isset($_POST['cw_complete_order'])) {
                update_option(CodesWholesaleConst::AUTOMATICALLY_COMPLETE_ORDER_OPTION_NAME, $_POST['cw_complete_order']);
            } else {
                update_option(CodesWholesaleConst::AUTOMATICALLY_COMPLETE_ORDER_OPTION_NAME, CodesWholesaleAutoCompleteOrder::NOT_COMPLETE);
            }

            $account = null;
            $error = null;

            try{
                CW()->refreshCodesWholesaleClient();
                $account = CW()->getCodesWholesaleClient()->getAccount();
            } catch(Exception $e) {
                $error = $e;
            }

            $auto_order_complete = get_option(CodesWholesaleConst::AUTOMATICALLY_COMPLETE_ORDER_OPTION_NAME);
            $settings = CW_Settings_Vo::fromOption(get_option(CodesWholesaleConst::SETTINGS_CODESWHOLESALE_PARAMS_NAME));

            ?>

            <div class="wrap">
                <h2>CodesWholesale Settings</h2>

                <form method="post">

                    <div id="poststuff">

                        <div id="post-body" class="metabox-holder columns-2">
                            <div id="post-body-content">

                                <table class="form-table">

                                    <tr>
                                        <th scope="row"><label for="cw_env_type">Use environment:</label></th>
                                        <td>

                                            <fieldset>

                                                <legend class="screen-reader-text"><span>Use environment</span></legend>

                                                <label title="Sandbox">
                                                    <input type="radio" name="cw_env_type" value="0" class="cw_env_type"
                                                           <?php if ($settings->getClientEndpoint() == CodesWholesale\CodesWholesale::SANDBOX_ENDPOINT) : ?>checked<?php endif; ?> />
                                                    <span>Sandbox</span>
                                                </label><br/>

                                                <label title="Live">
                                                    <input type="radio" name="cw_env_type" value="1" class="cw_env_type"
                                                           <?php if ($settings->getClientEndpoint() == CodesWholesale\CodesWholesale::LIVE_ENDPOINT) : ?>checked<?php endif; ?> />
                                                    <span>Live</span>
                                                </label>

                                            </fieldset>

                                        </td>
                                    </tr>

                                    <tr class="cw-settings-client">
                                        <th scope="row"><label for="cw_client_id">API Client id:</label></th>
                                        <td>
                                            <input name="cw_client_id" type="text" id="cw_client_id"
                                                   value="<?php echo $settings->getClientId(); ?>"
                                                   class="regular-text"/>

                                            <p class="description">
                                                Get client id from <a href="https://app.codeswholesale.com"
                                                                      target="_blank">CodesWholesale</a>
                                                platform under "Web Api" tab.
                                            </p>
                                        </td>
                                    </tr>

                                    <tr class="cw-settings-client">
                                        <th scope="row"><label for="cw_client_secret">API Client secret:</label></th>
                                        <td>
                                            <input name="cw_client_secret" type="text" id="cw_client_secret"
                                                   value="<?php echo $settings->getClientSecret(); ?>"
                                                   class="regular-text"/>

                                            <p class="description">
                                                Get client secret from <a href="https://app.codeswholesale.com"
                                                                          target="_blank">CodesWholesale</a>
                                                platform under "Web Api" tab.
                                            </p>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th scope="row">Orders</th>
                                        <td>
                                            <fieldset>
                                                <legend class="screen-reader-text"><span>Automatically complete order when payment is received</span></legend>
                                                <label for="cw_complete_order">
                                                    <input name="cw_complete_order"
                                                           type="checkbox"
                                                           id="cw_complete_order"
                                                           value="1"
                                                           <?php if ($auto_order_complete == 1) : ?>checked<?php endif; ?> />
                                                    Automatically complete order when payment is received
                                                </label>
                                            </fieldset>
                                        </td>
                                    </tr>

                                </table>


                                <p class="submit">
                                    <input type="submit" name="submit" id="submit" class="button button-primary"
                                           value="Save Changes">
                                </p>

                            </div>

                            <div id="postbox-container-1" class="postbox-container">

                                <div id="woocommerce_dashboard_status" class="postbox ">

                                    <div class="handlediv" title="Click to toggle"><br></div><h3 class="hndle"><span>Integration status</span></h3>
                                    <div class="inside">
                                        <ul>

                                            <?php if($error) : ?>

                                                <li class="updated">
                                                    <p><strong>Connection failed.</strong></p>
                                                </li>

                                                <li>
                                                    <b>Error:</b> <?php echo $error->getMessage(); ?>
                                                </li>

                                            <?php endif; ?>

                                            <?php if($account) : ?>

                                                <li class="updated">
                                                        <p><strong>Successfully connected.</strong></p>
                                                </li>
                                                <li>
                                                    <?php  echo $account->getFullName(); ?>
                                                </li>
                                                <li>
                                                    <?php  echo $account->getEmail(); ?>
                                                </li>

                                                <li>
                                                    <b>Money to use:</b> 
                                                    <?php echo "€". number_format($account->getTotalToUse(), 2, '.', ''); ?>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </form>

                <script type="text/javascript">

                    jQuery(".cw_env_type").change(function (val) {
                        var envType = jQuery("input[name='cw_env_type']:checked").val();
                        if (envType == 0) {
                            jQuery(".cw-settings-client").hide();
                            jQuery(".cw-settings-client input").val('');
                        } else {
                            jQuery(".cw-settings-client").show();
                        }
                    });

                    jQuery(".cw_env_type").change();

                </script>

            </div>

        <?php
        }

        private function updateSettings()
        {
            $settings = new CW_Settings_Vo();

            if (isset($_POST["cw_env_type"])) {
                $settings->setEnvType($_POST["cw_env_type"]);
            }

            // change to test endpoints
            if ($settings->isSandbox()) {
                $this->resetToSandbox($settings);
            } // change to live endpoints
            else if ($settings->isLive()) {

                $error = false;

                if (!isset($_POST['cw_client_id']) || empty($_POST['cw_client_id'])) {

                    $error = true;

                    ?>
                    <div id="cw-setting-error-invalid_client_id" class="error cw-settings-error">
                        <p><strong>To go live client id is required.</strong></p>
                    </div>
                <?php
                }

                if (!isset($_POST['cw_client_secret']) || empty($_POST['cw_client_secret'])) {

                    $error = true;

                    ?>
                    <div id="cw-setting-error-invalid_client_secret" class="error cw-settings-error">
                        <p><strong>To go live client secret is required.</strong></p>
                    </div>
                <?php
                }

                if (!$error) {
                    $settings->setClientId($_POST['cw_client_id']);
                    $settings->setClientSecret($_POST['cw_client_secret']);
                    $settings->setClientEndpoint(CodesWholesale\CodesWholesale::LIVE_ENDPOINT);
                } else {
                    $this->resetToSandbox($settings);
                }
            }

            update_option(CodesWholesaleConst::SETTINGS_CODESWHOLESALE_PARAMS_NAME, $settings->toOption());
        }

        /**
         * @param $settings
         */
        private function resetToSandbox($settings)
        {
            $settings->setClientId(CW_Install::$default_client_id);
            $settings->setClientSecret(CW_Install::$default_client_secret);
            $settings->setClientEndpoint(CW_Install::$default_env);
        }
    }

endif;

return new CW_Admin_Menus();