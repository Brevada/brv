<?php
if(!Brevada::IsLoggedIn()){ Brevada::Redirect('/home/logout'); }
if(!Permissions::has(Permissions::VIEW_ADMIN)){ Brevada::Redirect('/404'); }
?>
<h1 class="page-header">Stores</h1>

<h2 class="sub-header">Store Listing</h2>
<div class="table-responsive">
  <table class="table table-striped tablesorter editable">
    <thead>
      <tr>
        <th>#</th>
        <th class='editable'>Name</th>
        <th>Brevada URL</th>
        <th class='editable'>Phone #</th>
		<th data-sorter='false' class='sorter-false'>Options</th>
      </tr>
    </thead>
	<tbody>
	<?php
	$query = Database::query("SELECT `stores`.`id`, `stores`.`Name`, `stores`.`PhoneNumber`, `stores`.`URLName` FROM `stores`");
	while($row = $query->fetch_assoc()){
		$id = $row['id'];
		$name = $row['Name'];
		$phone = $row['PhoneNumber'];
		$url = $row['URLName'];
	?>
      <tr data-id='<?php echo $id; ?>'>
        <td><?php echo $id; ?></td>
        <td><?php echo $name; ?></td>
        <td>http://brevada.com/<?php echo $url; ?></td>
		<td><?php echo empty($phone) ? '' : $phone; ?></td>
		<td class='options'>
			<a href='<?php echo URL.$url; ?>' target='_blank'><i class='fa fa-link'></i></a>
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
	$.post('/admin/update/store.php', {'column' : column, 'id' : id, 'value' : value}, function(data){
		$('table.editable tr[data-id="'+id+'"] td > i').remove();
		$('table.editable tr[data-id="'+id+'"] td').removeClass('saving');
		if(data == 'Invalid' || data.indexOf('Error') !== 0){
			$('table.editable tr[data-id="'+id+'"] td:eq('+column+')').text($('table.editable tr[data-id="'+id+'"] td:eq('+column+')').data('previous-value'));
		}
	});
}
</script>