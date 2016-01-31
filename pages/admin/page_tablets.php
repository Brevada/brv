<?php
if(!Brevada::IsLoggedIn()){ Brevada::Redirect('/home/logout'); }
if(!Permissions::has(Permissions::VIEW_ADMIN)){ Brevada::Redirect('/404'); }

?>
<h1 class="page-header">Tablets</h1>

<?php if(empty($_GET['id'])){ ?>
<h2 class="sub-header">Tablet Data</h2>
<p>For Store Name, enter the ID not the name.</p>
<div class="table-responsive">
  <table class="table table-striped tablesorter editable">
    <thead>
      <tr>
        <th>#</th>
        <th class='editable'>Serial #</th>
        <th class='editable' placeholder='Enter ID'>Store Name</th>
		<th>Battery</th>
		<th>Online</th>
		<th class='editable editable-dropdown' data-dropdown-options='Shipped To Store, Shipped To Us, At Store, At Us, In Repair, Defective, Damaged'>Status</th>
		<th data-sorter='false' class='sorter-false'>Options</th>
      </tr>
    </thead>
	<tbody>
	<?php
	$query = Database::query("SELECT
	`tablets`.`id` as TabletID,
	`tablets`.`SerialCode`,
	`tablets`.`StoreID`,
	`stores`.`Name`,
	`stores`.`id` as StoreID,
	`tablets`.`Status`,
	`tablets`.`OnlineSince`,
	`tablets`.`BatteryPercent`,
	`tablets`.`BatteryPluggedIn`
	FROM `tablets` LEFT JOIN `stores` ON `stores`.`id` = `tablets`.`StoreID`");
	while($row = $query->fetch_assoc()){
		$id = $row['TabletID'];
		$storeID = $row['StoreID'];
		$serial = strtoupper($row['SerialCode']);
		$status = $row['Status'];
		$name = empty($row['Name']) ? 'NULL' : $row['Name'];
		$battery = 'N/A';
		if(!empty($row['BatteryPercent'])){
			$battery = $row['BatteryPercent'].'%' . ' / ' . ($row['BatteryPluggedIn'] ? 'Charging' : 'Unplugged');
		}
		$online = 'N/A';
		if(!empty($row['OnlineSince'])){
			$online = time() - @intval($row['OnlineSince']) > 600 ? 'Offline' : 'Online';
		}
	?>
      <tr data-id='<?php echo $id; ?>'>
        <td><a href='admin?show=tablets&id=<?php echo $id; ?>'><?php echo $id; ?></a></td>
        <td><?php echo $serial; ?></td>
        <td><?php echo $name; ?></td>
		<td><?php echo $battery; ?></td>
		<td><?php echo $online; ?></td>
		<td class='status'><?php echo $status; ?></td>
		<td class='options'>
			<a href='admin?show=tablets&id=<?php echo $id; ?>'><i class='fa fa-link'></i></a>
		</td>
      </tr>
	<?php
	}
	?>
	</tbody>
  </table>
</div>
<?php
} else {
	$id = @intval($_GET['id']);
	$query = Database::query("SELECT
	`tablets`.`id` as TabletID,
	`tablets`.`SerialCode`,
	`tablets`.`StoreID`,
	`stores`.`Name`,
	`stores`.`id` as StoreID,
	`tablets`.`Status`,
	`tablets`.`OnlineSince`,
	`tablets`.`BatteryPercent`,
	`tablets`.`BatteryPluggedIn`,
	`tablets`.`PositionLatitude`,
	`tablets`.`PositionLongitude`,
	`tablets`.`PositionTimestamp`,
	`tablets`.`StoredDataCount`,
	`tablets`.`DeviceVersion`,
	`tablets`.`DeviceModel`
	FROM `tablets`
	LEFT JOIN `stores` ON `stores`.`id` = `tablets`.`StoreID`
	WHERE `tablets`.`id` = {$id}");
	while($row = $query->fetch_assoc()){
?>
<?php
	if(isset($_GET['sent'])){
		echo "<div class='alert alert-success'>Command sent.</div>";
	}
	if(isset($_GET['error'])){
		echo "<div class='alert alert-danger'>Command failed to send.</div>";
	}
?>
<div class='panel panel-default'>
	<div class='panel-heading' style='overflow: hidden;'>
		<span class='pull-left'>#<?php echo $id; ?></span>
		<span class='pull-right'><?php echo time() - @intval($row['OnlineSince']) > 600 ? 'Offline' : 'Online'; ?></span>
	</div>
	<div class='panel-body'>
		<span>Store: <a href='admin?show=stores&id=<?php echo $row['StoreID']; ?>'><?php echo $row['Name']; ?></a></span><br />
		<?php if(!empty($row['OnlineSince'])){ ?>
		<span>Device: <?php echo $row['DeviceModel'].' / '.$row['DeviceVersion']; ?></span><br />
		<span>Battery: <?php echo $row['BatteryPercent'].'%' . ' / ' . ($row['BatteryPluggedIn'] ? 'Charging' : 'Unplugged'); ?></span><br />
		<span>Stored Data Count: <?php echo $row['StoredDataCount']; ?></span><br /><br />
		<?php
		if(!empty($row['PositionLatitude']) && !empty($row['PositionLongitude'])){
			$init = time() - (@intval($row['PositionTimestamp']));
			$hours = floor($init/3600);
			$minutes = floor(($init / 60) % 60);
			$seconds = $init % 60;
			$hoursAgo = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
		?>
		<form method='post' action='/admin/update/send_command.php'>
		<input type='hidden' name='id' value='<?php echo $id; ?>' />
		<input type='hidden' name='command' value='restart' />
		<button class='button' id='btnUpdate'>Update Software</button>
		</form>
		<br /><br />
		<?php if(@intval($row['PositionTimestamp']) == 0){ ?>
		<p>Cannot retrieve GPS coordinates.</p>
		<?php } else { ?>
		<span>GPS coordinates last updated <?php echo $hoursAgo; ?> hours ago.</span><br /><br />
		<iframe width="300" height="300" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?q=<?php echo "{$row['PositionLatitude']},{$row['PositionLongitude']}"; ?>&hl=es;z=14&amp;output=embed"></iframe>
		<?php } ?>
		<?php } ?>
		<?php } else { ?>
		<br /><p>Device has not been setup.</p>
		<?php } ?>
	</div>
</div>
<?php
	}
}
?>

<script type='text/javascript'>
function submitChange(column, id, value){
	$.post('/admin/update/tablet.php', {'column' : column, 'id' : id, 'value' : value}, function(data){
		$('table.editable tr[data-id="'+id+'"] td > i').remove();
		$('table.editable tr[data-id="'+id+'"] td').removeClass('saving');
		if(data != 'Invalid' && data.indexOf('Error') !== -1){
			if(column == 2){
				$('table.editable tr[data-id="'+id+'"] td:eq('+column+')').text(data);
				$('table.editable tr[data-id="'+id+'"] td:eq('+column+')').data('previous-value', data);
			}
		} else {
			$('table.editable tr[data-id="'+id+'"] td:eq('+column+')').text($('table.editable tr[data-id="'+id+'"] td:eq('+column+')').data('previous-value'));
		}
	});
}
</script>