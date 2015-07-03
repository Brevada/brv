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
	- If trial account.
	- Referral credits / credits.
	- Payment redirect.
	- Voting pages / feedback page.
	- Corporate.
*/

$user_id = Brevada::validate($_SESSION['user_id'], VALIDATE_DATABASE);

$query = Database::query("SELECT users.`name`, users.`url_name`, users.`corporate`, users.`level`, users.`trial`, UNIX_TIMESTAMP(users.`expiry_date`) as `Expiry`, codes.`code` as `ReferralCode` FROM users LEFT JOIN `codes` ON `codes`.referral_user = {$user_id} WHERE users.id = {$user_id} LIMIT 1");

while($row = $query->fetch_assoc()){
	if(!isset($row['Expiry']) || $row['Expiry'] < time()){
		/* Membership expired. */
	}
	
	/* Does this make sense? What level is a paid member at? Member who hasn't paid? */
	$level = $row['level'];
	if($level != '0' && $level != '1'){
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
			<img class='logo-quote link pull-left' src='/images/quote-white.png' data-link='' />
			<div class='dropdown pull-right'>
				<div class='three-lines btn btn-default dropdown-toggle'  data-toggle='dropdown'>
					<i class='fa fa-ellipsis-h'></i>
				</div>
				<ul class='dropdown-menu'>
					<li class='link' data-link='account'>Account Settings</li>
					<li class='link' data-link='logout'>Logout</li>
				</ul>
			</div>
			<a href="/<?php echo $url_name; ?>" target="_TOP"><div class='pull-right icon-button'><i class='fa fa-external-link'></i></div></a>
			<a href="hub/includes/marketing/promo_white.php" target="_BLANK"><div class='pull-right icon-button'><i class='fa fa-print'></i></div></a>
			<a href="/user_data/qr/<?php echo $user_id; ?>.png" target="_TOP"><div class='pull-right icon-button'><i class='fa fa-qrcode'></i></div></a>

		</div>
	</div>

	<div class='top-area row hidden-xs'>
		
		<div class="col-sm-4">
			<?php 
				if($data_overallAll>=50){
					$change = 'positive';
					$icon = 'fa-check-circle';
				} else if ($data_overallAll==0){
					$change = 'neutral';
					$icon = 'fa-minus-circle';
				} else {
					$change = 'negative';
					$icon = 'fa-times-circle';
				}
			?>
			<div class='overall-decrease block <?php echo $change; ?>'>
				<div class="block-left hidden-md hidden-sm">
					<i class='fa <?php echo $icon; ?>'></i>
				</div>
				<div class="block-right">
					<div class='big-number <?php echo $change; ?>'>
						<?php echo abs($data_overallAll)."%"; ?>
					</div>
				</div>
				<div class="block-bottom">
					All Time Average
				</div>
			</div>
		</div>

		<div class="col-sm-4">
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
			<div class='overall-improvement block <?php echo $change; ?>'>
				<div class="block-left hidden-md hidden-sm">
					<i class='fa <?php echo $icon; ?>'></i>
				</div>
				<div class="block-right">
					<div class='big-number <?php echo $change; ?>'>
						<?php echo abs($data_overall4W)."%"; ?>
					</div>
				</div>
				<div class="block-bottom">
					Change in the Past 4 Weeks
				</div>
			</div>
		</div>

		<div class="col-sm-4">
			<?php 
				if($data_relativeBenchmark>=1){
					$icon = 'fa-chevron-circle-up';
					$message = 'above the benchmark';
				} else if ($data_relativeBenchmark==0){
					$icon = 'fa-minus-circle';
					$message = ', same as benchmark average';
				} else {
					$icon = 'fa-chevron-circle-down';
					$message = 'below the benchmark';
				}
			?>
			<div class='below-benchmark block <?php echo numericalCSS($data_relativeBenchmark); ?>'>
				<div class="block-left hidden-md hidden-sm">
					<i class='fa <?php echo $icon; ?>'></i>
				</div>
				<div class="block-right">
					<div class='big-number <?php echo numericalCSS($data_relativeBenchmark); ?>'>
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
		</div>
	</div>

</div>

<div class="spacer hidden-xs">

</div>


<div class="spacer spacer-thin visible-xs">

</div>

<?php
	$query = Database::query("SELECT aspect_type.Title, aspects.Data_LastUpdate, aspects.Data_RatingPercent, aspects.Data_RatingPercentOther, aspects.Data_Percent4W, aspects.Data_Percent6M, aspects.Data_Percent1Y, (SELECT COUNT(*) FROM aspects as subaspects WHERE subaspects.Data_RatingPercent > aspects.Data_RatingPercent AND subaspects.`Active` = 1 AND subaspects.`OwnerID` = {$user_id} AND aspect_type.Title <> '') as Position FROM aspects LEFT JOIN aspect_type ON aspect_type.ID = aspects.AspectTypeID WHERE aspects.OwnerID = {$user_id} AND `Active` = 1 AND aspect_type.Title <> ''");
?>

<!-- Left side -->
<div class='aspect-area hidden-xs hidden-sm col-md-3 right-bar'>
	<div class='row'>
		<div class='col-sm-12 area-title'><i class='fa fa-area-chart'></i> Consultant</div>
		<div class="col-sm-12 hidden-xs">		
			
			<div class='col-sm-12 block consultant negative'>
			<span class='title'>Where to Focus</span>
			<div class=''>
				<?php
				foreach($areasOfFocus as $aspect){
					echo "<span class='aspect-title pull-left'>{$aspect}</span>";
				}
				if(empty($areasOfFocus)){
					echo "<span class='aspect-title placeholder'></span>";
				}
				?>
			</div>
			<br class="clear: both;" />
			</div>


			<div class='col-sm-12 block consultant positive'>
				<span class='title'>Areas of Least Concern</span>
				<div class=''>
					<?php
					foreach($areasOfLeastConcern as $aspect){
						echo "<span class='aspect-title pull-left'>{$aspect}</span>";
					}
					if(empty($areasOfLeastConcern)){
						echo "<span class='aspect-title placeholder'></span>";
					}
					?>
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
	<?php
	while($query !== false && $row = $query->fetch_assoc()){
		$title = $row['Title'];
		
		/* Adding '0' forces float(0) rather than float(-0). */
		
		$data_ratingPercent = round((float) $row['Data_RatingPercent'] + 0, 1);
		$data_ratingPercentOther = round((float) $row['Data_RatingPercentOther'] + 0);
		$data_percent4W = round((float) $row['Data_Percent4W'] + 0, 1);
		$data_percent1Y = round((float) $row['Data_Percent1Y'] + 0, 1);
		
		$order_num = ((int) $row['Position']) + 1;
		$order_denom = $query->num_rows;

		if($data_ratingPercent>=75) {
			$colour = 'positive';
		} else if ($data_ratingPercent<75 && $data_ratingPercent>=50) {
			$colour = 'neutral';
		} else {
			$colour = 'negative';
		}

	?>
		<div class="col-sm-3 col-md-3 col-lg-2">	
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
						<span class='fraction numerator'><?php echo $order_num; ?></span>
						<span class='fraction denominator'>Ratings<!-- Out of <?php echo $order_denom; ?> --></span>
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
		<?php } $query->close(); ?>
		<!-- numofFillers = numPerRow-1 -->
		<div class='aspect-container aspect-filler'></div>
		<div class='aspect-container aspect-filler'></div>
		<div class='aspect-container aspect-filler'></div>
	</div>
</div>



<div class="bottom-bar">
	&copy; 2015 Brevada Inc. &nbsp;
</div>

<script>
$(window).on('scroll',function(){
	var scroll = $(window).scrollTop();
	if(scroll<=20){
		$('.top-fixed').removeClass('scrolled');
	} else {
		$('.top-fixed').addClass('scrolled');
	}
});
</script>


