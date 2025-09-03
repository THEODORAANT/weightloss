<?php
header("Content-Type: application/json");

$requestMethod = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = str_replace("/api", "", $path);
//echo "path".$path ;
$routeFile = __DIR__ . "/routes{$path}.php";
//echo $routeFile ;
if (file_exists($routeFile)) {

    require $routeFile;
} else {
  //  http_response_code(404);
    echo json_encode(["error" => "Endpoint not found"]);
}
