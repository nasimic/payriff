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

$paymentPageUrl = $paymentGateway->createOrder(
  '100',             // amount
  'Asif Quliyev',    // description
  'AZN',             // currency
  'AZ',              // language
);

return redirect($paymentPageUrl);
```

Advanced Usage - Create order, pay after redirection and return user back to your website ( while process approved, declined or canceled ).

```php

use App\Services\PayriffService;


$paymentGateway = new PayriffService;

$paymentPageUrl = $paymentGateway->createOrder(
  '100',                      // amount
  'Asif Quliyev',             // description
  'AZN',                      // currency
  'AZ',                       // language
  route('paymentApproved'),   // aprroveUrl
  route('paymentCanceled'),   // cancelUrl
  route('paymentDeclined'),   // declineUrl
);

return redirect($paymentPageUrl);
```

*If the parameters aprroveUrl, cancelUrl, declineUrl are left blank, then user automatically will be redirected to proper Payriff pages (<a href="https://payriff.com/success.html">Success</a>, <a href="https://payriff.com/cancel.html">Cancel</a>, <a href="https://payriff.com/decline.html">Decline</a>)

If the transaction was successful, Payriff will return a POST request for approveURL with these transaction parameters:

```json
{
    "payload": {
        "version": "1.0",
        "orderID": 21100444,
        "sessionId": "6E41EC19A195AC7XEEFADD87A0CC6AC9",
        "transactionType": "",
        "RRN": "2532957",
        "PAN": "416974XXXXXX2588",
        "purchaseAmount": 19300,
        "currency": "944",
        "tranDateTime": "31/05/2021 16:38:13",
        "responseCode": "001",
        "responseDescription": "Approved, no balances available",
        "brand": "VISA",
        "orderStatus": "APPROVED",
        "approvalCode": "727229 A",
        "acqFee": "0",
        "orderDescription": "Emin Mammadov",
        "approvalCodeScr": "727229",
        "purchaseAmountScr": 195.00,
        "currencyScr": "AZN",
        "orderStatusScr": "TƏSDİQLƏNİB",
        "CardHolderName": ""
    },
    "code": "00000",
    "message": "Operation performed successfully",
}
```

## Documentation

For more detailed information please check <a href="https://payriff.com/docs/">Payriff Documentation</a> page.

## Credits

- [Nasimi Mammadov](https://github.com/nasimic)
