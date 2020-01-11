<?php

namespace CodesWholesaleFramework\Mailer;

/**
 * Interface SendCodeMailer
 */
interface SendCodeMailer
{
    public function sendCodeMail($order, $attachments, $keys, $totalPreOrders);
}