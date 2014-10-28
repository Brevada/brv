<?php
$this->addResource('/css/company_box_data.css');

$company_id = $this->getParameter('company_id');

$query = Database::query("SELECT * FROM posts WHERE user_id='{$company_id}' ORDER BY id DESC");

if($query->num_rows == 0){
	echo "<br /><span style='font-size:12px; color:#777; font-family:helvetica;'><center>This company has no posts.</center></span>";
}

$post_id = ''; $active = ''; $post_name = ''; $post_extension = ''; $post_description = '';

while($row = $query->fetch_assoc()){
	$post_id = $row['id'];
	$active = $row['active'];
	$post_name = $row['name'];
	$post_extension = $row['extension'];
	$post_description = $row['description'];
?>

<div class='company_data_box'>
	<div class="leftData" style="width:160px;"><?php echo $post_name; ?></div>
	<div class="leftData" style="width:160px;">
		<?php
		//Calculate Average
		$query2 = Database::query("SELECT * FROM feedback WHERE post_id='{$post_id}'");
		
		$total = 0;
		
		if($query2->num_rows > 0) {
			while($row = $query2->fetch_assoc()){
				$total += @intval($row['value']);
			}
		}
		
		$average = 0;
		
		if($query2->num_rows > 0){
			$average = round($total / $query2->num_rows, 2);
		}

		$color = 'red';
		if($average >= 80) {
			$color = 'green';
		} else if($average >= 60) {
			$color = 'orange';
		}
		?>
		<span style="color:<?php echo $color; ?>;">
			<?php echo $average; ?>
		</span>
		<span style="font-size:10px;">average in  <?php echo $query2->num_rows; ?> ratings.</span>
	</div>
	<div class="leftData" style="width:340px;">
		<?php
		//Comments
		$query3 = Database::query("SELECT * FROM comments WHERE post_id='{$post_id}' ORDER BY `id` DESC LIMIT 1");
		$comment_count = $query3->num_rows;
		if($query3->num_rows == 0){
			echo "No Comments";
		} else {
			while($row3 = $query3->fetch_assoc()){
				/*
				// NEVER USED.
				$c_id = $row3['id'];
				$date = $row3['date'];
				$createDate = new DateTime($date);
				$date = $createDate->format('d.m.Y');
				$date = strtotime($date);
				$date = date('F jS Y', $date);
				$country = $row3['country'];
				*/
				$comment = $row3['comment'];
				
				echo '"', substr($comment,0,40); if(strlen($comment)>40){echo '...';}
				echo '"';	
			}
		}
		?>
		<span style="font-size:10px;">(<?php echo $comment_count; ?> total comments.)</span>
	</div>
	<br style="clear:both;" />
</div>
<?php } ?>
<br style="clear:both;" />