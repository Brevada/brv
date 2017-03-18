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

$fields[] = [ 'title' => 'Company', 'value' => htmlentities('#'.$_SESSION['CompanyID']), 'short' => true ];
$fields[] = [ 'title' => 'Account', 'value' => htmlentities('#'.$_SESSION['AccountID']), 'short' => true ];
Slack::send([
	'username' => 'BrevadaSupport',
	'channel' => '#support',
	'attachments' => [
		[
			'fallback' => "User reported broken tablet.",
			'pretext' => "User reported broken tablet.",
			'color' => '#FF2B2B',
			'fields' => $fields
		]
	]
]);

Brevada::Redirect('/settings?section=tablets&thanks=1');
?>