<?php
namespace CodesWholesaleFramework\Database\Factories;

use CodesWholesaleFramework\Database\Models\PostbackImportModel;
use CodesWholesaleFramework\Database\Repositories\PostbackImportRepository;
/**
 * Class PostbackImportModelFactory
 */
class PostbackImportModelFactory
{
    /**
     * @param array $parameters
     *
     * @return PostbackImportModel
     *
     * @throws \Exception
     */
    public static function createInstanceToSave(array $parameters): PostbackImportModel
    {
        try {
            $model = new PostbackImportModel();

            $model
                ->setUserId($parameters[PostbackImportRepository::FIELD_USER_ID])
                ->setAction($parameters[PostbackImportRepository::FIELD_ACTION])
                ->setType($parameters[PostbackImportRepository::FIELD_TYPE])
            ;

            if ('all' !== $model->getType()) {
                $model
                    ->setInStockDaysAgo($parameters[PostbackImportRepository::FIELD_IN_STOCK_DAYS_AGO])
                    ->setFilters($parameters[PostbackImportRepository::FIELD_FILTERS])
                ;
            }

            return $model;

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param \stdClass $parameters
     *
     * @return PostbackImportModel
     */
    public static function resolve(\stdClass $parameters): PostbackImportModel
    {
        $model = new PostbackImportModel();

        $id = PostbackImportRepository::FIELD_ID;
        $externalId = PostbackImportRepository::FIELD_EXTERNAL_ID;
        $userId = PostbackImportRepository::FIELD_USER_ID;
        $createdAt = PostbackImportRepository::FIELD_CREATED_AT;
        $action = PostbackImportRepository::FIELD_ACTION;
        $type = PostbackImportRepository::FIELD_TYPE;
        $inStockDaysAgo = PostbackImportRepository::FIELD_IN_STOCK_DAYS_AGO;
        $filters = PostbackImportRepository::FIELD_FILTERS;
        $insertCount = PostbackImportRepository::FIELD_INSERT_COUNT;
        $updateCount = PostbackImportRepository::FIELD_UPDATE_COUNT;
        $totalCount = PostbackImportRepository::FIELD_TOTAL_COUNT;
        $doneCount = PostbackImportRepository::FIELD_DONE_COUNT;
        $status = PostbackImportRepository::FIELD_STATUS;
        $description = PostbackImportRepository::FIELD_DESCRIPTION;

        $model
            ->setId($parameters->$id)
            ->setUserId($parameters->$userId)
            ->setExternalId($parameters->$externalId)
            ->setCreatedAt(new \DateTime($parameters->$createdAt))
            ->setAction($parameters->$action)
            ->setType($parameters->$type)
            ->setInStockDaysAgo($parameters->$inStockDaysAgo)
            ->setFilters(json_decode($parameters->$filters, true))
            ->setInsertCount($parameters->$insertCount)
            ->setUpdateCount($parameters->$updateCount)
            ->setTotalCount($parameters->$totalCount)
            ->setDoneCount($parameters->$doneCount)
            ->setStatus($parameters->$status)
            ->setDescription($parameters->$description)
        ;

        return $model;
    }
}