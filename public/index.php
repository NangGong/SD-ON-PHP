<?php
require_once __DIR__."/../controller/Controller.php";

function handleRoute($route, $controller) {
    switch($route) {
        case "/api/wechat/dalle":
            header('Content-Type: application/json');
            require_once __DIR__."/../api/dalle/index.php";
            break;
        case "/api/wechat/sd":
            header('Content-Type: application/json');
            require_once __DIR__."/../api/sd/index.php";
            break;
        default:
            http_response_code(404);
            echo "404 Not Found";
            break;
    }
}

$s = isset($_GET['s']) ? $_GET['s'] : '/';
$controller = new Controller();
handleRoute($s, $controller);
