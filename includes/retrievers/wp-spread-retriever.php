<?php

use CodesWholesaleFramework\Postback\Retriever\SpreadRetriever;

class WP_Spread_Retriever implements SpreadRetriever {


    public function getSpreadParams(){

        $options = CW()->instance()->get_options();
        $spread_type = $options['spread_type'];
        $spread_value = $options['spread_value'];

        $spread_params = array(
            'cwSpreadType' => $spread_type,
            'cwSpread' => $spread_value
        );

        return $spread_params;
    }
}