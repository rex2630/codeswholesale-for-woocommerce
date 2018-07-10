<?php

use CodesWholesaleFramework\Model\ExternalProduct;

/**
 * Class WP_CodeswholesaleProductModelFactory
 */
class WP_CodeswholesaleProductModelFactory
{
    private $repository;
    
    public function __construct() {
       $this->repository = new WP_CodeswholesaleProductRepository(); 
    }
    
    public static function resolve(\stdClass $parameters): WP_CodeswholesaleProductModel
    {
        $model = new WP_CodeswholesaleProductModel();

        $id = WP_CodeswholesaleProductRepository::FIELD_ID;
        $productId = WP_CodeswholesaleProductRepository::FIELD_PRODUCT_ID;
        $createdAt = WP_CodeswholesaleProductRepository::FIELD_CREATED_AT;
        $description = WP_CodeswholesaleProductRepository::FIELD_DESCRIPTION;
        $title = WP_CodeswholesaleProductRepository::FIELD_TITLE;
        $image = WP_CodeswholesaleProductRepository::FIELD_IMAGE;
        $gallery = WP_CodeswholesaleProductRepository::FIELD_GALLERY;
        $preferredLanguage  = WP_CodeswholesaleProductRepository::FIELD_PREFERRED_LANGUAGE;
        
        $model
            ->setId($parameters->$id)
            ->setProductId($parameters->$productId)
            ->setCreatedAt(new \DateTime($parameters->$createdAt))
            ->setDescription($parameters->$description)
            ->setTitle($parameters->$title)
            ->setImage($parameters->$image)
            ->setGallery($parameters->$gallery)
            ->setPreferredLanguage($parameters->$preferredLanguage)
        ;

        return $model;
    }
    
    public function create(ExternalProduct $externalProduct, $lang) 
    {
        if($this->repository->isset($externalProduct->getProduct()->getProductId(), $lang)) {
            $this->update($externalProduct, $this->prepare($externalProduct->getProduct()->getProductId(), $lang));
            
            return;
        }
        
        $cwModel =  new WP_CodeswholesaleProductModel();
        
        $cwModel->setProductId($externalProduct->getProduct()->getProductId());
        $cwModel->setDescription($externalProduct->getDescription());
        $cwModel->setTitle(wc_clean($externalProduct->getProduct()->getName()));
        $cwModel->setPreferredLanguage($lang); 
        
        $thumb = $externalProduct->getThumbnail();
        
        if($thumb && $thumb['name']) {
            $cwModel->setImage($thumb['name']) ;
        }
        
        $photos = [];
 
        foreach($externalProduct->getPhotos() as $photo) {
            $photos[] = $photo['name'];
        }

        $cwModel->setGallery(implode("|", $photos));
                 
        $this->repository->save($cwModel);
    }
    
    public function prepare($product_id, $lang) 
    {
        $model = $this->repository->find($product_id, $lang);
        
        return $model;
    }
    
    public function update(ExternalProduct $externalProduct, WP_CodeswholesaleProductModel $cwModel) 
    {
        $cwModel->setDescription($externalProduct->getDescription());
        $cwModel->setTitle(wc_clean($externalProduct->getProduct()->getName()));
        $thumb = $externalProduct->getThumbnail();
        
        if($thumb && $thumb['name']) {
            $cwModel->setImage($thumb['name']) ;
        }
        
        $photos = [];
 
        foreach($externalProduct->getPhotos() as $photo) {
            $photos[] = $photo['name'];
        }

        $cwModel->setGallery(implode("|", $photos));
        
        $this->repository->update($cwModel);
    }
}