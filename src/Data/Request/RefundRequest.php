<?php

namespace Nasimic\Payriff\Data\Request;

class RefundRequest
{
    public function __construct(
        public readonly string $amount,
        public readonly string $orderId,
    ) {
    }
}
