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
    
    if (!$input || empty($input['pollUrl'])) {
        throw new Exception('Missing poll URL');
    }
    
    $pollUrl = $input['pollUrl'];
    
    // Initialize Paynow
    $paynow = new Paynow(
        $env['PAYNOW_INTEGRATION_ID'],
        $env['PAYNOW_INTEGRATION_KEY'],
        $env['PAYNOW_RESULT_URL'],
        $env['PAYNOW_RETURN_URL']
    );
    
    // Check payment status
    $status = $paynow->pollTransaction($pollUrl);
    
    error_log("Payment status check: " . print_r($status, true));
    
    $result = [
        'success' => true,
        'status' => $status->status(),
        'paid' => $status->paid(),
        'amount' => $status->amount(),
        'reference' => $status->reference(),
    ];
    
    echo json_encode($result);
    
} catch (Exception $e) {
    error_log("Status check error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
