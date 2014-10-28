<?php
/*
	Written by Noah Negin-Ulster
	Common Functions
*/

//Deprecated. From old system.

function f($type, $p) 
{ 
	if($type=='HTML'){
		$from_root="/"; 
	}
	else{
		$from_root=$_SERVER['DOCUMENT_ROOT'] . "/"; 
	}
	
	//DIRECTORIES
	
	$path_images=$from_root . "images/";
		$path_hubicons=$path_images . "hub_icons/";
	
	$path_home=$from_root . "home/";
	
	$path_user_data=$from_root . "user_data/";
		$path_qr=$path_user_data . "qr/";
		$path_qrposts=$path_user_data . "qr_posts/";
		$path_user_images=$from_root . "user_data/user_images/";
		$path_post_images=$path_user_data . "post_images/";
	
	$path_overall=$from_root . "overall/";
		$path_overall_packages=$path_overall . "packages/";
		$path_packages=$path_overall . "packages/";
		$path_insert=$path_overall . "insert/";
		$path_email=$path_overall . "email/";
		$path_public=$path_overall . "public/";
		$path_stylesheets=$path_overall . "stylesheets/";
	
	$path_hub=$from_root . "hub/";
		$path_update=$path_hub . "update/";
		$path_hub_includes=$path_hub . "includes/";
			$path_marketing=$path_hub_includes . "marketing/";
			$path_popups=$path_hub_includes . "popups/";
		$path_hub_posts=$path_hub . "posts/";
		$path_payment=$path_hub . "payment/";
		 
	$path_mobile=$from_root . "mobile/";
		$path_mobileprofile=$path_mobile . "profile/";
	
	$path_profile=$from_root . "profile/";
	
	$path_corporate=$from_root . "corporate/";
		$path_corporatehub=$path_corporate . "hub/";
		$path_corporateprofile=$path_corporate . "profile/";
	
	//FILES
	
	$file_home=$from_root . "index.php";
	$file_hub=$path_hub . "hub.php";
	$file_pricing=$path_home . "pricing.php";
	$file_logout=$path_home . "logout.php";
	$file_login=$path_home . "login.php";
	$file_signup=$path_home . "signup.php";
	$file_approved=$path_home . "approved.php";
	$file_upgrade=$path_payment . "upgrade.php";
	$file_complete=$path_home . "complete.php";
	$file_howitworks=$path_home . "howitworks.php";
	$file_prizes=$path_home . "prizes.php";
	
	return $$p;
} 

//Framework return file path
function p($type, $p, $file) 
{ 
	$path=f("$type", "$p"); 
	return $path . "$file"; 
}

//Picture functions

//switch this to look at database to see if post has a pic, then if user has a pic
//show picture for a post
function post_pic($width, $height, $post_id, $user_id, $post_extension, $extension) {
	$html=f('HTML', 'path_post_images');
	$php=f('PHP', 'path_post_images');
	$filename = "{$php}{$post_id}.{$post_extension}";
	$filenameHTML = "{$html}{$post_id}.{$post_extension}";
	
	if(!file_exists($filename)) {
		$html=f('HTML', 'path_user_images');
		$php=f('PHP', 'path_user_images');
		$filename = "{$php}{$user_id}.{$extension}";
		$filenameHTML = "{$html}{$user_id}.{$extension}";
		
		if(!file_exists($filename)) {
			$filenameHTML = "{$html}default.png";
		}
	}
	
	echo "<img id='box_pic' src='{$filenameHTML}' style='width: {$width}; height: {$height};' />";
}
 
//show picture for a user
function user_pic($width, $height, $user_id, $extension) {
	$filename = "../user_data/user_images/{$user_id}.{$extension}";
	$filenameHTML = "/user_data/user_images/{$user_id}.{$extension}";
	if(!file_exists($filename)) {
		$filenameHTML = "/user_data/user_images/default.png";
	}
	echo "<img src='{$filenameHTML}' style='width: {$width};" . (empty($height) ? '' : " height: {$height};") . "' />";
}

//Framework user info
function user($id){
	$query = Database::query("SELECT * FROM users WHERE id='$id' LIMIT 1");
	$user=$query->fetch_assoc();
	return $user;
}

//Framework user data
function userdata($id){

			$query2 = Database::query("SELECT * FROM feedback WHERE user_id='$id'");

			$count=0;
			$total=0;
			$good=0;
			$ok=0;
			$bad=0;
			
			WHILE ($rows2=$query2->fetch_assoc()){
			 $value=$rows2['value'];
			 $count+=1;
			 $total=$total+$value;
			 if($value>=80){$good+=1;}else if($value<50){$bad+=1;}else{$ok+=1;}
			}
			if($count!=0){
			 $average=$total/$count;
			 $good=($good/$count)*100;
			 $ok=($ok/$count)*100;
			 $bad=($bad/$count)*100;
			 }
			 else{
			 	$average=0;
			 }
			 $average=round($average, 2);
			 $user_good=$good;
			 $user_ok=$ok;
			 $user_bad=$bad;	
			 
			if($average>=70){
				$out['color']="#4EAF0E";
			}
			else if($average<=50){
				$out['color']="#E22A12";
			}
			else{
				$out['color']="#EDC812";
			}
			 
			$out['average'] = $average;
			$out['count'] = $count;
			$out['good'] = $user_good;
			$out['middle'] = $user_ok;
			$out['bad'] = $user_bad;
			return $out;

}

//Framework post data
function postdata($id){
			$query2 = Database::query("SELECT * FROM feedback WHERE post_id='$id'");

			$count=0;
			$total=0;
			$good=0;
			$ok=0;
			$bad=0;
			
			WHILE ($rows2=$query2->fetch_assoc()){
			 $value=$rows2['value'];
			 $count+=1;
			 $total=$total+$value;
			 if($value>=80){$good+=1;}else if($value<50){$bad+=1;}else{$ok+=1;}
			}
			if($count!=0){
			 $average=$total/$count;
			 $good=($good/$count)*100;
			 $ok=($ok/$count)*100;
			 $bad=($bad/$count)*100;
			 }
			 else{
			 	$average=0;
			 }
			 $average=round($average, 2);
			 $user_good=$good;
			 $user_ok=$ok;
			 $user_bad=$bad;	
			 
			if($average>=70){
				$out['color']="#4EAF0E";
			}
			else if($average<=50){
				$out['color']="#E22A12";
			}
			else{
				$out['color']="#EDC812";
			}
			 
			$out['average'] = $average;
			$out['count'] = $count;
			$out['good'] = $user_good;
			$out['middle'] = $user_ok;
			$out['bad'] = $user_bad;
			return $out;
}
?>