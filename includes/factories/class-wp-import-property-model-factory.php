<?php

/**
 * Class WP_ImportPropertyModelFactory
 */
class WP_ImportPropertyModelFactory
{
    /**
     * @param array $parameters
     *
     * @return WP_ImportPropertyModel
     *
     * @throws \Exception
     */
    public static function createInstanceToSave(array $parameters): WP_ImportPropertyModel
    {
        try {
            $model = new WP_ImportPropertyModel();

            $model
                ->setAction($parameters[WP_ImportPropertyRepository::FIELD_ACTION])
                ->setType($parameters[WP_ImportPropertyRepository::FIELD_TYPE])
            ;

            if ('all' !== $model->getType()) {
                $model
                    ->setInStockDaysAgo($parameters[WP_ImportPropertyRepository::FIELD_IN_STOCK_DAYS_AGO])
                    ->setFilters($parameters[WP_ImportPropertyRepository::FIELD_FILTERS])
                ;
            }

            return $model;

        } catch (\Exception $e) {
            throw new \Requests_Exception('Bad request', 'invalid_request');
        }
    }

    /**
     * @param \stdClass $parameters
     *
     * @return WP_ImportPropertyModel
     */
    public static function resolve(\stdClass $parameters): WP_ImportPropertyModel
    {
        $model = new WP_ImportPropertyModel();

        $id = WP_ImportPropertyRepository::FIELD_ID;
        $userId = WP_ImportPropertyRepository::FIELD_USER_ID;
        $createdAt = WP_ImportPropertyRepository::FIELD_CREATED_AT;
        $action = WP_ImportPropertyRepository::FIELD_ACTION;
        $type = WP_ImportPropertyRepository::FIELD_TYPE;
        $inStockDaysAgo = WP_ImportPropertyRepository::FIELD_IN_STOCK_DAYS_AGO;
        $filters = WP_ImportPropertyRepository::FIELD_FILTERS;
        $insertCount = WP_ImportPropertyRepository::FIELD_INSERT_COUNT;
        $updateCount = WP_ImportPropertyRepository::FIELD_UPDATE_COUNT;
        $totalCount = WP_ImportPropertyRepository::FIELD_TOTAL_COUNT;
        $doneCount = WP_ImportPropertyRepository::FIELD_DONE_COUNT;
        $status = WP_ImportPropertyRepository::FIELD_STATUS;
        $description = WP_ImportPropertyRepository::FIELD_DESCRIPTION;

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