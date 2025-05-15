<?php

namespace Nasimic\Payriff\Data\Response;

class CreateOrderResponse
{
    public function __construct(
        public readonly string $orderId,
        public readonly string $paymentUrl,
        public readonly string $transactionId,
    ) {
    }

    public static function fromPayload(array $payload): self
    {
        return new self(
            $payload['orderId'],
            $payload['paymentUrl'],
            $payload['transactionId'],
        );
    }
}
