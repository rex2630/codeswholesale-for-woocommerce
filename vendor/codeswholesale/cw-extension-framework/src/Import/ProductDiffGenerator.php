<?php
namespace CodesWholesaleFramework\Import;

/**
 * Class ProductDiffGenerator
 */
class ProductDiffGenerator
{
    const
        FIELD_ID = 'id',
        FIELD_STATUS = 'status',
        FIELD_NAME = 'name',
        FIELD_PLATFORMS = 'platforms',
        FIELD_REGIONS = 'regions',
        FIELD_LANGUAGES = 'languages',
        FIELD_PRICE = 'price',
        FIELD_STOCK = 'stock',
        FIELD_DESCRIPTION = 'description',
        FIELD_COVER = 'cover'
    ;

    const
        OLD_VALUE = 'old_value',
        NEW_VALUE = 'new_value'
    ;
    
    /**
     * @var array
     */
    public $diff = [];
    
    
    /**
     * @param $key
     *
     * @param $oldValue
     * @param $newValue
     */
    public function generateDiff($key, $oldValue, $newValue)
    {
        $this->diff[$key] = [
            self::OLD_VALUE => $oldValue,
            self::NEW_VALUE => $newValue
        ];
    }
    
    /**
     * @param $value
     * @return string
     */
    public static function implodeArray($value): string
    {
        if(is_array($value)) {
            $value = implode("|", $value);
        }

        return $value;
    }
}
