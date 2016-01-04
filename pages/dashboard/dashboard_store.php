<?php
if($this->getParameter('valid') !== true){ Brevada::Redirect('/404'); }
?>
<?php
$this->addResource('/css/layout.css');
$this->addResource('/css/dashboard.css');

$this->addResource('/js/Brevada.BDFF.js');
$this->addResource('/js/dashboard/dashboard.js');

$this->addResource('/js/dashboard/aspects/aspects.js');

$this->addResource('/js/dashboard/milestones/milestones.js');
$this->addResource('/js/dashboard/milestones/milestone.js');
$this->addResource('/js/dashboard/milestones/aspects.js');

$this->addResource('/js/dashboard/live/live.js');
$this->addResource('/js/dashboard/support/support.js');
$this->addResource('/js/dashboard/hoverpod/hoverpod.js');

$this->addResource('/js/dashboard/dashboard-slide.js');
$this->addResource('/js/dashboard/dashboard-graph.js');

$this->addResource('/css/brevada.tooltip.css');
$this->addResource('/js/brevada.tooltip.js');

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

function numericalCSS($i){
	return $i >= 0 ? 'positive' : 'negative';
}

$data_overall4W = DataResult::diffRating(
	(new Data())->store($store_id)->from(time()-(4*7*24*3600))->getAvg(),
	(new Data())->store($store_id)->to(time()-(4*7*24*3600))->getAvg()
);
$data_overallAll = (new Data())->store($store_id)->getAvg()->getRating();
$data_relativeBenchmark = DataResult::diffRating(
	(new Data())->store($store_id)->getAvg(),
	(new Data())->keyword($keywords)->getAvg()
);


$areasOfFocus = array();
$areasOfLeastConcern = array();

