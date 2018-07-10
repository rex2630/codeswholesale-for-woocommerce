<?php

/**
 * Class WP_CodeswholesaleProductModel
 */
class WP_CodeswholesaleProductModel
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $productId;
    
    /**
     * @var \DateTime
     */
    protected $createdAt;
    
    /**
     * @var string
     */
    protected $description;
    
    /**
     * @var string
     */
    protected $title;
    
    /**
     * @var string
     */
    protected $image;
    
    /**
     * @var string
     */
    protected $gallery;
    
    /**
     * @var string
     */
    protected $preferredLanguage;
    
    /**
     * 
     * @return boolean
     */
    public function isNew() {
        return !!$this->id;
    }
    
    public function getId() {
        return $this->id;
    }

    public function getProductId() {
        return $this->productId;
    }

    public function getCreatedAt(): \DateTime {
        return $this->createdAt;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getImage() {
        return $this->image;
    }

    public function getGallery() {
        return $this->gallery;
    }

    public function getPreferredLanguage() {
        return $this->preferredLanguage;
    }

    public function setId($id) {
        $this->id = $id;
        
        return $this;
    }

    public function setProductId($productId) {
        $this->productId = $productId;
        
        return $this;
    }

    public function setCreatedAt(\DateTime $createdAt) {
        $this->createdAt = $createdAt;
        
        return $this;
    }

    public function setDescription($description) {
        $this->description = $description;
        
        return $this;
    }

    public function setTitle($title) {
        $this->title = $title;
        
        return $this;
    }

    public function setImage($image) {
        $this->image = $image;
        
        return $this;
    }

    public function setGallery($gallery) {
        $this->gallery = $gallery;
        
        return $this;
    }

    public function setPreferredLanguage($preferredLanguage) {
        $this->preferredLanguage = $preferredLanguage;
        
        return $this;
    }
    
    public function isContentDiff($content) {
        $cw = preg_replace('/\s+/', '', trim(strip_tags($this->getDescription())));
        $in = preg_replace('/\s+/', '', trim(strip_tags($content)));
       
        return $cw !== $in;
    }
    
    public function isTitleDiff($title) {
       return trim(strip_tags($this->getTitle())) !== trim(strip_tags($title));
    }
    
    public function isThumbDiff($attach_title) {
        return $attach_title !== $this->getImage(); 
    }
    
    public function isGalleryDiff($names) {
       return implode('|', $names) !== $this->getGallery(); 
    }
}