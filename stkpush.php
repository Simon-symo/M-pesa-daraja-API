<?php
// Credentials from Safaricom Daraja API
$consumerKey = "7GwdVVKtmQ4nIf7ylTigS2GEfA1ZhxANXjgAoIB0IhNeJtRS";
$consumerSecret = "jtlxnz7FmiTcH6fXAzwUaL0N92rSrQXiIs7T5U1sMdgxqAdO06c90pJlkaRWNY6l";
$businessShortCode = "174379";
$lipaNaMpesaPasskey = "";
$callbackURL = "https://mydomain.com/pat";

// Get user input
$phone = $_POST['phone'];
$amount = $_POST['amount'];
$timestamp = date("YmdHis");
$password = base64_encode($businessShortCode . $lipaNaMpesaPasskey . $timestamp);

// Generate Access Token
$accessTokenUrl = "AqjGUuOP7AhCtzzwNov7hjbdSuSUh66xQi/VEWGskgMypbD+KJxEoRPlx+wEql/UNKDDUB+kAVW2C1oVDMBMgKdhzHbTAXSJ9UDJunhzfCeMXFn+KMWvafekjPmr5ylUmfZi5rfXtLLU8YcwbqcrZCPTld1I1HyE6+txjYzAaWFfK1S/vmSRVPbHUMUcHkg7SgrbGcviMFtVW76UN7QmLoTy1/j87RMthugInaAO2/m5/8aejU9N/gZWItp8y9nZSfHyv0BqH73al8zffQfBFsL6cUFq5bfADpnwEBXMzTUwrvnqZ58sHZWp8+HQgL5EYZOlqeCKbifyvhmmONpcqA==";
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
