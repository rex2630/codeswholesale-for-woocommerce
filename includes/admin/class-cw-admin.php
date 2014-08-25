<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Class CW_Admin
 */
class CW_Admin {

    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'init', array( $this, 'includes' ) );
    }

    /**
     * Include any classes we need within admin.
     */
    public function includes() {

        include( 'class-cw-admin-menus.php'    );
        include( 'class-cw-admin-assets.php'   );
        include( 'class-cw-admin-product.php'  );

    }
}

return new CW_Admin();