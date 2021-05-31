# Payriff Payment Service

---
This repo can be used as to implement Payriff Payment Service to Laravel Application. In future, will be available as package. 
---

## Installation

1. Add PayriffService.php file to your app (wherever you want). I prefer to add inside App/Services folder.

## Configuration

1. Add these lines to .env file with your proper informations

```bash
PAYRIFF_MERCHANT_SECRET=your secret here
PAYRIFF_MERCHANT_NUMBER=your unique merchant number here
```

2. Add these lines to config/app.php
```bash
'payriff_secret' => env('PAYRIFF_MERCHANT_SECRET'),
'payriff_number' => env('PAYRIFF_MERCHANT_NUMBER'),
```

Done.

## Usage

Check PayriffService file for all functions with details.

Simple Usage - Create order and pay after redirection.

```php

use App\Services\PayriffService;


$paymentGateway = new PayriffService;

$redirectUrl = $paymentGateway->createOrder(
  '100',             // amount
  'Asif Quliyev',    // description
  'AZN',             // currency
  'AZ',              // language
);

return redirect($redirectUrl);
```

Advanced Usage - Create order, pay after redirection and return user back to your website ( while process approved, declined or canceled ).

```php

use App\Services\PayriffService;


$paymentGateway = new PayriffService;

$redirectUrl = $paymentGateway->createOrder(
  '100',                      // amount
  'Asif Quliyev',             // description
  'AZN',                      // currency
  'AZ',                       // language
  route('paymentApproved'),   // aprroveUrl
  route('paymentCanceled'),   // cancelUrl
  route('paymentDeclined'),   // declineUrl
);

return redirect($redirectUrl);
```

*If the parameters aprroveUrl, cancelUrl, declineUrl are left blank, then we will automatically redirect to Success, Cancel, Decline



## Documentation

For more detailed information please check <a href="https://payriff.com/docs/">Payriff Documentation</a> page.

## Credits

- [Nasimi Mammadov](https://github.com/nasimic)
