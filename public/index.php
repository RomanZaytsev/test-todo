<?php

use app\configs\DB;
use app\models\User;

header('Content-type: text/html; charset=utf-8');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$URI = explode('?', $_SERVER['REQUEST_URI']);
$URI_PATH = explode('/', ltrim($URI[0], '/')) ?? [];
$url = 'index.php';
$baseUrl = $url;
if (!empty($URI[1])) {
    $url .= '?' . $URI[1];
}
if (empty($URI_PATH[array_key_last($URI_PATH)]) && $_SERVER['REQUEST_METHOD'] === 'GET') {
    header("Location: $url");
}
define("BASEURL", $baseUrl . '/..');
define("BASEPATH", getcwd() . '/..');
require_once(BASEPATH . '/vendor/autoload.php');

$mysqli = DB::connector();
$user = User::current();

$controllerName = !empty($_GET['controller']) ? ucfirst($_GET['controller']) : 'Site';
$actionName = !empty($_GET['action']) ? $_GET['action'] : 'index';

$controller_class = 'app\controllers\\' . $controllerName . 'Controller';
$controller = new $controller_class();

$controller->user = $user;
$controller->{$actionName}();
