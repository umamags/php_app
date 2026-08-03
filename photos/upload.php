<?php
header('Content-Type: application/json');

$timestamp = date('Y-m-d H:i:s');
$dataFolder = __DIR__ . '/../data';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'error' => 'Method not allowed',
        'timestamp' => $timestamp
    ]);
    exit;
}

if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode([
        'error' => 'No file uploaded or upload error',
        'timestamp' => $timestamp
    ]);
    exit;
}

$file = $_FILES['photo'];
$filename = $file['name'];
$tmpPath = $file['tmp_name'];
$fileType = mime_content_type($tmpPath);

$allowedTypes = ['image/jpeg', 'image/png'];
if (!in_array($fileType, $allowedTypes)) {
    http_response_code(400);
    echo json_encode([
        'error' => 'Invalid file type. Only JPG, JPEG, and PNG allowed.',
        'received' => $fileType,
        'timestamp' => $timestamp
    ]);
    exit;
}

if (!is_dir($dataFolder)) {
    if (!mkdir($dataFolder, 0755, true)) {
        http_response_code(500);
        echo json_encode([
            'error' => 'Failed to create data folder',
            'timestamp' => $timestamp
        ]);
        exit;
    }
}

$newFilename = uniqid() . '_' . basename($filename);
$destination = $dataFolder . '/' . $newFilename;

if (move_uploaded_file($tmpPath, $destination)) {
    echo json_encode([
        'message' => 'Photo uploaded successfully',
        'filename' => $newFilename,
        'original_name' => $filename,
        'path' => 'data/' . $newFilename,
        'size' => filesize($destination),
        'timestamp' => $timestamp
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'error' => 'Failed to move uploaded file',
        'timestamp' => $timestamp
    ]);
}
?>
