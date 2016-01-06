<?php
if(!Brevada::IsLoggedIn()){ Brevada::Redirect('/home/logout'); }
if(!Permissions::has(Permissions::VIEW_ADMIN)){ Brevada::Redirect('/404'); }
?>
<h1 class="page-header">Support</h1>

<?php
if(isset($_GET['id']) && !empty($_GET['id'])) {
	$id = @intval($_GET['id']);
	$accountID = -1;
	
	$query = Database::query("
		SELECT
			`support`.`Resolved`,
			UNIX_TIMESTAMP(`support`.`Date`) as `Date`,
			IFNULL(`accounts`.`FirstName`, 'N/A') as `FirstName`,
			`accounts`.`id` as `AccountID`,
			`accounts`.`EmailAddress`,
			IFNULL(`companies`.`PhoneNumber`, 'N/A') as `PhoneNumber`,
			`companies`.`Name`,
			`companies`.`id` as `CompanyID`,
			`support`.`Message`
		FROM `support`
		JOIN `accounts` ON `accounts`.`id` = `support`.`AccountID`
		JOIN `companies` ON `companies`.`id` = `accounts`.`CompanyID`
		WHERE `support`.`id` = {$id} LIMIT 1
	");
	while($row = $query->fetch_assoc()){
		$accountID = @intval($row['AccountID']);
		
		echo "<div class='panel panel-red support support-initial '>";
		echo "<div class='panel-heading' style='overflow:hidden;'><span class='pull-left'>Ticket #{$id} - ".($row['Resolved'] == '1' ? 'Closed' : 'Open')."</span><span class='pull-right'>".date('d-m-Y @ g:i:s a', $row['Date'])."</span></div>";
		echo "<div class='panel-body'>";
		
		echo "<span>Company: <a href='?show=companies&id={$id}'>{$row['Name']}</a></span><br/>";
		echo "<span>Company Phone #: {$row['PhoneNumber']}</span><br/>";
		echo "<span>Account Name: <a href='?show=accounts&id={$row['AccountID']}'>{$row['FirstName']}</a></span><br/>";
		echo "<span>Account Email: {$row['EmailAddress']}</span><br/><br/>";
		
		echo "<div class='panel panel-default'>";
		echo "<div class='panel-heading'>Message</div>";
		echo "<div class='panel-body'><p>".htmlentities($row['Message'])."</p></div>";
		echo "</div>";
		
		echo "</div>";
		echo "</div>";
	}
	$query->close();
	?>
	<div class='issue-pod'>
	<textarea class='issue' placeholder='Please type your reply...' maxlength='1000' data-sid='<?php echo $id; ?>'></textarea>
	<div class='submit'>Submit</div>
	</div>
	<?php
	$query = Database::query("
		SELECT
			UNIX_TIMESTAMP(`support_responses`.`Date`) as `Date`,
			IFNULL(`accounts`.`FirstName`, 'N/A') as `FirstName`,
			`support_responses`.`Message`, `support_responses`.`AccountID` as `AccountID`
		FROM `support_responses`
		JOIN `accounts` ON `accounts`.`id` = `support_responses`.`AccountID`
		WHERE `support_responses`.`SupportID` = {$id}
		ORDER BY `support_responses`.`Date` DESC
	");
	if($query->num_rows > 0){
		while($row = $query->fetch_assoc()){
			$isUser = $row['AccountID'] == $accountID;
			echo "<div class='panel panel-".($isUser ? 'red' : 'primary')."'>";
			echo "<div class='panel-heading' style='overflow:hidden;'><span class='pull-left'>".($isUser ? 'Customer: ' . $row['FirstName'] : 'Brevada: ' . $row['FirstName'])."</span><span class='pull-right'>".date('d-m-Y @ g:i:s a', $row['Date'])."</span></div>";
			echo "<div class='panel-body'><p>".htmlentities($row['Message'])."</p></div>";
			echo "</div>";
		}
	}
	$query->close();

} else {
?>
<div class="table-responsive">
  <table class="table table-striped tablesorter">
    <thead>
      <tr>
		<th>Time</th>
        <th>Date</th>
        <th>Issued By</th>
		<th>Replies</th>
		<th>Status</th>
		<th data-sorter='false' class='sorter-false'>Options</th>
      </tr>
    </thead>
	<tbody>
	<?php
	$query = Database::query("
	SELECT
	`support`.`id`, `companies`.`Name`, UNIX_TIMESTAMP(`support`.`Date`) as `Date`,
	`support`.`Resolved`,
	(SELECT COUNT(*) FROM `support_responses` WHERE `support_responses`.`SupportID` = `support`.`id`) as `Responses`
	FROM `support`
	JOIN `accounts` ON `accounts`.`id` = `support`.`AccountID`
	JOIN `companies` ON `companies`.`id` = `accounts`.`CompanyID`
	ORDER BY `support`.`Resolved` ASC, `support`.`Date` ASC
	");
	while($row = $query->fetch_assoc()){
		$id = $row['id'];
		$dateIssued = intval($row['Date']);
		$issuer = $row['Name'];
		$responses = $row['Responses'];
		$resolved = @intval($row['Resolved']) == 1;
	?>
      <tr>
		<td><?php echo date('g:i:s a', $dateIssued); ?></td>
        <td><?php echo date('d-m-Y', $dateIssued); ?></td>
		<td><?php echo $issuer; ?></td>
		<td><?php echo $responses; ?></td>
		<td class='status support'><i class='fa <?php echo $resolved ? 'fa-check resolved' : 'fa-times unresolved'; ?>' title='<?php echo $resolved ? 'Resolved' : 'Unresolved'; ?>'></i></td>
		<td class='options'>
			<a href='?show=support&id=<?php echo $id; ?>'>View</a>
		</td>
      </tr>
	<?php
	} $query->close();
	?>
	</tbody>
  </table>
</div>
<?php } ?>

<script type='text/javascript'>
$(document).ready(function(){
	$('div.issue-pod > div.submit').click(function(){
		var txtArea = $(this).parent().children('textarea');
		var msg = txtArea.val();
		if(msg){
			txtArea.slideUp(function(){
				$.post('/api/v1/bdff/issue', { 'message' : msg, 'sid' : txtArea.attr('data-sid'), 'localtime' : Math.ceil(((new Date()).getTime()/1000)) }, function(data){
					location.reload();
				});
			});
		}
	});
});
</script>