<?php

namespace CodesWholesaleFramework\Connection;

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

use CodesWholesale\CodesWholesale;
use CodesWholesale\ClientBuilder;
use CodesWholesale\Client;
use CodesWholesale\Storage\TokenDatabaseStorage;
use CodesWholesale\Storage\TokenSessionStorage;
use CodesWholesaleFramework\Postback\UpdateOrder\UpdateOrderInterface;
use CodesWholesaleFramework\Postback\UpdateProduct\UpdateProductInterface;

/**
 * Class Connection
 */
class Connection
{
    const SANDBOX_CLIENT_ID = 'ff72ce315d1259e822f47d87d02d261e';
    const SANDBOX_CLIENT_SECRET = '$2a$10$E2jVWDADFA5gh6zlRVcrlOOX01Q/HJoT6hXuDMJxek.YEo.lkO2T6';
    const SANDBOX_SIGNATURE = 'b4cded07-e13e-4021-8b9f-a3cee994109b';
    
    /**
     * @var Client|null
     */
    private static $connection;

    /**
     * @param array                       $options
     * @param UpdateProductInterface|null $productUpdater
     * @param UpdateOrderInterface|null   $orderUpdater
     *
     * @return Client
     */
    public static function getConnection(array $options): Client
    {
        if (self::$connection === null) {
            $builder = new ClientBuilder([
                'cw.endpoint_uri' => $options['environment'] == 0 ? CodesWholesale::SANDBOX_ENDPOINT : CodesWholesale::LIVE_ENDPOINT,
                'cw.client_id' => $options['environment'] == 0 ? self::SANDBOX_CLIENT_ID : $options['client_id'],
                'cw.client_secret' => $options['environment'] == 0 ? self::SANDBOX_CLIENT_SECRET : $options['client_secret'],
                'cw.token_storage' => isset($options['db']) && $options['db'] instanceof \PDO ? new TokenDatabaseStorage($options['db'], $options['prefix']) : new TokenSessionStorage(),
                'cw.client.headers' => [
                    'User-Agent' => $options['client_headers'],
                ],
                'cw.signature' => isset($options['signature']) ? $options['signature'] : self::SANDBOX_SIGNATURE
            ]);

            self::$connection = $builder->build();
       }
        
        return self::$connection;
    }
    
    /**
     * @return bool
     */
    public static function hasConnection(): bool{
        return self::$connection != null;
    }
}