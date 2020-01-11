<?php
namespace CodesWholesaleFramework\Database\Factories;

use CodesWholesaleFramework\Database\Models\ImportPropertyModel;
use CodesWholesaleFramework\Database\Repositories\ImportPropertyRepository;
/**
 * Class ImportPropertyModelFactory
 */
class ImportPropertyModelFactory
{
    /**
     * @param array $parameters
     *
     * @return ImportPropertyModel
     *
     * @throws \Exception
     */
    public static function createInstanceToSave(array $parameters): ImportPropertyModel
    {
        try {
            $model = new ImportPropertyModel();

            $model
                ->setUserId($parameters[ImportPropertyRepository::FIELD_USER_ID])
                ->setAction($parameters[ImportPropertyRepository::FIELD_ACTION])
                ->setType($parameters[ImportPropertyRepository::FIELD_TYPE])
            ;

            if ('all' !== $model->getType()) {
                $model
                    ->setInStockDaysAgo($parameters[ImportPropertyRepository::FIELD_IN_STOCK_DAYS_AGO])
                    ->setFilters($parameters[ImportPropertyRepository::FIELD_FILTERS])
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
     * @return ImportPropertyModel
     */
    public static function resolve(\stdClass $parameters): ImportPropertyModel
    {
        $model = new ImportPropertyModel();

        $id = ImportPropertyRepository::FIELD_ID;
        $userId = ImportPropertyRepository::FIELD_USER_ID;
        $createdAt = ImportPropertyRepository::FIELD_CREATED_AT;
        $action = ImportPropertyRepository::FIELD_ACTION;
        $type = ImportPropertyRepository::FIELD_TYPE;
        $inStockDaysAgo = ImportPropertyRepository::FIELD_IN_STOCK_DAYS_AGO;
        $filters = ImportPropertyRepository::FIELD_FILTERS;
        $insertCount = ImportPropertyRepository::FIELD_INSERT_COUNT;
        $updateCount = ImportPropertyRepository::FIELD_UPDATE_COUNT;
        $totalCount = ImportPropertyRepository::FIELD_TOTAL_COUNT;
        $doneCount = ImportPropertyRepository::FIELD_DONE_COUNT;
        $status = ImportPropertyRepository::FIELD_STATUS;
        $description = ImportPropertyRepository::FIELD_DESCRIPTION;

        $model
            ->setId($parameters->$id)
            ->setUserId($parameters->$userId)
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