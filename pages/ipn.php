<?php
/* Paypal IPN */
$this->IsScript = true;

$companyID = @intval(Brevada::FromGET('id'));
$promoCode = strtolower(Brevada::FromGET('promo'));

$raw_post_data = file_get_contents('php://input');
$raw_post_array = explode('&', $raw_post_data);
$postData = array();
foreach ($raw_post_array as $keyval) {
  $keyval = explode('=', $keyval);
  if (count($keyval) == 2)
     $postData[$keyval[0]] = urldecode($keyval[1]);
}

$req = 'cmd=_notify-validate';
if(function_exists('get_magic_quotes_gpc')) {
   $get_magic_quotes_exists = true;
} 
foreach ($postData as $key => $value) {        
   if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) { 
        $value = urlencode(stripslashes($value)); 
   } else {
        $value = urlencode($value);
   }
   $req .= "&$key=$value";
}


$ch = curl_init('https://www.paypal.com/cgi-bin/webscr');
curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));

if(($res = curl_exec($ch)) === false) {
    // error_log("Got " . curl_error($ch) . " when processing IPN data");
    curl_close($ch);
    exit;
}
curl_close($ch);

if(empty($res) || strcmp($res, "VERIFIED") !== 0){
	// IPN invalid.
	exit;
}


if(($check = Database::query("SELECT `id` FROM `companies` WHERE `companies`.`id` = {$companyID} LIMIT 1")) !== false){
	if($check->num_rows == 0){
		exit;
	}
}

$item_name = $_POST['item_name'];
$item_number = $_POST['item_number'];
$payment_status = $_POST['payment_status'];
$payment_amount = $_POST['mc_gross'];
$payment_currency = $_POST['mc_currency'];
$txn_id = $_POST['txn_id'];
$receiver_email = $_POST['receiver_email'];
$payer_email = $_POST['payer_email'];

$invalidPayment = false;
$confirmed = false;
$transactionStarted = false;

if(strtolower($receiver_email) !== "payments@brevada.com"){
	exit;
}

$FRAUD_CONSTANTS = array("AVS No Match", "AVS Partial Match", "AVS Unavailable/Unsupported", "Card Security Code (CSC) Mismatch", "Maximum Transaction Amount", "Unconfirmed Address", "Country Monitor", "Large Order Number", "Billing/Shipping Address Mismatch", "Risky ZIP Code", "Suspected Freight Forwarder Check", "Total Purchase Price Minimum", "IP Address Velocity", "Risky Email Address Domain Check", "Risky Bank Identification Number (BIN) Check", "Risky IP Address Range", "PayPal Fraud Model");
$fraud = array();
foreach($_POST as $post => $value){
	if(stripos(strtolower($post), "fraud_management_pending_filters") === 0){
		if(empty($value)){ continue; }
		$fraud[] = $FRAUD_CONSTANTS[intval($value)-1];
	}
}
$fraudString = '';
if(!empty($fraud)){
	$fraudString = implode(',', $fraud);
}

$d_txn_id = Brevada::validate($txn_id, VALIDATE_DATABASE);
$d_payer_email = Brevada::validate($payer_email, VALIDATE_DATABASE);
if(($check = Database::query("SELECT `id`, `Confirmed` FROM `transactions` WHERE `transactions`.`PaypalTransactionID` = '{$d_txn_id}' AND `transactions`.`PaypalPayerEmail` = '{$d_payer_email}' LIMIT 1")) !== false){
	if($check->num_rows > 0){
		$row = $check->fetch_assoc();
		$confirmed = $row['Confirmed'] == 1;
		$transactionStarted = true;
	}
}

