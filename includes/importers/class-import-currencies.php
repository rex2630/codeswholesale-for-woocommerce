<?php

/**
 * Created by PhpStorm.
 * User: maciejklowan
 * Date: 22/12/15
 * Time: 14:10
 */
class Import_Currencies
{

    public function import()
    {
        $xml = $this->getXML();
        return $rates = $xml->Cube->Cube->children();
    }

    public function getRateByCurrencyName($currency)
    {
        if ($currency === 'EUR') {

            return array (1);

        } else {

            $rates = $this->import();

            foreach ($rates as $rate) {
                if ($currency == $rate->attributes()->currency) {
                    return (array) floatval($rate->attributes()->rate);
                }
            }
        }
    }

    public function getXML()
    {
        return simplexml_load_file('http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml');
    }
}