<?php
if(!Brevada::IsLoggedIn()){ Brevada::Redirect('/home/logout'); }
if(!Permissions::has(Permissions::VIEW_ADMIN)){ Brevada::Redirect('/404'); }
?>
<h1 class="page-header">Overview</h1>

<p>Welcome to the Brevada Admin Panel!</p>
<p>Note that all administrative actions are logged for security purposes.</p>

<?php
$numOfResponses = 0;
$numDollars = 0;
$numUsers = 0;

$query = Database::query("SELECT COUNT(*) as Total FROM `feedback` WHERE `Date` > DATE_SUB(NOW(), INTERVAL 1 WEEK)");
if($row = $query->fetch_assoc()){
	$numOfResponses = $row['Total'];
}

$query = Database::query("SELECT SUM(Value) as Total FROM `transactions` WHERE `Date` > DATE_SUB(NOW(), INTERVAL 1 WEEK)");
if($row = $query->fetch_assoc()){
	$numDollars = '$'.number_format(floatval($row['Total'])/100, 2, '.',',');
}

$query = Database::query("SELECT COUNT(*) as Total FROM `accounts`");
if($row = $query->fetch_assoc()){
	$numUsers = $row['Total'];
}
?>
<h2 class='sub-header'>This Week At A Glance</h2>
<div class="row">
	<div class="col-lg-3 col-md-6">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<div class="row">
					<div class="col-xs-3">
						<i class="fa fa-comments fa-4x"></i>
					</div>
					<div class="col-xs-9 text-right">
						<div class="huge"><?php echo $numOfResponses; ?></div>
						<div>Responses</div>
					</div>
				</div>
			</div>
			<a href="#">
				<div class="panel-footer">
					<span class="pull-left">View Details</span>
					<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
					<div class="clearfix"></div>
				</div>
			</a>
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
						<div class="huge"><?php echo $numDollars; ?></div>
						<div>Of Purchases</div>
					</div>
				</div>
			</div>
			<a href="?show=finance">
				<div class="panel-footer">
					<span class="pull-left">View Details</span>
					<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
					<div class="clearfix"></div>
				</div>
			</a>
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
						<div class="huge"><?php echo $numUsers; ?></div>
						<div>Accounts</div>
					</div>
				</div>
			</div>
			<a href="?show=accounts">
				<div class="panel-footer">
					<span class="pull-left">View Details</span>
					<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
					<div class="clearfix"></div>
				</div>
			</a>
		</div>
	</div>
</div>