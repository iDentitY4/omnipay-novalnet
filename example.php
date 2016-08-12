<?php

use Omnipay\Novalnet\Gateway;

require __DIR__ . '/vendor/autoload.php';

$vendorId = 36;
$vendorAuthcode = 'xsobjkfadlwtcxqlluzjyllocpxgxz';
$productId = 3814;
$tariffId = 6442;
$paymentKey = '19ca14e7ea6328a42e0eb13d585e4c22';

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
     * with zerp parameters. Then start adding the parameters
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

