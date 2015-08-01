<?php 
$this->addResource('/css/layout.css');
$this->addResource('/css/home_header.css'); 
$this->addResource('/css/home/eco.css'); 

if(!isset($_GET['switch']) && ($query = Database::query("SELECT IFNULL(company_features.MaxTablets, 0) as MaxTablets, IFNULL(company_features.MaxAccounts, 1) as MaxAccounts, IFNULL(company_features.MaxStores, 1) as MaxStores FROM `companies` LEFT JOIN `company_features` ON `company_features`.id = `companies`.FeaturesID LIMIT 1")) !== false){
	while($row = $query->fetch_assoc()){
		$maxStores = $row['MaxStores'];
		$maxAccounts = $row['MaxAccounts'];
		$maxTablets = $row['MaxTablets'];
		
		if($maxTablets > 0){
			Brevada::Redirect('/hub/payment/payment.php');
		}
		
		break;
	}
}
?>					
<div class="home_section" style="background:#fff; margin-top:0px; padding-bottom:100px;">
	<div class="container">
		<div style="width:100%; text-align:center;">
			<img src="/images/brevada.png" style="width:150px; margin:0 auto; margin-top:10px;" />
			<div id="home_text">
				<?php _e("Take your <strong>pick</strong>!"); ?>
			</div>
            <div id="home_text2" style="float:none; width:500px; margin:0 auto; margin-top:20px; text-align:center;">
				<?php echo sprintf(__("Or give us a call at <span id='emphasis'>%s</span> and we'll help you figure out exactly what your business needs and get you set up!"), __('1 (844) BREVADA')); ?>
			</div>
			<div id="pricing_holder" style='margin-top:-50px;'>
						<!-- Pricing Module 1 -->
                        <div class="col-md-4">
							<div class="panel panel-success panel-pricing">
								<div class="panel-heading">
									<h4 class="text-center title"><?php _e("Basic"); ?></h4>
                                    <p class="lead text-center">
                                        <span class="price"><span class="currency">$</span>50<span class="time">/<?php _e("month"); ?></span></span>
                                        <br />
                                        <span class="sub-price"><?php echo sprintf(__("Charged annualy at $%s"), '600'); ?></span>
                                    </p>
								</div>
								<ul class="list-group list-group-flush text-center">
                                    <li class="list-group-item main-focus">
                                        <?php _e("Custom Feedback Page, QR Code, and Marketing Materials"); ?>
                                    </li>
									<li class="list-group-item">
										<?php _e("2 <emp>Tablets</emp>"); ?>
									</li>
									<li class="list-group-item">
										<?php echo sprintf(__("%s Login"), '1'); ?>
									</li>
									<li class="list-group-item">
										<?php _e("Advanced <emp>Data Reporting</emp> and <emp>Competition Comparison</emp> Reports "); ?>
									</li>
									<li class="list-group-item">
										<?php _e("24/7 Technical Support"); ?>
									</li>
                                    <li class="list-group-item">
                                        <?php _e("Weekly <emp>Email Reports</emp>"); ?>
                                    </li>
								</ul>
								<div class="panel-footer">
									<a href="payment.php?l=basic" class="btn btn-lg btn-block btn-default"><?php _e("Choose This Package"); ?></a>
								</div>
							</div>
						</div>

                        <!-- Pricing Module 2 -->
                        <div class="col-md-4">
                            <div class="panel panel-success panel-pricing">
                                <div class="panel-heading">
                                    <h4 class="text-center title"><?php _e("Premium"); ?></h4>
                                    <p class="lead text-center">
                                        <span class="price"><span class="currency">$</span>90<span class="time">/<?php _e("month"); ?></span></span>
                                        <br />
                                        <span class="sub-price"><?php echo sprintf(__("Charged annualy at $%s"), '1080'); ?></span>
                                    </p>
                                </div>
                                <ul class="list-group list-group-flush text-center">
                                    <li class="list-group-item main-focus">
                                        <?php _e("Custom Feedback Page, QR Code, and Marketing Materials"); ?>
                                    </li>
                                    <li class="list-group-item">
                                        <?php echo sprintf(__("%s <emp>Tablets</emp>"), '5'); ?>
                                    </li>
                                    <li class="list-group-item">
                                        <?php echo sprintf(__("%s Logins"), '3'); ?>
                                    </li>
                                    <li class="list-group-item">
                                        <?php _e("Advanced <emp>Data Reporting</emp> and <emp>Competition Comparison</emp> Reports "); ?>
                                    </li>
                                    <li class="list-group-item">
                                        <?php _e("24/7 Technical Support"); ?>
                                    </li>
                                    <li class="list-group-item">
                                        <?php _e("Weekly <emp>Email Reports</emp>"); ?>
                                    </li>
                                </ul>
                                <div class="panel-footer">
                                    <a href="payment.php?l=premium" class="btn btn-lg btn-block btn-default"><?php _e("Choose This Package"); ?></a>
                                </div>
                            </div>
                        </div>

                        <!-- Pricing Module 3 -->
                        <div class="col-md-4">
                            <div class="panel panel-success panel-pricing">
                                <div class="panel-heading">
                                    <h4 class="text-center title"><?php _e("Custom"); ?></h4>
                                    <p class="lead text-center">
                                        <span class="price" style="line-height: 10px;"><span class="time"><?php _e("Contact Us"); ?></span></span>
                                        <br />
                                        <span class="sub-price"></span>
                                    </p>
                                </div>
                                <ul class="list-group list-group-flush text-center">
                                    <li class="list-group-item">
                                        <?php _e("Get a custom number of tablets, logins, and marketing materials. For one to many locations."); ?>
                                    </li>
                                </ul>
                                <div class="panel-footer">
                                    <a href="mailto:customercare@brevada.com" class="btn btn-lg btn-block btn-default"><?php _e("Contact Us"); ?></a>
                                </div>
                            </div>
                        </div>
				<br style="clear:both;" />
			</div>
		</div>
		<br style="clear:both;" />
	</div>
</div>
<?php $this->add(new View('../template/long_footer.php')); ?>