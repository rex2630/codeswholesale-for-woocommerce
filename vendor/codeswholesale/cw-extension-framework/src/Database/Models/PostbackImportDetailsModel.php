<?php
namespace CodesWholesaleFramework\Database\Models;

class PostbackImportDetailsModel
{
    const
        STATUS_UPDATED      = 'UPDATED',
        STATUS_CREATED      = 'CREATED',
        STATUS_REJECTED     = 'REJECTED',
        STATUS_NO_CHANGES   = 'NO_CHANGES'
    ;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $importId;

    /**
     * @var string
     */
    protected $name;
    
    /**
     * @var string
     */
    protected $createdAt;
    
    /**
     * @var string
     */
    protected $status;
    
    /**
     * @var string
     */
    protected $productId;
    
    /**
     * @var string
     */
    protected $importTime;
    
    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $exceptions;
    
    function getId() {
        return $this->id;
    }

    function getImportId() {
        return $this->importId;
    }

    function getStatus() {
        return $this->status;
    }

    function getName() {
        return $this->name;
    }
    
    function getCreatedAt() {
        return $this->createdAt;
    }
    
    function getProductId() {
        return $this->productId;
    }

    function getImportTime() {
        return $this->importTime;
    }

    function getDescription() {
        return $this->description;
    }
    function getExceptions() {
        return $this->exceptions;
    }

    function setExceptions($exceptions) {
        $this->exceptions = $exceptions;
        
        return $this;
    }

    function setId($id) {
        $this->id = $id;
        
        return $this;
    }

    function setImportId($importId) {
        $this->importId = $importId;
        
        return $this;
    }

    function setStatus($status) {
        $this->status = $status;
        
        return $this;
    }

    function setName($name) {
        $this->name = $name;
        
        return $this;
    }
    
    function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;
        
        return $this;
    }
    
    function setProductId($productId) {
        $this->productId = $productId;
        
        return $this;
    }

    function setImportTime($importTime) {
        $this->importTime = $importTime;
        
        return $this;
    }

    function setDescription($description) {
        $this->description = $description;
        
        return $this;
    }


}
