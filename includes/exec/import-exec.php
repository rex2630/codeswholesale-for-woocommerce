<?php

require_once( dirname(__FILE__) . '/../../../../../wp-load.php' );
require_once( dirname(__FILE__) . '/../../codeswholesale.php' );

use CodesWholesale\Client;
use CodesWholesaleFramework\Model\ExternalProduct;
use CodesWholesaleFramework\Import\CsvImportGenerator;
use CodesWholesaleFramework\Import\ProductDiffGenerator;
use CodesWholesaleFramework\Database\Models\ImportPropertyModel;
use CodesWholesaleFramework\Database\Repositories\ImportPropertyRepository;

/**
 * Class ImportExec
 */
class ImportExec
{
    /**
     * @var ImportPropertyRepository
     */
    protected $importRepository;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var WP_Product_Updater
     */
    protected $updater;

    /**
     * @var ImportPropertyModel
     */
    protected $importModel;

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
    private $optionsArray;

    /**
     * ImportExec constructor.
     */
    public function __construct()
    {
        $this->importRepository = new ImportPropertyRepository(new WP_DbManager());
        $this->client = CW()->get_codes_wholesale_client();
        $this->updater = WP_Product_Updater::getInstance();
        $this->importModel = $this->importRepository->findActive();
        $this->diffGenerator = new ProductDiffGenerator();
        $this->csvImportGenerator = new CsvImportGenerator();

        $this->optionsArray = CW()->get_options();
        $this->createImportFolder();
    }
    
    /**
     * execute
     */
    public function execute()
    {
        try {
            $externalProducts = $this->client->getProducts($this->importModel->serializeFilters());

            $this->importModel->setStatus(ImportPropertyModel::STATUS_IN_PROGRESS);
            $this->importModel->setTotalCount(count($externalProducts));
            $this->importRepository->overwrite($this->importModel);

            /** @var \CodesWholesale\Resource\Product $product */
            foreach ($externalProducts as $product) {
                $this->importProduct($product);
            }

            $this->importModel->setStatus(ImportPropertyModel::STATUS_DONE);
            $this->importRepository->overwrite($this->importModel);

        } catch (\Exception $e) {
            $this->importModel->setStatus(ImportPropertyModel::STATUS_REJECT);
            $this->importModel->setDescription($e->getMessage());
            $this->importRepository->overwrite($this->importModel);
            throw $e;
        }

        $csv = $this->csvImportGenerator->finish();

        FileManager::setImportFile($csv, $this->importModel->getId());
        
        $this->sendImportFinishedMail();
    }
    
    private function importProduct($product) 
    {
        try {
            $externalProduct = (new ExternalProduct())
                 ->setProduct($product)
                 ->updateInformations($this->optionsArray[CodesWholesaleConst::PREFERRED_LANGUAGE_FOR_PRODUCT_OPTION_NAME])
             ;

             $relatedInternalProducts = CW()->get_related_wp_products($externalProduct->getProduct()->getProductId());

             if (0 === count($relatedInternalProducts)) {
                 $this->createNewProduct($externalProduct);
             } elseif (0 < count($relatedInternalProducts)) {
                $this->updateExistProducts($externalProduct, $relatedInternalProducts);
             }
            
            $this->importModel->increaseDoneCount();
            $this->importRepository->overwrite($this->importModel);
        } catch (\Exception $e) {
        }
    }
    
    private function createNewProduct(ExternalProduct $externalProduct) 
    {
        $this->updater->createWooCommerceProduct($this->importModel->getUserId(), $externalProduct);
        $this->importModel->increaseInsertCount();
        $this->csvImportGenerator->appendNewProduct($externalProduct);
    }
    
    private function updateExistProducts(ExternalProduct $externalProduct, $relatedInternalProducts) 
    {
        foreach ($relatedInternalProducts as $post) {
             $diff = $this->getDiff($externalProduct, $post);

             if (0 !== count($diff)) {
                 $this->updater->updateWooCommerceProduct($post->ID, $externalProduct);
                 $this->importModel->increaseUpdateCount();
                 $this->csvImportGenerator->appendUpdatedProduct($externalProduct, $diff);
             }
        }
    }

    private function sendImportFinishedMail() 
    {
        (new WP_Admin_Notify_Import_Finished())
                ->sendMail([ FileManager::getImportFilePath($this->importModel->getId())], $this->importModel);
    }
    
    private function createImportFolder() 
    {
        try {
            FileManager::createImportFolder($this->importModel->getId()); 
        } catch (Exception $ex) {
            $this->importModel->setStatus(ImportPropertyModel::STATUS_REJECT);
            $this->importModel->setDescription($ex->getMessage());
            $this->importRepository->overwrite($this->importModel);
        }
    }
    
    /**
     * @param ExternalProduct $externalProduct
     * @param WP_Post         $wpProduct
     *
     * @return array
     */
    private function getDiff(ExternalProduct $externalProduct, WP_Post $wpProduct): array
    {
        $this->diffGenerator->diff = [];
        
        $price = get_post_meta($wpProduct->ID, CodesWholesaleConst::PRODUCT_STOCK_PRICE_PROP_NAME, true);
        $stock = get_post_meta($wpProduct->ID, '_stock', true);

        $product = $externalProduct->getProduct();

        $ex_platform    = ProductDiffGenerator::implodeArray($product->getPlatform());
        $ex_regions     = ProductDiffGenerator::implodeArray($product->getRegions());
        $ex_languages   = ProductDiffGenerator::implodeArray($product->getLanguages());
        
        $in_platform    = ProductDiffGenerator::implodeArray(WP_Attribute_Updater::getInternalProductAttributes($wpProduct, WP_Attribute_Updater::ATTR_PLATFORM));
        $in_regions     = ProductDiffGenerator::implodeArray(WP_Attribute_Updater::getInternalProductAttributes($wpProduct, WP_Attribute_Updater::ATTR_REGION));
        $in_languages   = ProductDiffGenerator::implodeArray(WP_Attribute_Updater::getInternalProductAttributes($wpProduct, WP_Attribute_Updater::ATTR_LANGUAGE));
          

        if ((string) trim($product->getLowestPrice()) !== trim($price)) {
            $this->diffGenerator->generateDiff(ProductDiffGenerator::FIELD_PRICE, $price, $product->getLowestPrice());
        }

        if ((string) trim($product->getStockQuantity()) !== trim($stock)) {
            $this->diffGenerator->generateDiff(ProductDiffGenerator::FIELD_STOCK, $stock, $product->getStockQuantity());
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

$import = new ImportExec();

$import->execute();