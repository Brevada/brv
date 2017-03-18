<?php
$code=Database::escape_string(Brevada::validate($_POST['code']));
$value=Database::escape_string(Brevada::validate($_POST['value']));
$duration=Database::escape_string(Brevada::validate($_POST['duration']));
$uses=Database::escape_string(Brevada::validate($_POST['uses']));
$note=Database::escape_string(Brevada::validate($_POST['note']));
$level=Database::escape_string(Brevada::validate($_POST['level']));

$sql="INSERT INTO codes(code, value, uses, duration_months, notes, level) 
VALUES('{$code}','{$value}','{$uses}', '{$duration}', '{$note}', '{$level}')";

if(!Database::getCon()->connect_errno){
	$res=Database::query($sql);
	if($res == false){
		//Success inserting row.
		Brevada::Redirect('/secure/financials.php');
	} else {
		//Error inserting row.
		Brevada::Redirect('/secure/financials.php');
	}
} else {
	//Error. No connection.
	Brevada::Redirect('/secure/financials.php');
}
?>