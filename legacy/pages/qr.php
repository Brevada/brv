<?php
$this->IsScript = true;

$url_name = trim(Brevada::validate($_GET['name'], VALIDATE_DATABASE));

$query = Database::query("SELECT `id` FROM `stores` WHERE `URLName`='{$url_name}' LIMIT 1");

if($query === false || $query->num_rows == 0){
	exit;
}

if(($qr = Barcode::GenerateStoreQR($url_name)) !== false){
	$im = imagecreatefrompng($qr);	
	header('Content-Type: image/png');
	imagepng($im);
	imagedestroy($im);
	exit;
}
?>