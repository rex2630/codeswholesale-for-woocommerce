<?php
namespace CodesWholesaleFramework\Database\Repositories;

use CodesWholesaleFramework\Database\Factories\ImportPropertyModelFactory;
use CodesWholesaleFramework\Database\Models\ImportPropertyModel;

class ImportPropertyRepository extends Repository {
    const
        FIELD_ID                    = 'id',
        FIELD_USER_ID               = 'user_id',
        FIELD_CREATED_AT            = 'created_at',
        FIELD_INSERT_COUNT          = 'insert_count',
        FIELD_UPDATE_COUNT          = 'update_count',
        FIELD_TOTAL_COUNT           = 'total_count',
        FIELD_DONE_COUNT            = 'done_count',
        FIELD_STATUS                = 'status',
        FIELD_ACTION                = 'action',
        FIELD_TYPE                  = 'type',
        FIELD_IN_STOCK_DAYS_AGO     = 'in_stock_days_ago',
        FIELD_FILTERS               = 'filters',
        FIELD_DESCRIPTION           = 'description'
    ;

    const
        FILTERS_KEY_PLATFORM = 'platform',
        FILTERS_KEY_REGION   = 'region',
        FILTERS_KEY_LANGUAGE = 'language'
    ;

    const
        FILTERS_TYPE_ALL        = 'all',
        FILTERS_TYPE_BY_FILTERS = 'by_filters'
    ;

    /**
     * @param ImportPropertyModel $model
     * @return bool
     */
    public function save(ImportPropertyModel $model): bool
    {
        $result = $this->db->insert($this->getTableName(), [
            self::FIELD_USER_ID => $model->getUserId(),
            self::FIELD_ACTION => $model->getAction(),
            self::FIELD_TYPE => $model->getType(),
            self::FIELD_IN_STOCK_DAYS_AGO => $model->getInStockDaysAgo(),
            self::FIELD_FILTERS => $model->hasFilters() ? json_encode($model->getFilters()) : null,
        ]);

        return false === $result ? false : true;
    }

    /**
     * @param ImportPropertyModel $model
     * @return bool
     */
    public function overwrite(ImportPropertyModel $model): bool
    {
        $result = $this->db->update($this->getTableName(), [
            self::FIELD_USER_ID => $model->getUserId(),
            self::FIELD_CREATED_AT => $model->getCreatedAt()->format('Y-m-d H:i:s'),
            self::FIELD_INSERT_COUNT => $model->getInsertCount(),
            self::FIELD_UPDATE_COUNT => $model->getUpdateCount(),
            self::FIELD_TOTAL_COUNT => $model->getTotalCount(),
            self::FIELD_DONE_COUNT => $model->getDoneCount(),
            self::FIELD_STATUS => $model->getStatus(),
            self::FIELD_DESCRIPTION => $model->getDescription(),
        ], [
            self::FIELD_ID => $model->getId()
        ]);

        return false === $result ? false : true;
    }

    /**
     * @param int $id
     * @return ImportPropertyModel
     * @throws \Exception
     */
    public function find(int $id): ImportPropertyModel
    {
        $results = $this->db->get($this->getTableName(), [], [
            self::FIELD_ID => $id,
        ]);

        if (0 === count($results)) {
            throw new \Exception('Import not found');
        }

        if (1 > count($results)) {
            throw new \Exception('Too much results');
        }

        return ImportPropertyModelFactory::resolve((object) $results[0]);
    }

    /**
     * @return ImportPropertyModel
     * @throws \Exception
     */
    public function findActive(): ImportPropertyModel
    {
        $results = $this->db->get($this->getTableName(), [], [
            self::FIELD_STATUS =>  [ImportPropertyModel::STATUS_NEW, ImportPropertyModel::STATUS_IN_PROGRESS]
        ], 'OR');

        if (0 === count($results)) {
            throw new \Exception('Not found active import');
        }

        if (1 > count($results)) {
            throw new \Exception('Too much results');
        }

        return ImportPropertyModelFactory::resolve((object) $results[0]);
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        $results = $this->db->get($this->getTableName(), [], [
            self::FIELD_STATUS =>  [ImportPropertyModel::STATUS_NEW, ImportPropertyModel::STATUS_IN_PROGRESS]
        ], 'OR');

        if (0 === count($results)) {
            return false;
        }

        return true;
    }

    public function findAll(): array
    {
        $mappedResults = [];

        $results = $this->db->get($this->getTableName(), [], []);

        if (0 === count($results)) {
            return $mappedResults;
        }

        foreach ($results as $item) {
            $mappedResults[] = ImportPropertyModelFactory::resolve((object) $item);
        }

        return $mappedResults;
    }

    /**
     * @param ImportPropertyModel $model
     * @return bool
     */
    public function delete(ImportPropertyModel $model): bool
    {
        $result = $this->db->remove($this->getTableName(), [
            self::FIELD_ID => $model->getId(),
        ]);

        return false === $result ? false : true;
    }

    /**
     * @return bool
     */
    public function createTable(): bool
    {
        $this->db->addTable($this->getTableName(), [
            self::FIELD_ID => 'INT NOT NULL AUTO_INCREMENT',
            self::FIELD_USER_ID => 'INT',
            self::FIELD_CREATED_AT => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
            self::FIELD_ACTION => 'VARCHAR(100)',
            self::FIELD_TYPE => 'VARCHAR(100)',
            self::FIELD_IN_STOCK_DAYS_AGO => 'VARCHAR(100)',
            self::FIELD_FILTERS => 'TEXT',
            self::FIELD_INSERT_COUNT => 'INT NOT NULL DEFAULT 0',
            self::FIELD_UPDATE_COUNT => 'INT NOT NULL DEFAULT 0',
            self::FIELD_TOTAL_COUNT => 'INT NOT NULL DEFAULT 0',
            self::FIELD_DONE_COUNT => 'INT NOT NULL DEFAULT 0',
            self::FIELD_STATUS => 'VARCHAR(20) NOT NULL DEFAULT "' .  ImportPropertyModel::STATUS_NEW . '"',
            self::FIELD_DESCRIPTION => 'TEXT',
        ], self::FIELD_ID);

        return true;
    }

    /**
     * @return string
     */
    protected function getName(): string
    {
        return 'import_properties';
    }
}