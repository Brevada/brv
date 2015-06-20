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

$query = Database::query("SELECT users.`name`, users.`corporate`, users.`level`, users.`trial`, UNIX_TIMESTAMP(users.`expiry_date`) as `Expiry`, codes.`code` as `ReferralCode` FROM users LEFT JOIN `codes` ON `codes`.referral_user = {$user_id} WHERE users.id = {$user_id} LIMIT 1");

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
<div class='top-banner'>
	<img class='logo-quote link' src='/images/quote.png' data-link='' />
	<div class='dropdown-menu noselect'>
		<div class='three-lines'>
			<i class='fa fa-ellipsis-h'></i>
		</div>
		<ul>
			<li class='link' data-link='account'>Account Settings</li>
			<li class='link' data-link='logout'>Logout</li>
		</ul>
	</div>
</div>

<div class='spacer'></div>

<div class='top-area'>
	<div class='overall-improvement block'>
		<span class='big-number <?php echo $data_overall4W > 0 ?  'positive' : 'negative'; ?>'><?php echo abs($data_overall4W)."%"; ?></span>
		<span class='title'>Overall <?php echo $data_overall4W > 0 ? 'Improvement' : ($data_overall4W == 0 ? 'No Change' : 'Decrease'); ?></span>
		<span class='subtitle'>Past 4 Weeks</span>
	</div>
	<div class='overall-decrease block'>
		<span class='big-number <?php echo $data_overallAll >= 50 ?  'positive' : 'negative'; ?>'><?php echo abs($data_overallAll)."%"; ?></span>
		<span class='title'><?php echo $data_overallAll >= 50 ?  'Doing Well' : 'Needs Improvement'; ?></span>
		<span class='subtitle'>All Time</span>
	</div>
	<div class='below-benchmark block'>
		<span class='big-number <?php echo numericalCSS($data_relativeBenchmark); ?>'><?php echo abs($data_relativeBenchmark)."%"; ?></span>
		<span class='title'><?php echo $data_relativeBenchmark >= 0 ? 'Above' : 'Below'; ?> Benchmark</span>
		<span class='subtitle'>Past 4 Weeks</span>
	</div>
	<div class='attention-area block'>
		<div class='top'>
			<span class='title'>Suggested Areas of Focus</span>
			<div class='aspects'>
				<?php
				foreach($areasOfFocus as $aspect){
					echo "<span class='aspect-title'>{$aspect}</span>";
				}
				if(empty($areasOfFocus)){
					echo "<span class='aspect-title placeholder'></span>";
				}
				?>
			</div>
		</div>
		<div class='bottom'>
			<span class='title'>Areas of Least Concern</span>
			<div class='aspects'>
				<?php
				foreach($areasOfLeastConcern as $aspect){
					echo "<span class='aspect-title'>{$aspect}</span>";
				}
				if(empty($areasOfLeastConcern)){
					echo "<span class='aspect-title placeholder'></span>";
				}
				?>
			</div>
		</div>
	</div>
</div>

<?php
$query = Database::query("SELECT aspect_type.Title, aspects.Data_LastUpdate, aspects.Data_RatingPercent, aspects.Data_RatingPercentOther, aspects.Data_Percent4W, aspects.Data_Percent6M, aspects.Data_Percent1Y, (SELECT COUNT(*) FROM aspects as subaspects WHERE subaspects.Data_RatingPercent > aspects.Data_RatingPercent AND subaspects.`Active` = 1 AND subaspects.`OwnerID` = {$user_id} AND aspect_type.Title <> '') as Position FROM aspects LEFT JOIN aspect_type ON aspect_type.ID = aspects.AspectTypeID WHERE aspects.OwnerID = {$user_id} AND `Active` = 1 AND aspect_type.Title <> ''");
?>

<div class='aspect-area'>
	<div class='row'>
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
	?>
		<div class='aspect-container'>
			<div class='graphs'>
				<div class='left-graph graph' data-percent='<?php echo $data_ratingPercent; ?>'>
					<div class='percent'><?php echo "{$data_ratingPercent}%"; ?></div>
				</div>
				<div class='right-graph graph' data-percent='<?php echo $data_ratingPercentOther; ?>' data-tooltip='Market Benchmark (<?php echo "{$data_ratingPercentOther}%"; ?>)'></div>
			</div>
			<div class='graph-info'>
				<div class='left-block'>
					<span class='fraction numerator'><?php echo $order_num; ?></span>
					<span class='fraction denominator'>Out of <?php echo $order_denom; ?></span>
				</div>
				<div class='right-block'>
					<div class='top'>
						<i class='fa <?php echo $data_percent4W >= 0 ? 'fa-sort-asc' : 'fa-sort-desc'; ?> <?php echo numericalCSS($data_percent4W); ?>'></i>
						<span class='percent'><?php echo abs($data_percent4W)."%"; ?></span>
						<span class='duration'>(4w)</span>
					</div>
					<div class='bottom'>
						<i class='fa <?php echo $data_percent1Y >= 0 ? 'fa-sort-asc' : 'fa-sort-desc'; ?> <?php echo numericalCSS($data_percent1Y); ?>'></i>
						<span class='percent'><?php echo abs($data_percent1Y)."%"; ?></span>
						<span class='duration'>(1y)</span>
					</div>
				</div>
			</div>
			<span class='aspect-title'><?php echo $title; ?></span>
		</div>
		<?php } $query->close(); ?>
		<!-- numofFillers = numPerRow-1 -->
		<div class='aspect-container aspect-filler'></div>
		<div class='aspect-container aspect-filler'></div>
		<div class='aspect-container aspect-filler'></div>
	</div>
</div>