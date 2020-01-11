<?php

namespace CodesWholesaleFramework\Provider;

use CodesWholesaleFramework\Database\Interfaces\DbManagerInterface;

/**
 * Class PriceProvider
 */
class PriceProvider
{

    /**
     * @var DbManagerInterface
     */
    protected $db;

    /**
     * @var CurrencyProvider
     */
    protected $currencyProvider;

    /**
     * PriceProvider constructor.
     * @param DbManagerInterface $db
     */
    public function __construct(DbManagerInterface $db)
    {
        $this->db = $db;
        $this->currencyProvider = new CurrencyProvider($db);
    }

    /**
     * @param $spread_type
     * @param $spread_value
     * @param $stock_price
     * @param $product_price_charmer
     * @param $currency
     *
     * @return float
     */
    public function getCalculatedPrice($spread_type, $spread_value, $stock_price, $product_price_charmer, $currency)
    {
        $price = $stock_price;

        switch($spread_type) {
            case 0;
                $price = $this->calculateProductsPriceByFlat($spread_value, $stock_price, $currency);
                break;
            case 1:
                $price = $this->calculateProductsPriceByPercent($spread_value, $stock_price, $currency);
                break;
        }

        $price = round($price, 2);

        if($product_price_charmer) {
            $price = $this->priceAdjuster($price);
        }

        return $price;
    }

    /**
     * @param float $spread_value
     * @param       $stock_price
     * @param       $currency
     *
     * @return float
     */
    private function calculateProductsPriceByFlat(float $spread_value, $stock_price, $currency)
    {
        $calculatedPriceByCurrencyRate =  $this->getCalculatedPriceByCurrencyRate($stock_price, $currency);

        return $calculatedPriceByCurrencyRate + $spread_value;
    }

    /**
     * @param float $spread_value
     * @param       $stock_price
     * @param       $currency
     *
     * @return float
     */
    private function calculateProductsPriceByPercent(float $spread_value, $stock_price, $currency)
    {
        $calculatedPriceByCurrencyRate =  $this->getCalculatedPriceByCurrencyRate($stock_price, $currency);

        return $calculatedPriceByCurrencyRate +  ( $calculatedPriceByCurrencyRate * $spread_value / 100);
    }

    /**
     * @param $stock_price
     * @param $currency
     * @return float|int
     */
    private function getCalculatedPriceByCurrencyRate($stock_price, $currency) {
        try {
            $rate =  $this->currencyProvider->getRate($currency);
        } catch(\Exception $ex) {
            $rate = 1;
        }

        return  floatval($stock_price) * floatval($rate);
    }

    /**
     * @param $price
     *
     * @return float
     */
    private function priceAdjuster($price) {
        // min,max, value
        $price_ranges = [
            [01, 29, 29],
            [30, 49, 49],
            [50, 79, 79],
            [80, 99, 99],
            [00, 00, 99],
        ];

        list($whole, $decimal) = explode('.', $price);

        foreach ($price_ranges as &$value) {
            if($decimal >= $value[0] && $decimal <= $value[1]) {
                $decimal = $value[2];
                break;
            }
        }

        $price = $whole.'.'.$decimal;

        return floatval($price);
    }
}
