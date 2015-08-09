<?php
if($this->getParameter('valid') !== true){ exit('Error.'); }
$_POST = $this->getParameter('POST');

if($_SESSION['Corporate'] && !Permissions::has(Permissions::MODIFY_COMPANY_STORES)){
	exit('Permission denied.');
}

if(!$_SESSION['Corporate'] && !Permissions::has(Permissions::MODIFY_STORE)){
	exit('Permission denied.');
}
?>
<form id='frmAccount' action='settings?section=billing' method='post'>
<div class='form-account'>
	<span class="form-subheader"><?php echo sprintf(__("If you have any questions about a transaction, please feel free to contact us at %s."), __("customercare@brevada.com")); ?></span><br />
	
	<table class='table table-white table-bordered table-hover table-data'>
		<thead>
			<th><?php _e("Product"); ?></th>
			<th><?php _e("Price (CAD)"); ?></th>
			<th><?php _e("Date"); ?></th>
			<th><?php _e("Status"); ?></th>
		</thead>
		<tbody>
			<?php
				if(($query = Database::query("SELECT `Product`, `Confirmed`, `Value`, `Date` FROM `transactions` WHERE `transactions`.`CompanyID` = {$_SESSION['CompanyID']} ORDER BY `Date` DESC")) !== false){
					while($row = $query->fetch_assoc()){
						$product = ucwords($row['Product']);
						$price = '$'.number_format(floatval($row['Value'])/100, 2, '.', ',');
						$date = $row['Date'];
						$status = $row['Confirmed'] == 0 ? __("Pending")." <i class='fa fa-circle-o-notch fa-spin'></i>" : __("Good")." <i class='fa fa-check-circle-o'></i>";
			?>
						<tr>
							<td><?php _e($product); ?></td>
							<td><?php echo $price; ?></td>
							<td><?php echo $date; ?></td>
							<td class='status'><?php echo $status; ?></td>
						</tr>
			<?php
					}
				}
			?>
		</tbody>
	</table>
</div>
</form>