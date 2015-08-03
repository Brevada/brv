<?php
if(!Brevada::IsLoggedIn()){ Brevada::Redirect('/home/logout'); }
if(!Permissions::has(Permissions::VIEW_ADMIN)){ Brevada::Redirect('/404'); }
?>
<h1 class="page-header">Companies</h1>

<h2 class="sub-header">Companies</h2>
<div class="table-responsive">
  <table class="table table-striped tablesorter editable">
    <thead>
      <tr>
        <th>#</th>
        <th class='editable'>Company Name</th>
        <th class='editable'>Phone #</th>
        <th>Status</th>
		<th data-sorter='false' class='sorter-false'>Options</th>
      </tr>
    </thead>
	<tbody>
	<?php
	$query = Database::query("SELECT `companies`.`id`, `companies`.`Name`, `companies`.`PhoneNumber`, UNIX_TIMESTAMP(`companies`.ExpiryDate) as `Expiry`, `companies`.`Active` FROM `companies`");
	while($row = $query->fetch_assoc()){
		$id = $row['id'];
		$name = $row['Name'];
		$phone = $row['PhoneNumber'];
		$expiryDate = $row['Expiry'];
		$active = $row['Active'] == 1;
		
		$status = 'Active'; $statusCSS = 'active';
		if(!$active){
			$status = 'Not Setup';
			$statusCSS = 'notready';
		} else if(time() > intval($expiryDate)){
			$status = 'Expired';
			$statusCSS = 'expired';
		}
	?>
      <tr data-id='<?php echo $id; ?>'>
        <td><?php echo $id; ?></td>
        <td><?php echo $name; ?></td>
        <td><?php echo empty($phone) ? '' : $phone; ?></td>
        <td class='status <?php echo $statusCSS; ?>'><?php echo $status; ?></td>
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
	$.post('/admin/update/company.php', {'column' : column, 'id' : id, 'value' : value}, function(data){
		$('table.editable tr[data-id="'+id+'"] td > i').remove();
		$('table.editable tr[data-id="'+id+'"] td').removeClass('saving');
		if(data == 'Invalid' || data.indexOf('Error') === 0){
			$('table.editable tr[data-id="'+id+'"] td:eq('+column+')').text($('table.editable tr[data-id="'+id+'"] td:eq('+column+')').data('previous-value'));
		}
	});
}
</script>