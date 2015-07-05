<?php
$this->addResource('/css/layout.css');
$this->addResource('/css/dashboard.css');
$this->addResource('/js/dashboard.js');

$this->addResource('/css/brevada.tooltip.css');
$this->addResource('/js/brevada.tooltip.js');

if(!Brevada::IsLoggedIn()){
	Brevada::Redirect('/home/logout');
}

/*
	TODO:
	- Referral credits / credits.
	- Corporate.
*/

$user_id = Brevada::validate($_SESSION['user_id'], VALIDATE_DATABASE);

$query = Database::query("SELECT users.`name`, users.`url_name`, users.`corporate`, users.`level`, users.`active`, users.`trial`, UNIX_TIMESTAMP(users.`expiry_date`) as `Expiry`, codes.`code` as `ReferralCode` FROM users LEFT JOIN `codes` ON `codes`.referral_user = {$user_id} WHERE users.id = {$user_id} LIMIT 1");

while($row = $query->fetch_assoc()){
	if(!isset($row['Expiry']) || $row['Expiry'] < time()){
		/* Membership expired. */
	}
	
	/* Does this make sense? What level is a paid member at? Member who hasn't paid? */
	$level = $row['level'];
	$trial = $row['trial'];
	$active = $row['active'];
	if($level != '0' && $level != '1' && $trial != '1' && $active != 'yes'){
		Brevada::Redirect('/hub/payment/payment.php');
	}
	
	/* TODO: Corporate accounts need to be fixed up anyway. */
	$corporate = $row['corporate'];
	if($corporate == '1'){
		$_SESSION['corporate'] = 'active';
		$_SESSION['corporate_id'] = $user_id;
		Brevada::Redirect('/corporate/hub/corporate.php');
	}
	
	$name = $row['name'];
	$this->setTitle("Brevada Dashboard - {$name}");
	
	$referral_code = empty($row['ReferralCode']) ? '' : $row['ReferralCode'];
	
	$trial = $row['trial'];
	$url_name = $row['url_name'];
}

function numericalCSS($i){
	return $i >= 0 ? 'positive' : 'negative';
}

$data_overall4W = 0;
$data_overallAll = 0;
$data_relativeBenchmark = 0;

