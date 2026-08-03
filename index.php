<?php
header('Content-Type: application/json');

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$timestamp = date('Y-m-d H:i:s');

switch ($path) {
    case '/php_app/':
        echo json_encode([
            'message' => 'This is PHP',
            'path' => $path,
            'timestamp' => $timestamp
        ]);
        break;

    case '/php_app/hello':
        echo json_encode([
            'message' => 'Hello',
            'path' => $path,
            'timestamp' => $timestamp
        ]);
        break;

    default:
        http_response_code(404);
        echo json_encode([
            'error' => 'Not found',
            'path' => $path,
            'timestamp' => $timestamp
        ]);
        break;
}
?>
