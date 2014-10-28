<?php
$this->addResource('/css/message_display.css');

$user_id = $this->getParameter('user_id');

$query = Database::query("SELECT * FROM `messages` WHERE `user_id`='{$user_id}' ORDER BY `id` DESC");

if($query->num_rows==0){
	echo "<div class='text_clean' style='padding:4px;'>No Suggestions</div>";
}
while($row=$query->fetch_assoc()){
	$date = $row['date'];
	$createDate = new DateTime($date);
	$date=date('F jS Y', strtotime($createDate->format('d.m.Y')));
	$message=$row['message'];
	$message_id=$row['id'];
?>
<div class="one_message">
	<div id="inner_message">
	<?php echo $message; ?><br /><a href="/overall/generic_delete.php?db=messages&id=<?php echo $message_id; ?>" style="text-decoration:none; color:#E22A12; font-weight:bold; font-size:11px; margin-top:-2px;">Delete</a> 
	<span style="font-size:9px; color:#E22A12;"><?php echo $date; ?></span>				
	</div>
</div>
<br />
<?php
}
?>