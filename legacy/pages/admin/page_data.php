<?php
if(!Brevada::IsLoggedIn()){ Brevada::Redirect('/home/logout'); }
if(!Permissions::has(Permissions::VIEW_ADMIN)){ Brevada::Redirect('/404'); }

$storeName = empty($_GET['storeID']) || is_numeric($_GET['storeID']) ? false : trim($_GET['storeID']);
$storeID = empty($_GET['storeID']) ? false : @intval($_GET['storeID']);
$companyID = -1;

if($storeID !== false && $storeName === false){
	if(($stmt = Database::prepare("SELECT `Name`, `id`, `CompanyID` FROM `stores` WHERE `stores`.`id` = ? LIMIT 1")) !== false){
		$stmt->bind_param('i', $storeID);
		if($stmt->execute()){
			$stmt->bind_result($storeName, $storeID, $companyID);
			$stmt->fetch();
		}
		$stmt->close();
	}
} else if($storeName !== false){
	$storeNameLike = "%{$storeName}%";
	if(($stmt = Database::prepare("
		SELECT `Name`, `id`, `CompanyID` FROM ((SELECT `Name`, `id`, `CompanyID` FROM `stores` WHERE `stores`.`Name` = ?)
		UNION
		(SELECT `Name`, `id`, `CompanyID` FROM `stores` WHERE `stores`.`Name` LIKE ?)) S LIMIT 1")) !== false){
		$stmt->bind_param('ss', $storeName, $storeNameLike);
		if($stmt->execute()){
			$stmt->bind_result($storeName, $storeID, $companyID);
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
<?php
$keywords = [];
if(($stmt = Database::prepare("
	SELECT company_keywords_link.CompanyKeywordID
	FROM company_keywords_link
	WHERE
	company_keywords_link.`CompanyID` = ?")) !== false){
	$stmt->bind_param('i', $companyID);
	if($stmt->execute()){
		$stmt->bind_result($keywordID);
		while($stmt->fetch()){
			$keywords = @intval($keywordID);
		}
	}
	$stmt->close();
}

$rows = [];
if(($stmt = Database::prepare("SELECT `aspects`.`id` as `id`, `AspectTypeID`, `Title` FROM `aspects` JOIN `aspect_type` ON `aspect_type`.`id` = `aspects`.`AspectTypeID` WHERE `Active` = 1 AND `StoreID` = ? ORDER BY `Title`")) !== false){
	$stmt->bind_param('i', $storeID);
	if($stmt->execute()){
		$stmt->bind_result($id, $aspectTypeID, $title);
		while($stmt->fetch()){
			$rows[] = ['id' => $id, 'AspectTypeID' => $aspectTypeID, 'Title' => $title];
		}
	}
}
?>
<div class='row'>
<div class='well well-sm'>Breakdown by Aspects</div>
	<table class='table table-striped tablesorter-default'>
		<thead>
			<th>Aspect</th>
			<th>Responses</th>
			<th>Rating</th>
		</thead>
		<tbody>
		<?php
		foreach($rows as $row){
			$id = $row['id'];
			$aspectTypeID = $row['AspectTypeID'];
			$title = $row['Title'];
			
			$data = (new Data())->store($storeID)->aspectType($aspectTypeID);
			$result = $data->getAvg();
			
			echo "<tr>";
			echo "<td>{$title}</td>";
			echo "<td>".$result->getSize()."</td>";
			echo "<td>".$result->getRating()."%</td>";
			echo "</tr>";
		}
		?>
		</tbody>
	</table>
	<?php
	$cnt_three = 0; $cnt_multiple = 0; $cnt_jan18 = 0;
	if(($stmt = Database::prepare("SELECT COUNT(*) as cnt_a, (SELECT COUNT(DISTINCT f.`SessionCode`)
		FROM `feedback` f
		JOIN `aspects` asp ON asp.`id` = f.`AspectID`
		WHERE asp.Active = 1 AND asp.`StoreID` = ? AND EXISTS (SELECT COUNT(`SessionCode`) as sc FROM `feedback` WHERE `SessionCode` = f.`SessionCode` HAVING sc > 1 )) as cnt_multi, (SELECT COUNT(DISTINCT g.`SessionCode`)
		FROM `feedback` g
		JOIN `aspects` aspp ON aspp.`id` = g.`AspectID`
		WHERE aspp.Active = 1 AND aspp.`StoreID` = ? AND UNIX_TIMESTAMP(g.`Date`) >= 1453093200) as cnt_session
		FROM (SELECT DISTINCT
			(180*round(UNIX_TIMESTAMP(`Date`)/180)) as `DateRange`,
			`IPAddress`
		FROM `feedback`
		JOIN `aspects` ON `aspects`.`id` = `feedback`.`AspectID`
		WHERE `aspects`.Active = 1 AND `aspects`.`StoreID` = ?
		GROUP BY `IPAddress`, `DateRange`) a")) !== false){
		$stmt->bind_param('iii', $storeID, $storeID, $storeID);
		if($stmt->execute()){
			$stmt->bind_result($cnt_three, $cnt_multiple, $cnt_jan18);
			$stmt->fetch();
		}
		$stmt->close();
	}
	?>
	<div class='well well-sm'>
	Due to bad data from earlier versions of the tablet software, an exact number of customers is impossible to ascertain. Instead, we can approximate the number three different ways.
		<ul>
			<li><b><?= $cnt_three; ?></b> - Customers with at least 3 minutes between responses.</li>
			<li><b><?= $cnt_multiple; ?></b> - Customers who submitted at least 2 or more responses, identified by session (will not include poor data from old software).</li>
			<li><b><?= $cnt_jan18; ?></b> - True customer count, excluding all customers before Jan. 18, 2016.</li>
		</ul>
	</div>
</div>

<div class='row'>
<div class='well well-sm'>60 Days Relative To Industry</div>
<?php
	$self = (new Data())->store($storeID)->from(time() - (60*24*3600))->getAvg(60);
	$labelArray_self = [];
	$dataArray_self = [];
	$lastValue = 0;
	for($i = 0; $i < 60; $i++){
		$lastValue = $self->getRating($i) && $self->getSize($i) > 0 ? $self->getRating($i) : $lastValue;
		$dataArray_self[] = $lastValue;
		$labelArray_self[] = "'".date('M jS', $self->getUTCFrom($i)).' - '.date('M jS', $self->getUTCTo($i))." (".$self->getSize($i).")'";
	}
	$labelArray_self = implode(',', $labelArray_self);
	
	$other = (new Data())->keyword($keywords)->from(time() - (60*24*3600))->getAvg(60);
	$dataArray_other = [];
	$lastValue = 0;
	for($i = 0; $i < 60; $i++){
		$lastValue = $other->getRating($i) && $other->getRating() > 0 ? $other->getRating($i) : $lastValue;
		$dataArray_other[] = $lastValue;
	}
	
	$change = [];
	for($i = 0; $i < 60; $i++){
		$change[] = $dataArray_self[$i] - $dataArray_other[$i];
	}
	$changeString = implode(',', $change);
	
	$minY = max(-200, ceil(min($change)-10));
	$maxY = min(200, ceil(max($change)+10));
?>
	<div class='aspect-chart'>
		<div class='chart-container'>
		<canvas id='oneyear_overall' class='aspect-chart'></canvas>
		</div>
		<script type='text/javascript'>
			new Chart(document.getElementById("oneyear_overall")
			.getContext('2d'), {
				type: 'line',
				data: {
					labels: [<?php echo $labelArray_self; ?>],
					datasets: [
						{
							label: "<?php echo $storeName; ?>",
							fill: true,
							backgroundColor: "rgba(255,43,43,0.1)",
							borderColor: "rgba(255,43,43,1)",
							pointBackgroundColor: "rgba(255,43,43,1)",
							pointBorderColor: "#fff",
							pointHoverBackgroundColor: "#fff",
							pointHoverBorderColor: "rgba(220,220,220,1)",
							borderWidth: 0.5,
							tension: 0.4,
							data: [<?php echo $changeString; ?>]
						}
					]
				},
				options: {
					responsive : true, maintainAspectRatio : false,
					stacked: true,
					scales : {
						xAxes: [{
							ticks : {
								autoSkip: false,
								display: false
							},
							gridLines: {
								display: false
							}
						}],
						yAxes: [{
							ticks : {
								beginAtZero: true,
								min: <?= $minY ?>,
								max: <?= $maxY ?>,
								display: false
							},
							gridLines: {
								display: true
							}
						}]
					},
					legend : {
						display: false
					},
					title : {
						display: false
					},
					tooltips : {
						callbacks : {
							label : function(item, data){
								var x = data.datasets[0].data[item.index];
								var label = data.labels[item.index];
								if(x < 0){
									return '-' + Math.abs(x) + '%';
								} else {
									return '+' + Math.abs(x) + '%';
								}
							}
						}
					}
				}
			});
		</script>
	</div>
</div>

<div class='row'>
<div class='well well-sm'>The past 60 days of data.</div>
<?php
foreach($rows as $row){
	$id = $row['id']; $aspectTypeID = $row['AspectTypeID'];
	$title = $row['Title'];
	
	$data = (new Data())->store($storeID)->aspectType($aspectTypeID)->from(time() - (60*24*3600));
	$result = $data->getAvg(5);
	
	$labelArray = [];
	$dataArray = [];
	
	$sum = 0;
	
	for($i = 0; $i < 5; $i++){
		$dataArray[] = $result->getRating($i) ? $result->getRating($i) : 0;
		$labelArray[] = "'".date('M jS', $result->getUTCFrom($i)).' - '.date('M jS', $result->getUTCTo($i))." (".$result->getSize($i).")'";
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
										autoSkip: false,
										display: false
									},
									gridLines: {
										display: false
									}
								}],
								yAxes: [{
									ticks : {
										beginAtZero: true,
										min: 0,
										max: 100,
										display: false
									},
									gridLines: {
										display: true
									}
								}]
							},
							legend : {
								display: false
							},
							title : {
								display: false
							},
							tooltips : {
								callbacks : {
									label : function(item, data){
										var x = data.datasets[0].data[item.index];
										var label = data.labels[item.index];
										return x + '%';
									}
								}
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
?>
</div>
<?php } ?>