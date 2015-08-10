<?php
if(!Brevada::IsLoggedIn()){ Brevada::Redirect('/home/logout'); }
if(!Permissions::has(Permissions::VIEW_ADMIN)){ Brevada::Redirect('/404'); }
?>
<h1 class="page-header">Tablets</h1>


<h2 class="sub-header">Tablet Data</h2>
<p>For Store Name, enter the ID not the name.</p>
<div class="table-responsive">
  <table class="table table-striped tablesorter editable">
    <thead>
      <tr>
        <th>#</th>
        <th class='editable'>Serial #</th>
        <th class='editable' placeholder='Enter ID'>Store Name</th>
		<th class='editable editable-dropdown' data-dropdown-options='Shipped To Store, Shipped To Us, At Store, At Us, In Repair, Defective, Damaged'>Status</th>
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
      <tr data-id='<?php echo $id; ?>'>
        <td><?php echo $id; ?></td>
        <td><?php echo $serial; ?></td>
        <td><?php echo $name; ?></td>
		<td class='status'><?php echo $status; ?></td>
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

<script type='text/javascript'>
function submitChange(column, id, value){
	$.post('/admin/update/tablet.php', {'column' : column, 'id' : id, 'value' : value}, function(data){
		$('table.editable tr[data-id="'+id+'"] td > i').remove();
		$('table.editable tr[data-id="'+id+'"] td').removeClass('saving');
		if(data != 'Invalid' && data.indexOf('Error') !== 0){
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