if(($query = Database::prepare("SELECT dashboard.Data_Overall4W, dashboard.Data_OverallAll, dashboard.Data_RelativeBenchmark FROM `dashboard` WHERE dashboard.OwnerID = ? LIMIT 1")) !== false){
	$query->bind_param('i', $user_id);
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

$query = Database::query("SELECT aspect_type.Title FROM aspects LEFT JOIN aspect_type ON aspects.AspectTypeID = aspect_type.ID WHERE aspect_type.Title IS NOT NULL AND aspects.OwnerID = {$user_id} AND aspects.`Active` = 1 ORDER BY aspects.Data_AttentionScore DESC LIMIT 2");
if($query !== false){
	while($row = $query->fetch_assoc()){
		$areasOfFocus[] = $row['Title'];
	}
	$query->close();
}

$query = Database::query("SELECT aspect_type.Title FROM aspects LEFT JOIN aspect_type ON aspects.AspectTypeID = aspect_type.ID WHERE aspect_type.Title IS NOT NULL AND aspects.OwnerID = {$user_id} AND aspects.`Active` = 1 ORDER BY aspects.Data_AttentionScore ASC LIMIT 2");
if($query !== false){
	while($row = $query->fetch_assoc()){
		$areasOfLeastConcern[] = $row['Title'];
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
					<li class='link' data-link='account'>Account Settings</li>
					<li class='link' data-link='logout' style="border-bottom: none;">Logout</li>
				</ul>
			</div>
			<div class='name pull-right'>
				Current User: <span class="variable"><?php echo $name; ?></span>
			</div>
		</div>
	</div>

	<div class="mid-banner row">
			<a href="/<?php echo $url_name; ?>" target="_TOP">
				<div class='pull-left icon-button'>
					<i class='fa fa-external-link'></i>
					<div class='icon-subtext'>Your Page</div>
				</div>
			</a>
			<a href="hub/includes/marketing/promo_white.php" target="_TOP">
				<div class='pull-left icon-button'>
					<i class='fa fa-print'></i>
					<div class='icon-subtext'>Printables</div>
				</div>
			</a>
			<a href="/user_data/qr/<?php echo $user_id; ?>.png" target="_TOP">
				<div class='pull-left icon-button'>
					<i class='fa fa-qrcode'></i>
					<div class='icon-subtext'>QR Code</div>
				</div>
			</a>
	</div>

</div>

<div class="spacer">

</div>

<?php
	$query = Database::query("SELECT aspect_type.Title, aspects.Data_LastUpdate, aspects.Data_RatingPercent, aspects.Data_RatingPercentOther, aspects.Data_Percent4W, aspects.Data_Percent6M, aspects.Data_Percent1Y, (SELECT COUNT(*) FROM feedback WHERE feedback.Rating > -1 AND feedback.AspectID = aspects.ID) as Total FROM aspects LEFT JOIN aspect_type ON aspect_type.ID = aspects.AspectTypeID WHERE aspects.OwnerID = {$user_id} AND `Active` = 1 AND aspect_type.Title <> ''");
?>

<!-- Left side -->
<div class='aspect-area hidden-xs hidden-sm col-md-3 right-bar'>
	<div class='row'>
		<div class='col-sm-12 area-title'><i class='fa fa-area-chart'></i> Consultant</div>
		<div class="col-sm-12 hidden-xs">		

			<div class='col-sm-12 area-title'><i class='fa fa-area-chart'></i> Consultant</div>

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
					Overall Score
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
					Change in the Past 4 Weeks
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
						echo "$message"; 
					?>
				</div>
			</div>

			<div class='col-sm-12 block consultant'>
				<div class='title'>Areas For Improvement</div>
					<div class='body'>
						<?php
						foreach($areasOfFocus as $aspect){
							echo "<span class='aspect-title pull-left negative'>{$aspect}</span>";
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
				<div class='title'>Strengths</div>
				<div class='body'>
					<?php
					foreach($areasOfLeastConcern as $aspect){
						echo "<span class='aspect-title pull-left positive'>{$aspect}</span>";
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
	<div class='col-sm-12 area-title'><i class='fa fa-comments'></i> Feedback</div>
	
	<?php if ($level < 2) { ?>
	<br style="clear:both;" />
	<div class="upgrade">
		<div class="message">Welcome to Brevada! Please <b>upgrade your account</b> to view your feedback.</div>
		<a class="btn btn-primary btn-pay" href="upgrade.php">Upgrade</a>
		<a class="btn btn-default" target="_TOP" href="<?php echo $url_name; ?>">Checkout Your Page</a>
		<div class="sub-message">Or feel free to contact us at customercare@brevada.com for any help.</div>
	</div>

	<?php
	} else {
	while($query !== false && $row = $query->fetch_assoc()){
		$title = $row['Title'];
		
		/* Adding '0' forces float(0) rather than float(-0). */
		
		$data_ratingPercent = round((float) $row['Data_RatingPercent'] + 0, 1);
		$data_ratingPercentOther = round((float) $row['Data_RatingPercentOther'] + 0);
		$data_percent4W = round((float) $row['Data_Percent4W'] + 0, 0);
		$data_percent1Y = round((float) $row['Data_Percent1Y'] + 0, 0);
		
		$total_responses = ((int) $row['Total']);

		if($data_ratingPercent>=80) {
			$colour = 'positive';
		} else if ($data_ratingPercent<80 && $data_ratingPercent>=60) {
			$colour = 'great';
		} else if ($data_ratingPercent<60 && $data_ratingPercent>=40) {
			$colour = 'neutral';
		} else if ($data_ratingPercent<40 && $data_ratingPercent>=20) {
			$colour = 'bad';
		} else {
			$colour = 'negative';
		}
	?>
		<div class="col-sm-6 col-md-6 col-lg-4 pod-holder">
			<div class="pod">
				<div class="header">
					<span class='aspect-title <?php echo $colour; ?>'><?php echo $title; ?></span>
				</div>
				<div class="body">
					<div class="pull-left col-md-6 pod-body-left">
						<div class='top'>
							<i class='pull-left fa <?php echo $data_percent4W >= 0 ? 'fa-arrow-circle-up' : 'fa-arrow-circle-down'; ?>'></i>
							<span class='pull-left percent'><?php echo abs($data_percent4W)."%"; ?></span>
							<span class='duration'>4 weeks</span>
						</div>
						<div class='top'>
							<i class='pull-left fa <?php echo $data_percent1Y >= 0 ? 'fa-arrow-circle-up' : 'fa-arrow-circle-down'; ?>'></i>
							<span class='pull-left percent'><?php echo abs($data_percent1Y)."%"; ?></span>
							<span class='duration'>1 year</span>
						</div>
					</div>
					<div class="pull-right col-md-6 pod-body-right">
						<div class='pod-body-rating <?php echo $colour; ?>-text'><?php echo "{$data_ratingPercent}%"; ?></div>
						<div class="rating-text">in <?php echo $total_responses; ?> responses.</div>
						<div class='pod-body-rating external'><?php echo "{$data_ratingPercentOther}%"; ?></div>
						<div class="rating-text external">industry average.</div>
					</div>
					<div class="col-md-12 pod-body-bottom">
						<div class='graphs'>
						<div class='left-graph graph <?php echo $colour; ?>' data-percent='<?php echo $data_ratingPercent; ?>'>
							<div class='percent'><?php echo "{$data_ratingPercent}%"; ?></div>
						</div>
						<div class='right-graph graph' data-percent='<?php echo $data_ratingPercentOther; ?>' data-tooltip='Market Benchmark (<?php echo "{$data_ratingPercentOther}%"; ?>)'></div>
						</div>
					</div>
					<br style="clear: both;" />
				</div>
			</div>
		</div>



		<div class="col-sm-3 col-md-3 col-lg-3" style="display: none;">	
			<div class='aspect-container'>
				<span class='aspect-title <?php echo $colour; ?>'><?php echo $title; ?></span>
				<div class='graphs'>
					<div class='left-graph graph' data-percent='<?php echo $data_ratingPercent; ?>'>
						<div class='percent'><?php echo "{$data_ratingPercent}%"; ?></div>
					</div>
					<div class='right-graph graph' data-percent='<?php echo $data_ratingPercentOther; ?>' data-tooltip='Market Benchmark (<?php echo "{$data_ratingPercentOther}%"; ?>)'></div>
				</div>
				<div class='graph-info'>
					<div class='left-block pull-left'>
						<!-- SWITCH TO NUM RATINGS -->
						<span class='fraction numerator'><?php echo $total_responses; ?></span>
						<span class='fraction denominator'>Responses</span>
					</div>
					<div class='right-block pull-right'>
						<div class='top'>
							<i class='fa <?php echo $data_percent4W >= 0 ? 'fa-sort-asc' : 'fa-sort-desc'; ?> <?php echo numericalCSS($data_percent4W); ?>-text'></i>
							<span class='percent'><?php echo abs($data_percent4W)."%"; ?></span>
							<span class='duration'>(4w)</span>
						</div>
						<div class='bottom'>
							<i class='fa <?php echo $data_percent1Y >= 0 ? 'fa-sort-asc' : 'fa-sort-desc'; ?> <?php echo numericalCSS($data_percent1Y); ?>-text'></i>
							<span class='percent'><?php echo abs($data_percent1Y)."%"; ?></span>
							<span class='duration'>(1y)</span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php } $query->close(); } ?>
	</div>
</div>



<div class="bottom-bar">
	&copy; 2015 Brevada Inc. &nbsp;
</div>

