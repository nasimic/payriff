<?php

namespace Nasimic\Payriff\Data\Request;

use Nasimic\Payriff\Exceptions\CurrencyNotAllowedException;
use Nasimic\Payriff\Exceptions\LanguageNotAllowedException;
use Nasimic\Payriff\Exceptions\OperationNotAllowedException;

class CreateOrderRequest
{
    /**
     * @throws CurrencyNotAllowedException
     * @throws LanguageNotAllowedException
     * @throws OperationNotAllowedException
     */
    public function __construct(
        public readonly float $amount,
        public readonly ?string $callbackUrl = null,
        public readonly ?string $currency = "AZN",
        public readonly ?string $language = "AZ",
        public readonly ?string $description = null,
        public readonly ?bool $cardSave  = null,
        public readonly ?string $operation = "PURCHASE",
        public readonly ?array $installment  = null,
    ) {
        if (!in_array($this->currency, ['AZN', 'USD', 'EUR'])){
            throw new CurrencyNotAllowedException();
        }

        if (!in_array($this->language, ['AZ', 'EN', 'RU'])){
            throw new LanguageNotAllowedException();
        }

        if (!in_array($this->operation, ['PURCHASE', 'PRE_AUTH'])){
            throw new OperationNotAllowedException();
        }
    }
}
