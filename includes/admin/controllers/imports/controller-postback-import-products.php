<?php

use CodesWholesale\Client;
use CodesWholesaleFramework\Import\PostbackImport;
use CodesWholesaleFramework\Database\Repositories\PostbackImportRepository;
use CodesWholesaleFramework\Database\Models\PostbackImportModel;
use CodesWholesaleFramework\Import\CsvPostbackImportGenerator;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!class_exists('CW_Controller_Postback_Import_products')) :
    include_once(plugin_dir_path( __FILE__ ).'controller-import.php');
   
    /**
     * 
     */     
    class CW_Controller_Postback_Import_products extends CW_Controller_Import
    {
        /**
         *
         * @var PostbackImportModel[]
         */
        public $import_history;
        
        /**
         * 
         * @var bool
         */
        public $import_in_progress;
        
        /**
         * 
         * @var PostbackImportModel
         */
        public $active_import;
        /**
         *
         * @var PostbackImportRepository
         */
        public $import_repository;
        
        /**
         * 
         */
        public function __construct()
        {
            parent::__construct();

            $this->import_repository = new PostbackImportRepository(new WP_DbManager()); 
            $this->import_in_progress = $this->import_repository->isActive();
            
            if($this->import_in_progress) {
                $this->getActive();
            }
            
            $this->import_history   = $this->import_repository->findAll();
        }
       
    
        public function getActive() {
            try {
                $this->active_import = $this->import_repository->findActive();
                $import = PostbackImport::history($this->active_import->getExternalId());

                $this->active_import->setStatus($import->getImportStatus());
                $this->import_repository->updateStatusInformation($this->active_import->getId(), $import);

            } catch (\Exception $ex) {
                $this->active_import = null;
                $this->import_in_progress = false;
            }
        }
        
        public function registerAjaxFunctions() {
            // ajax actions
            $ajaxs = [
                $this->getAjaxFunctionNameToStartImport(),
                $this->getAjaxFunctionNameToRemoveHistory(),
                $this->getAjaxFunctionNameToCancelImport(),
                $this->getAjaxFunctionNameToCheckImportStatus()
            ];
            
            foreach($ajaxs as $name) {
                add_action( 'wp_ajax_' . $name, array($this, $name));
            }
        }
        
        public function page() {
            return 'cw-postback-import-products';
        }
        
        public function setType() {
            $this->type = self::TYPE_POSTPAID;
        }
        
        public function getAjaxFunctionNameToStartImport() 
        {
          return 'import_postback_products_async';
        }
    
        public function getAjaxFunctionNameToCancelImport() 
        {
            return 'cancel_import_postback_products_async';
        }
    
        public function getAjaxFunctionNameToRemoveHistory() 
        {
            return 'remove_postback_import_details_async';
        }
        
        public function getAjaxFunctionNameToCheckImportStatus() {
            return 'check_status_import_postback_products_async';
        }
    
        public function check_status_import_postback_products_async() {
            $result = [ 'inProgress' => $this->import_repository->isActive()];

            echo json_encode($result);

            wp_die();
        }
        
        public function cancel_import_postback_products_async() {
            PostbackImport::cancel(new WP_DbManager());
                        
            wp_die();
        }
        
        public function remove_postback_import_details_async() {
            $id = $_POST['id'];

            $result = new stdClass();

            try{
                $model = $this->import_repository->find($id);

                $this->import_repository->delete($model);
                $result->status = true;
                $result->message = 'Done';
            } catch(\Exception $e) {
                $result->status = false;
                $result->message = $e->getMessage();
            }

            echo json_encode($result);

            wp_die();
        }

        public function import_postback_products_async() 
        {
            $result = $this->processImport($_POST);
            
            echo json_encode($result);

            wp_die();
        }
        
        protected function initImport($data) 
        {
            $importModel = \CodesWholesaleFramework\Database\Factories\PostbackImportModelFactory::createInstanceToSave($data);

            $this->import_repository->save($importModel);

            PostbackImport::start(new WP_DbManager(), CW()->get_options()[CodesWholesaleConst::PREFERRED_LANGUAGE_FOR_PRODUCT_OPTION_NAME]);
        }
        
        public function getImports() {
            return PostbackImport::history();
        }
        
        public function getImportDetailsReport($id) 
        {
            $filename = $id . '-postback';  
            
            if (! FileManager::importFileExist($filename)) {
                $csv = (new CsvPostbackImportGenerator(new WP_DbManager()))->generateReport($id);

                FileManager::setImportFile($csv, $id . '-postback');
            }
            
            return FileManager::getImportFileUrl($id . '-postback');
        }
    }  
endif;
