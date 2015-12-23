<?php
if($this->getParameter('valid') !== true){ Brevada::Redirect('/404'); }
?>
<?php
$this->addResource('/css/layout.css');
$this->addResource('/css/dashboard.css');
$this->addResource('/js/dashboard/dashboard.js');
$this->addResource('/js/dashboard/milestones.js');
$this->addResource('/js/dashboard/live.js');
$this->addResource('/js/dashboard/support.js');
$this->addResource('/js/dashboard/hoverpod.js');
$this->addResource('/js/dashboard/dashboard-slide.js');
$this->addResource('/js/dashboard/dashboard-graph.js');

$this->addResource('/css/brevada.tooltip.css');
$this->addResource('/js/brevada.tooltip.js');

if(!Brevada::IsLoggedIn()){
	Brevada::Redirect('/home/logout');
}

$store_id = Brevada::validate($_SESSION['StoreID'], VALIDATE_DATABASE);

$query = Database::query("SELECT stores.`Name`, stores.`URLName`, companies.`Active`, UNIX_TIMESTAMP(companies.`ExpiryDate`) as Expiry FROM stores LEFT JOIN companies ON companies.`id` = stores.`CompanyID` WHERE `stores`.id = {$store_id} LIMIT 1");

$company_active = false; /* False if account has NEVER been set up. */
$company_expired = false;

while($row = $query->fetch_assoc()){
	if((!isset($row['Expiry']) || $row['Expiry'] < time())){
		/* Membership expired. */
		$company_expired = true;
	}
	
	$company_active = $row['Active'] != 0;
	
	$name = $row['Name'];
	$this->setTitle("Brevada Dashboard - {$name}");
	
	$url_name = $row['URLName'];
}

function numericalCSS($i){
	return $i >= 0 ? 'positive' : 'negative';
}

$data_overall4W = 0;
$data_overallAll = 0;
$data_relativeBenchmark = 0;

if(($query = Database::prepare("SELECT dashboard.Data_Overall4W, dashboard.Data_OverallAll, dashboard.Data_RelativeBenchmark FROM `dashboard` WHERE dashboard.StoreID = ? LIMIT 1")) !== false){
	$query->bind_param('i', $store_id);
	if($query->execute()){
		$query->bind_result($data_overall4W, $data_overallAll, $data_relativeBenchmark);
		while($query->fetch()){
			$data_overall4W = round((float) $data_overall4W + 0, 0);
			$data_overallAll = round((float) $data_overallAll + 0, 0);
			$data_relativeBenchmark = round((float) $data_relativeBenchmark + 0, 0);
		}
	}
	$query->close();
}


$areasOfFocus = array();
$areasOfLeastConcern = array();

$query = Database::query("SELECT aspect_type.Title FROM aspects LEFT JOIN aspect_type ON aspects.AspectTypeID = aspect_type.ID WHERE aspect_type.Title IS NOT NULL AND aspects.StoreID = {$store_id} AND aspects.`Active` = 1 ORDER BY aspects.Data_AttentionScore DESC LIMIT 2");
if($query !== false){
	while($row = $query->fetch_assoc()){
		$areasOfFocus[] = $row['Title'];
	}
	$query->close();
}

$query = Database::query("SELECT aspect_type.Title FROM aspects LEFT JOIN aspect_type ON aspects.AspectTypeID = aspect_type.ID WHERE aspect_type.Title IS NOT NULL AND aspects.StoreID = {$store_id} AND aspects.`Active` = 1 ORDER BY aspects.Data_AttentionScore ASC LIMIT 2");
if($query !== false){
	while($row = $query->fetch_assoc()){
		$areasOfLeastConcern[] = $row['Title'];
	}
	$query->close();
}

