<?php
$serial = Brevada::validate(Brevada::FromPOSTGET('m'), VALIDATE_DATABASE);

if(!empty($serial)){
	if(($query = Database::query("SELECT users.url_name as UrlName, users.id as UID FROM `tablets` LEFT JOIN users ON users.id = tablets.UserID WHERE tablets.`SerialCode` = '{$serial}' LIMIT 1")) !== false){
		$row = $query->fetch_assoc();
		if($row && !empty($row)){
			$tablet_id = $row['UID'];
			$tablet_url = $row['UrlName'];
			
			$this->add(new View("../pages/profile/profile.php", array('tablet_id' => $tablet_id, 'tablet_url' => $tablet_url)));
		} else { Brevada::Redirect('/404?tablet'); }
	} else { Brevada::Redirect('/404?tablet'); }
} else { Brevada::Redirect('/404?tablet'); }
?>