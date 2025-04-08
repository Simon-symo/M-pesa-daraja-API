<?php
// Credentials from Safaricom Daraja API
$consumerKey = "7GwdVVKtmQ4nIf7ylTigS2GEfA1ZhxANXjgAoIB0IhNeJtRS";
$consumerSecret = "jtlxnz7FmiTcH6fXAzwUaL0N92rSrQXiIs7T5U1sMdgxqAdO06c90pJlkaRWNY6l";
$businessShortCode = "174379";
$lipaNaMpesaPasskey = "Os6VCCnGofAz84i7XSLdUETa/oLB80AYh1JmeXHC6Ig8kxMS/cOHgLW+CBWZWTZ2+u+9zlRml/r+IeaxQGoaUbbPs98j/BF7C8nyxdrw4jauAkprb+Uj8grrR2bZEqP9DqL/RFQQcgoUxWjHN5kNS1nnI/O4rZ+3zgCZ0I2gbIqCeeGix5uOcBvc9AqZ7u+Jc77X6Sq+lo2PmhDJ51EVFiPiRWq2HVZGbIx3Jv32vyh7Q1AbdR2mfe+l4XJyySed//IofupIhNevUoDT8WC50QRbCSf6gLkDYtJOs5CLDR4dCsNvMbu7ePVSX5WFYBsQOE/MZP5pW0M+kYlyLQu44g==";
$callbackURL = "https://yourwebsite.com/callback.php";

// Get user input
$phone = $_POST['phone'];
$amount = $_POST['amount'];
$timestamp = date("YmdHis");
$password = base64_encode($businessShortCode . $lipaNaMpesaPasskey . $timestamp);

// Generate Access Token
$accessTokenUrl = "https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials";
$credentials = base64_encode("$consumerKey:$consumerSecret");

$ch = curl_init($accessTokenUrl);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Basic $credentials"]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$result = json_decode(curl_exec($ch));
curl_close($ch);

$accessToken = $result->access_token;

// STK Push Request
$stkPushUrl = "https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest";

$requestData = [
    "BusinessShortCode" => $businessShortCode,
    "Password" => $password,
    "Timestamp" => $timestamp,
    "TransactionType" => "CustomerPayBillOnline",
    "Amount" => $amount,
    "PartyA" => $phone,
    "PartyB" => $businessShortCode,
    "PhoneNumber" => $phone,
    "CallBackURL" => $callbackURL,
    "AccountReference" => "Order123",
    "TransactionDesc" => "Payment for Order123"
];

$ch = curl_init($stkPushUrl);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $accessToken"
]);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$response = json_decode(curl_exec($ch));
curl_close($ch);

if (isset($response->ResponseCode) && $response->ResponseCode == "0") {
    echo "Payment request sent. Please complete the payment on your phone.";
} else {
    echo "Error initiating payment: " . json_encode($response);
}
?>
