<?php

namespace CodesWholesaleFramework\Orders\Codes;

/**
 *   This file is part of codeswholesale-plugin-framework.
 *
 *   codeswholesale-plugin-framework is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   codeswholesale-plugin-framework is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with codeswholesale-plugin-framework; if not, write to the Free Software
 *   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

use CodesWholesale\Resource\Product;
use CodesWholesale\Resource\Order;
use CodesWholesaleFramework\Orders\Utils\DataBaseExporter;

/**
 * Class PurchaseCode
 */
class PurchaseCode
{
    /**
     * @param array            $productsToBuy
     * @param array            $productIds
     * @param DataBaseExporter $exporter
     * @param int              $internalOrderId
     *
     * @return int
     */
    public function purchase(array $productsToBuy, array $productIds, DataBaseExporter $exporter, int $internalOrderId): int
    {
        $preOrdersCount = 0;
        $order = Order::createOrder($productsToBuy, $internalOrderId, true);

        foreach ($order->getProducts() as $product) {
            $numberOfPreOrders = 0;
            $links = [];

            foreach ($product->getCodes() as $code) {

                if ($code->isPreOrder()) {
                    $numberOfPreOrders++;
                    $preOrdersCount++;
                }

                $links[] = $code->getCodeId();
            }

            $orderedCodes = [
                'cwOrderId' => $order->getOrderId(),
                'links' => $links,
                'preOrders' => $numberOfPreOrders
            ];

            $exporter->export(null, $orderedCodes, $productIds[$product->getProductId()], $internalOrderId);
        }

        return $preOrdersCount;
    }
}