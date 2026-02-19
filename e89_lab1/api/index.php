<?php
require_once __DIR__ . '/BookController.php';

$method = $_SERVER['REQUEST_METHOD'];

// Build a normalized path from PATH_INFO or REQUEST_URI so the API works with PHP built-in server and Apache rewrites
$path = '';
if (!empty($_SERVER['PATH_INFO'])) {
    $path = trim($_SERVER['PATH_INFO'], '/');
} else {
    $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $scriptName = dirname($_SERVER['SCRIPT_NAME']);
    $path = trim(substr($requestUri, strlen($scriptName)), '/');
}

$segments = $path === '' ? [] : explode('/', $path);

// Determine ID if present (supports both /books/{id} and /{id} when using index.php as entry)
$id = null;
if (isset($segments[0]) && $segments[0] !== '') {
    if ($segments[0] === 'books') {
        if (isset($segments[1]) && is_numeric($segments[1])) {
            $id = (int)$segments[1];
        }
    } elseif (is_numeric($segments[0])) {
        $id = (int)$segments[0];
    }
}

try {
    $controller = new BookController();

    $validRoot = empty($segments) || $segments[0] === 'books' || is_numeric($segments[0]);
    if (!$validRoot) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Not Found']);
        exit;
    }

    $controller->handleRequest($method, $id);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error']);
}
