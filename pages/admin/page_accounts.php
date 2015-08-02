<?php
if(!Brevada::IsLoggedIn()){ Brevada::Redirect('/home/logout'); }
if(!Permissions::has(Permissions::VIEW_ADMIN)){ Brevada::Redirect('/404'); }
?>
<h1 class="page-header">Accounts</h1>

<h2 class="sub-header">Account Listing</h2>
<div class="table-responsive">
  <table class="table table-striped tablesorter">
    <thead>
      <tr>
        <th>#</th>
        <th>First Name</th>
        <th>Last Name</th>
		<th>Email Address</th>
		<th>Company</th>
		<th>Store</th>
		<th>Permissions</th>
		<th data-sorter='false' class='sorter-false'>Options</th>
      </tr>
    </thead>
	<tbody>
	<?php
	$query = Database::query("SELECT `accounts`.id as AccountID, `accounts`.`FirstName`, `accounts`.`LastName`, `accounts`.EmailAddress, `accounts`.Permissions, `companies`.`Name` as CompanyName, `stores`.`Name` as StoreName, `accounts`.StoreID, `accounts`.CompanyID FROM `accounts` LEFT JOIN `companies` ON `companies`.id = `accounts`.CompanyID LEFT JOIN `stores` ON `stores`.id = `accounts`.StoreID");
	while($row = $query->fetch_assoc()){
		$id = $row['AccountID'];
		$firstName = $row['FirstName'];
		$lastName = $row['LastName'];
		$email = $row['EmailAddress'];
		$companyName = $row['CompanyName'];
		$storeName = $row['StoreName'];
		$permissions = $row['Permissions'];
	?>
      <tr>
        <td><?php echo $id; ?></td>
        <td><?php echo $firstName; ?></td>
        <td><?php echo $lastName; ?></td>
		<td><?php echo $email; ?></td>
		<td><?php echo $companyName; ?></td>
		<td><?php echo $storeName; ?></td>
		<td><?php echo $permissions; ?></td>
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