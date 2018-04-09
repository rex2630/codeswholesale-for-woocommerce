<div>
    <p>
        <?php 
            _e("Import all products in bulk instead of adding every product manually. It takes between 20-40 minutes to import all products to your store." , "woocommerce");
        ?>
    </p>
</div>


<?php
    $afterStartImportStyle="display:none";
    $beforeImportingProductsStyle="display:block";
    
    if($this->import_in_progress) {
        $afterStartImportStyle="display:block";
        $beforeImportingProductsStyle="display:none";
    }
?>

<div id="beforeImportingProducts" style="<?php echo $beforeImportingProductsStyle ?>">
    <h2><?php _e('Import products', 'woocommerce') ?>:</h2>
    <div>
        <div style="display: inline-block; padding:0 30px 20px 0;">
            <h4>
                <input checked type="radio" value="<?php echo WP_ImportPropertyRepository::FILTERS_TYPE_ALL ?>" name="import_products_type" id="import_type_all" />
                <label for="import_type_all"><?php _e('All products available', 'woocommerce') ?></label>          
            </h4>
 
        </div>
        <div style="display: inline-block; padding:0 30px 20px 0;">
            <h4>
                <input type="radio" value="<?php echo WP_ImportPropertyRepository::FILTERS_TYPE_BY_FILTERS ?>" name="import_products_type" id="import_type_by_filter" />
                <label for="import_type_by_filter"><?php _e('Apply filters first', 'woocommerce') ?></label>      
            </h4>
        </div>
    </div>
    <form id="import_all_products_form">
        <div id="import_filters" style="display:none">
            <hr>
            <div>
                <h4><?php _e('Import products not older than', 'woocommerce') ?>:</h4>
                    <?php 
                       $in_stock_days_ago_options = array(
                           '30'  =>  '30 days', 
                           '60'  =>  '60 days',
                           ''    =>  'Import all'
                       );
                       
                       $this->form_element_generator->codeswholesale_wp_radio(
                            array(
                                'id' => 'cwh_import_propery_in_stock_days_ago',
                                'options' => $in_stock_days_ago_options,
                                'value' => '',
                                'wrapper_class' => 'codeswholesale-inline-block'
                            )
                       );
                    ?>
            </div>
            <hr>
            <div>
                <h4><?php _e('Import products by platform', 'woocommerce') ?>:</h4>
                    <?php 
                        $this->form_element_generator->codeswholesale_wp_checkboxes( 
                            array (
                                'name'    => 'cwh_import_propery_platform',
                                'wrapper_class' => 'codeswholesale-inline-block',
                                'options' => $this->getPlatformOptions(),
                                'checked' => 'checked'
                            )
                        );
                    ?>
            </div>
            <hr>
            <div>
                <h4><?php _e('Import products by region', 'woocommerce') ?>:</h4> 
                    <?php 
                        $this->form_element_generator->codeswholesale_wp_checkboxes( 
                            array (
                                'name'    => 'cwh_import_propery_region',
                                'wrapper_class' => 'codeswholesale-inline-block',
                                'options' => $this->getRegionOptions(),
                                'checked' => 'checked'
                            )
                        );
                    ?>
            </div>
            <hr>
            <div>
                <h4><?php _e('Import products by language', 'woocommerce') ?>:</h4> 
                    <?php 
                        $this->form_element_generator->codeswholesale_wp_checkboxes( 
                            array (
                                'name'    => 'cwh_import_propery_language',
                                'wrapper_class' => 'codeswholesale-inline-block',
                                'options' => $this->getLanguageOptions(),
                                'checked' => 'checked'
                            )
                        );
                    ?>
            </div>    
            <hr>
        </div>

        <br>
        <input type="submit" name="submit" id="submit_import" class="button button-primary" value="<?php _e('Import products', 'woocommerce') ?>">
    </form>  
</div>

<div id="preparingImport" style="display: none">
    <?php _e('Preparing import...', 'woocommerce'); ?>
</div>

<div id="afterStartImport" style="<?php echo $afterStartImportStyle ?>">
    <div class="content"><?php _e("The import is in progress", "woocommerce") ?></div>
</div>


<script>
    var $ = jQuery.noConflict();
    
    $(document).ready(function () {
        toggleFilters();

        $('input[name="import_products_type"]').change(function() {
            toggleFilters();
        });
        
        $('#import_all_products_form').submit(function() {
            
            handleStartImporting();
            var type = $('input[name="import_products_type"]:checked').val();
            var inStockDaysAgo = $('input[name="cwh_import_propery_in_stock_days_ago"]:checked').val();

            var filters = {
                 'platform' : [],
                 'region' : [],
                 'language' : [],
             };
            
            $('input:checkbox[name="cwh_import_propery_platform"]:checked').each(function(){
                 filters.platform.push($(this).val());
            });
            
            $('input:checkbox[name="cwh_import_propery_region"]:checked').each(function(){
                 filters.region.push($(this).val());
            });
            
            $('input:checkbox[name="cwh_import_propery_language"]:checked').each(function(){
                 filters.language.push($(this).val());
            });     
            
            $.post(ajaxurl, {
                'action': 'import_products_async',
                'type': type,
                'filters': filters,
                'in_stock_days_ago': inStockDaysAgo
            }, function(response) {
                var res = $.parseJSON(response);
                
                if(res.status) {
                    $('#afterStartImport .content').html('<div class="updated inline"><p class="success">'+res.message+'</p><div>');
                } else {
                    $('#afterStartImport .content').html('<div class="error inline"><p class="warning">'+res.message+'</p><div>');
                }
                handleStopImporting();

            });
            return false;
        });

        function toggleFilters() {
            if($('input#import_type_by_filter').is(':checked')) {
                $('#import_filters').show();
            } else {
                $('#import_filters').hide();
            }
        }

        function handleStartImporting() {
            $('#beforeImportingProducts').hide();
            $('#preparingImport').show();
        }
        
        function handleStopImporting() {
            $('#preparingImport').hide();
            $('#afterStartImport').show();
        }
    });
</script>