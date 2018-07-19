<?php
if( isset( $_GET[ 'tab' ] ) ) {
    $active_tab = $_GET[ 'tab' ];
} // end if
?>

<div class="wrap">
    <div class="cw-content cw-ip-content">
        <div class="cw-ip-header">
            <div class="cw-ip-title">
                <h1 class="wp-heading-inline cw-title">
                    <i class="fas fa-download cw-icon-green"></i>
                    <?php _e('Import products', 'woocommerce') ?>
                </h1>
            </div>
        </div>

        
        <nav class="nav-tab-wrapper cw-nav-tab-wrapper">
            <a href="?page=cw-import-products&tab=tab-import" class="nav-tab <?php if( !$active_tab || 'tab-import' == $active_tab){ echo 'nav-tab-active'; } ?>">
                <?php _e('Import', 'woocommerce'); ?>
            </a>
            <a href="?page=cw-import-products&tab=tab-history" class="nav-tab <?php if( 'tab-history' == $active_tab){ echo 'nav-tab-active'; } ?>">
                <?php _e('History', 'woocommerce'); ?>
            </a>
        </nav>

         <?php

            if(!$active_tab || 'tab-import' == $active_tab) {
                include_once(plugin_dir_path( __FILE__ ) . './view-import-products-tabs/-tab-import.php');
            } else if('tab-history' == $active_tab) {
                include_once(plugin_dir_path( __FILE__ ) . './view-import-products-tabs/-tab-history.php');
            }

         ?> 
    </div>
</div>