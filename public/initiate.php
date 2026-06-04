<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$env = parse_ini_file(__DIR__ . '/../.env');

use Paynow\Payments\Paynow;

try {
    // Get POST data
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Invalid request body');
    }
    
    // Validate required fields
    $required = ['orderId', 'email', 'phone', 'amount', 'method'];
    foreach ($required as $field) {
        if (empty($input[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }
    
    $orderId = $input['orderId'];
    $email = $input['email'];
    $phone = $input['phone'];
    $amount = floatval($input['amount']);
    $method = $input['method']; // 'ecocash' or 'paynow'
    
    // Initialize Paynow
    $paynow = new Paynow(
        $env['PAYNOW_INTEGRATION_ID'],
        $env['PAYNOW_INTEGRATION_KEY'],
        $env['PAYNOW_RESULT_URL'],
        $env['PAYNOW_RETURN_URL']
    );
    
    // Create payment
    $payment = $paynow->createPayment($orderId, $email);
    $payment->add('Order Total', $amount);
    
    // Send payment based on method
    if ($method === 'ecocash') {
        // Mobile money - sends USSD prompt to phone
        error_log("Initiating EcoCash payment to: $phone");
        $response = $paynow->sendMobile($payment, $phone, 'ecocash');
    } else {
        // Web payment - returns redirect URL
        error_log("Initiating Paynow web payment");
        $response = $paynow->send($payment);
    }
    
    // Log the response
    error_log("Paynow response: " . print_r($response, true));
    
    // Check if payment was successful
    if ($response->success()) {
        // Get response data
        $result = [
            'success' => true,
            'pollUrl' => $response->pollUrl(),
            'redirectUrl' => $response->redirectUrl(),
            'instructions' => $response->instructions(),
        ];
        
        error_log("Payment initiated successfully: " . json_encode($result));
        echo json_encode($result);
    } else {
        // Payment failed
        $error = [
            'success' => false,
            'error' => $response->errors(),
        ];
        
        error_log("Payment failed: " . json_encode($error));
        echo json_encode($error);
    }
    
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
