<?php
/**
Controller

Entry point for website.
*/

define('ROOT_PATH', '/');
define('URL', 'http://brevada.com/');

session_start();
//session_regenerate_id();

//header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
//header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

function __autoload($c){
	if(file_exists("classes/{$c}.php")){
		include_once "classes/{$c}.php";
	}
}

require 'classes/Language.php';
require 'Brevada.php';
require 'Common.php';
require 'View.php';

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
	} else {
		//Check if scores/profile
		if(preg_match('#scores/([a-z0-9_\-\s]+)/?#i', $page, $matches)){
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

if(isset($_GET['secure'])){
	if (!isset($_SESSION['secure']) || time() - ((int)$_SESSION['secure']) > 3600) {
		echo 'Authentication required. <a href="/login">Login</a>'; exit;
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

$view->printView();
?>