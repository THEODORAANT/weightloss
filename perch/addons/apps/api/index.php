<?php
header("Content-Type: application/json");

$rawPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$relativePath = preg_replace('#^/api#', '', $rawPath ?? '');
$relativePath = '/' . ltrim((string)$relativePath, '/');
$relativePath = preg_replace('#/+#', '/', $relativePath);
$relativePath = rtrim($relativePath, '/');
if ($relativePath === '') {
    $relativePath = '/';
}

$segments = $relativePath === '/' ? [] : explode('/', ltrim($relativePath, '/'));
$routeFile = null;
$routeParams = [];
$routesDir = __DIR__ . '/routes';

for ($i = count($segments); $i >= 0; $i--) {
    $currentSegments = array_slice($segments, 0, $i);
    $currentPath = $i ? '/' . implode('/', $currentSegments) : '';
    $fileCandidate = $currentPath === ''
        ? $routesDir . '/index.php'
        : $routesDir . $currentPath . '.php';
    $directoryCandidate = $currentPath === ''
        ? $fileCandidate
        : $routesDir . $currentPath . '/index.php';

    if (file_exists($fileCandidate)) {
        $routeFile = $fileCandidate;
        $routeParams = array_slice($segments, $i);
        break;
    }

    if (file_exists($directoryCandidate)) {
        $routeFile = $directoryCandidate;
        $routeParams = array_slice($segments, $i);
        break;
    }
}
//print_r($routeFile);
if ($routeFile) {
    global $_ROUTE;
    $_ROUTE = [
        'path' => $relativePath,
        'segments' => $segments,
        'params' => $routeParams,
    ];
    require $routeFile;
    return;
}

http_response_code(404);
echo json_encode(["error" => "Endpoint not found"]);
