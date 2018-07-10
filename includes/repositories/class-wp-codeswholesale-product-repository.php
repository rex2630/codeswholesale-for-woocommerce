<?php

/**
 * Class WP_CodeswholesaleProductRepository
 */
class WP_CodeswholesaleProductRepository extends WP_AbstractRepository
{
    const
        FIELD_ID                    = 'id',
        FIELD_PRODUCT_ID            = 'product_id',
        FIELD_CREATED_AT            = 'created_at',
        FIELD_DESCRIPTION           = 'description',
        FIELD_TITLE                 = 'title',
        FIELD_IMAGE                 = 'image',
        FIELD_GALLERY               = 'gallery',
        FIELD_PREFERRED_LANGUAGE    = 'preferred_language'
    ;
        
    /**
     * @param WP_CodeswholesaleProductModel $model
     * 
     * @return bool
     */
    public function save(WP_CodeswholesaleProductModel $model): bool
    {
        $result = $this->db->insert($this->getTableName(), [
            self::FIELD_PRODUCT_ID => $model->getProductId(),
            self::FIELD_DESCRIPTION => $model->getDescription(),
            self::FIELD_TITLE => $model->getTitle(),
            self::FIELD_IMAGE => $model->getImage(),
            self::FIELD_GALLERY => $model->getGallery(),
            self::FIELD_PREFERRED_LANGUAGE => $model->getPreferredLanguage(),
        ]);

        return false === $result ? false : true;
    }
    
    
    public function update(WP_CodeswholesaleProductModel $model): bool
    {
        $result = $this->db->update($this->getTableName(), [
            self::FIELD_PRODUCT_ID => $model->getProductId(),
            self::FIELD_DESCRIPTION => $model->getDescription(),
            self::FIELD_TITLE => $model->getTitle(),
            self::FIELD_IMAGE => $model->getImage(),
            self::FIELD_GALLERY => $model->getGallery(),
            self::FIELD_PREFERRED_LANGUAGE => $model->getPreferredLanguage(),
        ], [
            self::FIELD_ID => $model->getId()
        ]);

        return false === $result ? false : true;
    }
       
    /**
     * 
     * @param type $product_id
     * @param type $lang
     * @return boolean
     */
    public function isset($product_id, $lang) {
        try {
            $model = $this->find($product_id, $lang);
            
            return !!$model->getId();
        } catch (Requests_Exception $ex) {
            return false;
        }
    }
    
    /**
     * @param string $id
     * 
     * @return \WP_CodeswholesaleProductModel
     * 
     * @throws Requests_Exception
     */
    public function find(string $id, string $lang): WP_CodeswholesaleProductModel
    {
        $query = sprintf("SELECT * FROM %s WHERE %s and %s",
            $this->getTableName(),
            self::FIELD_PRODUCT_ID . " = '"  . $id . "'",
            self::FIELD_PREFERRED_LANGUAGE . " = '"  . $lang . "'"
        );

        $results = $this->db->get_results($this->db->prepare($query, []));


        if (0 === count($results)) {
            throw new Requests_Exception('No result', 'db_exception');
        }

        if (1 > count($results)) {
            throw new Requests_Exception('Too much results', 'db_exception');
        }

        return WP_CodeswholesaleProductModelFactory::resolve($results[0]);
    }
    
    /** 
     * @param WP_CodeswholesaleProductModel $model
     * 
     * @return bool
     */
    public function delete(WP_CodeswholesaleProductModel $model): bool
    {
        $result = $this->db->delete($this->getTableName(), [
            self::FIELD_ID => $model->getId(),
        ]);

        return false === $result ? false : true;
    }

    
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
                ". self::FIELD_PRODUCT_ID ."            VARCHAR(100),
                ". self::FIELD_CREATED_AT ."            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                ". self::FIELD_PREFERRED_LANGUAGE ."    VARCHAR(100),
                ". self::FIELD_DESCRIPTION ."           TEXT,
                ". self::FIELD_TITLE ."                 TEXT,
                ". self::FIELD_IMAGE ."                 TEXT,
                ". self::FIELD_GALLERY ."               TEXT,
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
        return 'products';
    }
}