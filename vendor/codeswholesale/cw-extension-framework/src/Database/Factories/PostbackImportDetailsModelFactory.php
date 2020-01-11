<?php
namespace CodesWholesaleFramework\Database\Factories;

use CodesWholesaleFramework\Database\Models\PostbackImportDetailsModel;
use CodesWholesaleFramework\Database\Repositories\PostbackImportDetailsRepository;
/**
 * Class PostbackImportDetailsModelFactory
 */
class PostbackImportDetailsModelFactory
{
    /**
     * @param \stdClass $parameters
     *
     * @return PostbackImportDetailsModel
     */
    public static function resolve(\stdClass $parameters): PostbackImportDetailsModel
    {
        $model = new PostbackImportDetailsModel();

        $id = PostbackImportDetailsRepository::FIELD_ID;
        $importId = PostbackImportDetailsRepository::FIELD_IMPORT_ID;
        $productId = PostbackImportDetailsRepository::FIELD_PRODUCT_ID;
        $createdAt = PostbackImportDetailsRepository::FIELD_CREATED_AT;
        $status = PostbackImportDetailsRepository::FIELD_STATUS;
        $name = PostbackImportDetailsRepository::FIELD_NAME;
        $importTime = PostbackImportDetailsRepository::FIELD_IMPORT_TIME;
        $description = PostbackImportDetailsRepository::FIELD_DESCRIPTION;
        $exceptions = PostbackImportDetailsRepository::FIELD_EXCEPTIIONS;
        
        $model
            ->setId($parameters->$id)
            ->setImportId($parameters->$importId)
            ->setProductId($parameters->$productId)
            ->setCreatedAt($parameters->$createdAt)
            ->setStatus($parameters->$status)
            ->setName($parameters->$name)
            ->setImportTime($parameters->$importTime)
            ->setDescription($parameters->$description)
            ->setExceptions($parameters->$exceptions)
        ;

        return $model;
    }
}