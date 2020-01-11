<?php
namespace CodesWholesaleFramework\Database\Repositories;

use CodesWholesaleFramework\Database\Factories\PostbackImportModelFactory;
use CodesWholesaleFramework\Database\Models\PostbackImportModel;

class PostbackImportRepository extends Repository {
    const
        FIELD_ID                    = 'id',
        FIELD_USER_ID               = 'user_id',
        FIELD_EXTERNAL_ID           = 'external_id',
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
     * @param PostbackImportModel $model
     * @return bool
     */
    public function save(PostbackImportModel $model): bool
    {
        $result = $this->db->insert($this->getTableName(), [
            self::FIELD_USER_ID => $model->getUserId(),
            self::FIELD_EXTERNAL_ID => $model->getExternalId(),
            self::FIELD_ACTION => $model->getAction(),
            self::FIELD_TYPE => $model->getType(),
            self::FIELD_IN_STOCK_DAYS_AGO => $model->getInStockDaysAgo(),
            self::FIELD_FILTERS => $model->hasFilters() ? json_encode($model->getFilters()) : null,
        ]);

        return false === $result ? false : true;
    }

    /**
     * @param PostbackImportModel $model
     * @return bool
     */
    public function overwrite(PostbackImportModel $model): bool
    {
 
        $result = $this->db->update($this->getTableName(), [
            self::FIELD_USER_ID => $model->getUserId(),
            self::FIELD_EXTERNAL_ID => $model->getExternalId(),
            self::FIELD_CREATED_AT => $model->getCreatedAt()->format('Y-m-d H:i:s'),
            self::FIELD_TOTAL_COUNT => $model->getTotalCount(),
            self::FIELD_STATUS => $model->getStatus(),
            self::FIELD_DESCRIPTION => $model->getDescription(),
        ], [
            self::FIELD_ID => $model->getId()
        ]);

        return false === $result ? false : true;
    }

    /**
     * @param int $id
     * @return PostbackImportModel
     * @throws \Exception
     */
    public function find(int $id): PostbackImportModel
    {
        $results = $this->db->get($this->getTableName(), [], [
            self::FIELD_ID => $id,
        ]);

        if (0 === count($results)) {
            throw new \Exception('No result');
        }

        if (1 > count($results)) {
            throw new \Exception('Too much results');
        }

        return PostbackImportModelFactory::resolve((object) $results[0]);
    }

    /**
     * @return PostbackImportModel
     * @throws \Exception
     */
    public function findActive(): PostbackImportModel
    {
        $results = $this->db->get($this->getTableName(), [], [
            self::FIELD_STATUS =>  [PostbackImportModel::STATUS_NEW, PostbackImportModel::STATUS_IN_PROGRESS, PostbackImportModel::STATUS_AWAITING]
        ], 'OR');

        if (0 === count($results)) {
            throw new \Exception('No result');
        }

        if (1 > count($results)) {
            throw new \Exception('Too much results');
        }

        return PostbackImportModelFactory::resolve((object) $results[0]);
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        $results = $this->db->get($this->getTableName(), [], [
            self::FIELD_STATUS =>  [PostbackImportModel::STATUS_NEW, PostbackImportModel::STATUS_IN_PROGRESS, PostbackImportModel::STATUS_AWAITING]
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
            $mappedResults[] = PostbackImportModelFactory::resolve((object) $item);
        }

        return $mappedResults;
    }

    /**
     * @param PostbackImportModel $model
     * @return bool
     */
    public function delete(PostbackImportModel $model): bool
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
            self::FIELD_EXTERNAL_ID => 'VARCHAR(100)',
            self::FIELD_CREATED_AT => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
            self::FIELD_ACTION => 'VARCHAR(100)',
            self::FIELD_TYPE => 'VARCHAR(100)',
            self::FIELD_IN_STOCK_DAYS_AGO => 'VARCHAR(100)',
            self::FIELD_FILTERS => 'TEXT',
            self::FIELD_INSERT_COUNT => 'INT NOT NULL DEFAULT 0',
            self::FIELD_UPDATE_COUNT => 'INT NOT NULL DEFAULT 0',
            self::FIELD_TOTAL_COUNT => 'INT NOT NULL DEFAULT 0',
            self::FIELD_DONE_COUNT => 'INT NOT NULL DEFAULT 0',
            self::FIELD_STATUS => 'VARCHAR(20) NOT NULL DEFAULT "' .  PostbackImportModel::STATUS_NEW . '"',
            self::FIELD_DESCRIPTION => 'TEXT',
        ], self::FIELD_ID);

        return true;
    }

    /**
     * @return string
     */
    protected function getName(): string
    {
        return 'postback_import_properties';
    }
    
    public function increaseCreatedCount($id) {
        $query = sprintf("UPDATE %s SET %s = %s + 1 WHERE  %s = %s",
            $this->getTableName(),
            self::FIELD_INSERT_COUNT,
            self::FIELD_INSERT_COUNT,
            self::FIELD_ID,
            $id
        );
        
        $result = $this->db->sql($query);
        
        return false === $result ? false : true;
    }
    
    public function increaseUpdatedCount($id) {
        $query = sprintf("UPDATE %s SET %s = %s + 1 WHERE  %s = %s",
            $this->getTableName(),
            self::FIELD_UPDATE_COUNT,
            self::FIELD_UPDATE_COUNT,
            self::FIELD_ID,
            $id
        );
        
        $result = $this->db->sql($query);
        
        return false === $result ? false : true;
    }
    
    public function increaseDoneCount($id) {
        $query = sprintf("UPDATE %s SET %s = %s + 1 WHERE  %s = %s",
            $this->getTableName(),
            self::FIELD_DONE_COUNT,
            self::FIELD_DONE_COUNT,
            self::FIELD_ID,
            $id
        );
        
        $result = $this->db->sql($query);
        
        return false === $result ? false : true;
    }
    
    public function updateStatusInformation($id, $import) {
        $result = $this->db->update($this->getTableName(), [
            self::FIELD_STATUS => $import->getImportStatus(),
            self::FIELD_DESCRIPTION => $import->getMessage(),
        ], [
            self::FIELD_ID => $id
        ]);

        return false === $result ? false : true;
    }
}