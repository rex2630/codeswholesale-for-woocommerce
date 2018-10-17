<?php
/**
 * Class WP_Product_Updater
 */
class WP_Product_Updater
{
    /**
     * @var WP_Product_Updater
     */
    private static $instance;

    /**
     * @var CW_Create_Internal_Product
     */
    public $createProduct;
    
    /**
     * @var CW_Update_Internal_Product
     */
    public $updateProduct;
    
    /**
     * WP_Product_Updater constructor.
     */
    private function __construct()
    {
        $this->createProduct = new CW_Create_Internal_Product();
        $this->updateProduct = new CW_Update_Internal_Product();
    }

    public static function getInstance()
    {
        if(self::$instance === null) {
            self::$instance = new WP_Product_Updater();
        }
        return self::$instance;
    }
}