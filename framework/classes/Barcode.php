<?php
class Barcode
{
	public static function GenerateStoreQR($urlName)
	{
		if(empty($urlName)){return false;}
		
		///MAKE QR CODE///   
		include_once '../framework/packages/phpqrcode/qrlib.php'; 
		include_once '../framework/packages/phpqrcode/qrconfig.php'; 
		 
		
		$fileName = trim(strtolower($urlName)) . '.png'; 

		$urlRelativeFilePath = "../user_data/qr/".$fileName; 
		 
		// generating 
		if (!file_exists($urlRelativeFilePath)) { 
			QRcode::png("http://brevada.com/{$urlName}", $urlRelativeFilePath, QR_ECLEVEL_L, 10, 1); 
		}
		
		return $urlRelativeFilePath;
	}
}
?>