<?php
require_once( dirname(__FILE__) . '/../../../../../wp-load.php' );
require_once( dirname(__FILE__) . '/../../codeswholesale.php' );
require_once( dirname(__FILE__) . '/../import/ImportAbstract.php' );

use CodesWholesaleFramework\Database\Models\ImportPropertyModel;
use CodesWholesaleFramework\Database\Repositories\ImportPropertyRepository;

use CodesWholesaleFramework\Model\ProductModel;
use CodesWholesaleFramework\Factories\ProductModelFactory;

/**
 * Class ImportExec
 */
class ImportExec extends ImportAbstract
{
    /**
     * @var ImportPropertyRepository
     */
    protected $importRepository;

    /**
     * @var ImportPropertyModel
     */
    protected $importModel;
    
    /**
     * ImportExec constructor.
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->importRepository = new ImportPropertyRepository(new WP_DbManager());
        $this->importModel = $this->importRepository->findActive();
        
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
            $productModel = ProductModelFactory::resolveProduct($product, $this->optionsArray[CodesWholesaleConst::PREFERRED_LANGUAGE_FOR_PRODUCT_OPTION_NAME]);

            $relatedInternalProducts = CW()->get_related_wp_products($productModel->getProductId());

             if (0 === count($relatedInternalProducts)) {
                $this->createNewProduct($productModel);
             } elseif (0 < count($relatedInternalProducts)) {
                $this->updateExistProducts($productModel, $relatedInternalProducts);
             }
            
            $this->importModel->increaseDoneCount();
            $this->importRepository->overwrite($this->importModel);
        } catch (\Exception $e) {
        }
    }
    
    private function createNewProduct(ProductModel $productModel) 
    {
        $this->updater->createProduct->create($productModel, $this->importModel->getUserId());
        $this->importModel->increaseInsertCount();
        $this->csvImportGenerator->appendNewProduct($productModel);
    }
    
    private function updateExistProducts(ProductModel $productModel, $relatedInternalProducts) 
    {
        foreach ($relatedInternalProducts as $post) {
             $diff = $this->getDiff($productModel, $post);

             if (0 !== count($diff)) {
                $this->updater->updateProduct->update($productModel, $post->ID);
                $this->importModel->increaseUpdateCount();
                $this->csvImportGenerator->appendUpdatedProduct($productModel, $diff);
             }
        }
    }

    private function sendImportFinishedMail() 
    {
        (new WP_Admin_Notify_Import_Finished())
                ->sendMail([ FileManager::getImportFilePath($this->importModel->getId())], $this->importModel);
    }
    
    protected function createImportFolder() 
    {
        try {
            FileManager::createImportFolder($this->importModel->getId()); 
        } catch (Exception $ex) {
            $this->importModel->setStatus(ImportPropertyModel::STATUS_REJECT);
            $this->importModel->setDescription($ex->getMessage());
            $this->importRepository->overwrite($this->importModel);
        }
    }
}

$import = new ImportExec();

$import->execute();