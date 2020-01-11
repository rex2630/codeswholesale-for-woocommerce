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
use CodesWholesale\Resource\Code;
use CodesWholesale\Util\Base64Writer;
use CodesWholesaleFramework\Action;
use CodesWholesaleFramework\Dispatcher\OrderNotificationDispatcher;
use CodesWholesaleFramework\Mailer\SendCodeMailer;
use CodesWholesaleFramework\Provider\OrderDetailsProvider;
use CodesWholesaleFramework\Retriever\LinkRetriever;

/**
 * Class SendCodesAction
 */
class SendCodesAction implements Action
{
    /**
     * @var OrderDetailsProvider
     */
    private $orderDetailsProvider;

    /**
     * @var SendCodeMailer
     */
    private $sendCodeMailer;

    /**
     * @var OrderNotificationDispatcher
     */
    private $orderNotificationDispatcher;

    /**
     * @var LinkRetriever
     */
    private $linkRetriever;

    /**
     * @var array
     */
    private $orderDetails;

    /**
     * @var string
     */
    private $imageCodesPath;


    public function __construct(
        OrderDetailsProvider $orderDetailsProvider,
        SendCodeMailer $sendCodeMailer,
        OrderNotificationDispatcher $orderNotificationDispatcher,
        LinkRetriever $linkRetriever,
        string $imageCodesPath = 'Cw_Attachments'
    )
    {
        $this->orderDetailsProvider = $orderDetailsProvider;
        $this->sendCodeMailer   = $sendCodeMailer;
        $this->orderNotificationDispatcher = $orderNotificationDispatcher;
        $this->linkRetriever    = $linkRetriever;
        $this->imageCodesPath   = $imageCodesPath;
    }

    public function setOrderDetails($orderId)
    {
        $this->orderDetails = $this->orderDetailsProvider->provide($orderId);
    }

    public function process()
    {
        $attachments = array();
        $totalNumberOfKeys = 0;
        $totalPreOrders = 0;
        $keys = array();

        $orderDetails = $this->orderDetails;

        foreach ($orderDetails['orderedItems'] as $item_key => $item) {

            $links = $this->linkRetriever->getLinks($item_key);

            $codes = array();

            if (!empty($links)) {

                foreach ($links as $link) {

                    $code = Code::get($link);

                    if ($code->isImage()) {

                        /** @var Code $code */
                        $attachments[] = Base64Writer::writeImageCode($code, $this->imageCodesPath);
                    }

                    if ($code->isPreOrder()) {

                        $totalPreOrders++;
                    }

                    $codes[] = $code;
                    $totalNumberOfKeys++;
                }

                $keys[] = array(
                    'item' => $item,
                    'codes' => $codes
                );
            }
        }

        $this->sendCodeMailer->sendCodeMail($orderDetails['order'], $attachments, $keys, $totalPreOrders);
        $this->orderNotificationDispatcher->complete($orderDetails['order'], $totalNumberOfKeys, $totalPreOrders);
    }

    /**
     * @param $attachments
     */
    private function cleanAttach($attachments)
    {
        foreach ($attachments as $attachment) {
            if (file_exists($attachment)) {

                unlink($attachment);
            }
        }
    }
}
