<?php
// Architex Axis - Backend Entry Point

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *"); // Allow requests from any origin (for development)
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Basic routing logic (can be expanded with a proper router)
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Example: /api/users
if (preg_match('/\/api\/([a-zA-Z0-9_-]+)/', $requestUri, $matches)) {
    $resource = $matches[1];
    $endpointFile = __DIR__ . '/api/' . $resource . '.php';

    if (file_exists($endpointFile)) {
        require $endpointFile;
    } else {
        http_response_code(404);
        echo json_encode(['error' => "Resource not found: $resource"]);
    }
} else {
    // Default response for the root backend URL
    echo json_encode([
        'message' => 'Welcome to the Architex Axis PHP Backend API!',
        'timestamp' => date('c')
    ]);
}

?>
