<?php
if(!Brevada::IsLoggedIn()){ Brevada::Redirect('/home/logout'); }
if(!Permissions::has(Permissions::VIEW_ADMIN)){ Brevada::Redirect('/404'); }
?>
<h1 class="page-header">Accounts</h1>

<h2 class="sub-header">Edit Accounts</h2>
<p>For Company and Store, enter the ID not the name.</p>
<div class="table-responsive">
  <table class="table table-striped tablesorter editable">
    <thead>
      <tr>
        <th>#</th>
        <th class='editable'>First Name</th>
        <th class='editable'>Last Name</th>
		<th class='editable'>Email Address</th>
		<th class='editable'>Password</th>
		<th class='editable' placeholder='Enter ID'>Company</th>
		<th class='editable' placeholder='Enter ID'>Store</th>
		<th class='editable'>Permissions</th>
		<th data-sorter='false' class='sorter-false'>Options</th>
      </tr>
    </thead>
	<tbody>
	<?php
	$query = Database::query("SELECT `accounts`.id as AccountID, `accounts`.`FirstName`, `accounts`.`LastName`, `accounts`.EmailAddress, `accounts`.`Password`, `accounts`.Permissions, `companies`.`Name` as CompanyName, `stores`.`Name` as StoreName, `accounts`.StoreID, `accounts`.CompanyID FROM `accounts` LEFT JOIN `companies` ON `companies`.id = `accounts`.CompanyID LEFT JOIN `stores` ON `stores`.id = `accounts`.StoreID");
	while($row = $query->fetch_assoc()){
		$id = $row['AccountID'];
		$firstName = $row['FirstName'];
		$lastName = $row['LastName'];
		$email = $row['EmailAddress'];
		$companyName = $row['CompanyName'];
		$storeName = $row['StoreName'];
		$permissions = $row['Permissions'];
		$password = $row['Password'];
	?>
      <tr data-id='<?php echo $id; ?>'>
        <td><?php echo $id; ?></td>
        <td><?php echo $firstName; ?></td>
        <td><?php echo $lastName; ?></td>
		<td><?php echo $email; ?></td>
		<td<?php echo strlen($password) < 60 ? '' : ' class="not-editable secure"'; ?>><?php echo strlen($password) < 60 ? $password : 'secure'; ?></td>
		<td><?php echo $companyName; ?></td>
		<td><?php echo empty($storeName) ? 'null' : $storeName; ?></td>
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
<br /><br />
<div class='well well-sm'>Want to reset the password for your admin account? <a id='resetAdmin' href='#'>Click Here</a></div>

<script type='text/javascript'>
function submitChange(column, id, value){
	$.post('/admin/update/account.php', {'column' : column, 'id' : id, 'value' : value}, function(data){
		$('table.editable tr[data-id="'+id+'"] td > i').remove();
		$('table.editable tr[data-id="'+id+'"] td').removeClass('saving');
		if(data == 'Invalid' || data.indexOf('Error') === 0){
			$('table.editable tr[data-id="'+id+'"] td:eq('+column+')').text($('table.editable tr[data-id="'+id+'"] td:eq('+column+')').data('previous-value'));
		} else {
			if(column == 5 || column == 6){
				$('table.editable tr[data-id="'+id+'"] td:eq('+column+')').text(data);
				$('table.editable tr[data-id="'+id+'"] td:eq('+column+')').data('previous-value', data);
			}
		}
	});
}

$(document).ready(function(){
	$('#resetAdmin').click(function(){
		var pass = prompt('Enter your new password (must be at least 8 characters).');
		if(pass && pass.length >= 8){
			var passConfirm = prompt('Please re-type your password.');
			if(passConfirm && passConfirm == pass){
				$.post('/admin/update/admin_password.php', { password : pass }, function(){
					alert('You will be logged out. Please log back in with your new password.');
					window.location.href = '/logout';
				});
			} else {
				alert('Passwords do not match.');
			}
		} else {
			alert('Too short.');
		}
		return false;
	});
});
</script>