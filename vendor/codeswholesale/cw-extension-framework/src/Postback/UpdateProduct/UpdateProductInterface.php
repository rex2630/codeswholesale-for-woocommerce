<?php

namespace CodesWholesaleFramework\Postback\UpdateProduct;

use CodesWholesale\Resource\FullProduct;
/**
 * Interface UpdateProductInterface
 */
interface UpdateProductInterface
{
    public function updateProduct($cwProductId, $quantity = null, $priceSpread = null);

    public function hideProduct($cwProductId);

    public function newProduct($cwProductId);
    
    /**
     * 
     * @param FullProduct[]
     */
    public function fullProducts(array $fullProducts);
}