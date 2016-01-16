<?php
if(!Brevada::IsLoggedIn()){ Brevada::Redirect('/home/logout'); }
if(!Permissions::has(Permissions::VIEW_ADMIN)){ Brevada::Redirect('/404'); }

$storeName = empty($_GET['storeID']) || is_numeric($_GET['storeID']) ? false : trim($_GET['storeID']);
$storeID = empty($_GET['storeID']) ? false : @intval($_GET['storeID']);

if($storeID !== false && $storeName === false){
	if(($stmt = Database::prepare("SELECT `Name`, `id` FROM `stores` WHERE `stores`.`id` = ? LIMIT 1")) !== false){
		$stmt->bind_param('i', $storeID);
		if($stmt->execute()){
			$stmt->bind_result($storeName, $storeID);
			$stmt->fetch();
		}
		$stmt->close();
	}
} else if($storeName !== false){
	$storeNameLike = "%{$storeName}%";
	if(($stmt = Database::prepare("
		SELECT `Name`, `id` FROM ((SELECT `Name`, `id` FROM `stores` WHERE `stores`.`Name` = ?)
		UNION
		(SELECT `Name`, `id` FROM `stores` WHERE `stores`.`Name` LIKE ?)) S LIMIT 1")) !== false){
		$stmt->bind_param('ss', $storeName, $storeNameLike);
		if($stmt->execute()){
			$stmt->bind_result($storeName, $storeID);
			$stmt->fetch();
		}
		$stmt->close();
	}
}
?>
<h1 class="page-header">Data Analysis</h1>

<form method='get' action='/admin'>
	<input type='hidden' name='show' value='data' />
	<div class='col-lg-6 col-md-6 input-group'>
		<input type='text' class='form-control' name='storeID' placeholder='Enter the store ID or name...' value='<?php echo empty($storeName) ? '' : $storeName; ?>' /> 
		<span class='input-group-btn'><button type='submit' class='btn btn-default'>Analyze</button></span>
	</div>
</form>

<h2 class="sub-header"><?php echo $storeID && $storeName ? "#{$storeID}: {$storeName}" : ''; ?></h2>

<?php if(!$storeID){ ?>
	<p>No store is selected.</p>
<?php } else { ?>
<div class='row'>
<div class='well well-sm'>The past 60 days of data.</div>
<?php
if(($stmt = Database::prepare("SELECT `aspects`.`id` as `id`, `AspectTypeID`, `Title` FROM `aspects` JOIN `aspect_type` ON `aspect_type`.`id` = `aspects`.`AspectTypeID` WHERE `Active` = 1 AND `StoreID` = ? ORDER BY `Title`")) !== false){
	$stmt->bind_param('i', $storeID);
	if($stmt->execute()){
		$stmt->bind_result($id, $aspectTypeID, $title);
		while($stmt->fetch()){
			$rows[] = ['id' => $id, 'AspectTypeID' => $aspectTypeID, 'Title' => $title];
		}
		foreach($rows as $row){
			$id = $row['id']; $aspectTypeID = $row['AspectTypeID'];
			$title = $row['Title'];
			
			$data = (new Data())->store(295)->aspectType($aspectTypeID)->from(time() - (60*24*3600));
			$result = $data->getAvg(5);
			
			$labelArray = [];
			$dataArray = [];
			
			$sum = 0;
			
			for($i = 0; $i < 5; $i++){
				$dataArray[] = $result->getRating($i) ? $result->getRating($i) : 0;
				$labelArray[] = "'".date('M jS', $result->getUTC($i))." (".$result->getSize($i).")'";
				$sum += $result->getSize($i);
			}
			
			$labelArray = implode(',', $labelArray);
			$dataArray = implode(',', $dataArray);
			?>
			<div class='col-lg-3 col-md-6 col-sm-12'>
				<div class='panel panel-default'>
					<div class='panel-heading'>
						<?php echo $title; ?>
						<span style='float: right;'><?php echo $data->getAvg(); ?>%</span>
					</div>
					<div class='panel-body aspect-chart'>
						<?php if($sum > 0){ ?>
						<div class='chart-container'>
						<canvas id='aspect-<?php echo $id; ?>' class='aspect-chart'></canvas>
						</div>
						<script type='text/javascript'>
							new Chart(document.getElementById("aspect-<?php echo $id; ?>")
							.getContext('2d'), {
								type: 'bar',
								data: {
									labels: [<?php echo $labelArray; ?>],
									datasets: [
										{
											label: "<?php echo $title; ?>",
											fill: true,
											backgroundColor: "rgba(255,43,43,1)",
											borderColor: "rgba(220,220,220,1)",
											pointBackgroundColor: "rgba(220,220,220,1)",
											pointBorderColor: "#fff",
											pointHoverBackgroundColor: "#fff",
											pointHoverBorderColor: "rgba(220,220,220,1)",
											borderWidth: 0.5,
											data: [<?php echo $dataArray; ?>]
										}
									]
								},
								options: {
									responsive : true, maintainAspectRatio : false,
									scales : {
										xAxes: [{
											ticks : {
												autoSkip: false
											}
										}],
										yAxes: [{
											ticks : {
												beginAtZero: true,
												min: 0,
												max: 100,
											}
										}]
									},
									legend : {
										display: false
									},
									title : {
										display: false
									}
								}
							});
						</script>
						<?php } else { ?>
						<i class='fa fa-ban'></i>
						<p>No data available.</p>
						<?php } ?>
					</div>
				</div>
			</div>
			<?php
		}
	}
	$stmt->close();
}
?>
</div>
<?php } ?>