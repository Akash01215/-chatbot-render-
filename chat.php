<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Autoload dependencies
require_once 'vendor/autoload.php';

use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Get the API key
$apiKey = $_ENV['OPENAI_API_KEY'] ?? null;

if (!$apiKey) {
    echo json_encode(["error" => "API Key is missing or invalid."]);
    exit;
}

// Set response header
header('Content-Type: application/json');

// Get the POST input
$input = file_get_contents("php://input");
$data = json_decode($input, true);

// Check for valid message
if (!isset($data['message']) || empty(trim($data['message']))) {
    echo json_encode(["error" => "Message is missing"]);
    exit;
}

$userMessage = $data['message'];

// Prepare API request
$payload = [
    "model" => "gpt-3.5-turbo",
    "messages" => [
        ["role" => "user", "content" => $userMessage]
    ]
];

$ch = curl_init("https://api.openai.com/v1/chat/completions");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $apiKey"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

// Get response
$response = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

// Check cURL error
if ($error) {
    echo json_encode(["error" => "cURL Error: " . $error]);
    exit;
}

// Decode and check response
$result = json_decode($response, true);

if (isset($result['choices'][0]['message']['content'])) {
    $reply = $result['choices'][0]['message']['content'];
    echo json_encode(["reply" => $reply]);
} else {
    echo json_encode(["error" => "Invalid response from OpenAI", "raw" => $result]);
}
?>
