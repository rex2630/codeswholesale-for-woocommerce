<?php session_start(); ?>
<div class="wrap codeswholesale">
    <div id="cst-title"><img src="<?php  echo $this->plugin_img() ?>" id="nav-img"></div>
    <h1 class="wp-heading-inline"><?php _e('Settings', 'woocommerce') ?></h1>
    
    <form action="options.php" method="POST">
        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
                <div id="postbox-container-1" class="postbox-container">

                    <div id="postconnectionstatu" class="postbox">
                        <h2 class="hndle ui-sortable-handle">
                            <span><?php _e('Connection status', 'woocommerce')?></span>
                        </h2>
                        <div class="misc-pub-section">
                            <ul id="status-ul">

                                <?php if ($error) : ?>

                                    <li class="updated">
                                        <p class="failed"><?php _e('Connection failed.', 'woocommerce')?></p>
                                    </li>

                                    <li>
                                        <b><?php _e('Error', 'woocommerce')?>:</b> <?php echo $error->getMessage(); ?>
                                    </li>

                                <?php endif; ?>

                                <?php if ($account) : ?>

                                    <li class="updated">
                                        <p class="success"><?php _e('Successful', 'woocommerce')?></p>
                                    </li>
                                    <li>
                                        <b>Account:</b>
                                        <?php echo $account->getFullName(); ?>
                                    </li>
                                    <li>
                                        <b><?php _e('Email address', 'woocommerce'); ?>:</b>
                                        <?php echo $account->getEmail(); ?>
                                    </li>
                                    <li>
                                        <strong><?php _e('Account balance', 'woocommerce'); ?>:</strong>
                                        <?php echo "€" . number_format($account->getTotalToUse(), 2, '.', ''); ?>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div id="post-body-content" style="position: relative;">
                    <?php
                        if ( isset( $_REQUEST['settings-updated'] ) ) {
                            $this->checkEnvironment();

                            echo '<div class="updated inline"><p>' . __( 'Your changes have been saved.', 'codeswholesale-for-woocommerce' ) . '</p></div>';
                            try {
								if($this->isChangedPriceSettings()) {
								   ExecManager::exec(ExecManager::getPhpPath(), 'update-products-price.php');
									echo '<div class="updated inline"><p>' . __( 'Your products price have been updated.', 'codeswholesale-for-woocommerce' ) . '</p></div>';
								}
                              } catch (Exception $ex) {
                                echo '<div class="error inline"><p><strong>' . __('Warning!') . '</strong> ' . $ex->getMessage() . '</p></div>';
                            }
							
							$this->clearSettingsSession();
                        }

                        settings_fields('cw-settings-group');
                        do_settings_sections('cw_options_page_slug');
                        submit_button();
                    ?>
                </div>
            </div>

        </div>
    </form>
</div>

<script type="text/javascript">

    var firstGo = 0;
    jQuery(".cw_env_type").change(function (val) {

        var envType = jQuery(".cw_env_type:checked").val();

        if (envType == 0) {

            jQuery(".form-table tr:eq(1)").hide();
            jQuery(".form-table tr:eq(1) input").val('<?php echo CW_Install::$default['api_client_id']; ?>');
            jQuery(".form-table tr:eq(1) input").removeAttr('required');
            jQuery(".form-table tr:eq(2)").hide();
            jQuery(".form-table tr:eq(2) input").val('<?php echo CW_Install::$default['api_client_secret']; ?>');
            jQuery(".form-table tr:eq(2) input").removeAttr('required');
            jQuery(".form-table tr:eq(3)").hide();
            jQuery(".form-table tr:eq(3) input").val('<?php echo CW_Install::$default['api_client_singature']; ?>');
            jQuery(".form-table tr:eq(3) input").removeAttr('required');

        } else {
            jQuery(".form-table tr:eq(1) input").attr("required", true);
            jQuery(".form-table tr:eq(2) input").attr("required", true);
            jQuery(".form-table tr:eq(3) input").attr("required", true);
            jQuery(".form-table tr:eq(1)").show();
            jQuery(".form-table tr:eq(2)").show();
            jQuery(".form-table tr:eq(3)").show();

            if (firstGo > 1) {
                jQuery(".form-table tr:eq(1) input").val('');
                jQuery(".form-table tr:eq(2) input").val('');
                jQuery(".form-table tr:eq(3) input").val('');
            }

        }

        firstGo++;
    });

    jQuery(".cw_env_type").change();
    
    var spreadType = 'input[name="cw_options[spread_type]"]';
    var spreadValue = 'input[name="cw_options[spread_value]"]';
   
    jQuery(spreadType).change(function() {
        setStepToSpreadValue(jQuery(this).val());
    });
    jQuery( document ).ready(function() {
        setStepToSpreadValue(jQuery(spreadType+":checked").val());
    });
    
    function setStepToSpreadValue(selected) {
        if("0" === selected) {
            jQuery(spreadValue).attr( "step", "any" );
        } else {
            jQuery(spreadValue).attr( "step", "1" );
        }      
    }
</script>
