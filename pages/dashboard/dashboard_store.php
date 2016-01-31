<?php
if($this->getParameter('valid') !== true){ Brevada::Redirect('/404'); }
?>
<?php
$this->addResource('/css/bootstrap-datetimepicker.css');
$this->addResource('/js/bootstrap-datetimepicker.min.js');

$this->addResource('/css/layout.css');
$this->addResource('/css/dashboard.css');

$this->addResource('/css/brevada.tooltip.css');
$this->addResource('/js/brevada.tooltip.js');

$this->addResource('/js/jQRangeSlider/jQRangeSlider-min.js');
$this->addResource('/js/jQRangeSlider/jQDateRangeSlider-min.js');
$this->addResource('/js/jQRangeSlider/css/iThing.css');
// $this->addResource('/js/jQueryUI/jquery-ui.css');
// $this->addResource('/js/jQueryUI/jquery-ui.js');

$this->addResource('/js/Brevada.BDFF.js');
$this->addResource('/js/dashboard/dashboard.js');

$this->addResource('/js/dashboard/aspects/aspects.js');

$this->addResource('/js/dashboard/milestones/milestones.js');

$this->addResource('/js/dashboard/milestones/milestone.js');
$this->addResource('/js/dashboard/milestones/aspects.js');

$this->addResource('/js/dashboard/live/live.js');

$this->addResource('/js/dashboard/support/support.js');

$this->addResource('/js/dashboard/hoverpod/hoverpod.js');

$this->addResource('/js/dashboard/complete/complete.js');

$this->addResource('/js/dashboard/dashboard-slide.js');
$this->addResource('/js/dashboard/dashboard-graph.js');

if(!Brevada::IsLoggedIn()){
	Brevada::Redirect('/home/logout');
}

$store_id = Brevada::validate($_SESSION['StoreID'], VALIDATE_DATABASE);
$company_id = -1;

