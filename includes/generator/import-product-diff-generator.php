<?php

use CodesWholesaleFramework\Model\ExternalProduct;

/**
 * Class ImportProductDiffGenerator
 */
class ImportProductDiffGenerator
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
    protected $diff = [];

    /**
     * @param ExternalProduct $externalProduct
     * @param WP_Post         $wpProduct
     *
     * @return array
     */
    public function getDiff(ExternalProduct $externalProduct, WP_Post $wpProduct): array
    {
        $this->diff = [];

        $price = get_post_meta($wpProduct->ID, CodesWholesaleConst::PRODUCT_STOCK_PRICE_PROP_NAME, true);
        $stock = get_post_meta($wpProduct->ID, '_stock', true);

        $product = $externalProduct->getProduct();

        if (trim($product->getName()) !== trim($wpProduct->post_title)) {
            $this->generateDiff(self::FIELD_NAME, $wpProduct->post_title, $product->getName());
        }

        if ((string) trim($product->getLowestPrice()) !== trim($price)) {
            $this->generateDiff(self::FIELD_PRICE, $price, $product->getLowestPrice());
        }

        if ((string) trim($product->getStockQuantity()) !== trim($stock)) {
            $this->generateDiff(self::FIELD_STOCK, $stock, $product->getStockQuantity());
        }

        return $this->diff;
    }

    /**
     * @param $value
     * @return string
     */
    private function implodeArray($value): string
    {
        if(is_array($value)) {
            $value = implode("|", $value);
        }

        return $value;
    }

    /**
     * @param $key
     *
     * @param $oldValue
     * @param $newValue
     */
    private function generateDiff($key, $oldValue, $newValue)
    {
        $this->diff[$key] = [
            self::OLD_VALUE => $oldValue,
            self::NEW_VALUE => $newValue
        ];
    }
}