<?php

namespace Nasimic\Payriff\Interfaces;

use Nasimic\Payriff\Data\Request\CreateOrderRequest;
use Nasimic\Payriff\Data\Request\RefundRequest;
use Nasimic\Payriff\Data\Response\CreateOrderResponse;
use Nasimic\Payriff\Data\Response\OrderInformationResponse;

interface PayriffClientInterface
{
    public function createOrder(CreateOrderRequest $orderRequest): CreateOrderResponse;

    public function getOrderInformation(string $orderId): OrderInformationResponse;

    public function getOrderStatus(string $orderId): string;

    public function refund(RefundRequest $refundRequest): bool;

}
