<?php

use CodesWholesaleFramework\Retriever\ItemRetriever;

class WP_Order_Item_Retriever implements ItemRetriever
{

    public function retrieveItem($mergedValues)
    {
        $cw_product_id = get_post_meta($mergedValues['item']['product_id'], CodesWholesaleConst::PRODUCT_CODESWHOLESALE_ID_PROP_NAME, true);
        $qty = intval($mergedValues['item']['qty']);

        $item = array(
            'productId' => $cw_product_id,
            'quantity' => $qty
        );
        
        if ($this->isDoubleCheckPrice()) {
            $item['price'] = get_post_meta($mergedValues['item']['product_id'], CodesWholesaleConst::PRODUCT_STOCK_PRICE_PROP_NAME , true);
        }

        return $item;
    }

    private function isDoubleCheckPrice() 
    {
        $options = CW()->get_options();
        
        if (array_key_exists(CodesWholesaleConst::DOUBLE_CHECK_PRICE_PROP_NAME, $options)) {
            if (1 == $options[CodesWholesaleConst::DOUBLE_CHECK_PRICE_PROP_NAME]) {
                return true;
            }
        }
        
        return false;
    }
}