$areasOfLeastConcern = array_diff($areasOfLeastConcern, $areasOfFocus);
?>
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
	  <button data-id="aspects" type="button" class="btn icon-button toggle-button">
      	<!-- <i class='fa fa-th-list'></i> -->
      	Aspects
      </button>
      <button data-id="live" type="button" class="btn icon-button toggle-button">
      	<!-- <i class='fa fa-asterisk'></i> -->
      	Live
      </button>
	  <button data-id="milestones" type="button" class="btn icon-button toggle-button">
      	<!-- <i class='fa fa-star'></i> -->
      	Milestones
      </button>
      <button data-id="support" type="button" class="btn icon-button toggle-button">
      	<!-- <i class='fa fa-star'></i> -->
      	Support
      </button>
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
	$query = Database::query("SELECT aspect_type.Title, IFNULL(aspects.`Data_Bucket`, '[]') as `Data_Bucket`, aspects.id, aspects.Data_LastUpdate, aspects.Data_RatingPercent, aspects.Data_RatingPercentOther, aspects.Data_Percent4W, aspects.Data_Percent6M, aspects.Data_Percent1Y, (SELECT COUNT(*) FROM feedback WHERE feedback.Rating > -1 AND feedback.AspectID = aspects.ID AND UNIX_TIMESTAMP(`feedback`.`Date`) < `aspects`.`Data_LastUpdate`) as Total FROM aspects LEFT JOIN aspect_type ON aspect_type.ID = aspects.AspectTypeID WHERE aspects.StoreID = {$store_id} AND `Active` = 1 AND aspect_type.Title <> '' ORDER BY `aspect_type`.Title ASC");
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
			<a type="button" class="btn btn-default btn-sidebar" href="/<?php echo $url_name; ?>" target="_blank">
				<i class='fa fa-external-link'></i>
				<div class='icon-subtext'><?php _e('Your Page'); ?></div>
			</a>
			<a href="hub/includes/marketing/promo_white.php" target="_blank" type="button" class="btn btn-default btn-sidebar">
					<i class='fa fa-print'></i>
					<div class='icon-subtext'><?php _e('Printables'); ?></div>
			</a>
			<a href="/qr/<?php echo $url_name; ?>.png" target="_blank" type="button" class="btn btn-default btn-sidebar">
					<i class='fa fa-qrcode'></i>
					<div class='icon-subtext'><?php _e('QR Code'); ?></div>
			</a>
			<a href="#" id="email-display" class="slide-down-trigger btn btn-default btn-sidebar" type="button">
					<i class='fa fa-envelope-o'></i>
					<div class='icon-subtext'><?php _e('Email List'); ?></div>
			</a>
			<button type="button" class="btn btn-default btn-sidebar">
		      	<i class='fa fa-gear'></i>
		      	<br />
		      	Settings
		      </button>
			<?php if(isset($_SESSION['Corporate']) && $_SESSION['Corporate']){ ?>
			<a href="/dashboard" type="button" class="btn btn-default btn-sidebar">
					<i class='fa fa-briefcase'></i>
					<div class='icon-subtext'><?php _e('Corporate'); ?></div>
			</a>
			<?php } ?>
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
			} else {
			while($query !== false && $row = $query->fetch_assoc()){
				$title = $row['Title'];
				$id = $row['id'];
				
				/* Adding '0' forces float(0) rather than float(-0). */
				
				$data_ratingPercent = round((float) $row['Data_RatingPercent'] + 0, 1);
				$data_ratingPercentOther = round((float) $row['Data_RatingPercentOther'] + 0);
				$data_percent4W = round((float) $row['Data_Percent4W'] + 0, 0);
				$data_percent1Y = round((float) $row['Data_Percent1Y'] + 0, 0);
				
				$total_responses = ((int) $row['Total']);

				if($data_ratingPercent >= 80) {
					$colour = 'positive';
				} else if ($data_ratingPercent >= 60) {
					$colour = 'great';
				} else if ($data_ratingPercent >= 40) {
					$colour = 'neutral';
				} else if ($data_ratingPercent >= 20) {
					$colour = 'bad';
				} else {
					$colour = 'negative';
				}
				
				$bucket = json_decode($row['Data_Bucket'], true);
				
				$transDate = function($a){
					return date('d/m/Y', $a);
				};
				$bucketDates = array_map($transDate, array_column($bucket, 'Date'));
				
				$roundData = function($a){
					return round($a, 1);
				};
				$bucketData = array_map($roundData, array_column($bucket, 'Data'));
				
				$bucketJSON = array('dates' => $bucketDates, 'data' => $bucketData);
				$bucketJSON = json_encode($bucketJSON);
			?>
				<!-- Aspect Box -->
				<div class="col-sm-6 col-md-4 col-lg-3 pod-holder">
					<div id="pod<?php echo $id; ?>" class="pod">
						<div class="body">
							<div class="header">
								<span class='aspect-title <?php echo $colour; ?>'><?php _e($title); ?></span>
							</div>
							<div class="pull-left col-md-6 pod-body-left">
								<div class='top'>
									<i class='pull-left fa <?php echo $data_percent4W >= 0 ? 'fa-arrow-circle-up' : 'fa-arrow-circle-down'; ?>'></i>
									<span class='pull-left percent'><?php echo abs($data_percent4W)."%"; ?></span>
									<span class='duration'><?php _e('4 weeks'); ?></span>
								</div>
								<div class='top'>
									<i class='pull-left fa <?php echo $data_percent1Y >= 0 ? 'fa-arrow-circle-up' : 'fa-arrow-circle-down'; ?>'></i>
									<span class='pull-left percent'><?php echo abs($data_percent1Y)."%"; ?></span>
									<span class='duration'><?php _e('1 year'); ?></span>
								</div>
							</div>
							<div class="pull-right col-md-6 pod-body-right">
								<div class='pod-body-rating <?php echo $colour; ?>-text'><?php echo "{$data_ratingPercent}%"; ?></div>
								<div class="rating-text"><?php _e('in'); ?> <?php echo $total_responses; ?> <?php _e('responses'); ?>.</div>
								<div class='pod-body-rating external'><?php echo "{$data_ratingPercentOther}%"; ?></div>
								<div class="rating-text external"><?php _e('industry average'); ?>.</div>
							</div>
							
							<div class="col-md-12 pod-body-bottom">
								<input class="graph-toggle" type="checkbox" checked data-toggle="toggle" data-onstyle="default" data-on="Line" data-off="Bar" data-size="mini" data-width="100" data-height="25">
								<div class='graphs'>
									<div class="bar-graph">
										<div class='left-graph graph <?php echo $colour; ?>' data-percent='<?php echo $data_ratingPercent; ?>'>
											<div class='percent'><?php echo "{$data_ratingPercent}%"; ?></div>
										</div>
										<div class='right-graph graph' data-percent='<?php echo $data_ratingPercentOther; ?>' data-tooltip='<?php _e('Market Benchmark'); ?> (<?php echo "{$data_ratingPercentOther}%"; ?>)'>
										</div>
									</div>

									<div class="line-graph">
										<script type='text/javascript'>
											build_line_graph(<?php echo $bucketJSON; ?>, "pod<?php echo $id; ?>");
										</script>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php } if($query !== false){ $query->close(); } } ?>
			</div>
		</div>
    </div>
</div>





<!-- <div class="bottom-bar">
	&copy; 2015 Brevada Inc. &nbsp;
</div> -->