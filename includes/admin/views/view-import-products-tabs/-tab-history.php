<br>
<table class="wp-list-table widefat fixed striped posts">
    <thead>
        <tr>
           <th><?php _e('Import details', 'woocommerce'); ?></th>  
           <th></th>
           <th><?php _e('Product details', 'woocommerce'); ?></th>
           <th><?php _e('Filter details', 'woocommerce'); ?></th>
           <th><?php _e('Actions', 'woocommerce'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php /** @var $item WP_ImportPropertyModel */ ?>
        <?php foreach ($this->import_history as $item) : ?>
            <tr id="import_row<?php echo $item->getId(); ?>">
                <td colspan="2">
                    <table>
                        <tr>
                            <th class="codeswholesale_no_vertical_padding codeswholesale_no_wrap"><?php _e('Import ID: ', 'woocommerce'); ?></th>
                            <td class="codeswholesale_no_vertical_padding codeswholesale_no_wrap"><?php echo $item->getId(); ?></td>
                        </tr>
                        <tr>
                            <th class="codeswholesale_no_vertical_padding codeswholesale_no_wrap"><?php _e('Status: ', 'woocommerce'); ?></th>
                            <td class="codeswholesale_no_vertical_padding codeswholesale_no_wrap"><?php echo $item->getStatus(); ?></td>
                        </tr>
                        <tr>
                            <th class="codeswholesale_no_vertical_padding codeswholesale_no_wrap"><?php _e('User: ', 'woocommerce'); ?></th>
                            <td class="codeswholesale_no_vertical_padding codeswholesale_no_wrap">
                                <?php
                                    $user_info = get_userdata($item->getUserId());
                                    echo $user_info->nickname;
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="codeswholesale_no_vertical_padding codeswholesale_no_wrap"><?php _e('Created on: ', 'woocommerce'); ?></th>
                            <td class="codeswholesale_no_vertical_padding codeswholesale_no_wrap"><?php echo $item->getCreatedAt()->format('Y-m-d H:i'); ?></td>
                        </tr>
                    </table>
                    
                
                </td>
                <td>
                    <table>
                        <tr>
                            <th class="codeswholesale_no_vertical_padding codeswholesale_no_wrap"><?php _e('Total: ', 'woocommerce'); ?></th>
                            <td class="codeswholesale_no_vertical_padding"><?php echo $item->getDoneCount() . '/' . $item->getTotalCount(); ?></td>
                        </tr>
                        <tr>
                            <th class="codeswholesale_no_vertical_padding codeswholesale_no_wrap"><?php _e('Created: ', 'woocommerce'); ?></th>
                            <td class="codeswholesale_no_vertical_padding"><?php echo $item->getInsertCount(); ?></td>
                        </tr>
                        <tr>
                            <th class="codeswholesale_no_vertical_padding codeswholesale_no_wrap"><?php _e('Updated: ', 'woocommerce'); ?></th>
                            <td class="codeswholesale_no_vertical_padding codeswholesale_no_wrap"><?php echo $item->getUpdateCount(); ?></td>
                        </tr>
                    </table>
                </td>
                <td>
                    
                    <br>
                    <strong><?php _e('Type: ', 'woocommerce'); ?></strong><?php echo $item->getType() ?>
                    <?php if(WP_ImportPropertyRepository::FILTERS_TYPE_BY_FILTERS === $item->getType()): ?>
                       
                        <br>
                        <?php foreach ($item->getFilters() as $key => $filter): ?>
                            <?php echo '<strong>'.ucfirst($key).'</strong>' ?>: <?php echo join(', ', $filter); ?><br>
                        <?php endforeach; ?>
                            
                    <?php else: ?>  
                        <br>
                        <?php  _e('No import filters ', 'woocommerce'); ?>
                    <?php endif; ?>
                            
                    <?php if($item->getInStockDaysAgo()): ?>
                        <strong> <?php  _e('Products in stock in the last: ', 'woocommerce'); ?> </strong>
                        <?php echo $item->getInStockDaysAgo(); ?> <?php  _e('days ', 'woocommerce'); ?> <?php  _e('(60 days)', 'woocommerce'); ?>
                    <?php endif; ?>
                   
                </td>
                <td>
                    <div>
                        <span>
                            <a  href="<?php echo $item->getDetailsPath() ?>" download><?php _e('Get details', 'woocommerce'); ?></a>
                        </span>
                        <br>
                        <span class="trash"> 
                            <a class="cw_import_remove_action" data-id="<?php echo $item->getId(); ?>" href="#"><?php _e('Remove', 'woocommerce'); ?></a>
                        </span>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<script>
    var $ = jQuery.noConflict();

    $(document).ready(function () {
        $('.cw_import_remove_action').click(function() {
            var id = $(this).data('id');

            $.post(ajaxurl, {
                'action': 'remove_import_details_async',
                'id': id
            }, function(response) {
                var res = $.parseJSON(response);

                console.log(res);
                if(res.status) {
                    $('#import_row'+id).remove();
                } else {
                    alert(res.message);
                }
            });

            return false;
        });


    });
</script>