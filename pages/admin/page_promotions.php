<?php
if(!Brevada::IsLoggedIn()){ Brevada::Redirect('/home/logout'); }
if(!Permissions::has(Permissions::VIEW_ADMIN)){ Brevada::Redirect('/404'); }
?>
<h1 class="page-header">Promotions</h1>

<div>
	<h2 class="sub-header">Create a Promo Code</h2>
	<p>All fields are required.</p>

	<form action='admin/update/new_promo.php' method="POST">
		<div class='col-lg-6 col-md-6 input-group'>
			<input type="text" class='form-control' placeholder="Dollar value e.g. $50.75" name="txtValue" />
			<input type="text" class='form-control' placeholder="Enter a code e.g. PROMO365" name="txtCode" />
		</div>
		<br />
		<div class='col-lg-6 col-md-6 input-group'>
			<span>Which product is the promo for?</span>
			<select name='ddPaypalItem' class='form-control'>
				<option value='Brevada Basic Package (1 Year)'>Brevada Basic Package (1 Year)</option>
				<option value='Brevada Premium Package (1 Year)'>Brevada Premium Package (1 Year)</option>
			</select>
		</div><br />
		<button type='submit' class='btn btn-default'>Create</button>
	</form>
</div>

<h2 class="sub-header">Promo Codes</h2>
<div class="table-responsive">
  <table class="table table-striped tablesorter">
    <thead>
      <tr>
        <th>#</th>
        <th>Date Issued</th>
        <th>Value</th>
        <th>Issued By</th>
		<th>Code</th>
		<th>Paypal Item</th>
		<th>Status</th>
		<th data-sorter='false' class='sorter-false'>Options</th>
      </tr>
    </thead>
	<tbody>
	<?php
	$query = Database::query("SELECT `promo_codes`.`id`, `promo_codes`.`Code`,  `promo_codes`.`DateIssued`, `accounts`.`EmailAddress`, UNIX_TIMESTAMP(`promo_codes`.DateIssued) as `DateIssued`, `promo_codes`.`Used`, `promo_codes`.`PaypalItemName`, `promo_codes`.DiscountedValue FROM `promo_codes` LEFT JOIN `accounts` ON `accounts`.`id` = `promo_codes`.`IssuerID`");
	while($row = $query->fetch_assoc()){
		$id = $row['id'];
		$dateIssued = intval($row['DateIssued']);
		$issuer = $row['EmailAddress'];
		$value = $row['DiscountedValue'];
		$used = $row['Used'] == 1 ? 'Used' : 'Unused';
		$code = $row['Code'];
		$paypalItem = $row['PaypalItemName'];
	?>
      <tr>
        <td><?php echo $id; ?></td>
        <td><?php echo date('d-m-Y', $dateIssued); ?></td>
        <td>$<?php echo number_format($value, 2, '.', ''); ?></td>
		<td><?php echo $issuer; ?></td>
		<td><?php echo strtoupper($code); ?></td>
		<td><?php echo ucwords($paypalItem); ?></td>
		<td><?php echo $used; ?></td>
		<td class='options'>
			
		</td>
      </tr>
	<?php
	}
	?>
	</tbody>
  </table>
</div>