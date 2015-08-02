<?php
if(!Brevada::IsLoggedIn()){ Brevada::Redirect('/home/logout'); }
if(!Permissions::has(Permissions::VIEW_ADMIN)){ Brevada::Redirect('/404'); }
?>
<h1 class="page-header">Stores</h1>

<h2 class="sub-header">Store Listing</h2>
<div class="table-responsive">
  <table class="table table-striped tablesorter">
    <thead>
      <tr>
        <th>#</th>
        <th>Name</th>
        <th>Brevada URL</th>
        <th>Phone #</th>
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
      <tr>
        <td><?php echo $id; ?></td>
        <td><?php echo $name; ?></td>
        <td>http://brevada.com/<?php echo $url; ?></td>
		<td><?php echo empty($phone) ? '-' : $phone; ?></td>
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