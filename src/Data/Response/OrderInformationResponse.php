<?php

namespace Nasimic\Payriff\Data\Response;

class OrderInformationResponse
{
    public function __construct(
        public readonly string $orderId,
        public readonly float $amount,
        public readonly string $currencyType,
        public readonly ?string $merchantName,
        public readonly string $operationType,
        public readonly string $paymentStatus,
        public readonly bool $auto,
        public readonly string $createdDate,
        public readonly string $description,
        public readonly array $transactions,
    ) {
    }

    public static function fromPayload(array $payload): self
    {
        return new self(
            orderId: $payload['orderId'],
            amount: $payload['amount'],
            currencyType: $payload['currencyType'],
            merchantName: $payload['merchantName'],
            operationType: $payload['operationType'],
            paymentStatus: $payload['paymentStatus'],
            auto: $payload['auto'],
            createdDate: $payload['createdDate'],
            description: $payload['description'],
            transactions: $payload['transactions'],
        );
    }
}
