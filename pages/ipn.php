<?php
	$paypal_email='payments@brevada.com';
	$id=@intval(empty($_GET['id']) ? 0 : $_GET['id']);
	$query = Databse::query("SELECT * FROM payments WHERE user_id='{$id}' ORDER BY id DESC LIMIT 1");
	//table, * means all info (all columns)
	
	if($query->num_rows==0){
		$months=12;
		$promo_code='NONE';
		
		//FIND ACCOUNT LEVEL
		$query = Database::query("SELECT * FROM users WHERE id='{$id}'");
		//table, * means all info (all columns)
		$level = 0;
		WHILE ($rows=$query->fetch_assoc()){
			$level=$rows['level'];
		}
		if($level==2){
			$value='168.00';
		} else if($level==4){
			$value='1080.00';
		} else {
			$level=3;
			$value='360.00';
		}

		$sqlPay = "INSERT INTO payments(user_id, `value`, promo_code) VALUES('{$id}','{$value}', '{$promo_code}')";
		Database::query($sqlPay);
	} else {
		while($rows=$query->fetch_assoc()){
			$value = $rows['value'];
			$promo_code = $rows['promo_code'];
			//level
			//FIND ACCOUNT LEVEL
			$query = Database::query("SELECT * FROM users WHERE id='{$id}'");
			//table, * means all info (all columns)
			$level = 0;
			WHILE ($rows=$query->fetch_assoc()){
				$level=$rows['level'];
			}
			//find duration of promo code
			$queryMonths = Database::query("SELECT * FROM codes WHERE code='{$promo_code}' LIMIT 1");
			WHILE ($rowsMonths=$queryMonths->fetch_assoc()){
				$months=$rowsMonths['duration_months'];
				$referral_user=$rowsMonths['referral_user'];
				$uses=$rowsMonths['uses'];
				$uses--;
				//subtract a use from the promo code
				$sqlUpdateCodes ="UPDATE codes SET uses='{$uses}' WHERE code='{$promo_code}'";
				Database::query($sqlUpdateCodes);
				//give cash to referral user (if applicable)
				
				if($referral_user!=0){
					$sqlReferral ="UPDATE users 
 			SET referral_credits = referral_credits + 50 
  			WHERE id = '{$referral_user}'";
					Database::query($sqlReferral);
				}

			}

		}

	}

	// tell PHP to log errors to ipn_errors.log in this directory
	ini_set('log_errors', true);
	ini_set('error_log', dirname(__FILE__).'/ipn_errors.log');
	// intantiate the IPN listener
	include('../pages/ipnlistener.php');
	$listener = new IpnListener();
	// tell the IPN listener to use the PayPal test sandbox
	$listener->use_sandbox = true;
	// try to process the IPN POST
	try {
		$listener->requirePostMethod();
		$verified = $listener->processIpn();
	}

	catch (Exception $e) {
		error_log($e->getMessage());
		exit(0);
	}

	$verified=true;
	// TODO: Handle IPN Response here
	
	if ($verified) {
		$errmsg = '';
		// stores errors from fraud checks
		// 1. Make sure the payment status is "Completed" 
		
		if ($_POST['payment_status'] != 'Completed') {
			// simply ignore any IPN that is not completed
			mail('robbie.goldfarb@yahoo.com', 'IPN Fraud Warning', 'WHAT??');
			exit(0);
		}

		// 2. Make sure seller email matches your primary account email.
		
		if ($_POST['receiver_email'] != 'payments@brevada.com') {
			$errmsg .= "'receiver_email' does not match: ";
			$errmsg .= $_POST['receiver_email']."\n";
			$errmsg .= $seller."\n";
		}

		// 3. Make sure the amount(s) paid match
		
		if ($_POST['mc_gross'] != $value) {
			$errmsg .= "'mc_gross' does not match: $value ";
			$errmsg .= $_POST['mc_gross']."\n";
			$price=$value;
			$errmsg .= $price."\n";
		}

		// 4. Make sure the currency code matches
		
		if ($_POST['mc_currency'] != 'CAD') {
			$errmsg .= "'mc_currency' does not match: ";
			$errmsg .= $_POST['mc_currency']."\n";
		}

		// TODO: Check for duplicate txn_id
		
		if (!empty($errmsg)) {
			//TRANSACTION NOT COMPLETE, POTENTIAL FRAUD, NOTIFY SELLER IN HUB
			// manually investigate errors from the fraud checking
			$body = "IPN failed fraud checks: \n$errmsg\n\n";
			$body .= $listener->getTextReport();
			mail('robbie.goldfarb@yahoo.com', 'IPN Fraud Warning', $body);
		} else {
			// TODO: process order here
			//update the order to payed=1
			$duration=$months*30;
			$expire = date('Y-m-d',strtotime(date("Y-m-d", time()) . " + $duration day"));
			$sql ="UPDATE payments SET complete=1 WHERE user_id = '{$id}'";
			Database::query($sql);
			$query = Database::query("SELECT * FROM users WHERE id='$id'");
			//table, * means all info (all columns)
			$email = '';
			WHILE ($rows=$query->fetch_assoc()){
				$email=$rows['email'];
				//include'overall/email/emails/mail_2.php';
			}

			$body .= $listener->getTextReport();
			// mail('robbie.goldfarb@yahoo.com', 'Order Confirmation', $body);
			$sql ="UPDATE users SET active='yes', expiry_date='{$expire}', trial='0', promo_code='{$promo_code}', level='{$level}' WHERE id = '{$id}'";
			Database::query($sql);
		}

	} else {
		// manually investigate the invalid IPN
		mail('$seller', 'Invalid IPN', $listener->getTextReport());
	}
?>