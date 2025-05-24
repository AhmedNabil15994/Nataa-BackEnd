<?php

namespace Modules\Transaction\Services;

interface PaymentInterface
{
    public function send($order, $payment = "knet", $type = "api-order");

    public function getResultForPayment($order, $type = "api-order", $payment = "knet");
}
