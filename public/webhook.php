<?php
// Paynow webhook handler - receives payment status updates

require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$env = parse_ini_file(__DIR__ . '/../.env');

// Log the webhook data
$postData = file_get_contents('php://input');
error_log("Webhook received: " . $postData);

// Parse the webhook data
parse_str($postData, $data);

// Log parsed data
error_log("Parsed webhook data: " . print_r($data, true));

// You can update your database here based on the payment status
// Example fields in webhook:
// - reference: Your order ID
// - paynowreference: Paynow transaction ID  
// - amount: Payment amount
// - status: Payment status (Paid, Cancelled, etc)
// - pollurl: URL to check status

// Return success
http_response_code(200);
echo "OK";
