<?php session_start(); ?>

<div class="wrap codeswholesale">
    
    <div id="poststuff">
      
        <div id="post-body" class="metabox-holder columns-2">
            <aside id="postbox-container-1" class="postbox-container">

                <div id="cw-create-account" class="postbox cw-content">
                    <h2 class="cw-title">
                        <i class="fas fa-user cw-icon-green"></i>
                        <?php _e( 'Create new account', 'codeswholesale-for-woocommerce' ); ?>
                    </h2>
                    <div class="misc-pub-section">
                        <p class="cw-padding-bottom">
                            <?php _e( 'We are always looking to improve our services. Let us know what you think about our plugin.', 'codeswholesale-for-woocommerce' ) ?>
                        </p>
                        <button class="cw-btn cw-btn-success cw-full"><?php _e( 'Go to registry', 'codeswholesale-for-woocommerce' ); ?></button>
                    </div>
                </div>

                <div id="cw-leave-your-feedback" class="postbox cw-content">
                    <h2 class="cw-title">
                        <i class="fas fa-clipboard-list cw-icon-green"></i>
                        <?php _e( 'Your feedback', 'codeswholesale-for-woocommerce' ); ?>
                    </h2>
                    <div class="misc-pub-section">
                        <p  class="cw-padding-bottom">
                            <?php _e( 'We are always looking to improve our services. Let us know what you think about our plugin.', 'codeswholesale-for-woocommerce' ) ?>
                        </p>
                        <button class="cw-btn cw-btn-success cw-full"><?php _e( 'Leave feedback', 'codeswholesale-for-woocommerce' ); ?></button>
                    </div>
                </div>
            </aside>
            <div id="post-body-content" class="cw-content" style="position: relative;">
                <h1 class="wp-heading-inline cw-title">
                    <i class="fas fa-cog cw-icon-green"></i>
                    <?php _e('Main settings', 'woocommerce') ?>
                </h1>
                <form id="cw-main-settings" class="cw-form" action="options.php" method="POST">
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
                        
                    ?>
                    
                    <button type="submit" class="cw-btn cw-btn-success">
                        <?php  _e( 'Save Changes' ); ?>
                        <i class="fas fa-save cw-text-margin-left"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    var firstGo = 0;
    
    jQuery( document ).ready(function() {
        setStepToSpreadValue(jQuery(spreadType+":checked").val());
        initCurrencyDescription();
        initCwView();
    });
    
    
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

    jQuery('#currency').change(function() {
        initCurrencyDescription();
    });
    
    function initCwView() {
        jQuery('#cw-main-settings br').remove();
    }
    
    function setStepToSpreadValue(selected) {
        if("0" === selected) {
            jQuery(spreadValue).attr( "step", "any" );
        } else {
            jQuery(spreadValue).attr( "step", "1" );
        }      
    }
    
    
    function initCurrencyDescription() {
        var container = jQuery('#currency').parent('td');
        var currency = jQuery('#currency').val();
        
        if (currency === 'EUR') {
            container.find(".description").remove();
        } else {
            jQuery.post(ajaxurl, {
                'action': 'get_currency_rate',
                'id': currency,
            }, function(response) {
                
                var html = 'EUR - ' + currency + '\u0020 ('+ JSON.parse(response) +')';
                
                if ((container.find(".description").length > 0)){
                    container.find(".description").html(html);
                } else {
                    container.append('<span class="description cw-text-margin-left">'+html+'</span>');
                }                
            }); 
        }
    }
</script>
