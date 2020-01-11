<?php
namespace CodesWholesaleFramework\Database\Repositories;



use CodesWholesaleFramework\Database\Factories\CurrencyControlModelFactory;
use CodesWholesaleFramework\Database\Models\CurrencyControlModel;

class CurrencyControlRepository extends Repository
{
    const
        FIELD_ID = 'id',
        FIELD_CURRENCY = 'currency',
        FIELD_CURRENCY_NAME = 'currency_name',
        FIELD_UPDATED = 'updated_at',
        FIELD_RATE = 'rate',
        FIELD_RATE_UPDATED = 'rate_updated_at'
    ;

    /**
     * @param CurrencyControlModel $model
     * @return bool
     */
    public function save(CurrencyControlModel $model): bool
    {
        $result = $this->db->insert($this->getTableName(), [
            self::FIELD_CURRENCY => $model->getCurrency(),
            self::FIELD_CURRENCY_NAME => $model->getCurrencyName(),
            self::FIELD_RATE => $model->getRate(),
        ]);

        return false === $result ? false : true;
    }

    /**
     * @param CurrencyControlModel $model
     * @return bool
     */
    public function overwrite(CurrencyControlModel $model): bool
    {
        $result = $this->db->update($this->getTableName(), [
            self::FIELD_CURRENCY => $model->getCurrency(),
            self::FIELD_CURRENCY_NAME => $model->getCurrencyName(),
            self::FIELD_UPDATED => $this->getDateNow(),
            self::FIELD_RATE => $model->getRate(),
        ], [
            self::FIELD_ID => $model->getId()
        ]);

        return false === $result ? false : true;
    }

    /**
     * @param $id
     * @param $rate
     * @return bool
     */
    public function setRate($id, $rate) {


        $result = $this->db->update($this->getTableName(), [
            self::FIELD_RATE => $rate,
            self::FIELD_RATE_UPDATED => $this->getDateNow()
        ], [
            self::FIELD_CURRENCY => $id
        ]);

        return false === $result ? false : true;
    }

    /**
     * @param string $currency
     * @return CurrencyControlModel
     * @throws \Exception
     */
    public function find(string $currency): CurrencyControlModel
    {
        $results = $this->db->get($this->getTableName(),[
        ], [
            self::FIELD_CURRENCY => $currency,
        ]);

        if(count($results) === 0 ) {
            throw new \Exception('Currency not found in database');
        }

        return CurrencyControlModelFactory::resolve((object) $results[0]);
    }

    /**
     * @return array
     */
    public function findAll(): array
    {
        $mappedResults = [];

        $results = $this->db->get($this->getTableName(), [], []);

        if (0 === count($results)) {
            return $mappedResults;
        }

        foreach ($results as $item) {
            $mappedResults[] = CurrencyControlModelFactory::resolve((object) $item);
        }

        return $mappedResults;
    }

    /**
     * @return bool
     */
    public function createTable(): bool
    {
        $this->db->addTable($this->getTableName(), [
            self::FIELD_ID => ' INT NOT NULL AUTO_INCREMENT',
            self::FIELD_CURRENCY => ' VARCHAR(50)',
            self::FIELD_CURRENCY_NAME => ' VARCHAR(255)',
            self::FIELD_UPDATED => ' TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
            self::FIELD_RATE => ' VARCHAR(50)',
            self::FIELD_RATE_UPDATED => ' TIMESTAMP'
        ], self::FIELD_ID);

        return true;
    }

    /**
     * @return string
     */
    protected function getName(): string
    {
        return 'currency_control';
    }

    protected function getDateNow() {
        $dt = new \DateTime('now');
        $o = new \ReflectionObject($dt);
        $p = $o->getProperty('date');
        return  $p->getValue($dt);
    }
}