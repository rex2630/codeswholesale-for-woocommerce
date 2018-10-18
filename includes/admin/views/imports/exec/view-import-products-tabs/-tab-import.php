<div class="cw-ipt-title">
    <p>
        <strong>
            <?php 
                _e("Import all products in bulk instead of adding every product manually. It takes between 20-40 minutes to import all products to your store." , "woocommerce");
            ?>
        </strong>
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
    <div class="cw-ipt-select">
        <div class="cw-radio-button">
            <input checked type="radio" value="<?php echo \CodesWholesaleFramework\Database\Repositories\ImportPropertyRepository::FILTERS_TYPE_ALL ?>" name="import_products_type" id="import_type_all" />
            <label for="import_type_all"><?php _e('All products available', 'woocommerce') ?></label>          
        </div>
        <div class="cw-radio-button">
            <input type="radio" value="<?php echo \CodesWholesaleFramework\Database\Repositories\ImportPropertyRepository::FILTERS_TYPE_BY_FILTERS ?>" name="import_products_type" id="import_type_by_filter" />
            <label for="import_type_by_filter"><?php _e('Apply filters first', 'woocommerce') ?></label>      
        </div>
    </div>
    <hr>

    <p id="import_all_products_copy"><?php _e('Import is best and easiest way to get products from CodesWholesale to your shop.') ?></p>

    <form id="import_all_products_form" class="cw-form">
        <div id="import_filters" style="display:none">

            <p id="import_specific_products_copy"><?php _e('Please select which filters you want to use for the import:', 'woocommerce') ?></p>

            <table class="form-table cw-ipt-table">
                <tr class="cst-label">
                    <th><?php _e('Import products not older than', 'woocommerce') ?>:</th>
                    <td>
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
                                    'value' => ''
                                )
                        );
                        ?>
                    </td> 
                </tr>

                <tr class="cst-label">
                    <th><?php _e('Import products by platform', 'woocommerce') ?>:</th>
                    <td>
                        <?php 
                            $this->form_element_generator->codeswholesale_wp_checkboxes( 
                                array (
                                    'name'    => 'cwh_import_propery_platform',
                                    'options' => $this->getPlatformOptions(),
                                    'checked' => 'checked'
                                )
                            );
                        ?>
                    </td>
                </tr>

                <tr class="cst-label">
                    <th><?php _e('Import products by region', 'woocommerce') ?>:</th>
                    <td>
                        <?php 
                            $this->form_element_generator->codeswholesale_wp_checkboxes( 
                                array (
                                    'name'    => 'cwh_import_propery_region',
                                    'options' => $this->getRegionOptions(),
                                    'checked' => 'checked'
                                )
                            );
                        ?>
                    </td>     
                </tr>
                <tr class="cst-label">
                    <th><?php _e('Import products by language', 'woocommerce') ?>:</th>
                    <td>
                        <?php 
                            $this->form_element_generator->codeswholesale_wp_checkboxes( 
                                array (
                                    'name'    => 'cwh_import_propery_language',
                                    'options' => $this->getLanguageOptions(),
                                    'checked' => 'checked'
                                )
                            );
                        ?> 
                    </td>
                </tr>
            </table>           
        </div>

        <br>
        <br>
        <button type="submit" id="submit_import" class="cw-btn cw-btn-success">
            <?php _e('Import products', 'woocommerce') ?>
            <i class="fas fa-download cw-text-margin-left"></i>
        </button>
    </form>  
</div>

<div id="preparingImport" style="display: none">
    <?php _e('Preparing import...', 'woocommerce'); ?>
</div>

<div id="afterStartImport" style="<?php echo $afterStartImportStyle ?>">
    <div class="content"><?php _e("The import is in progress", "woocommerce") ?></div>
    
    <?php if($this->getAjaxFunctionNameToCancelImport()):  ?>
        <br>
        <button id="cancelImport" type="button" class="cw-btn cw-btn-success"><?php _e("Cancel") ?></button>
    <?php endif;  ?>
</div>


<script>
    var $ = jQuery.noConflict();

    $(document).ready(function () {
        toggleFilters();
        
        startImportObserver(<?php $this->import_in_progress ?>);

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
                'language' : []
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
                'action': '<?php echo $this->getAjaxFunctionNameToStartImport(); ?>',
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
                startImportObserver(true);
                
                handleStopImporting();
            });
            return false;
        });

        $('#cancelImport').click(function() {         
            $.post(ajaxurl, {
                'action': '<?php echo $this->getAjaxFunctionNameToCancelImport(); ?>',
            }, function(response) {
               location.reload();
            });
        });
        
        function toggleFilters() {
            if($('input#import_type_by_filter').is(':checked')) {
                $('#import_filters').show();
                $('#import_all_products_copy').hide();
            } else {
                $('#import_filters').hide();
                $('#import_all_products_copy').show();
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
        
        function startImportObserver(inProgress) {
            if(inProgress) {
                var obInterval = setInterval(() => { 
                $.post(ajaxurl, {
                    'action': '<?php echo $this->getAjaxFunctionNameToCheckImportStatus(); ?>'
                }, function(response) {
                        var res = $.parseJSON(response);

                        if(! res.inProgress) {
                            clearInterval(obInterval);
                            location.reload();    
                        }
                    });
                }, 3000);
            }
        } 
    });
</script>