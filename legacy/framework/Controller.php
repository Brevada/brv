<?php
/**
Controller

Entry point for website.
*/
define('DEBUG', true);

define('ROOT_PATH', '/');
define('URL', 'http://brevada.com/');

if (DEBUG) {
    define('BETA_URL', 'http://beta.' . $_SERVER['HTTP_HOST'] . '/');
} else {
    define('BETA_URL', 'https://beta.' . 'brevada.com' . '/');
}

session_name('brevada_session');
session_set_cookie_params(0, '/', '.' . (DEBUG ? $_SERVER['HTTP_HOST'] : 'brevada.com'));
session_start();
//session_regenerate_id();

mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
date_default_timezone_set('America/New_York');

if(DEBUG){
	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
}

function __autoload($c){
	if(file_exists("classes/{$c}.php")){
		include_once "classes/{$c}.php";
	}
}

require_once 'classes/Language.php';
require_once 'Minify.php';
require_once 'Brevada.php';
require_once 'classes/Data/Data.php';
require_once 'View.php';

if(!empty($_GET['lang']) || !empty($_COOKIE['lang'])){
	setLocalization(empty($_GET['lang']) ? $_COOKIE['lang'] : $_GET['lang'], true);
}

$page = empty($_GET['page']) ? '404' : trim($_GET['page']);

if(stripos($page, '?') !== false){
	$page = substr($page, 0, strpos($page, '?'));
}

$ext = @pathinfo($page, PATHINFO_EXTENSION);
if(!empty($ext)){
	$page = substr($page, 0, strlen($page)-strlen($ext)-1);
}

$viewPath = '';

$isWidget = false;

$viewPath = "../pages/{$page}.php";
if(!file_exists($viewPath)){
	$viewPath = "../pages/{$page}/{$page}.php";
}

if(!file_exists($viewPath)){
	if(preg_match('#widget/.*#i', $page, $matches)){
		$page = substr($page, 7);
		$viewPath = "../widgets/{$page}.php";
		$isWidget = true;
	} else if(preg_match('#api/v1/(.*)#i', $page, $matches)){
		$request = $matches[1];
		try{
			$api = new BrevadaAPI;
			$api->process('1', $_SERVER['REQUEST_METHOD'], $request);
		} catch (Exception $e){
			echo json_encode(Array('error' => array($e->getMessage())));
		}
		exit;
	} else {

		if(preg_match('#qr/([a-z0-9_\-]+)#i', $page, $matches)){
			$_GET['name'] = $matches[1];
			$viewPath = "../pages/qr.php";
		} else if(preg_match('#scores/([a-z0-9_\-\s]+)/?#i', $page, $matches)){
			$_GET['name'] = $matches[1];
			$viewPath = "../pages/profile/scores/scores.php";
		} else if(preg_match('#([a-z0-9_\-\s]+)/?#i', $page, $matches)){
			$_GET['name'] = $matches[1];
			$viewPath = "../pages/profile/profileloader.php";
		} else {
			$viewPath = '../pages/404.php';
		}
	}
}

$view = new View($viewPath);
if($isWidget){ $view->IsScript = true; } //temp; until $isWidget is phased out.
if(!$isWidget && !$view->IsScript){
	$view->addResource("../template/head.php", true);
	$view->RootPage = true;
	$view->DocType = true;
} else {
	$view->RootPage = false;
	$view->DocType = false;
}

echo Minify::sanitize($view);
?>
