<?php
header("Content-Type: application/json");

$requestMethod = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = str_replace("/api", "", $path);

$routeFile = __DIR__ . "/routes{$path}.php";

if (!file_exists($routeFile)) {
    $segments = explode('/', trim($path, '/'));
    if (count($segments) >= 2 && $segments[0] === 'products') {
        $_GET['id'] = $segments[1];
        if (isset($segments[2]) && $segments[2] === 'variants') {
            $routeFile = __DIR__ . "/routes/product_variants.php";
        } else {
            $routeFile = __DIR__ . "/routes/product_single.php";
        }
    }
}

if (file_exists($routeFile)) {

    require $routeFile;
} else {
    http_response_code(404);
    echo json_encode(["error" => "Endpoint not found"]);
}
