<?php
require_once( dirname(__FILE__) . '/ImportAbstract.php' );

use CodesWholesale\Resource\FullProduct;
use CodesWholesaleFramework\Model\ProductModel;
use CodesWholesaleFramework\Factories\ProductModelFactory;
use CodesWholesaleFramework\Database\Models\PostbackImportModel;
use CodesWholesaleFramework\Database\Models\PostbackImportDetailsModel;
use CodesWholesaleFramework\Database\Repositories\PostbackImportRepository;
use CodesWholesaleFramework\Database\Repositories\PostbackImportDetailsRepository;
use CodesWholesaleFramework\Import\PostbackImport;
use CodesWholesaleFramework\Import\CsvPostbackImportGenerator;
/**
 * Class PostbackImport
 */
class PostbackImportProduct extends ImportAbstract
{    
    /**
     * @var PostbackImportModel
     */
    protected $importModel;
    
    /**
     * @var PostbackImportDetailsModel
     */
    protected $importDetailsModel;
    
    /**
     * @var PostbackImportRepository
     */
    protected $importRepository;
    
    /**
     * @var PostbackImportDetailsRepository
     */
    protected $importDetailsRepository;

    /**
     * PostbackImportExec constructor.
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->importRepository = new PostbackImportRepository(new WP_DbManager());
        $this->importDetailsRepository = new PostbackImportDetailsRepository(new WP_DbManager());
        
        $this->importModel = $this->importRepository->findActive();
        
        $this->createImportFolder();
    }
    
    /**
     * 
     * @param FullProduct[]
     */
    public function execute(array $fullProducts)
    {
        $this->beforeExecute();
        
        /** @var FullProduct $product  */
        foreach($fullProducts as $fullProduct) {
            $time_start = microtime(true); 
            
            try {
                $this->prepareDetails($fullProduct->getProductId(), $this->importModel->getId());
                $this->importProduct($fullProduct);
            } catch (Exception $ex) {
                $this->importDetailsModel
                    ->setStatus(PostbackImportDetailsModel::STATUS_REJECTED)
                    ->setDescription($ex->getMessage());
            }
            
            $execution_time = microtime(true) - $time_start;
            
            $this->importDetailsModel->setImportTime($execution_time);
            
            $this->importDetailsRepository->save($this->importDetailsModel);
            
            $this->importRepository->overwrite($this->importModel);
        }
        
        $this->afterExecute();
    }
    
    private function beforeExecute() {
        if($this->importModel->getStatus() !== PostbackImportModel::STATUS_AWAITING) {
            return;
        }
  
        $import = PostbackImport::history($this->importModel->getExternalId());
        
        //$this->importModel->setTotalCount(2000);
        $this->importModel->setStatus($import->getImportStatus());
        $this->importRepository->overwrite($this->importModel); 
    }
    
    private function afterExecute() {
       $this->checkImportStatus(); 
    }
    
    /**
     * 
     * @param type $productId
     */
    private function prepareDetails($productId, $importId) 
    {
        $this->importDetailsModel = new PostbackImportDetailsModel();
        $this->importDetailsModel
                ->setStatus(PostbackImportDetailsModel::STATUS_NO_CHANGES)
                ->setProductId($productId)
                ->setImportId($importId);
    }
    
    /**
     * 
     * @param FullProduct $fullProduct
     */
    private function importProduct(FullProduct $fullProduct) 
    {
        $productModel = ProductModelFactory::resolveFullProduct($fullProduct, $this->optionsArray[CodesWholesaleConst::PREFERRED_LANGUAGE_FOR_PRODUCT_OPTION_NAME]);

        $this->importDetailsModel->setName($productModel->getName());
        
        $relatedInternalProducts = CW()->get_related_wp_products($productModel->getProductId());

        if (0 === count($relatedInternalProducts)) {
            $this->createNewProduct($productModel);
        } elseif (0 < count($relatedInternalProducts)) {
           $this->updateExistProducts($productModel, $relatedInternalProducts);
        } 
         
        $this->importRepository->increaseDoneCount($this->importModel->getId());
    }
    
    /**
     * 
     * @param ProductModel $productModel
     */
    private function createNewProduct(ProductModel $productModel) 
    {
        $exceptions = $productModel->getExceptions();
        
        try {
            $this->updater->createProduct->create($productModel, $this->importModel->getUserId());
            $this->importDetailsModel->setStatus(PostbackImportDetailsModel::STATUS_CREATED);
            $this->importRepository->increaseCreatedCount($this->importModel->getId());
        } catch (Exception $ex) {
            $exceptions[] = $ex->getMessage();
            $this->importDetailsModel->setStatus(PostbackImportDetailsModel::STATUS_REJECTED);
        }
   
        $this->importDetailsModel->setExceptions(serialize($exceptions));  
    }
    
    /**
     * 
     * @param ProductModel $productModel
     * @param type $relatedInternalProducts
     */
    private function updateExistProducts(ProductModel $productModel, $relatedInternalProducts) 
    {
        $exceptions = $productModel->getExceptions();
         
        try {
            foreach ($relatedInternalProducts as $post) {
                $diff = $this->getDiff($productModel, $post);

                if (0 !== count($diff)) {
                    $this->updater->updateProduct->update($productModel, $post->ID);
                    $this->importDetailsModel->setStatus(PostbackImportDetailsModel::STATUS_UPDATED);
                    $this->importDetailsModel->setDescription(serialize($diff));

                    $this->importRepository->increaseUpdatedCount($this->importModel->getId());
                } 
            }   
        } catch (Exception $ex) {
            $exceptions[] = $ex->getMessage();
            $this->importDetailsModel->setStatus(PostbackImportDetailsModel::STATUS_REJECTED);
        }
        
        $this->importDetailsModel->setExceptions(serialize($exceptions)); 
    }
    
    /**
     * 
     */
    protected function createImportFolder() 
    {
        try {
            FileManager::createImportFolder($this->importModel->getExternalId()); 
        } catch (Exception $ex) {
            $this->importModel->setStatus(PostbackImportModel::STATUS_REJECT);
            $this->importModel->setDescription($ex->getMessage());
            $this->importRepository->overwrite($this->importModel);
        }
    }
    
    /**
     * 
     * @return type
     */
    private function checkImportStatus()
    {
        if (PostbackImport::isImportActive($this->importModel->getExternalId())) {
            return;
        }
        
        $this->finishImport();
    }
    
    /**
     * 
     */
    private function finishImport()
    {
        $import = PostbackImport::history($this->importModel->getExternalId());
    
        $this->importModel->setStatus($import->getImportStatus());  
        
        $this->importRepository->overwrite($this->importModel);
        
        $csv = (new CsvPostbackImportGenerator(new WP_DbManager()))->generateReport($this->importModel->getId());
        
        FileManager::setImportFile($csv, $this->importModel->getId() . '-postback');
        
        $this->sendImportFinishedMail();
    }
    
    /**
     * 
     */
    private function sendImportFinishedMail() 
    {
        (new WP_Admin_Notify_Import_Finished())
                ->sendMail([ FileManager::getImportFilePath($this->importModel->getId(). '-postback')], $this->importModel);
    }
    
}
