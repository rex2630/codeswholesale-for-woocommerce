<?php

namespace CodesWholesaleFramework\Api;

/**
 * Class Currency
 */
class Currency
{
    const API = 'https://free.currconv.com/api/v7';
    const MAX_REQUEST = 3;
    const REQUEST_SLEEP_TIME = 1;
    const API_KEY_QUERY = "apiKey=b2b0cbe2f18969bca470";

    private static $lastUsedCurrency = '';
    private static $lastUsedRate = '';

    private static $requestNumber = 1;

    /**
     * @return mixed
     * @throws \Exception
     */
    public static function getAllCurrencies()
    {
        $content = @file_get_contents(self::API . "/currencies?" . self::API_KEY_QUERY);

        if (!$content) {
            throw new \Exception("Currency provider is not responding.");
        }

        $result  = json_decode($content);

        return $result->results;
    }

    /**
     * @param $id
     * @throws \Exception
     */
    public static function setRate($id) {
        if('EUR' === $id) {
            self::$lastUsedCurrency = $id;
            self::$lastUsedRate = 1;
        } else {
            self::convert($id);
        }
    }

    /**
     * @param $id
     * @return string
     * @throws \Exception
     */
    public static function getRate($id)
    {
        if(!$id || self::$lastUsedCurrency !== $id) {
            self::setRate($id);
        }

        return self::$lastUsedRate;
    }

    /**
     * @param $result
     * @param $convert
     * @return bool
     */
    public static function issetRate($result, $convert) {
        return
            isset($result->results) &&
            isset($result->results->$convert->val) &&
            $result->results->$convert->val > 0;
    }

    /**
     * @param $id
     * @throws \Exception
     */
    private static function convert($id) {
        $convert = "EUR_" . $id;

        $content = @file_get_contents(self::API . "/convert?q=" . $convert . "&" . self::API_KEY_QUERY);

        $result  = json_decode($content);

        if($id && self::$requestNumber <= 3) {
            if(self::issetRate($result, $convert)) {
                self::$lastUsedCurrency = $id;
                self::$lastUsedRate = $result->results->$convert->val;
            } else {
                self::$requestNumber++;
                sleep(self::REQUEST_SLEEP_TIME);
                self::convert($id);
            }
        } else {
            throw new \Exception("Currency provider is not responding.");
        }
    }
}
