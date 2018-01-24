<?php

use CodesWholesaleFramework\Retriever\LinkRetriever;

class WP_Links_Retriever implements LinkRetriever
{

    public function getLinks($item_key){

       $links = json_decode(wc_get_order_item_meta($item_key, CodesWholesaleConst::ORDER_ITEM_LINKS_PROP_NAME));

        return $links;
    }
}