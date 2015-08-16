<?php
$this->IsScript = true;

if(!Brevada::IsLoggedIn()){
	Brevada::Redirect('/logout');
}

if($_SESSION['Corporate'] && !Permissions::has(Permissions::MODIFY_COMPANY_STORES)){
	exit('Permission denied.');
}

if(!$_SESSION['Corporate'] && !Permissions::has(Permissions::MODIFY_STORE)){
	exit('Permission denied.');
}

if(!isset($_SESSION['Last_ReportTablet'])){
	Email::build()->setSubject('Report - Tablet')->setTo('contact@brevada.com', 'Admin')->loadTemplate('report_broken.html')->send();
	
	$_SESSION['Last_ReportTablet'] = time();
}

Brevada::Redirect('/settings?section=tablets&thanks=1');
?>