<?php
namespace CodesWholesaleFramework\Errors;

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

use CodesWholesale\Resource\ResourceError;

/**
 * Class Errors
 */
class Errors
{
    /**
     * @var ErrorHandler
     */
    private $sendAdminErrorMail;

    /**
     * @var ErrorHandler
     */
    private $sendAdminGeneralErrorMail;

    public function __construct(ErrorHandler $sendAdminErrorMail, ErrorHandler $sendAdminGeneralErrorMail)
    {

        $this->sendAdminErrorMail = $sendAdminErrorMail;
        $this->sendAdminGeneralErrorMail = $sendAdminGeneralErrorMail;
    }

    /*
     *   Error support
     */
    public function supportResourceError($order, ResourceError $e)
    {

        if ($e->isInvalidToken()) {

            $this->sendAdminErrorMail->handleError($order, 'Invalid token', $e);
        } else

            // handle scenario when account's balance is not enough to make order
            if ($e->getStatus() == 400 && $e->getErrorCode() == 10002) {

                $this->sendAdminErrorMail->handleError($order, 'Balance too low', $e);
            } else
                // handle scenario when code details where not found
                if ($e->getStatus() == 404 && $e->getErrorCode() == 50002) {

                    $this->sendAdminErrorMail->handleError($order, 'Code not found', $e);
                } else
                    // handle scenario when product was not found in price list
                    if ($e->getStatus() == 404 && $e->getErrorCode() == 20001) {

                        $this->sendAdminErrorMail->handleError($order, 'Product not found', $e);
                    } else
                        // handle when quantity was less then 1
                        if ($e->getStatus() == 400 && $e->getErrorCode() == 40002) {

                            $this->sendAdminErrorMail->handleError($order, 'Quantity less then 1', $e);
                        } else {
                            $this->supportError($order, $e);
                        }
    }

    /**
     * @param            $order
     * @param \Exception $e
     */
    public function supportError($order, \Exception $e)
    {
        return $this->sendAdminGeneralErrorMail->handleError($order, 'Issue', $e);
    }


}