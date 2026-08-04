<?php
// CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$timestamp = date('Y-m-d H:i:s');

// Validate required parameters
$dataFolder = $_POST['dataFolder'] ?? null;
$state = $_POST['state'] ?? null;
$city = $_POST['city'] ?? null;
$temple = $_POST['temple'] ?? null;
$description = $_POST['description'] ?? null;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'error' => 'Method not allowed',
        'timestamp' => $timestamp
    ]);
    exit;
}

if (!$dataFolder || !$state || !$city || !$temple) {
    http_response_code(400);
    echo json_encode([
        'error' => 'Missing required parameters: dataFolder, state, city, temple',
        'timestamp' => $timestamp
    ]);
    exit;
}

// Create the directory structure
$baseDir = __DIR__ . '/' . $dataFolder;
$templeDir = $baseDir . '/' . $state . '/' . $city . '/' . $temple;

if (!is_dir($templeDir)) {
    if (!mkdir($templeDir, 0755, true)) {
        http_response_code(500);
        echo json_encode([
            'error' => 'Failed to create directory structure',
            'attempted_path' => $templeDir,
            'timestamp' => $timestamp
        ]);
        exit;
    }
}

$response = [
    'message' => 'Data saved successfully',
    'timestamp' => $timestamp,
    'path' => $templeDir,
    'files' => []
];

// Handle image upload (optional)
if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
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

    // Handle filename conflicts by renaming
    $destination = $templeDir . '/' . $filename;
    $counter = 1;
    $pathInfo = pathinfo($filename);

    while (file_exists($destination)) {
        $newFilename = $pathInfo['filename'] . '_' . $counter . '.' . $pathInfo['extension'];
        $destination = $templeDir . '/' . $newFilename;
        $counter++;
    }

    if (move_uploaded_file($tmpPath, $destination)) {
        $response['files']['image'] = [
            'original_name' => $filename,
            'saved_name' => basename($destination),
            'size' => filesize($destination),
            'full_path' => $destination
        ];
    } else {
        http_response_code(500);
        echo json_encode([
            'error' => 'Failed to move uploaded file',
            'timestamp' => $timestamp
        ]);
        exit;
    }
}

// Handle description (optional)
if (!empty($description)) {
    $descriptionFile = $templeDir . '/description.md';
    $counter = 1;

    while (file_exists($descriptionFile)) {
        $descriptionFile = $templeDir . '/description_' . $counter . '.md';
        $counter++;
    }

    if (file_put_contents($descriptionFile, $description)) {
        $response['files']['description'] = [
            'filename' => basename($descriptionFile),
            'size' => strlen($description),
            'full_path' => $descriptionFile
        ];
    } else {
        http_response_code(500);
        echo json_encode([
            'error' => 'Failed to save description file',
            'timestamp' => $timestamp
        ]);
        exit;
    }
}

// Ensure at least image or description was provided/saved
if (empty($response['files'])) {
    http_response_code(400);
    echo json_encode([
        'error' => 'No image or description provided',
        'timestamp' => $timestamp
    ]);
    exit;
}

echo json_encode($response);
?>

