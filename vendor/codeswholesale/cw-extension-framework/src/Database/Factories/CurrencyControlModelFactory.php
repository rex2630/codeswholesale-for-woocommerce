<?php
namespace CodesWholesaleFramework\Database\Factories;

use CodesWholesaleFramework\Database\Models\CurrencyControlModel;
use CodesWholesaleFramework\Database\Repositories\CurrencyControlRepository;

class CurrencyControlModelFactory
{
    /**
     * @param \stdClass $parameters
     *
     * @return CurrencyControlModel
     */
    public static function resolve(\stdClass $parameters): CurrencyControlModel
    {
        $model = new CurrencyControlModel();

        $id = CurrencyControlRepository::FIELD_ID;
        $currency = CurrencyControlRepository::FIELD_CURRENCY;
        $currencyName = CurrencyControlRepository::FIELD_CURRENCY_NAME;
        $updatedAt = CurrencyControlRepository::FIELD_UPDATED;
        $rate = CurrencyControlRepository::FIELD_RATE;
        $rateUpdatedAt = CurrencyControlRepository::FIELD_RATE_UPDATED;

        $model
            ->setId($parameters->$id)
            ->setCurrency($parameters->$currency)
            ->setCurrencyName($parameters->$currencyName)
            ->setUpdatedAt(new \DateTime($parameters->$updatedAt))
            ->setRate($parameters->$rate)
            ->setRateUpdatedAt(new \DateTime($parameters->$rateUpdatedAt))
        ;

        return $model;
    }

    public static function createInstanceToSave($obj): CurrencyControlModel
    {
        $model = new CurrencyControlModel();

        $model
            ->setCurrency($obj->id)
            ->setCurrencyName($obj->currencyName)
            ->setUpdatedAt(new \DateTime())
        ;

        return $model;
    }
}