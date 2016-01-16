<?php
if($this->getParameter('valid') !== true){ Brevada::Redirect('/404'); }
?>
<?php
$this->addResource('/css/layout.css');
$this->addResource('/css/dashboard.css');

$this->addResource('/js/dashboard/dashboard.js');

$this->addResource('/js/dashboard/dashboard-slide.js');
$this->addResource('/js/dashboard/dashboard-graph.js');

$this->addResource('/css/brevada.tooltip.css');
$this->addResource('/js/brevada.tooltip.js');

if(!Brevada::IsLoggedIn()){
	Brevada::Redirect('/home/logout');
}

$company_id = Brevada::validate($_SESSION['CompanyID'], VALIDATE_DATABASE);

$query = Database::query("SELECT companies.`Name`, companies.`Active`, UNIX_TIMESTAMP(companies.`ExpiryDate`) as Expiry FROM companies WHERE `companies`.id = {$company_id} LIMIT 1");

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
}

function numericalCSS($i){
	return $i >= 0 ? 'positive' : 'negative';
}

$keyword_rows = [];
if(($stmt = Database::prepare("
	SELECT company_keywords_link.CompanyKeywordID
	FROM company_keywords_link
	WHERE
	company_keywords_link.`CompanyID` = ?")) !== false){
	$stmt->bind_param('i', $company_id);
	if($stmt->execute()){
		$result = $stmt->get_result();
		$keyword_rows = $result->fetch_all(MYSQLI_ASSOC);
	}
	$stmt->close();
}
$keywords = [];
foreach($keyword_rows as $row){
	if(!empty($row['CompanyKeywordID'])){
		$keywords[] = @intval($row['CompanyKeywordID']);
	}
}

$data_overall4W = 0;
$data_overallAll = round((new Data())->company($company_id)->getAvg()->getRating(), 1);
$data_relativeBenchmark = $data_overallAll - (new Data())->keyword($keywords)->getAvg()->getRating();

$data_percent4W_A = (new Data())->company($company_id)->from(time()-(4*7*24*3600))->getAvg();
$data_percent4W_B = (new Data())->company($company_id)->to(time()-(4*7*24*3600))->getAvg();
$data_overall4W = null;
if($data_percent4W_A->getSize() > 0){
	$data_overall4W = $data_percent4W_A->getRating() - $data_percent4W_B->getRating();
}

$areasOfFocus = array();
$areasOfLeastConcern = array();
$query = Database::query("SELECT `stores`.Name, AVG(`data_cache`.`TotalAverage`) as total_avg FROM `data_cache`
						  JOIN aspect_type ON `data_cache`.Domain_AspectID = aspect_type.`id`
						  JOIN aspects ON aspects.StoreID = `data_cache`.Domain_StoreID AND aspects.AspectTypeID = aspect_type.`id`
						  JOIN `stores` ON `stores`.`id` = aspects.StoreID
						  WHERE
							`data_cache`.Domain_CompanyID = {$company_id}
							AND `data_cache`.`DaysBack` = -1
							AND `data_cache`.`EndDate` = '0000-00-00 00:00:00'
							AND aspects.`Active` = 1
						  GROUP BY `stores`.Name
						  ORDER BY `data_cache`.`TotalAverage` ASC LIMIT 2");
if($query !== false){
	while($row = $query->fetch_assoc()){
		$areasOfFocus[] = $row['Name'];
	}
	$query->close();
}
$query = Database::query("SELECT `stores`.Name, AVG(`data_cache`.`TotalAverage`) as total_avg FROM `data_cache`
						  JOIN aspect_type ON `data_cache`.Domain_AspectID = aspect_type.`id`
						  JOIN aspects ON aspects.StoreID = `data_cache`.Domain_StoreID AND aspects.AspectTypeID = aspect_type.`id`
						  JOIN `stores` ON `stores`.`id` = aspects.StoreID
						  WHERE
							`data_cache`.Domain_CompanyID = {$company_id}
							AND `data_cache`.`DaysBack` = -1
							AND `data_cache`.`EndDate` = '0000-00-00 00:00:00'
							AND aspects.`Active` = 1
						  GROUP BY `stores`.Name
						  ORDER BY `data_cache`.`TotalAverage` DESC LIMIT 2");
if($query !== false){
	while($row = $query->fetch_assoc()){
		$areasOfLeastConcern[] = $row['Name'];
	}
	$query->close();
}
$areasOfLeastConcern = array_diff($areasOfLeastConcern, $areasOfFocus);
?>

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
			<div class='name pull-right'>
				<?php _e('Current User'); ?>: <span class="variable"><?php echo $name; ?></span>
			</div>
		</div>
	</div>

	<div class="mid-banner row">
		
	</div>

</div>

<div class="spacer">

</div>

<div id="slide-down" class="slide-down">
	<div id="email-display-holder">
		<?php if($company_active && !$company_expired){ ?>
		<?php $this->add(new View('../widgets/dashboard/email_display.php', array('company_id' => $company_id))); ?>
		<?php } else { ?>
		<br /><p><?php _e("You must activate your account to view the email list."); ?> <div id="email-close" class="slide-down-button"><?php _e("Close"); ?></div></p>
		<?php } ?>
	</div>
</div>

<?php
$data_ratingPercentOther = round((new Data())->company($company_id)->keyword($keywords)->getAvg()->getRating(), 1);

$query = Database::query("SELECT `stores`.`id`, `stores`.`Name` FROM `stores` LEFT JOIN `dashboard` ON `dashboard`.StoreID = `stores`.id WHERE `stores`.CompanyID = {$company_id} GROUP BY `stores`.`id` ORDER BY `stores`.`Name` ASC");
?>

<!-- Left side -->
<div class='aspect-area hidden-xs hidden-sm col-md-3 right-bar'>
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

<!-- Right Side -->
<div class='aspect-area col-md-9'>
	<div class='row'>
	<div class='col-sm-12 area-title'><i class='fa fa-comments'></i> <?php _e('Feedback'); ?></div>
	<?php if(isset($_GET['thanks'])){ ?>
	<div class="message-container">
		<div class='close'><i class='fa fa-times'></i></div>
		<div class="message"><?php _e('Thank you for your purchase and welcome to Brevada!'); ?></div>
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
		$title = $row['Name'];
		$storeID = $row['id'];
		if(empty($title)){ continue; }
		
		$data = (new Data())->store($storeID)->getAvg();
		
		$data_ratingPercent = round($data->getRating(), 1);
		
		$total_responses = $data->getSize();

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
	?>
		<div class="col-sm-6 col-md-6 col-lg-4 pod-holder">
			<div class="pod">
				<div class="body">
					<div class="header">
						<span class='aspect-title link' data-link='dashboard?s=<?php echo $storeID; ?>'><?php _e($title); ?></span>
					</div>
					<div class="pull-right col-md-6 pod-body-right">
						<div class='pod-body-rating <?php echo $colour; ?>-text'><?php echo "{$data_ratingPercent}%"; ?></div>
						<div class="rating-text"><?php _e('in'); ?> <?php echo $total_responses; ?> <?php _e('responses'); ?>.</div>
						<div class='pod-body-rating external'><?php echo "{$data_ratingPercentOther}%"; ?></div>
						<div class="rating-text external"><?php _e('corporate average'); ?>.</div>
					</div>
					
					<div class="col-md-12 pod-body-bottom">
						<div class='graphs'>
						<div class='left-graph graph <?php echo $colour; ?>' data-percent='<?php echo $data_ratingPercent; ?>'>
							<div class='percent'><?php echo "{$data_ratingPercent}%"; ?></div>
						</div>
						<div class='right-graph graph' data-percent='<?php echo $data_ratingPercentOther; ?>' data-tooltip='<?php _e('Corporate Benchmark'); ?> (<?php echo "{$data_ratingPercentOther}%"; ?>)'></div>
						</div>
					</div>
				</div>
			</div>
		</div>



		<div class="col-sm-3 col-md-3 col-lg-3" style="display: none;">	
			<div class='aspect-container'>
				<span class='aspect-title <?php echo $colour; ?>'><?php _e($title); ?></span>
				<div class='graphs'>
					<div class='left-graph graph' data-percent='<?php echo $data_ratingPercent; ?>'>
						<div class='percent'><?php echo "{$data_ratingPercent}%"; ?></div>
					</div>
					<div class='right-graph graph' data-percent='<?php echo $data_ratingPercentOther; ?>' data-tooltip='<?php _e('Corporate Benchmark'); ?> (<?php echo "{$data_ratingPercentOther}%"; ?>)'></div>
				</div>
				<div class='graph-info'>
					<div class='left-block pull-left'>
						<!-- SWITCH TO NUM RATINGS -->
						<span class='fraction numerator'><?php echo $total_responses; ?></span>
						<span class='fraction denominator'><?php _e('Responses'); ?></span>
					</div>
				</div>
			</div>
		</div>
		<?php } $query->close(); } ?>
	</div>
</div>

<script type='text/javascript'>
$('.graph').each(function(){
	var percent = $(this).attr('data-percent');
	var original = $(this).height();
	var target = (parseFloat(percent)/100)*($(this).parent().height() - original);
	$(this).animate({ height : Math.min(Math.floor(original+target), $(this).parent().height()) }, 1500);
});
</script>