<?php
if( isset( $_GET[ 'tab' ] ) ) {
    $active_tab = $_GET[ 'tab' ];
} else {
	$active_tab = 'tab-import';
}
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
        <?php if($this->isLiveMode()): ?>
            <nav class="nav-tab-wrapper cw-nav-tab-wrapper">
                <a href="?page=<?php echo $this->page() ?>&tab=tab-import" class="nav-tab <?php if( 'tab-import' == $active_tab){ echo 'nav-tab-active'; } ?>">
                    <?php _e('Import', 'woocommerce'); ?>
                </a>
                <a href="?page=<?php echo $this->page() ?>&tab=tab-history" class="nav-tab <?php if( 'tab-history' == $active_tab){ echo 'nav-tab-active'; } ?>">
                    <?php _e('History', 'woocommerce'); ?>
                </a>
            </nav>

             <?php
                if( 'tab-import' == $active_tab) {
                    include_once(plugin_dir_path( __FILE__ ) . './view-import-products-tabs/-tab-import.php');
                } else if('tab-history' == $active_tab) {
                    include_once(plugin_dir_path( __FILE__ ) . './view-import-products-tabs/-tab-history.php');
                }
    //
    //            try {
    //                $history = $this->getImports();
    //                foreach($history as $registeredImport) {
    //                    echo "history Import ID: " . $registeredImport->getImportId() . "<br>";
    //                    echo "history Import status: " . $registeredImport->getImportStatus() . "<br>";
    //                    echo "history Import number of fails: " . $registeredImport->getNumberOfFails() . "<br>";
    //                    echo "history Import message: " . $registeredImport->getMessage() . "<br>";
    //                }
    //            } catch (\Error $ex) {
    //                    var_dump($ex->getMessage());
    //            } catch (\Exception $ex) {
    //                    var_dump($ex->getMessage());
    //            }
             ?> 
        <?php else: ?>
            <div class="cw-ipt-title">
                <p>
                    <strong>
                        <?php 
                            _e("Importing is available only for live mode." , "woocommerce");
                        ?>
                    </strong>
                </p>
            </div>
                
        <?php endif; ?>

    </div>
</div>
