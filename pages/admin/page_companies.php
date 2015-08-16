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
		<th>Max Stores</th>
		<th>Max Tablets</th>
		<th>Max Logins</th>
        <th>Status</th>
		<th data-sorter='false' class='sorter-false'>Options</th>
      </tr>
    </thead>
	<tbody>
	<?php
	$query = Database::query("SELECT `companies`.`id`, `companies`.`Name`, `companies`.`PhoneNumber`, UNIX_TIMESTAMP(`companies`.ExpiryDate) as `Expiry`, `companies`.`Active`, IFNULL(`company_features`.MaxStores, 0) as MaxStores, IFNULL(`company_features`.MaxAccounts, 0) as MaxAccounts, IFNULL(`company_features`.MaxTablets, 0) as MaxTablets FROM `companies` LEFT JOIN `company_features` ON `company_features`.`id` = `companies`.FeaturesID");
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
		
		$maxStores = $row['MaxStores'];
		$maxTablets = $row['MaxTablets'];
		$maxLogins = $row['MaxAccounts'];
	?>
      <tr data-id='<?php echo $id; ?>'>
        <td><?php echo $id; ?></td>
        <td><?php echo $name; ?></td>
        <td><?php echo empty($phone) ? '' : $phone; ?></td>
		<td><?php echo $maxStores; ?></td>
		<td><?php echo $maxTablets; ?></td>
		<td><?php echo $maxLogins; ?></td>
        <td class='status <?php echo $statusCSS; ?>'><?php echo $status; ?></td>
		<td class='options'>
			<a href='#' class='makeTransaction'><i class='fa fa-credit-card'></i></a>
		</td>
      </tr>
	<?php
	}
	?>
	</tbody>
  </table>
</div>

<div id='givemonths' class='modal fade' role='dialog'>
	<div class='modal-dialog'>
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Create Free Transaction for <span class='modalCompanyName' style='font-weight:bold;'>COMPANY NAME</span></h4>
			</div>
			<div class="modal-body">
				<p>All fields are required. Numbers only. All fields are ADDITIVE, this means the value entered here will be ADDED to the existing values.</p>

				<form action='admin/update/new_transaction.php' method="POST">
					
						<input type="text" class='form-control' placeholder="How many months?" name="txtMonths" pattern="[0-9]+" required />
						<br />
						<input type="text" class='form-control' placeholder="How many logins?" name="txtLogins" pattern="[0-9]+" required />
						<input type="text" class='form-control' placeholder="How many stores?" name="txtStores" pattern="[0-9]+" required />
						<input type="text" class='form-control' placeholder="How many tablets?" name="txtTablets" pattern="[0-9]+" required />
						
						<input type='hidden' name='id' id='companyID' value='' />
					
					<br />
					<button type='submit' class='btn btn-default'>Complete Transaction</button>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
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

$(document).ready(function(){
	$('a.makeTransaction').click(function(){
		var companyName = $(this).parent().parent().children('td:eq(1)').text();
		$('#givemonths').find('span.modalCompanyName').text(companyName);
		$('#companyID').val($(this).parent().parent().data('id'));
		$('#givemonths').modal('show');
	});
});
</script>