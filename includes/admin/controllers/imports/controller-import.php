<?php

use CodesWholesale\Client;

include_once(plugin_dir_path( __FILE__ ).'../controller.php');

abstract class CW_Controller_Import extends CW_Controller
{       
    const TYPE_EXEC = 'exec';
    const TYPE_POSTPAID = 'postpaid';
    
    protected $type;
    /**
     * 
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->setType();
        $this->registerAjaxFunctions();
    }
    
    public abstract function page();
    
    public abstract function setType();
    
    public abstract function registerAjaxFunctions();
    
    public abstract function getAjaxFunctionNameToStartImport();
    
    public abstract function getAjaxFunctionNameToCancelImport();
    
    public abstract function getAjaxFunctionNameToRemoveHistory();
    
    public abstract function getAjaxFunctionNameToCheckImportStatus();

    protected abstract function initImport($data);

    public function isLiveMode() {
        return CW()->get_options()['environment'] == 1;
    }
    
    public function getRegionOptions() {
        $options = [];

        foreach(Client::getInstance()->getRegions() as $region) {
            /** @var $region CodesWholesale\Resource\V2\Region */
            $options[$region->getName()] = $region->getName();
        }

        return $options;
    }

    public function getPlatformOptions() {
        $options = [];

        foreach(Client::getInstance()->getPlatforms() as $platform) {
            /** @var $platform CodesWholesale\Resource\V2\Platform */
            $options[$platform->getName()] = $platform->getName();
        }

        return $options;
    }

    public function getLanguageOptions() {
        $options = [];

        foreach(Client::getInstance()->getLanguages() as $language) {
            /** @var $language CodesWholesale\Resource\V2\Language */
            $options[$language->getName()] = $language->getName();
        }

        return $options;  
    }
    
    public function initView() {
        $this->init_account();

        include(plugin_dir_path( __FILE__ ).'../../views/header.php');

        if (!CW()->get_codes_wholesale_client() instanceof \CodesWholesale\Client) {
            include_once(plugin_dir_path( __FILE__ ).'../../views/view-blocked.php');
            return;
        }

        include_once(plugin_dir_path( __FILE__ ) . '../../views/imports/' . $this->type . '/view-import-products.php');
    }

    protected function processImport($data) {
        $result =  new stdClass();

        try {
            // WP_ConfigurationChecker::checkPhpVersion();
            // WP_ConfigurationChecker::checkDbConnection();

            (new WP_Attribute_Updater())->init(); 
            
            $data['user_id'] = get_current_user_id();
            
            $this->initImport($data);

            $result->status = true;
            $result->message = __("Import started", "woocommerce");

        } catch (\Exception $e) {
            $result->status = false;
            $result->message = $e->getMessage();
        }  
        
        return $result;
    }
}