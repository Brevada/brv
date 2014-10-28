<?php
/**
Controller
//**/
session_start();
//session_regenerate_id();

//header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
//header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

require 'View.php';
require 'Database.php';
require 'Common.php';
require 'Brevada.php';

$page = empty($_GET['page']) ? '404' : trim($_GET['page']);

if(stripos($page, '?') !== false){
	$page = substr($page, 0, strpos($page, '?'));
}

$ext = @pathinfo($page, PATHINFO_EXTENSION);
if(!empty($ext)){
	$page = substr($page, 0, strlen($page)-strlen($ext)-1);
}

$viewPath = '';

//MOBILE CODE
/*if(Brevada::IsMobile())
{
	$viewPath = "../pages/mobile/{$page}.php";
	if(!file_exists($viewPath)){
		$viewPath = "../pages/mobile/{$page}/{$page}.php";
	}
} else {
	$viewPath = "../pages/{$page}.php";
	if(!file_exists($viewPath)){
		$viewPath = "../pages/{$page}/{$page}.php";
	}
}*/

$viewPath = "../pages/{$page}.php";
if(!file_exists($viewPath)){
	$viewPath = "../pages/{$page}/{$page}.php";
}

if(!file_exists($viewPath)){
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

$view = new View($viewPath);
$view->addResource("../template/head.php", true);
$view->RootPage = true;
$view->DocType = true;
$view->printView();
?>