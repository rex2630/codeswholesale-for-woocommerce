<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly


if (!class_exists('CW_Settings_Vo')) :
    /**
     * CW_Admin_Product Class
     */
    class CW_Settings_Vo
    {
        private $env_type;
        private $client_id;
        private $client_secret;
        private $client_endpoint;
        private $currency;

        /**
         * @param mixed $client_id
         */
        public function setClientId($client_id)
        {
            $this->client_id = $client_id;
        }

        /**
         * @return mixed
         */
        public function getClientId()
        {
            return $this->client_id;
        }

        /**
         * @param mixed $client_secret
         */
        public function setClientSecret($client_secret)
        {
            $this->client_secret = $client_secret;
        }

        /**
         * @return mixed
         */
        public function getClientSecret()
        {
            return $this->client_secret;
        }

        /**
         * @param mixed $env_type
         */
        public function setEnvType($env_type)
        {
            $this->env_type = $env_type;
        }

        /**
         * @return mixed
         */
        public function getEnvType()
        {
            return $this->env_type;
        }

        /**
         * @return bool
         */
        public function isSandbox() {
            return $this->getEnvType() == 0;
        }

        /**
         * @return bool
         */
        public function isLive() {
            return $this->getEnvType() == 1;
        }

        /**
         * @param mixed $client_endpoint
         */
        public function setClientEndpoint($client_endpoint)
        {
            $this->client_endpoint = $client_endpoint;
        }

        /**
         * @return mixed
         */
        public function getClientEndpoint()
        {
            return $this->client_endpoint;
        }

        public function getCurrency()
        {
            return $this->currency;
        }

        public function setCurrency($currency)
        {
            $this->currency = $currency;
        }


        /**
         * @return string
         */
        public function toOption() {
            $params = array(
                'cw.client_id' => $this->getClientId(),
                'cw.client_secret' => $this->getClientSecret(),
                'cw.endpoint_uri' => $this->getClientEndpoint()
            );

            return json_encode($params);
        }

        /**
         * @param $option
         * @return CW_Settings_Vo
         */
        public static function fromOption($option) {
            $params = get_object_vars(json_decode($option));

            $vo = new CW_Settings_Vo();
            $vo->setClientEndpoint($params["cw.endpoint_uri"]);
            $vo->setClientId($params["cw.client_id"]);
            $vo->setClientSecret($params["cw.client_secret"]);
            return $vo;
        }

    }

endif;