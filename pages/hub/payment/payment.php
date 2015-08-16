<?php
$this->addResource('/css/layout.css'); 
$this->addResource('/css/payment.css');

$companyID = Brevada::validate($_SESSION['CompanyID']);

$selectedPlan = strtolower(trim(Brevada::FromPOSTGET('l')));

$promoCode = Brevada::FromGET('promo');

$name = ''; $company_active = false; $company_expired = false;

if(($query = Database::query("SELECT companies.`Name`, companies.`Active`, UNIX_TIMESTAMP(companies.`ExpiryDate`) as `Expiry`, IFNULL(company_features.MaxTablets, 0) as MaxTablets, IFNULL(company_features.MaxAccounts, 1) as MaxAccounts, IFNULL(company_features.MaxStores, 1) as MaxStores FROM companies LEFT JOIN company_features ON company_features.id = companies.FeaturesID WHERE `companies`.id = {$companyID} LIMIT 1")) !== false){
	while($row = $query->fetch_assoc()){
		$name = $row['Name'];
		$company_active = $row['Active'] != 0;
		$company_expired = !isset($row['Expiry']) || $row['Expiry'] < time();
		
		$maxAccounts = @intval($row['MaxAccounts']);
		$maxTablets = @intval($row['MaxTablets']);
		$maxStores = @intval($row['MaxStores']);
	}
}

$plan = 1;
$payment_price = 600;

if($selectedPlan == 'basic' || ($maxTablets == 2 && $maxAccounts == 1)){
	$plan = 1;
	$payment_price = 600;
} else if($selectedPlan == 'premium' || ($maxTablets == 5 && $maxAccounts == 3)){
	$plan = 2;
	$payment_price = 1080;
}

$discountedValue = $payment_price;
$validPromo = false;

if(!empty($promoCode)){
	$d_promoCode = strtolower(Brevada::validate($promoCode, VALIDATE_DATABASE));
	$d_paypalItemName = Brevada::validate($plan, VALIDATE_DATABASE);
	if(($query = Database::query("SELECT `id`, `DiscountedValue` FROM `promo_codes` WHERE `Code` = '{$d_promoCode}' AND `Used` = 0 ORDER BY `id` DESC LIMIT 1")) !== false){
		if($query->num_rows > 0){
			$row = $query->fetch_assoc();
			$discountedValue = @intval($row['DiscountedValue']);
			$validPromo = true;
		}
	}
}

if($company_active && $company_expired){
	/* Previously purchased plan has expired. Now renewing. */
	/* TODO. When renewing, see how many tablets have been purchased etc.. */
} else if($company_active && !$company_expired){
	/* Previously purchased plan is still in effect. */
	Brevada::Redirect('/dashboard');
} else if(!$company_active){
	/* Plan has never been purchased before. This is the first time. */
}

?>
<div class="home_section" style="background:#fff; margin-top:0px; padding-bottom:100px;">
	<div class="container">
		<div style="width:100%; text-align:center;">
			<img src="/images/brevada.png" style="width:150px; margin:0 auto; margin-top:10px;" />
			<div id="home_text">
				<?php echo sprintf(__("You're only <strong>one step</strong> away %s!"), $name); ?>
			</div>
			<div id="home_text2" style="float:none; width:500px; margin:0 auto; margin-top:20px; text-align:center;">
				<?php _e("Give us a call at <span class='emphasis'>1 (844) BREVADA</span> and we'll help you figure out exactly what your business needs and get you set up!"); ?>
			</div>
			<br />
			<div id="pricing_holder">
				<div id="pricing_box">
					<div id="pricing_top" style="background:#FF2B2B;">
						<div class="pricing_title">
							<div  id="home_text" style="color:#fff;">
							<?php echo $plan == 1 ? strtoupper(__("Basic")) : strtoupper(__("Premium")); ?>
							</div>
							<div  id="pricing_price">
							<?php if(!$validPromo){ ?>
							<span id="price">$<?php echo $plan == 1 ? __("50") : __("90"); ?></span>/<?php _e("month"); ?>
							<?php
							} else if($validPromo){
							?>
							<strike><span id="price">$<?php echo $plan == 1 ? __("50") : __("90"); ?></span>/<?php _e("month"); ?></strike><br />
							<span id="price">$<?php echo ceil(floatval($discountedValue)/12.0); ?></span>/<?php _e("month"); ?>
							<?php
							}
							?>
							</div>
						</div>
						<div id="pricing_under">
							<i><?php
							if($validPromo){
								echo sprintf(__("Billed at <strike>$%s</strike> $%s for one year."), $payment_price, $discountedValue);
							} else {
								echo sprintf(__("Billed at $%s for one year."), $payment_price);
							}
							?></i>
							<br />
							<strong><?php echo $plan == 1 ? __("The basic package.") : __("The premium package."); ?></strong>
						</div>
					</div>
					<div class="pricing_bottom">
						<form name="_xclick" action="https://www.paypal.com/cgi-bin/webscr" method="post">
								 <input type="hidden" name="cmd" value="_xclick">
								 <input type="hidden" name="business" value="payments@brevada.com">
								 <input type="hidden" name="currency_code" value="CAD">
								 <input type="hidden" name="item_name" value="Brevada <?php echo $plan == 1 ? __("Basic") : __("Premium"); ?> Package (1 Year)">
								 <input type="hidden" name="amount" value="<?php echo number_format((float) $discountedValue, 2, '.', ''); ?>">
								 <input type="hidden" name="return" value="http://www.brevada.com/thanks">
								 <input type="hidden" name="notify_url" value="http://www.brevada.com/ipn.php?id=<?php echo $companyID; echo $validPromo ? "&promo=".$promoCode : ''; ?>">
								 <input type="submit" id="pricing_button"  name="submit" value="<?php _e("Pay Now"); ?>" style="border:none; color:#f9f9f9; padding:5px; width:200px;">
					   </form>
					</div>
					<form method='get' action='payment.php'><input type='hidden' name='l' value='<?php echo Brevada::FromGET('l'); ?>' /><div class='promo-code pricing_bottom'><?php _e("Do you have a Promo code?"); ?><br /><input type='text' name='promo' placeholder='<?php _e("Promo Code"); ?>' id='txtPromoCode' /><div id='applyPromo' onclick='$(this).parent().parent().submit();'><i class='fa fa-chevron-circle-right'></i></div></div></form>
				</div>
				<br />
				<br />
				<a href="upgrade.php?switch"><?php _e("Switch Package"); ?></a>
			</div>
		</div>
		<br style="clear:both;" />
	</div>
</div>
<?php $this->add(new View('../template/long_footer.php')); ?>