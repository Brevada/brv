<?php
class Barcode
{
	public static function GeneratePostQR($codeContents)
	{
		if(empty($codeContents)){return;}
		
		///MAKE QR CODE///   
		include_once '../framework/packages/phpqrcode/qrlib.php'; 
		include_once '../framework/packages/phpqrcode/qrconfig.php'; 
		 
		// we need to generate filename somehow,  
		// with md5 or with database ID used to obtains $codeContents... 
		$fileName=$new_id . '.png'; 

		$pngAbsoluteFilePath="../user_data/qr_posts/".$fileName; 
		$urlRelativeFilePath="/user_data/qr_posts/". $fileName; 
		 
		// generating 
		if (!file_exists($pngAbsoluteFilePath)) { 
			QRcode::png($codeContents, $pngAbsoluteFilePath, QR_ECLEVEL_L, 10, 1); 
			//echo 'File generated!'; 
		} else { 
			//echo 'File already generated! We can use this cached file to speed up site on common codes!'; 
		}
	}
}
?>