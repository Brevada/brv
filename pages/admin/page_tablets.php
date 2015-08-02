<?php
if(!Brevada::IsLoggedIn()){ Brevada::Redirect('/home/logout'); }
if(!Permissions::has(Permissions::VIEW_ADMIN)){ Brevada::Redirect('/404'); }
?>
<h1 class="page-header">Tablets</h1>


<h2 class="sub-header">Tablet Data</h2>
<div class="table-responsive">
  <table class="table table-striped tablesorter">
    <thead>
      <tr>
        <th>#</th>
        <th>Serial #</th>
        <th>Store Name</th>
		<th>Status</th>
		<th data-sorter='false' class='sorter-false'>Options</th>
      </tr>
    </thead>
	<tbody>
	<?php
	$query = Database::query("SELECT `tablets`.`id` as TabletID, `tablets`.`SerialCode`, `tablets`.`StoreID`, `stores`.`Name`, `stores`.`id` as StoreID, `tablets`.`Status` FROM `tablets` LEFT JOIN `stores` ON `stores`.`id` = `tablets`.`StoreID`");
	while($row = $query->fetch_assoc()){
		$id = $row['TabletID'];
		$storeID = $row['StoreID'];
		$serial = $row['SerialCode'];
		$status = $row['Status'];
		$name = $row['Name'];
	?>
      <tr>
        <td><?php echo $id; ?></td>
        <td><?php echo $serial; ?></td>
        <td><?php echo $name; ?></td>
		<td><?php echo $status; ?></td>
		<td class='options'>
			<!--<a href='#'><i class='fa fa-link'></i></a>
			<a href='#'><i class='fa fa-bar-chart'></i></a>
			<a href='#'><i class='fa fa-cog'></i></a>
			<a href='#'><i class='fa fa-credit-card'></i></a>-->
		</td>
      </tr>
	<?php
	}
	?>
	</tbody>
  </table>
</div>