if(!$confirmed){
	$plan_basic = "Brevada Basic Package (1 Year)";
	$plan_premium = "Brevada Premium Package (1 Year)";
	$plan = '';
	
	$maxTablets = 0;
	$maxAccounts = 0;
	$maxStores = 0;
	
	$expectedValue = 0; $discountedValue = 1080*100;
	if(strtolower(trim($item_name)) == strtolower(trim($plan_basic))){
		$expectedValue = 600*100;
		$plan = $plan_basic;
		
		$maxTablets = 2; $maxAccounts = 1; $maxStores = 1;
	} else if(strtolower(trim($item_name)) == strtolower(trim($plan_premium))){
		$expectedValue = 1080*100;
		$plan = $plan_premium;
		
		$maxTablets = 5; $maxAccounts = 3; $maxStores = 1;
	}
	
	if($expectedValue > 0 && !empty($plan)){
		if(!empty($promoCode)){
			$d_promoCode = Brevada::validate($promoCode, VALIDATE_DATABASE);
			$d_paypalItemName = Brevada::validate($plan, VALIDATE_DATABASE);
			if(($query = Database::query("SELECT `id`, `DiscountedValue` FROM `promo_codes` WHERE `Code` = '{$d_promoCode}' AND `PaypalItemName` = '{$d_paypalItemName}' AND `Used` = 0 ORDER BY `id` DESC LIMIT 1")) !== false){
				if($query->num_rows > 0){
					$row = $query->fetch_assoc();
					$discountedValue = @intval($row['DiscountedValue']);
					$expectedValue = $discountedValue;
				}
			}
		}
		
		$payment_amount = @intval(ceil(floatval($payment_amount)*100));
		
		if($expectedValue != $payment_amount && $payment_amount == 0){ exit; }
		
		if(($expectedValue == 0 && $payment_amount == 0) || abs(($expectedValue-$payment_amount)/$payment_amount) <= abs($expectedValue-$payment_amount) && strtolower($payment_currency) == 'cad'){
			/* Everything checks out. */
			$payment_status = strtolower($payment_status);
			if($payment_status == 'pending' && !$transactionStarted){
				if(($stmt = Database::prepare("INSERT INTO `transactions` (`Date`, `CompanyID`, `Value`, `Currency`, `Product`, `Confirmed`, `PaypalTransactionID`, `PaypalPayerEmail`, `Fraud`) VALUES (NOW(), ?, ?, 'CAD', ?, 0, ?, ?, ?)")) !== false){
					$stmt->bind_param('iissss', $companyID, $expectedValue, $plan, $txn_id, $payer_email, $fraudString);
					$stmt->execute();
					$stmt->close();
				}
			} else if($payment_status == 'completed' && $transactionStarted){
				if(($stmt = Database::prepare("UPDATE `transactions` SET `Confirmed` = 1, `Fraud` = ? WHERE `CompanyID` = ? AND `PaypalTransactionID` = ? AND `PaypalPayerEmail` = ? AND `Confirmed` = 0 ORDER BY `id` DESC LIMIT 1")) !== false){
					$stmt->bind_param('ssss', $fraudString, $companyID, $txn_id, $payer_email);
					if($stmt->execute()){
						Database::query("UPDATE `companies` SET `Active` = 1, `ExpiryDate` = (NOW() + INTERVAL 365 DAY) WHERE `companies`.`id` = {$companyID}");
						
						if($query = Database::query("SELECT 1 FROM `company_features` LEFT JOIN `companies` ON `companies`.FeaturesID = `company_features`.`id` LIMIT 1")){
							if($query->num_rows == 0){
								Database::query("INSERT INTO `company_features` (`MaxTablets`, `MaxAccounts`, `MaxStores`) VALUES ({$maxTablets}, {$maxAccounts}, {$maxStores})");
								$featuresID = Database::getCon()->insert_id;
								
								Database::query("UPDATE `companies` SET `FeaturesID` = {$featuresID} WHERE `companies`.id = {$companyID}");
							} else {
								Database::query("UPDATE `company_features` SET `MaxTablets` = {$maxTablets}, `MaxAccounts` = {$maxAccounts}, `MaxStores` = {$maxStores} LEFT JOIN `companies` ON `companies`.FeaturesID = `company_features`.`id` WHERE `companies`.`id` = {$companyID}");
							}
						}
												
					}
					$stmt->close();
				}
			} else if($payment_status == 'denied'){
				exit;
			}
			
		}
	}
}
?>