<?php

namespace CodesWholesaleFramework\Provider;

use CodesWholesaleFramework\Api\Currency;
use CodesWholesaleFramework\Database\Factories\CurrencyControlModelFactory;
use CodesWholesaleFramework\Database\Interfaces\DbManagerInterface;
use CodesWholesaleFramework\Database\Models\CurrencyControlModel;
use CodesWholesaleFramework\Database\Repositories\CurrencyControlRepository;

/**
 * Class CurrencyProvider
 */
class CurrencyProvider
{
    /**
     * @var CurrencyControlRepository
     */
    protected $repository;

    /**
     * CurrencyProvider constructor.
     * @param DbManagerInterface $db
     */
    public function __construct(DbManagerInterface $db)
    {
        $this->repository = new CurrencyControlRepository($db);
    }

    /**
     * @param string $default
     * @return array|CurrencyControlModel[]
     */
    public function getAllCurrencies($default = 'EUR')
    {
        /* @var $currencies CurrencyControlModel[] */
        $currencies = $this->repository->findAll();

        try {
            return $this->preProcessCurrencies($currencies);
        } catch(\Exception $ex) {
            /* @var $currency CurrencyControlModel */
            $currency = (new CurrencyControlModel())->setCurrency($default)->setCurrencyName($default);

            return  [$currency];
        }
    }

    /**
     * @param $currencyId
     * @return float
     * @throws \Exception
     */
    public function getRate($currencyId)
    {
        try {
            /* @var $currency CurrencyControlModel */
            $currency = $this->repository->find($currencyId);

            return (float) $this->preProcessCurrencyRate($currency);
        } catch(\Exception $ex) {
            throw $ex;
        }
    }


    /**
     * @param CurrencyControlModel $currency
     * @return null|string
     * @throws \Exception
     */
    private function preProcessCurrencyRate(CurrencyControlModel $currency)
    {
        if(! $currency->getRate() || $currency->getRate() == null || $this->isOldDate($currency->getRateUpdatedAt())) {
            try {
                return $this->setCurrencyRate($currency);
            } catch(\Exception $ex) {
                throw $ex;
            }
        }

        return $currency->getRate();
    }

    /**
     * @param CurrencyControlModel $currency
     * @return null|string
     * @throws \Exception
     */
    private function setCurrencyRate(CurrencyControlModel $currency)
    {
        try {
            $rate = Currency::getRate($currency->getCurrency());
            $this->repository->setRate($currency->getCurrency(), $rate);

            return $rate;
        } catch(\Exception $ex) {
            if ($currency->getRate() && $currency->getRate() != null) {
                return $currency->getRate();
            }

            throw $ex;
        }
    }

    /**
     * @param CurrencyControlModel[]
     * @return CurrencyControlModel[]
     * @throws \Exception
     */
    private function preProcessCurrencies($currencies)
    {
        if(count($currencies) == 0) {
            try {
                return $this->setNewCurrencies();
            } catch(\Exception $ex) {
                throw $ex;
            }
        }

        /* @var $currency CurrencyControlModel */
        $currency = $currencies[0];

        if($this->isOldDate($currency->getUpdatedAt())) {
            return $this->setUpdatedCurrencies($currencies);
        }

        return $currencies;
    }

    /**
     * @return CurrencyControlModel[]
     * @throws \Exception
     */
    private function setNewCurrencies()
    {
        try {
            $currencies = Currency::getAllCurrencies();
        } catch(\Exception $ex) {
            throw $ex;
        }

        $models = [];

        foreach($currencies as $c) {
            $model = CurrencyControlModelFactory::createInstanceToSave($c);
            $models[] = $model;

            $this->repository->save($model);
        }

        return $models;
    }

    /**
     * @param $default
     * @return array
     */
    private function setUpdatedCurrencies($default)
    {
        try {
            $currencies = Currency::getAllCurrencies();
        } catch(\Exception $ex) {
            return $default;
        }

        $models = [];

        foreach($currencies as $c) {
            try {
                $model = $this->repository->find($c->id);
                $this->repository->overwrite($model);
            } catch(\Exception $ex) {
                $model = CurrencyControlModelFactory::createInstanceToSave($c);

                $this->repository->save($model);
            }
            $models[] = $model;
        }

        return $models;
    }

    /**
     * @param $used
     * @return bool
     */
    private function isOldDate($used) {
        $yesterday = new \DateTime("yesterday");

        return $used <= $yesterday;
    }
}