$query = Database::query("SELECT stores.`Name`, stores.`URLName`,
						  companies.`Active`, companies.`id` as CompanyID,
						  UNIX_TIMESTAMP(companies.`ExpiryDate`) as Expiry,
						  company_keywords_link.CompanyKeywordID
						  FROM stores
						  JOIN companies ON companies.`id` = stores.`CompanyID`
						  LEFT JOIN company_keywords_link ON company_keywords_link.`CompanyID` = `companies`.`id`
						  WHERE `stores`.id = {$store_id}");

$company_active = false; /* False if account has NEVER been set up. */
$company_expired = false;

$keywords = [];

while($row = $query->fetch_assoc()){
	if((!isset($row['Expiry']) || $row['Expiry'] < time())){
		/* Membership expired. */
		$company_expired = true;
	}
	
	$company_active = $row['Active'] != 0;
	
	$name = $row['Name'];
	$this->setTitle("Brevada Dashboard - {$name}");
	
	$url_name = $row['URLName'];
	
	$company_id = $row['CompanyID'];
	
	if(!empty($row['CompanyKeywordID'])){
		$keywords[] = intval($row['CompanyKeywordID']);
	}
}

$qAspects = Database::query("SELECT COUNT(*) as cnt FROM aspects WHERE aspects.StoreID = {$store_id} AND `Active` = 1");
$aspectCount = $qAspects->fetch_assoc()['cnt'];
?>
<script type='text/javascript'>bdff.storeID(<?php echo $store_id; ?>);</script>
<div id="alert-holder"></div>
<div class="top-fixed">

	<div class='top-banner row'>
		<div class='col-lg-12'>
			<img class='logo-quote link pull-left' src='/images/brevada.png' data-link='' />
			<div class='dropdown pull-right'>
				<div class='three-lines btn btn-default dropdown-toggle'  data-toggle='dropdown'>
					<i class='fa fa-ellipsis-h'></i>
				</div>
				<ul class='dropdown-menu'>
					<li class='link' data-link='settings'><?php _e('Settings'); ?></li>
					<li class='link' data-link='logout' style="border-bottom: none;"><?php _e('Logout'); ?></li>
				</ul>
			</div>
			<div class='name pull-right hidden-xs'>
				<?php _e('Current User'); ?>: <span class="variable"><?php echo $name; ?></span>
			</div>
		</div>
	</div>

	<div class="mid-banner row" style="">
	  	<a type="button" class="btn icon-button" href="/<?php echo $url_name; ?>" target="_blank">
      		<?php _e('Your Page'); ?>
      	</a>
      	<a href="/qr/<?php echo $url_name; ?>.png" target="_blank" type="button" class="btn icon-button">
			<?php _e('QR Code'); ?>
		</a>
      	<a href="hub/includes/marketing/promo_white.php" target="_blank" type="button" class="btn icon-button">
			<?php _e('Printables'); ?>
		</a>

		<a href="#" id="email-display" class="slide-down-trigger btn icon-button" type="button">
			<?php _e('Email List'); ?>
		</a>
		<?php if(isset($_SESSION['Corporate']) && $_SESSION['Corporate']){ ?>
		<a href="/dashboard" type="button" class="btn icon-button">
			<?php _e('Corporate'); ?>
		</a>
		<?php } ?>
	</div>

</div>

<div class="spacer"></div>

<div id="slide-down" class="slide-down">
	<div id="email-display-holder">
		<?php if($company_active && !$company_expired){ ?>
		<?php $this->add(new View('../widgets/dashboard/email_display.php', array('store_id' => $store_id))); ?>
		<?php } else { ?>
		<br /><p><?php _e("You must activate your account to view the email list."); ?> <div id="email-close" class="slide-down-button"><?php _e("Close"); ?></div></p>
		<?php } ?>
	</div>
</div>

<div id="wrapper">
	<!-- Side bar -->
    <div id="sidebar-wrapper">

        <div class="sidebar btn-group-vertical">

        	<button type="button" data-id="complete" class="btn btn-sidebar toggle-button icon-button">
				<div class="icon">
					<i class='fa fa-area-chart'></i>
				</div>
				<div class='icon-subtext hidden-xs'><?php _e('Complete'); ?></div>
			</button>

	        <button type="button" data-id="aspects" class="btn btn-sidebar toggle-button icon-button">
				<div class="icon">
					<i class='fa fa-list'></i>
				</div>
				<div class='icon-subtext hidden-xs'><?php _e('Details'); ?></div>
			</button>
			
			 <button data-id="live" class="btn btn-sidebar toggle-button icon-button">
				<div class="icon">
					<i class='fa fa-check'></i>
				</div>
				<div class='icon-subtext hidden-xs'><?php _e('Live'); ?></div>
			</button>
			<button type="button" data-id="milestones" class="btn btn-sidebar toggle-button icon-button">
				<div class="icon">
					<i class='fa fa-calendar'></i>
				</div>
				<div class='icon-subtext hidden-xs'><?php _e('Milestones'); ?></div>
			</button>
			<!--
			<button type="button" data-id="support" class="btn btn-sidebar toggle-button icon-button">
				<div class="icon">
					<i class='fa fa-support'></i>
				</div>
				<div class='icon-subtext hidden-xs'><?php _e('Support'); ?></div>
			</button>
			-->
        </div>
    </div>
    <!-- Right Side -->
    <div id="page-content-wrapper">
    	<div class='aspect-area'>
			<div id="main-container" class='row'>
			<?php if(isset($_GET['thanks'])){ ?>
			<div class="message-container">
				<div class='close'><i class='fa fa-times'></i></div>
				<div class="message"><?php _e('Thank you for your purchase and welcome to Brevada!'); ?></div>
				<div class="sub-message"><?php echo sprintf(__('Feel free to contact us at %s or 1 (844) BREVADA for any questions regarding your Brevada experience.'), __('customercare@brevada.com')); ?></div>
			</div>
			<?php } ?>
			<?php if($aspectCount == 0){ ?>
			<div class="message-container">
				<div class='close'><i class='fa fa-times'></i></div>
				<div class="message"><?php _e('You can enable and disable aspects on the Settings page, or just click below.'); ?></div>
				<div class='sub-message'><a class="btn btn-default" href="/settings?section=feedback"><?php _e('Turn On Aspects'); ?></a></div>
				<div class="sub-message"><?php echo sprintf(__('Feel free to contact us at %s or 1 (844) BREVADA for any questions regarding your Brevada experience.'), __('customercare@brevada.com')); ?></div>
			</div>
			<?php } ?>
			<?php if (!$company_active) { ?>
			<div class="message-container">
				<div class="message"><?php _e('Welcome to Brevada! Please <b>purchase a package</b> to activate your account.'); ?></div>
				<div class="sub-message">
				<a class="btn btn-primary btn-pay" href="upgrade.php"><?php _e('Activate Your Account'); ?></a>
				<a class="btn btn-default" target="_TOP" href="<?php echo $url_name; ?>"><?php _e('Checkout Your Page'); ?></a>
				</div>
				<div class="sub-message"><?php echo sprintf(__('Or feel free to contact us at %s for any help.'), __('customercare@brevada.com')); ?></div>
			</div>
			<?php } else if($company_expired) { ?>
			<div class="message-container">
				<div class="message"><?php _e('Your account has expired. Please <b>renew your account</b> to view your feedback.'); ?></div>
				<a class="btn btn-primary btn-pay" href="/hub/payment/payment.php"><?php _e('Renew Account'); ?></a>
				<a class="btn btn-default" target="_TOP" href="<?php echo $url_name; ?>"><?php _e('Checkout Your Page'); ?></a>
				<div class="sub-message"><?php echo sprintf(__('Or feel free to contact us at %s for any help.'), __('customercare@brevada.com')); ?></div>
			</div>
			<?php
			}
			?>
			</div>
		</div>
    </div>
</div>