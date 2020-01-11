<?php
namespace CodesWholesaleFramework\Import;

use CodesWholesale\Resource\Import;
use CodesWholesaleFramework\Database\Interfaces\DbManagerInterface;
use CodesWholesaleFramework\Database\Models\PostbackImportModel;
use CodesWholesaleFramework\Database\Repositories\PostbackImportRepository;

/**
 * Class PostbackImport
 */
class PostbackImport
{
    public static function start(DbManagerInterface $db, $territory) {
        $importRepository = new PostbackImportRepository($db);
        /** @var PostbackImportModel $importModel */
        $importModel = $importRepository->findActive();
        
        try {
            $registeredImport = Import::registerImport([
                "filters" => $importModel->serializeFilters(),
                "territory" => $territory,
              //  "webHookUrl" => ""
           ]);
            
            $importModel->setExternalId($registeredImport->getImportId());
            $importModel->setStatus($registeredImport->getImportStatus());
            
        } catch (\Exception $exception) {
            $importModel->setStatus(PostbackImportModel::STATUS_REJECT);
            $importModel->setDescription($exception->getMessage());
        }
        
        $importRepository->overwrite($importModel);
    }
    
    public static function cancel(DbManagerInterface $db) {
        $importRepository = new PostbackImportRepository($db);
         
        /** @var PostbackImportModel $importModel */
        try {
            $importModel = $importRepository->findActive();
        } catch (\Exception $ex) {
            return;
        }
        
        try { 
            if($importModel->getExternalId()) {
              Import::cancelImport($importModel->getExternalId());  
            }
        } catch (\Error $ex) {
            printf($ex->getMessage());
        } catch (\Exception $ex) {
            printf($ex->getMessage());
        }
        
        $importModel->setStatus(PostbackImportModel::STATUS_CANCEL);
        
        $importRepository->overwrite($importModel);
    }
    
    public static function history($id = null) {
        return $id ? Import::getImport($id) :  Import::getImports();
    }
    
    public static function isImportActive($id) {
        $import = PostbackImport::history($id);
        
        $activeStatuses = [PostbackImportModel::STATUS_AWAITING, PostbackImportModel::STATUS_IN_PROGRESS];
        
        return in_array($import->getImportStatus(), $activeStatuses);
    }
}

