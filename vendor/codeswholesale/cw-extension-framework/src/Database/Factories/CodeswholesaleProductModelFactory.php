<?php
namespace CodesWholesaleFramework\Database\Factories;

use CodesWholesaleFramework\Model\ProductModel;
use CodesWholesaleFramework\Database\Interfaces\DbManagerInterface;
use CodesWholesaleFramework\Database\Models\CodeswholesaleProductModel;
use CodesWholesaleFramework\Database\Repositories\CodeswholesaleProductRepository;

/**
 * Class CodeswholesaleProductModelFactory
 */
class CodeswholesaleProductModelFactory
{
    /**
     * @var CodeswholesaleProductRepository
     */
    private $repository;

    public function __construct(DbManagerInterface $db) {
        $this->repository = new CodeswholesaleProductRepository($db);
    }

    /**
     * 
     * @param \stdClass $parameters
     * @return CodeswholesaleProductModel
     */
    public static function resolve(\stdClass $parameters): CodeswholesaleProductModel
    {
        $model = new CodeswholesaleProductModel();

        $id = CodeswholesaleProductRepository::FIELD_ID;
        $productId = CodeswholesaleProductRepository::FIELD_PRODUCT_ID;
        $createdAt = CodeswholesaleProductRepository::FIELD_CREATED_AT;
        $description = CodeswholesaleProductRepository::FIELD_DESCRIPTION;
        $title = CodeswholesaleProductRepository::FIELD_TITLE;
        $image = CodeswholesaleProductRepository::FIELD_IMAGE;
        $gallery = CodeswholesaleProductRepository::FIELD_GALLERY;
        $preferredLanguage  = CodeswholesaleProductRepository::FIELD_PREFERRED_LANGUAGE;

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

    /**
     * 
     * @param ProductModel $productModel
     * @param type $lang
     * @return type
     */
    public function create(ProductModel $productModel, $lang)
    {
        if($this->repository->isset($productModel->getProductId(), $lang)) {
            $this->update($productModel, $this->prepare($productModel, $lang));

            return;
        }

        $cwModel =  new CodeswholesaleProductModel();

        $cwModel->setProductId($productModel->getProductId());
        $cwModel->setDescription($productModel->getFactSheets());
        $cwModel->setTitle($productModel->getName());
        $cwModel->setPreferredLanguage($lang);

        $thumb = $productModel->getImage();

        if($thumb && $thumb['name']) {
            $cwModel->setImage($thumb['name']) ;
        }

        $photos = [];

        foreach($productModel->getPhotos() as $photo) {
            $photos[] = $photo['name'];
        }

        $cwModel->setGallery(implode("|", $photos));

        $this->repository->save($cwModel);
    }

    /**
     * 
     * @param ProductModel $productModel
     * @param type $lang
     * @return type
     */
    public function prepare(ProductModel $productModel, $lang)
    {
        try {
            return $this->repository->find($productModel->getProductId(), $lang);
        } catch (\Exception $ex) {
            $this->create($productModel, $lang);
            return $this->prepare($productModel, $lang);
        }
    }

    /**
     * 
     * @param ProductModel $productModel
     * @param CodeswholesaleProductModel $cwModel
     */
    public function update(ProductModel $productModel, CodeswholesaleProductModel $cwModel)
    {
        $cwModel->setDescription($productModel->getFactSheets());
        $cwModel->setTitle($productModel->getName());
        $thumb = $productModel->getImage();

        if($thumb && $thumb['name']) {
            $cwModel->setImage($thumb['name']) ;
        }

        $photos = [];

        foreach($productModel->getPhotos() as $photo) {
            $photos[] = $photo['name'];
        }

        $cwModel->setGallery(implode("|", $photos));

        $this->repository->overwrite($cwModel);
    }
}
