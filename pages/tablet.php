<?php
$serial = Brevada::validate(Brevada::FromPOSTGET('m'), VALIDATE_DATABASE);

if(!empty($serial)){
	if(($query = Database::query("SELECT stores.URLName, stores.id as SID FROM `tablets` LEFT JOIN stores ON stores.id = tablets.StoreID WHERE tablets.`SerialCode` = '{$serial}' LIMIT 1")) !== false){
		$row = $query->fetch_assoc();
		if($row && !empty($row)){
			$tablet_id = $row['SID'];
			$tablet_url = $row['URLName'];
			
			$this->add(new View("../pages/profile/profile.php", array('tablet_id' => $tablet_id, 'tablet_url' => $tablet_url)));
		} else { Brevada::Redirect('/404?tablet'); }
	} else { Brevada::Redirect('/404?tablet'); }
} else { Brevada::Redirect('/404?tablet'); }
?>