$query = Database::query("SELECT aspect_type.Title FROM `data_cache`
						  JOIN aspect_type ON `data_cache`.Domain_AspectID = aspect_type.`id`
						  JOIN aspects ON aspects.StoreID = `data_cache`.Domain_StoreID AND aspects.AspectTypeID = aspect_type.`id`
						  WHERE
							`data_cache`.Domain_StoreID = {$store_id}
							AND `data_cache`.`DaysBack` = -1
							AND `data_cache`.`EndDate` = '0000-00-00 00:00:00'
							AND aspects.`Active` = 1
						  ORDER BY `data_cache`.`TotalAverage` ASC LIMIT 2");
if($query !== false){
	while($row = $query->fetch_assoc()){
		$areasOfFocus[] = $row['Title'];
	}
	$query->close();
}

$query = Database::query("SELECT aspect_type.Title FROM `data_cache`
						  JOIN aspect_type ON `data_cache`.Domain_AspectID = aspect_type.`id`
						  JOIN aspects ON aspects.StoreID = `data_cache`.Domain_StoreID AND aspects.AspectTypeID = aspect_type.`id`
						  WHERE
							`data_cache`.Domain_StoreID = {$store_id}
							AND `data_cache`.`DaysBack` = -1
							AND `data_cache`.`EndDate` = '0000-00-00 00:00:00'
							AND aspects.`Active` = 1
						  ORDER BY `data_cache`.`TotalAverage` DESC LIMIT 2");
if($query !== false){
	while($row = $query->fetch_assoc()){
		$areasOfLeastConcern[] = $row['Title'];
	}
	$query->close();
}

$areasOfLeastConcern = array_diff($areasOfLeastConcern, $areasOfFocus);
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

<div class="spacer">

</div>

<div id="slide-down" class="slide-down">
	<div id="email-display-holder">
		<?php if($company_active && !$company_expired){ ?>
		<?php $this->add(new View('../widgets/dashboard/email_display.php', array('store_id' => $store_id))); ?>
		<?php } else { ?>
		<br /><p><?php _e("You must activate your account to view the email list."); ?> <div id="email-close" class="slide-down-button"><?php _e("Close"); ?></div></p>
		<?php } ?>
	</div>
</div>

<?php
/* All aspects. */
	$query = Database::query("SELECT aspect_type.Title, aspects.id as AspectID, aspect_type.id as AspectTypeID
	FROM aspects LEFT JOIN aspect_type ON aspect_type.ID = aspects.AspectTypeID
	WHERE aspects.StoreID = {$store_id}
	AND `Active` = 1 ORDER BY `aspect_type`.Title ASC");
?>

<!-- Left side -->
<div class='aspect-area hidden-xs hidden-sm col-md-3 right-bar' style="display: none;">
	<div class='row'>
		<div class='col-sm-12 area-title'><i class='fa fa-area-chart'></i> <?php _e('Consultant'); ?></div>
		<div class="col-sm-12 hidden-xs">		

			<div class='col-sm-12 area-title'><i class='fa fa-area-chart'></i> <?php _e('Consultant'); ?></div>

			<!-- Overall Average -->
			<?php 
				if($data_overallAll>=50){
					$change = 'positive';
					$icon = 'fa-thumbs-up';
				} else if ($data_overallAll==0){
					$change = 'neutral';
					$icon = 'fa-minus-circle';
				} else {
					$change = 'negative';
					$icon = 'fa-thumbs-down';
				}
			?>
			<div class='col-sm-12 overall-decrease block <?php echo $change; ?>-text'>
				<div class="block-left hidden-sm">
					<i class='fa <?php echo $icon; ?>'></i>
				</div>
				<div class="block-right">
					<div class='big-number <?php echo $change; ?>-text'>
						<?php echo abs($data_overallAll)."%"; ?>
					</div>
				</div>
				<div class="block-bottom">
					<?php _e('Overall Score'); ?>
				</div>
			</div>


			<!-- Past 4 weeks -->
			<?php 
				if($data_overall4W>=50){
					$change = 'positive';
					$icon = 'fa-chevron-circle-up';
				} else if ($data_overall4W==0){
					$change = 'neutral';
					$icon = 'fa-minus-circle';
				} else {
					$change = 'negative';
					$icon = 'fa-chevron-circle-down';
				}
			?>
			<div class='col-sm-12 overall-improvement block <?php echo $change; ?>-text'>
				<div class="block-left hidden-sm">
					<i class='fa <?php echo $icon; ?>'></i>
				</div>
				<div class="block-right">
					<div class='big-number <?php echo $change; ?>-text'>
						<?php echo abs($data_overall4W)."%"; ?>
					</div>
				</div>
				<div class="block-bottom">
					<?php _e('Change in the Past 4 Weeks'); ?>
				</div>
			</div>

			<!-- vs benchmark -->
			<?php 
				if($data_relativeBenchmark>=1){
					$icon = 'fa-chevron-circle-up';
					$message = 'above the industry average';
				} else if ($data_relativeBenchmark==0){
					$icon = 'fa-minus-circle';
					$message = 'same as industry average';
				} else {
					$icon = 'fa-chevron-circle-down';
					$message = 'below the industry average';
				}
			?>
			<div class='col-sm-12 below-benchmark block <?php echo numericalCSS($data_relativeBenchmark); ?>-text'>
				<div class="block-left hidden-sm">
					<i class='fa <?php echo $icon; ?>'></i>
				</div>
				<div class="block-right">
					<div class='big-number <?php echo numericalCSS($data_relativeBenchmark); ?>-text'>
						<?php echo abs($data_relativeBenchmark)."%"; ?>
					</div>
				</div>
				<div class="block-bottom">
					<?php 
						$abs_relative = abs($data_relativeBenchmark);
						_e($message); 
					?>
				</div>
			</div>

			<div class='col-sm-12 block consultant'>
				<div class='title'><?php _e('Areas For Improvement'); ?></div>
					<div class='body'>
						<?php
						foreach($areasOfFocus as $aspect){
							echo "<span class='aspect-title pull-left negative'>".__($aspect)."</span>";
						}
						if(empty($areasOfFocus)){
							echo "<span class='aspect-title placeholder'></span>";
						}
						?>
						<br class="clear: both;" />
					</div>
				<br class="clear: both;" />
			</div>


			<div class='col-sm-12 block consultant'>
				<div class='title'><?php _e('Strengths'); ?></div>
				<div class='body'>
					<?php
					foreach($areasOfLeastConcern as $aspect){
						echo "<span class='aspect-title pull-left positive'>".__($aspect)."</span>";
					}
					if(empty($areasOfLeastConcern)){
						echo "<span style='color: #BBB;'>None yet.</span>";
					}
					?>
					<br class="clear: both;" />
				</div>
				<br class="clear: both;" />
			</div>


		</div>
	</div>
</div>

<div id="wrapper">
	<!-- Side bar -->
    <div id="sidebar-wrapper">

        <div class="sidebar btn-group-vertical">
	        <button type="button" data-id="aspects" class="btn btn-sidebar toggle-button icon-button">
				<div class="icon">
					<i class='fa fa-list'></i>
				</div>
				<div class='icon-subtext hidden-xs'><?php _e('Overall'); ?></div>
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

			<button type="button" data-id="support" class="btn btn-sidebar toggle-button icon-button">
				<div class="icon">
					<i class='fa fa-support'></i>
				</div>
				<div class='icon-subtext hidden-xs'><?php _e('Support'); ?></div>
			</button>
        </div>
    </div>
    <!-- Right Side -->
    <div id="page-content-wrapper">
    	<div class='aspect-area container'>
			<div id="main-container" class='row'>
			<div class='col-sm-12 area-title'><i class='fa fa-comments'></i> <?php _e('Feedback'); ?></div>
			<?php if(isset($_GET['thanks'])){ ?>
			<div class="message-container">
				<div class='close'><i class='fa fa-times'></i></div>
				<div class="message"><?php _e('Thank you for your purchase and welcome to Brevada!'); ?></div>
				<div class="sub-message"><?php echo sprintf(__('Feel free to contact us at %s or 1 (844) BREVADA for any questions regarding your Brevada experience.'), __('customercare@brevada.com')); ?></div>
			</div>
			<?php } ?>
			<?php if($query === false || $query->num_rows == 0){ ?>
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
			} else { if($query !== false){ $query->close(); } } ?>
			</div>
		</div>
    </div>
</div>

<!-- <div class="bottom-bar">
	&copy; 2015 Brevada Inc. &nbsp;
</div> -->