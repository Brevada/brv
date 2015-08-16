<?php
if(!Brevada::IsLoggedIn()){ Brevada::Redirect('/home/logout'); }
if(!Permissions::has(Permissions::EDIT_ADMIN)){ exit('Error Code: 404'); }

$value = strtolower(trim(Brevada::FromPOST('txtValue')));
$code = strtolower(trim(Brevada::FromPOST('txtCode')));
$paypalItem = strtolower(trim(Brevada::FromPOST('ddPaypalItem')));

if(empty($value) || empty($code) || empty($paypalItem)){ Brevada::Redirect('/admin?show=promotions&error'); }

if(strpos($value, '$') === 0){
	$value = substr($value, 1);
}

$value = @intval($value);

if(($stmt = Database::prepare("INSERT INTO `promo_codes` (`DateIssued`, `IssuerID`, `DiscountedValue`, `Used`, `Code`, `PaypalItemName`) VALUES (NOW(), ?, ?, 0, ?, ?)")) !== false){
	$issuerID = $_SESSION['AccountID'];
	$stmt->bind_param('iiss', $issuerID, $value, $code, $paypalItem);
	if(!$stmt->execute()){
		Brevada::Redirect('/admin?show=promotions&error');
	}
	$stmt->close();
	
	Logger::info("Account #{$_SESSION['AccountID']} created a new Promo Code '{$code}', with value '{$value}' for item '{$paypalItem}'.");
}

Brevada::Redirect('/admin?show=promotions');
?>