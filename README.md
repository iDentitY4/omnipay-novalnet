# Omnipay: Novalnet

**Novalnet gateway for the Omnipay PHP payment processing library**

[![Build Status](https://travis-ci.org/fruitcake/omnipay-novalnet.png?branch=master)](https://travis-ci.org/fruitcake/omnipay-novalnet)
[![Latest Stable Version](https://poser.pugx.org/fruitcake/omnipay-novalnet/version.png)](https://packagist.org/packages/fruitcake/omnipay-novalnet)
[![Total Downloads](https://poser.pugx.org/fruitcake/omnipay-novalnet/d/total.png)](https://packagist.org/packages/fruitcake/omnipay-novalnet)

[Omnipay](https://github.com/omnipay/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP 5.3+. This package implements Novalnet support for Omnipay.


## Installation

Omnipay is installed via [Composer](http://getcomposer.org/). To install, simply require this package with:

```
composer require fruitcake/omnipay-novalnet
```


## Basic Usage

The following gateways are provided by this package:

* Novalnet

For general usage instructions, please see the main [Omnipay](https://github.com/omnipay/omnipay)
repository. You can also check out to the documentation provided by Novalnet.

For common but obscure errors check out these [errors](errors.md). If you find more errors like these, please create a PR to help the others out.


## Example 1 - I will figure it out

```php
use Omnipay\Novalnet\Gateway;

if (isset($_GET['notify'])) {
    // handle notify request
    die();
}

if (isset($_POST['tid']) && isset($_POST['status'])) {
    switch ($_POST['status']) {
        case 100: // Success
            echo $_POST['status_text'];
            break;
        case 90: // Paypal Payment Pending
            echo $_POST['status_text'];
            break;
        default:
            echo "Failed - " . $_POST['status'] . " | " . $_POST['status_text'];
            break;
    }
    die();
}

if (!isset($_POST['tid'])) {
    /*
     * 1. Create the gateway
     */
    $gateway = new Gateway();

    $gateway->setVendorId($vendorId);
    $gateway->setVendorAuthcode($vendorAuthcode);
    $gateway->setProductId($productId);
    $gateway->setTariffId($tariffId);


    /*
     * 2. Choose the desired payment method
     */
    // without redirect
    # $gateway->setPaymentMethod(Gateway::SEPA_METHOD);

    // with redirect
    # $gateway->setPaymentMethod(Gateway::GIROPAY_METHOD);
    # $gateway->setPaymentMethod(Gateway::IDEAL_METHOD);
    # $gateway->setPaymentMethod(Gateway::ONLINE_TRANSFER_METHOD);
    # $gateway->setPaymentMethod(Gateway::EPS_METHOD);
    # $gateway->setPaymentMethod(Gateway::PAYPAL_METHOD);
    # $gateway->setPaymentMethod(Gateway::CREDITCARD_METHOD);


    /*
     * 3. Create the request
     *
     * The easiest way to get a payment method working is starting
     * with zero parameters. Then start adding the parameters
     * which are noted as missed on sending the request.
     */
    $request = $gateway->purchase([
        'amount' => 10.21,
        'currency' => 'EUR',
        'transactionId' => '12345678',
        'iban' => 'DE24300209002411761956',

        'notifyUrl' => 'https://example.com/notify',
        'returnUrl' => 'https://example.com/success',
        'cancelUrl' => 'https://example.com/failed',
        'paymentKey' => $paymentKey,

        // client details
        'card' => [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'address1' => 'Streetname 1', // note the house number in the
            'postcode' => '1234AB',
            'city' => 'Amsterdam',
            'country' => 'NL',
            'email' => 'info@example.com',
            'phone' => '+31612345678',
            'number' => '4200 0000 0000 0000',
            'expiryMonth' => date('m', strtotime('+1 month')),
            'expiryYear' => date('Y', strtotime('+1 month')),
            'cvv' => 123,
        ],
    ]);


    /*
     * 4. Receive the response
     */
    $response = $request->send();


    /*
     * 5. Handle the response appropriate
     */
    if ($response->isSuccessful()) {
        echo $response->getMessage();
    } elseif ($response->isRedirect()) {
        // redirect to offsite payment gateway
        $response->redirect();
    } else {
        // payment failed: display message to customer
        return "Error " .$response->getCode() . ': ' . $response->getMessage();
    }
}
```


## Available Payment Methods

* 0 - Direct Debit SEPA
* 33 - Online Transfer Sofort
* 49 - Online Transfer iDEAL
* 69 - Online bank transfer (giropay)
* 34 - PayPal
* 50 - eps
* 6 - Creditcard


## Support

If you are having general issues with Omnipay, we suggest posting on
[Stack Overflow](http://stackoverflow.com/). Be sure to add the
[omnipay tag](http://stackoverflow.com/questions/tagged/omnipay) so it can be easily found.

If you want to keep up to date with release anouncements, discuss ideas for the project,
or ask more detailed questions, there is also a [mailing list](https://groups.google.com/forum/#!forum/omnipay) which
you can subscribe to.

If you believe you have found a bug, please report it using the [GitHub issue tracker](https://github.com/fruitcake/omnipay-novalnet/issues),
or better yet, fork the library and submit a pull request.