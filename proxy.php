<?php
// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Enable CORS for your frontend
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-API-Key');
header('Content-Type: application/json');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Your MakameSco Secret Key
$apiKey = 'sk_321460063d3e6092d78a6ffd419ebe6124e0d6f16605c7f0f0af4012be712546';
$url = 'https://pay.makamesco-tech.co.ke/api/payments/stkpush';

// Get the request body
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Log the request for debugging
error_log("MakameSco Request: " . $input);

if (!$data) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request data']);
    exit();
}

// Validate required fields
if (empty($data['phoneNumber']) || empty($data['amount'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing phoneNumber or amount']);
    exit();
}

// Initialize cURL
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-API-Key: ' . $apiKey
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Set to true in production with proper SSL
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

// Execute request
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);

// Log the response for debugging
error_log("MakameSco Response Code: " . $http_code);
error_log("MakameSco Response: " . $response);

curl_close($ch);

// Handle cURL errors
if ($curl_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'CURL Error: ' . $curl_error]);
    exit();
}

// Return the response
echo $response;
?>
