<?php
$mac = Brevada::validate(Brevada::FromPOSTGET('m'), VALIDATE_DATABASE);

if(!empty($mac)){
	if(($query = Database::query("SELECT users.url_name as UrlName, users.id as UID FROM `tablets` LEFT JOIN users ON users.id = tablets.UserID WHERE tablets.`MacAddress` = '{$mac}' LIMIT 1")) !== false){
		$row = $query->fetch_assoc();
		if(!empty($row)){
			$tablet_id = $row['UID'];
			$tablet_url = $row['UrlName'];
			
			$this->add(new View("../pages/profile/profile.php", array('tablet_id' => $tablet_id, 'tablet_url' => $tablet_url)));
		} else { Brevada::Redirect('/404'); }
	} else { Brevada::Redirect('/404'); }
} else { Brevada::Redirect('/404'); }
?>