<?php

use CodesWholesale\Client;
use CodesWholesaleFramework\Model\ProductModel;
use CodesWholesaleFramework\Import\CsvImportGenerator;
use CodesWholesaleFramework\Import\ProductDiffGenerator;

abstract class ImportAbstract 
{
    /**
     * @var Client
     */
    protected $client;
        
    /**
     * @var WP_Product_Updater
     */
    protected $updater;
    
    /**
     * @var ProductDiffGenerator
     */
    protected $diffGenerator;
    
    /**
     * @var CsvImportGenerator
     */
    protected $csvImportGenerator;
        
    /**
     * @var array|mixed|void
     */
    protected $optionsArray;
    
    public function __construct()
    {
        $this->client = CW()->get_codes_wholesale_client();
        $this->diffGenerator = new ProductDiffGenerator();
        $this->csvImportGenerator = new CsvImportGenerator();
        $this->optionsArray = CW()->get_options();
        
        $this->updater = WP_Product_Updater::getInstance();
    }
    
    /**
     * 
     * @param ProductModel $productModel
     * @param WP_Post $wpProduct
     * @return array
     */
    protected function getDiff(ProductModel $productModel, WP_Post $wpProduct): array
    {
        $this->diffGenerator->diff = [];
        
        $price = get_post_meta($wpProduct->ID, CodesWholesaleConst::PRODUCT_STOCK_PRICE_PROP_NAME, true);
        $stock = get_post_meta($wpProduct->ID, '_stock', true);


        $ex_platform    = ProductDiffGenerator::implodeArray($productModel->getPlatform());
        $ex_regions     = ProductDiffGenerator::implodeArray($productModel->getRegions());
        $ex_languages   = ProductDiffGenerator::implodeArray($productModel->getLanguages());
        
        $in_platform    = ProductDiffGenerator::implodeArray(WP_Attribute_Updater::getInternalProductAttributes($wpProduct, WP_Attribute_Updater::ATTR_PLATFORM));
        $in_regions     = ProductDiffGenerator::implodeArray(WP_Attribute_Updater::getInternalProductAttributes($wpProduct, WP_Attribute_Updater::ATTR_REGION));
        $in_languages   = ProductDiffGenerator::implodeArray(WP_Attribute_Updater::getInternalProductAttributes($wpProduct, WP_Attribute_Updater::ATTR_LANGUAGE));
          

        if ((string) trim($productModel->getPrice()) !== trim($price)) {
            $this->diffGenerator->generateDiff(ProductDiffGenerator::FIELD_PRICE, $price, $productModel->getPrice());
        }

        if ((string) trim($productModel->getQuantity()) !== trim($stock)) {
            $this->diffGenerator->generateDiff(ProductDiffGenerator::FIELD_STOCK, $stock, $productModel->getQuantity());
        }

        if ((string) trim($ex_platform) !== trim( $in_platform)) {
            $this->diffGenerator->generateDiff(ProductDiffGenerator::FIELD_PLATFORMS, $in_platform,  $ex_platform);
        }
     
        if ((string) trim($ex_regions) !== trim( $in_regions)) {
            $this->diffGenerator->generateDiff(ProductDiffGenerator::FIELD_REGIONS, $in_regions,  $ex_regions);
        }
        
        if ((string) trim($ex_languages) !== trim($in_languages)) {
            $this->diffGenerator->generateDiff(ProductDiffGenerator::FIELD_LANGUAGES, $in_languages, $ex_languages);
        }  
        
        return $this->diffGenerator->diff;
    }
}
