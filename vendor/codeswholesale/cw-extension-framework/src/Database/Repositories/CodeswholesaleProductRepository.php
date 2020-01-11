<?php
namespace CodesWholesaleFramework\Database\Repositories;

use CodesWholesaleFramework\Database\Factories\CodeswholesaleProductModelFactory;
use CodesWholesaleFramework\Database\Models\CodeswholesaleProductModel;


class CodeswholesaleProductRepository extends Repository {
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
     * @param CodeswholesaleProductModel $model
     * @return bool
     */
    public function save(CodeswholesaleProductModel $model): bool
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

    /**
     * @param CodeswholesaleProductModel $model
     * @return bool
     */
    public function overwrite(CodeswholesaleProductModel $model): bool
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
     * @param $product_id
     * @param $lang
     * @return bool
     */
    public function isset($product_id, $lang) {
        try {
            $model = $this->find($product_id, $lang);

            return !!$model->getId();
        } catch (\Exception $ex) {
            return false;
        }
    }

    /**
     * @param string $id
     * @param string $lang
     * @return CodeswholesaleProductModel
     * @throws \Exception
     */
    public function find(string $id, string $lang): CodeswholesaleProductModel
    {
        $results = $this->db->get($this->getTableName(),[
        ], [
            self::FIELD_PRODUCT_ID => $id,
            self::FIELD_PREFERRED_LANGUAGE => $lang
        ]);

        if(count($results) === 0 ) {
            throw new \Exception('Codeswholesale product not found in database');
        }

        return CodeswholesaleProductModelFactory::resolve((object) $results[0]);
    }

    /**
     * @param CodeswholesaleProductModel $model
     * @return bool
     */
    public function delete(CodeswholesaleProductModel $model): bool
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
        $this->db->addTable($this->getTableName(),[
            self::FIELD_ID => 'INT NOT NULL AUTO_INCREMENT',
            self::FIELD_PRODUCT_ID => 'VARCHAR(100)',
            self::FIELD_CREATED_AT => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
            self::FIELD_PREFERRED_LANGUAGE => 'VARCHAR(100)',
            self::FIELD_DESCRIPTION => 'TEXT',
            self::FIELD_TITLE=> 'TEXT',
            self::FIELD_IMAGE=> 'TEXT',
            self::FIELD_GALLERY => ' TEXT',
        ], self::FIELD_ID);


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