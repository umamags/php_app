<?php
// CORS headers - allow specific origins
$allowedOrigins = [
    'https://umamags.github.io',
    'http://localhost:5173',
    'http://localhost:5174',
    'http://localhost:5175'
];

$requestOrigin = $_SERVER['HTTP_ORIGIN'] ?? null;
if (in_array($requestOrigin, $allowedOrigins)) {
    header('Access-Control-Allow-Origin: ' . $requestOrigin);
}

header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$timestamp = date('Y-m-d H:i:s');

// Hardcoded data folder path
$dataFolder = '../../data/temples';

// Validate required parameters
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

if (!$state || !$city || !$temple) {
    http_response_code(400);
    echo json_encode([
        'error' => 'Missing required parameters: state, city, temple',
        'timestamp' => $timestamp
    ]);
    exit;
}

// Create folder structure: dataFolder/state/city/temple
$baseDir = __DIR__ . '/' . $dataFolder;
$stateDir = $baseDir . '/' . $state;
$cityDir = $stateDir . '/' . $city;
$templeDir = $cityDir . '/' . $temple;

if (!is_dir($templeDir)) {
    if (!mkdir($templeDir, 0755, true)) {
        http_response_code(500);
        echo json_encode([
            'error' => 'Failed to create folder structure',
            'attempted_path' => $templeDir,
            'timestamp' => $timestamp
        ]);
        exit;
    }
}

$response = [
    'message' => 'Data saved successfully',
    'timestamp' => $timestamp,
    'folder' => $templeDir,
    'files' => []
];

// Handle image upload (optional)
if (isset($_FILES['photo'])) {
    if ($_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode([
            'error' => 'File upload error: ' . $_FILES['photo']['error'],
            'timestamp' => $timestamp
        ]);
        exit;
    }

    $file = $_FILES['photo'];
    $filename = $file['name'];
    $tmpPath = $file['tmp_name'];

    if (!file_exists($tmpPath)) {
        http_response_code(400);
        echo json_encode([
            'error' => 'Temporary file does not exist',
            'tmpPath' => $tmpPath,
            'timestamp' => $timestamp
        ]);
        exit;
    }

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

    $destination = $templeDir . '/' . $filename;

    // Check if destination folder is writable
    if (!is_writable($templeDir)) {
        http_response_code(500);
        echo json_encode([
            'error' => 'Destination folder is not writable',
            'folder' => $templeDir,
            'permissions' => substr(sprintf('%o', fileperms($templeDir)), -4),
            'timestamp' => $timestamp
        ]);
        exit;
    }

    if (move_uploaded_file($tmpPath, $destination)) {
        $response['files']['image'] = [
            'original_name' => $filename,
            'saved_name' => $filename,
            'size' => filesize($destination),
            'full_path' => $destination
        ];
    } else {
        http_response_code(500);
        echo json_encode([
            'error' => 'Failed to move uploaded file to destination',
            'source' => $tmpPath,
            'destination' => $destination,
            'tmpExists' => file_exists($tmpPath),
            'destDirExists' => is_dir($templeDir),
            'destDirWritable' => is_writable($templeDir),
            'timestamp' => $timestamp
        ]);
        exit;
    }
}

// Handle description (optional)
if (!empty($description)) {
    $descriptionFile = $templeDir . '/description.md';

    if (file_put_contents($descriptionFile, $description)) {
        $response['files']['description'] = [
            'filename' => 'description.md',
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

