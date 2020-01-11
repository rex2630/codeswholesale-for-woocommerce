<?php

namespace CodesWholesaleFramework\Database\Repositories;

use CodesWholesaleFramework\Database\Factories\PostbackImportDetailsModelFactory;
use CodesWholesaleFramework\Database\Models\PostbackImportDetailsModel;

class PostbackImportDetailsRepository extends Repository {
    const
        FIELD_ID                    = 'id',
        FIELD_CREATED_AT            = 'created_at',
        FIELD_STATUS                = 'status',
        FIELD_NAME                  = 'name',
        FIELD_IMPORT_ID             = 'import_id',
        FIELD_PRODUCT_ID            = 'product_id',
        FIELD_IMPORT_TIME           = 'import_time',
        FIELD_DESCRIPTION           = 'description',
        FIELD_EXCEPTIIONS           = 'exceptions'
    ;
    
    /**
     * @param PostbackImportDetailsModel $model
     * @return bool
     */
    public function save(PostbackImportDetailsModel $model): bool
    {
        $result = $this->db->insert($this->getTableName(), [
            self::FIELD_STATUS => $model->getStatus(),
            self::FIELD_NAME => $model->getName(),
            self::FIELD_IMPORT_ID => $model->getImportId(),
            self::FIELD_PRODUCT_ID => $model->getProductId(),
            self::FIELD_IMPORT_TIME => $model->getImportTime(),
            self::FIELD_DESCRIPTION => $model->getDescription(),
            self::FIELD_EXCEPTIIONS => $model->getExceptions(),
        ]);

        return false === $result ? false : true;
    }
       

    public function findByImport($importId): array
    {
        $mappedResults = [];

        $results = $this->db->get($this->getTableName(), [], [
            self::FIELD_IMPORT_ID => $importId
        ]);

        if (0 === count($results)) {
            return $mappedResults;
        }

        foreach ($results as $item) {
            $mappedResults[] = PostbackImportDetailsModelFactory::resolve((object) $item);
        }

        return $mappedResults;
    }
    
    /**
     * @return bool
     */
    public function createTable(): bool
    {
        $this->db->addTable($this->getTableName(), [
            self::FIELD_ID => 'INT NOT NULL AUTO_INCREMENT',
            self::FIELD_CREATED_AT => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
            self::FIELD_IMPORT_ID => 'INT',
            self::FIELD_NAME => 'VARCHAR(100)',
            self::FIELD_STATUS => 'VARCHAR(100)',
            self::FIELD_IMPORT_TIME => 'VARCHAR(100)',
            self::FIELD_PRODUCT_ID => 'VARCHAR(100)',
            self::FIELD_DESCRIPTION => 'TEXT',
            self::FIELD_EXCEPTIIONS => 'TEXT',
        ], self::FIELD_ID);

        return true;
    }
    
    /**
     * @return string
     */
    protected function getName(): string
    {
        return 'postback_import_details';
    }
}