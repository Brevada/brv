<?php
if(!Brevada::IsLoggedIn()){ Brevada::Redirect('/home/logout'); }
if(!Permissions::has(Permissions::VIEW_ADMIN)){ Brevada::Redirect('/404'); }
?>
<h1 class="page-header">Finance</h1>


<h2 class="sub-header">Financial Data</h2>
<div class="table-responsive">
  <table class="table table-striped tablesorter">
    <thead>
      <tr>
        <th>#</th>
        <th>Date</th>
        <th>Company</th>
		<th>Value</th>
		<th>Product</th>
		<th>Paypal TXN ID</th>
		<th>Paypal Payer</th>
		<th>Fraud</th>
		<th>Status</th>
		<th data-sorter='false' class='sorter-false'>Options</th>
      </tr>
    </thead>
	<tbody>
	<?php
	$query = Database::query("SELECT `transactions`.id as TransactionID, UNIX_TIMESTAMP(`transactions`.`Date`) as `Date`, `transactions`.CompanyID, `transactions`.`Value`, `transactions`.`Value`, `transactions`.Product, `transactions`.COnfirmed, `transactions`.PaypalTransactionID, `transactions`.PaypalPayerEmail, `transactions`.`Fraud`, `companies`.`Name` FROM `transactions` LEFT JOIN `companies` ON `companies`.id = `transactions`.CompanyID");
	while($row = $query->fetch_assoc()){
		$id = $row['TransactionID'];
		$date = date('d-m-Y', intval($row['Date']));
		$companyID = $row['CompanyID'];
		$companyName = $row['CompanyName'];
		$value = number_format(floatval($row['Value'])/100, 2, '.');
		$product = $row['Product'];
		$status = $row['Confirmed'] == 1 ? 'Confirmed' : 'Pending';
		$paypalID = $row['PaypalTransactionID'];
		$paypalEmail = $row['PaypalPayerEmail'];
		$fraud = $row['Fraud'];
	?>
      <tr>
        <td><?php echo $id; ?></td>
        <td><?php echo $date; ?></td>
        <td><?php echo $companyName; ?></td>
		<td><?php echo $value; ?></td>
		<td><?php echo $product; ?></td>
		<td><?php echo $paypalID; ?></td>
		<td><?php echo $paypalEmail; ?></td>
		<td><?php echo $fraud; ?></td>
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