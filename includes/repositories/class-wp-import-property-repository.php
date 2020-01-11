<?php

/**
 * Class WP_ImportPropertyRepository
 */
class WP_ImportPropertyRepository extends WP_AbstractRepository
{
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
     * @param WP_ImportPropertyModel $model
     *
     * @return bool
     */
    public function save(WP_ImportPropertyModel $model): bool
    {
        $result = $this->db->insert($this->getTableName(), [
            self::FIELD_USER_ID => get_current_user_id(),
            self::FIELD_ACTION => $model->getAction(),
            self::FIELD_TYPE => $model->getType(),
            self::FIELD_IN_STOCK_DAYS_AGO => $model->getInStockDaysAgo(),
            self::FIELD_FILTERS => $model->hasFilters() ? json_encode($model->getFilters()) : null,
        ]);

        return false === $result ? false : true;
    }

    /**
     * @param WP_ImportPropertyModel $model
     *
     * @return bool
     */
    public function update(WP_ImportPropertyModel $model): bool
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
     * @param WP_ImportPropertyModel $model
     *
     * @return bool
     */
    public function delete(WP_ImportPropertyModel $model): bool
    {
        $result = $this->db->delete($this->getTableName(), [
            self::FIELD_ID => $model->getId(),
        ]);

        return false === $result ? false : true;
    }

    /**
     * @param int $id
     *
     * @return WP_ImportPropertyModel
     *
     * @throws Requests_Exception
     */
    public function find(int $id): WP_ImportPropertyModel
    {
        $query = sprintf("SELECT * FROM %s WHERE %s = %s",
            $this->getTableName(),
            self::FIELD_ID,
            $id
        );

        $results = $this->db->get_results($this->db->prepare($query, []));


        if (0 === count($results)) {
            throw new Requests_Exception('No result', 'db_exception');
        }

        if (1 > count($results)) {
            throw new Requests_Exception('Too much results', 'db_exception');
        }

        return WP_ImportPropertyModelFactory::resolve($results[0]);
    }

    /**
     * @return WP_ImportPropertyModel
     *
     * @throws Requests_Exception
     */
    public function findActive(): WP_ImportPropertyModel
    {
        $query = sprintf("SELECT * FROM %s WHERE %s IN('%s', '%s')",
            $this->getTableName(),
            self::FIELD_STATUS,
            WP_ImportPropertyModel::STATUS_NEW,
            WP_ImportPropertyModel::STATUS_IN_PROGRESS
        );

        $results = $this->db->get_results($this->db->prepare($query, []));

        if (0 === count($results)) {
            throw new Requests_Exception('No result', 'db_exception');
        }

        if (1 > count($results)) {
            throw new Requests_Exception('Too much results', 'db_exception');
        }

        return WP_ImportPropertyModelFactory::resolve($results[0]);
    }
    
    /**
     * 
     * @return bool
     */
    public function isActive(): bool
    {
        $query = sprintf("SELECT * FROM %s WHERE %s IN('%s', '%s')",
            $this->getTableName(),
            self::FIELD_STATUS,
            WP_ImportPropertyModel::STATUS_NEW,
            WP_ImportPropertyModel::STATUS_IN_PROGRESS
        );
        
        $results = $this->db->get_results($this->db->prepare($query, []));
        
        if (0 === count($results)) {
            return false;
        }
        
        return true;
        
    }

    /**
     * @return WP_ImportPropertyModel[]
     *
     * @throws Requests_Exception
     */
    public function findAll(): array
    {
        $mappedResults = [];
        
        $query = sprintf("SELECT * FROM %s",
            $this->getTableName()
        );

        $results = $this->db->get_results($this->db->prepare($query, []));

        if (0 === count($results)) {
            return $mappedResults;
        }
        
        foreach ($results as $item) {
            $mappedResults[] = WP_ImportPropertyModelFactory::resolve($item);
        }
        
        return $mappedResults;
    }

    /************ ABSTRACTION ************ ABSTRACTION ************ ABSTRACTION ************ ABSTRACTION ************/

    /**
     * @return bool
     */
    public function createTable(): bool
    {
        $tableName = $this->getTableName();

        if (!$this->exists()) {
            $charset_collate = $this->db->get_charset_collate();

            $query = 'CREATE TABLE ' . $tableName . " (
                ". self::FIELD_ID ."                    INT NOT NULL AUTO_INCREMENT,
                ". self::FIELD_USER_ID ."               INT,
                ". self::FIELD_CREATED_AT ."            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                ". self::FIELD_ACTION ."                VARCHAR(100),
                ". self::FIELD_TYPE ."                  VARCHAR(100),
                ". self::FIELD_IN_STOCK_DAYS_AGO ."     VARCHAR(50),
                ". self::FIELD_FILTERS ."               TEXT,
                ". self::FIELD_INSERT_COUNT ."          INT NOT NULL DEFAULT 0,
                ". self::FIELD_UPDATE_COUNT ."          INT NOT NULL DEFAULT 0,
                ". self::FIELD_TOTAL_COUNT ."           INT NOT NULL DEFAULT 0,
                ". self::FIELD_DONE_COUNT ."            INT NOT NULL DEFAULT 0,
                ". self::FIELD_STATUS ."                VARCHAR(20) NOT NULL DEFAULT '" . WP_ImportPropertyModel::STATUS_NEW . "',
                ". self::FIELD_DESCRIPTION ."           TEXT,
                PRIMARY KEY (". self::FIELD_ID .")
            )";

            $query = sprintf('%s %s;', $query, $charset_collate);

            dbDelta($query);
        }

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