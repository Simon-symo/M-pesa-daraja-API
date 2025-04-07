<?php
header("Content-Type: application/json");

$response = file_get_contents("php://input");
$logFile = "mpesa_callback.json";
file_put_contents($logFile, $response, FILE_APPEND);

$data = json_decode($response, true);

if (isset($data["Body"]["stkCallback"]["ResultCode"])) {
    $resultCode = $data["Body"]["stkCallback"]["ResultCode"];
    if ($resultCode == 0) {
        echo "Payment successful!";
    } else {
        echo "Payment failed!";
    }
}
?>
