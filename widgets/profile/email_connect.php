<?php
$store_id = $this->getParameter('store_id');
$this->addResource('/js/communicate_pod.js');

$store_id = @intval($store_id);

$query = Database::query("SELECT `company_features`.`id` FROM `company_features` LEFT JOIN `companies` ON `companies`.`FeaturesID` = `company_features`.`id` LEFT JOIN `stores` ON `stores`.`CompanyID` = `companies`.`id` WHERE `stores`.`id` = {$store_id} AND `company_features`.EmailRequest IS NOT NULL AND `company_features`.EmailRequest = 1 LIMIT 1");
$emailRequestEnabled = ($query !== false && $query->num_rows > 0);
?>

<div class="thanks-header">
	<h1><?php _e("Thanks for the feedback!"); ?></h1> 
	<?php if($emailRequestEnabled){ ?><h2><?php _e("Let's stay in touch."); ?></h2><?php } ?>
</div>

<?php if($emailRequestEnabled){ ?>
<div id="communicate_form">
	 	<input type="hidden" name="store_id" value="<?php echo $store_id; ?>" />
	 	<div class="input-group input-group-lg">
	 		<span class="input-group-addon" id="basic-addon1"><?php _e("Email:"); ?></span>
	 		<input id="emailTie" class="form-control" type="email" class="inp" name="emailTie" placeholder="example@gmail.com" />
	 		
	 		<span class="input-group-btn">
		        <input id="email-submit" class="btn btn-success disabled" type="submit" value="Go" />
		    </span>
	 	</div>
	 <div id="finished" class="btn opt-out"><?php _e("I'd rather not."); ?></div>
</div>
<?php } ?>

 <div id="reset" class="refresh">
 	<i class="fa fa-refresh"></i>
 </div>