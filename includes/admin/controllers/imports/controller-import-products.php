<?php

use CodesWholesale\Client;
use CodesWholesaleFramework\Database\Repositories\ImportPropertyRepository;
use CodesWholesaleFramework\Database\Models\ImportPropertyModel;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!class_exists('CW_Controller_Import_products')) :
    include_once(plugin_dir_path( __FILE__ ).'controller-import.php');
   
    /**
     * 
     */     
    class CW_Controller_Import_products extends CW_Controller_Import
    {
        /**
         *
         * @var ImportPropertyModel[]
         */
        public $import_history;
        
        /**
         * 
         * @var bool
         */
        public $import_in_progress;
        
        /**
         *
         * @var ImportPropertyRepository
         */
        public $import_repository;
        
        /**
         * 
         */
        public function __construct()
        {
            parent::__construct();

            $this->import_repository = new ImportPropertyRepository(new WP_DbManager());
            $this->import_history   = $this->import_repository->findAll();
            $this->import_in_progress = $this->import_repository->isActive();
        }
        
        public function registerAjaxFunctions() {
            // ajax actions
            $ajaxs = [
                $this->getAjaxFunctionNameToStartImport(),
                $this->getAjaxFunctionNameToRemoveHistory(),
                $this->getAjaxFunctionNameToCheckImportStatus()
            ];
            
            foreach($ajaxs as $name) {
                add_action( 'wp_ajax_' . $name, array($this, $name));
            }
        }
        
        public function page() {
            return 'cw-import-products';
        }
        
        public function setType() {
            $this->type = self::TYPE_EXEC;
        }
        
        public function getAjaxFunctionNameToStartImport() 
        {
          return 'import_products_async';
        }
    
        public function getAjaxFunctionNameToCancelImport() 
        {
            return '';
        }
    
        public function getAjaxFunctionNameToRemoveHistory() 
        {
            return 'remove_import_details_async';
        }
        
        public function getAjaxFunctionNameToCheckImportStatus() {
            return 'check_status_import_products_async';
        }
    
        public function check_status_import_products_async() {
            $result = [ 'inProgress' => $this->import_repository->isActive()];

            echo json_encode($result);

            wp_die();
        }
        
        public function remove_import_details_async() {
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

        public function import_products_async() {
            $result = $this->processImport($_POST);
            
            echo json_encode($result);

            wp_die();
        }
        
        protected function initImport($data) {
            $importModel = \CodesWholesaleFramework\Database\Factories\ImportPropertyModelFactory::createInstanceToSave($data);

            $this->import_repository->save($importModel);

            ExecManager::startImportProducts();
        }
    }
endif;
