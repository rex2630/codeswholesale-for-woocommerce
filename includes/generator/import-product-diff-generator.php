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

        $productAttributes = get_post_meta($wpProduct->ID, '_product_attributes', true);
        $price = get_post_meta($wpProduct->ID, CodesWholesaleConst::PRODUCT_STOCK_PRICE_PROP_NAME, true);
        $stock = get_post_meta($wpProduct->ID, '_stock', true);

        $product = $externalProduct->getProduct();

        $platform = $this->implodeArray($product->getPlatform());
        $regions = $this->implodeArray($product->getRegions());
        $languages = $this->implodeArray($product->getLanguages());

        if (trim($product->getName()) !== trim($wpProduct->post_title)) {
            $this->generateDiff(self::FIELD_NAME, $wpProduct->post_title, $product->getName());
        }

        if (trim($platform) !== trim($productAttributes[self::FIELD_PLATFORMS]['value'])) {
            $this->generateDiff(self::FIELD_PLATFORMS, $productAttributes[self::FIELD_PLATFORMS]['value'], $platform);
        }

        if (trim($regions) !== trim($productAttributes[self::FIELD_REGIONS]['value'])) {
            $this->generateDiff(self::FIELD_REGIONS, $productAttributes[self::FIELD_REGIONS]['value'], $regions);
        }

        if (trim($languages) !== trim($productAttributes[self::FIELD_LANGUAGES]['value'])) {
            $this->generateDiff(self::FIELD_LANGUAGES, $productAttributes[self::FIELD_LANGUAGES]['value'], $languages);
        }

        if ((string) trim($product->getLowestPrice()) !== trim($price)) {
            $this->generateDiff(self::FIELD_PRICE, $price, $product->getLowestPrice());
        }

        if ((string) trim($product->getStockQuantity()) !== trim($stock)) {
            $this->generateDiff(self::FIELD_STOCK, $stock, $product->getStockQuantity());
        }

        if (trim($externalProduct->getDescription()) !== trim($wpProduct->post_content)) {
            $this->generateDiff(self::FIELD_DESCRIPTION, $wpProduct->post_content, $externalProduct->getDescription());
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