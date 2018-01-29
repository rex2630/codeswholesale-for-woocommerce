<style>
    .codeswholesale_loader {
        border: 12px solid #f3f3f3; /* Light grey */
        border-top: 12px solid #3498db; /* Blue */
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 2s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
<div class="wrap">
    <div id="cst-title"><img src="<?php  echo $this->plugin_img() ?>" id="nav-img"></div>
    <h1 class="wp-heading-inline"><?php _e('Order History', 'woocommerce') ?> (<?php echo count($this->orders) ?>)</h1>
    <div>
        <form method="GET">
            <input name="page" type="hidden" value="cw-check-orders">
            <label><?php _e('From', 'woocommerce'); ?></label>
            <input placeholder="YYYY-MM-DD" name="from" type="date" value="<?php echo $this->from ?>">
            <label><?php _e('To', 'woocommerce'); ?></label>
            <input placeholder="YYYY-MM-DD" name="to" type="date" value="<?php echo $this->to ?>">
            <button type="submit"><?php _e('Show', 'woocommerce'); ?></button>
        </form>
        <?php if($this->error) : ?>
            <div class="error inline"><p class="warning"><strong><?php  echo $this->error; ?></strong></p></div>
        <?php endif; ?>
        
        <?php if($this->orders):?>
            <table class="wp-list-table widefat fixed striped posts">
                <thead>
                    <tr>
                        <th><?php _e('Order ID', 'woocommerce'); ?></th>
                        <th><?php _e('WooCommerce', 'woocommerce'); ?></th>
                        <th><?php _e('Order status', 'woocommerce'); ?></th>
                        <th><?php _e('Total price', 'woocommerce'); ?> (EUR)</th>
                        <th><?php _e('Created on', 'woocommerce'); ?></th>
                        <th><?php _e('Actions', 'woocommerce'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php /** @var \CodesWholesale\Resource\Order $item */ ?>
                    <?php foreach ($this->orders as $item) : ?>
                        <tr>
                            <td><p><?php echo $item->getOrderId(); ?></p></td>
                            <td><p><?php echo CW()->getWooCommerceOrderIdByExternalId($item->getOrderId()) ?></p></td>
                            <td><?php echo $item->getStatus(); ?></td>
                            <td><?php echo $item->getTotalPrice(); ?></td>
                            <td><?php echo (new \DateTime($item->getCreatedOn()))->format('Y-m-d H:i:s'); ?></td>
                            <td>
                                <a href="" data-order="<?php echo $item->getOrderId(); ?>" class="order_get_invoice">
                                    <?php _e('Get invoice', 'woocommerce'); ?>
                                </a>
                                |
                                <a href="" data-order="<?php echo $item->getOrderId(); ?>" class="order_show_codes">
                                    <?php _e('Show codes', 'woocommerce'); ?>
                                </a>

                            </td>
                        </tr>
                        <tr></tr>
                        <tr class="<?php echo $item->getOrderId(); ?> product_codes" style="display: none">
                            <td colspan="6">
                                <div class="codeswholesale_loader"></div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="error inline"><p class="warning"><strong><?php _e('No results', 'woocommerce'); ?></strong></p></div>
        <?php endif; ?>

    </div>
</div>

<script>
    jQuery(document).ready(function () {
        jQuery('.order_show_codes').click(function() {
            var id =  jQuery(this).data('order');

            if(jQuery(this).hasClass('active')) {
                jQuery('.order_show_codes').removeClass('active');
                jQuery('.product_codes').hide();
            } else {
                jQuery('.order_show_codes').removeClass('active');
                jQuery(this).addClass('active');

                jQuery('.product_codes').hide();
                jQuery('.product_codes.'+id).show();

                getOrderDetails(id);
        }


            return false;
        });

        jQuery('.order_get_invoice').click(function() {
            var id =  jQuery(this).data('order');
            jQuery.post(ajaxurl, {
                'action': 'get_invoice_async',
                'id': id
            }, function(response) {
                download(response);
            });
            
            return false;
        });

        function getOrderDetails(id) {
            var key_product = "<?php _e('Product', 'woocommerce'); ?>";

            jQuery.post(ajaxurl, {
                'action': 'get_codes_by_order_async',
                'id': id
            }, function(response) {
                var res = jQuery.parseJSON(response);
                var html = '<hr>';

                jQuery.each( res, function( key, value ) {
                    html +='<h3>'+key_product+': '+key+'</h3>';

                    jQuery.each( value, function( k, code ) {
                        html += '<p>'+code+'</p>';
                    });
                });

                jQuery('.product_codes.'+id+' td').html(html);
            });
        }

        function download(file_path) {
            var a = document.createElement('A');
            a.href = file_path;
            a.download = file_path.substr(file_path.lastIndexOf('/') + 1);
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }
        
    });
</script>
