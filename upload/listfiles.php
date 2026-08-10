<?php
// Check if folder parameter is provided
if (!isset($_GET['folder'])) {
    http_response_code(400);
    die(json_encode(['error' => 'folder parameter is required']));
}

$folder = $_GET['folder'];

// Trim and remove trailing slashes
$folder = trim($folder, '/');

// Validate folder parameter to prevent directory traversal attacks
if (empty($folder) || strpos($folder, '..') !== false) {
    http_response_code(400);
    die(json_encode(['error' => 'Invalid folder path']));
}

// Construct the full path
$basePath = realpath(dirname(dirname(__DIR__)) . '/data');
$fullPath = dirname(dirname(__DIR__)) . '/data/' . $folder;

// Resolve the path and verify it exists
if (!file_exists($fullPath) || !is_dir($fullPath)) {
    http_response_code(404);
    die(json_encode(['error' => 'Folder not found']));
}

$requestedPath = realpath($fullPath);

// Verify the requested path is within the data directory
if ($requestedPath === false || strpos($requestedPath, $basePath) !== 0) {
    http_response_code(400);
    die(json_encode(['error' => 'Invalid folder path or folder does not exist']));
}

// Recursively list all files
$files = [];
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($requestedPath, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

foreach ($iterator as $fileinfo) {
    if ($fileinfo->isFile()) {
        $files[] = [
            'path' => str_replace($requestedPath, '', $fileinfo->getPathname()),
            'name' => $fileinfo->getFilename(),
            'size' => $fileinfo->getSize(),
            'modified' => date('Y-m-d H:i:s', $fileinfo->getMTime())
        ];
    }
}

header('Content-Type: application/json');
echo json_encode([
    'folder' => $folder,
    'files' => $files,
    'count' => count($files)
], JSON_PRETTY_PRINT);
?>
