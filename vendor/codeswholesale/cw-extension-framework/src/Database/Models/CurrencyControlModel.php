<?php
namespace CodesWholesaleFramework\Database\Models;

class CurrencyControlModel
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @var string
     */
    protected $currency;

    /**
     * @var string
     */
    protected $currencyName;

    /**
     * @var string
     */
    protected $rate;

    /**
     * @var \DateTime
     */
    protected $rateUpdatedAt;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return CurrencyControlModel
     */
    public function setId(int $id): CurrencyControlModel
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     * @return CurrencyControlModel
     */
    public function setUpdatedAt(\DateTime $updatedAt): CurrencyControlModel
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     * @return CurrencyControlModel
     */
    public function setCurrency(string $currency): CurrencyControlModel
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @return string
     */
    public function getCurrencyName(): string
    {
        return $this->currencyName;
    }

    /**
     * @param string $currencyName
     * @return CurrencyControlModel
     */
    public function setCurrencyName(string $currencyName): CurrencyControlModel
    {
        $this->currencyName = $currencyName;

        return $this;
    }

    /**
     * @return string
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * @param string $rate
     * @return CurrencyControlModel
     */
    public function setRate(string $rate = null): CurrencyControlModel
    {
        $this->rate = $rate;

        return $this;
    }

    /**
     * @return \DateTime | null
     */
    public function getRateUpdatedAt()
    {
        return $this->rateUpdatedAt;
    }

    /**
     * @param \DateTime $rateUpdatedAt
     * @return CurrencyControlModel
     */
    public function setRateUpdatedAt(\DateTime $rateUpdatedAt): CurrencyControlModel
    {
        $this->rateUpdatedAt = $rateUpdatedAt;

        return $this;
    }
}
