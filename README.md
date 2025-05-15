# Payriff package for Laravel apps

This repo can be used as to implement Payriff Payments (V3) to Laravel application. Supporting PHP 8.1+ versions.


## Installation

```bash
composer require nasimic/payriff
```

## Configuration

Normally config file will be published automatically. In case of manual publish:
```bash
php artisan vendor:publish --tag=payriff-config
```

Add your credentials to .env file
```bash
PAYRIFF_SECRET_KEY=your secret here
```

## Usage

PayriffServiceProvider generates PayriffClient to inject directly to a constructor or to a method. And it is highly recommended to chose Dependency Injection over Facades.

With constructor:
```php
use Nasimic\Payriff\PayriffClient;

public function __construct(
    private readonly PayriffClient $payriffClient,
) {
}
```
With method
```php
use Nasimic\Payriff\PayriffClient;

public function pay(PayriffClient $payriffClient) {
...
}
```

Alternatively, you could get PayriffClient instance from container or bind with PayriffClientInterface on service provider.
## Examples



## Create order
You will play with Data classes (DTO) to send and get populated data. Good for validation purposes and valid states.
```php
use Nasimic\Payriff\PayriffClient;
use Nasimic\Payriff\Data\Request\CreateOrderRequest;

...

public function createPaymentUrl(Order $order) {
    $orderRequestDto = new CreateOrderRequest(
        amount: $order->price,
        callbackUrl: CUSTOM_CALLBACK_URL // optional
    );

    $orderResponseDto = $this->payriffClient->createOrder($orderRequestDto);
    
    // You will need orderId to retrieve order info later.
    // Save orderId. This represents payment of your order on Payriff.
    // Let's say save it on DB column.
    $order->update(['payriff_payment_id' => $orderResponseDto->orderId]);
    
    return $orderResponseDto->paymentUrl;
}
```
You will get [CreateOrderResponse](src/Data/Response/CreateOrderResponse.php) class with populated data. Save orderId and redirect user to the Payment URL. 

If the callbackUrl are left blank, then user will be redirected to proper Payriff pages (<a href="https://payriff.com/success.html">Success</a>, <a href="https://payriff.com/cancel.html">Cancel</a>, <a href="https://payriff.com/decline.html">Decline</a>)

On successful transaction, Payriff will POST transaction data to callbackURL. However, it is recommended to handle payment callback with [Get Order](#get-order) (after Payriff redirecting to callbackUrl). Also consider updating statuses of abandoned Payments. 

More opinionated Create order request:
```php
$orderRequestDto = new CreateOrderRequest(
    amount: 75,
    callbackUrl: CUSTOM_CALLBACK_URL,
    currency: "USD", // default is AZN
    language: "RU", // default is AZ
    description: "Test order",
    cardSave: true,
    operation: "PRE_AUTH", // default is PURCHASE
    installment: [
        "type" => "TAMKART", 
        "period" => "PERIOD_2"
    ]
);
}
```

## Get Order
You can retrieve order with multiple functions depending on purpose
```php
public function handleCallback(Order $order) {

    // To get all information about order (returns OrderInformationResponse)
    $this->payriffClient->getOrderInformation($order->payriff_payment_id);
    
    // To get only payment status (returns string)
    $this->payriffClient->getOrderStatus($order->payriff_payment_id);
    
    // To check order is paid (returns bool)
    $this->payriffClient->isOrderPaid($order->payriff_payment_id);
    
    ...
}
```
OrderInformationResponse is Data object and has these properties populated:
```php
string $orderId,
float $amount,
string $currencyType,
?string $merchantName,
string $operationType,
string $paymentStatus,
bool $auto,
string $createdDate,
string $description,
array $transactions,
```


## Refund

```php
public function refund(Order $order) {
    
    $refundRequestDto = new RefundRequest(
        amount: $order->price,
        orderId: $order->payriff_payment_id
    );
    
    // Returns bool
    $this->payriffClient->refund($refundRequestDto);
    
    ...
}
```
**NOTE**: The refund request is used to cancel a payment for an order. This functionality is available within a limited period (specified by the bank) after a payment has been executed.

## Exceptions
Consider catching exceptions and providing user-friendly errors

- [CurrencyNotAllowedException](src/Exceptions/CurrencyNotAllowedException.php)
- [LanguageNotAllowedException](src/Exceptions/LanguageNotAllowedException.php)
- [OperationNotAllowedException](src/Exceptions/OperationNotAllowedException.php)
- [OrderCreationException](src/Exceptions/OrderCreationException.php)
- [OrderInformationException](src/Exceptions/OrderInformationException.php)
- [OrderStatusException](src/Exceptions/OrderStatusException.php)
- [RefundException](src/Exceptions/RefundException.php)

like

```php
use \Nasimic\Payriff\Exceptions\OrderCreationException;

try {
    $orderResponseDto = $this->payriffClient->createOrder($orderRequestDto);
} catch (OrderCreationException $exception) {
    // log error $exception->getMessage()
    // print user-friendly message
}
```

## Documentation

For more detailed information please check <a href="https://docs.payriff.com">Payriff Documentation</a> page.

## What is next?

These functions on TODO list. Will be added soon
- AutoPay

## Credits

- [Nasimi Mammadov](https://github.com/nasimic)
