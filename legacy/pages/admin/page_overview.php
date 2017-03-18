<?php
if(!Brevada::IsLoggedIn()){ Brevada::Redirect('/home/logout'); }
if(!Permissions::has(Permissions::VIEW_ADMIN)){ Brevada::Redirect('/404'); }
?>
<h1 class="page-header">Overview</h1>

<p>Welcome to the Brevada Admin Panel!</p>
<p>Note that all administrative actions are logged for security purposes.</p>

<?php
$week_numDollars = 0;
$week_numUsers = 0;

$query = Database::query("SELECT SUM(Value) as Total FROM `transactions` WHERE `Date` > DATE_SUB(NOW(), INTERVAL 1 WEEK)");
if($row = $query->fetch_assoc()){
	$week_numDollars = '$'.number_format(floatval($row['Total'])/100, 2, '.',',');
}

$query = Database::query("SELECT COUNT(*) as Total FROM `accounts` WHERE `DateCreated` > DATE_SUB(NOW(), INTERVAL 1 WEEK)");
if($row = $query->fetch_assoc()){
	$week_numUsers = $row['Total'];
}

$days = 15;
$self = (new Data())->from(time() - ($days*24*3600))->getAvg($days);
?>
<div class="row">
<div class='panel panel-default'>
	<div class='panel-heading'>2 Weeks</div>
	<div class='panel-body'>
	<div class="col-lg-3 col-md-6">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<div class="row">
					<div class="col-xs-3">
						<i class="fa fa-comments fa-4x"></i>
					</div>
					<div class="col-xs-9 text-right">
						<div class="huge"><?php echo (new Data())->from(time() - ($days*24*3600))->getAvg()->getSize(); ?></div>
						<div>Responses</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-3 col-md-6">
		<div class="panel panel-green">
			<div class="panel-heading">
				<div class="row">
					<div class="col-xs-3">
						<i class="fa fa-usd fa-4x"></i>
					</div>
					<div class="col-xs-9 text-right">
						<div class="huge"><?php echo $week_numDollars; ?></div>
						<div>Of Purchases</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-3 col-md-6">
		<div class="panel panel-yellow">
			<div class="panel-heading">
				<div class="row">
					<div class="col-xs-3">
						<i class="fa fa-users fa-4x"></i>
					</div>
					<div class="col-xs-9 text-right">
						<div class="huge"><?php echo $week_numUsers; ?></div>
						<div>Accounts</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	</div>
</div>
</div>
<div class='row'>
	<div class='panel panel-default'>
	<div class='panel-heading'>Response Growth (2 Weeks)</div>
	<div class='panel-body'>
		<?php
			$labelArray_self = [];
			$dataArray_self = [];
			for($i = 1; $i < $days; $i++){
				$dataArray_self[] = $self->getSize($i-1) == 0 ? 0 : round(100*($self->getSize($i) - $self->getSize($i-1))/$self->getSize($i-1), 1);
				$from = date('M jS', $self->getUTCFrom($i));
				$to = date('M jS', $self->getUTCTo($i-1));
				$labelArray_self[] = "'".($from == $to ? $from : $from.' - '.$to)."'";
			}
			$minY = min($dataArray_self)-10;
			$maxY = max($dataArray_self)+10;
			$labelArray_self = implode(',', $labelArray_self);
			$dataArray_self = implode(',', $dataArray_self);
		?>
		<div class='aspect-chart col-xs-12'>
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
								label: "Responses",
								fill: true,
								backgroundColor: "rgba(255,43,43,0.1)",
								borderColor: "rgba(255,43,43,1)",
								pointBackgroundColor: "rgba(255,43,43,1)",
								pointBorderColor: "#fff",
								pointHoverBackgroundColor: "#fff",
								pointHoverBorderColor: "rgba(220,220,220,1)",
								borderWidth: 0.5,
								tension: 0.4,
								data: [<?php echo $dataArray_self; ?>]
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
									display: true
								},
								gridLines: {
									display: true
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
	</div>
</div>