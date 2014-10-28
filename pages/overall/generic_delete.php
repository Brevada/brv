<?php
$id = $_GET['id'];
$db = Brevada::validate($_GET['db'], VALIDATE_DATABASE);

/*
	This page shouldn't exist.
	
	For every type of delete (delete post, delete review, delete user...)
	there should be a DIFFERENT page dedicated to each delete.
	
	On the page, there should be a check to determine if the user has
	permission to delete the item from the database. For example,
	is the post made to a page which the user controls?
*/

Database::query("DELETE FROM `{$db}` WHERE `id`='{$id}'");

Brevada::Redirect('/hub/hub.php');
?>