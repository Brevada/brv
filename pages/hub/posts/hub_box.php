<?php
$this->addResource('/css/hub_box.css');
$this->addResource('/js/hub_box.js');

$user_id = $this->getParameter('user_id');
$post_id = $this->getParameter('post_id');
$post_extension = $this->getParameter('post_extension');
$extension = $this->getParameter('extension');
$post_name = $this->getParameter('post_name');
$post_description = $this->getParameter('post_description');
$active = $this->getParameter('active');
?>
<?php if($active=='yes'){ ?>
<div id="hbox">
<?php } else { ?>
<div id="hbox" style="opacity:0.5;">
<?php
}
//Calculate Average
$query2=Database::query("SELECT `post_id`, `value` FROM feedback WHERE post_id='{$post_id}'");

$count=0; $total=0; $good=0; $ok=0; $bad=0;

while($rows2=$query2->fetch_assoc()){
	$value = @intval($rows2['value']);
	$count++;
	$total+=$value;
	if($value>=80){$good++;}else if($value<50){$bad++;}else{$ok++;}
}

$average = 0;

if($count > 0){
	$average= round($total/$count, 2);
	$good= ($good/$count)*100;
	$ok= ($ok/$count)*100;
	$bad= ($bad/$count)*100;
}
?>
	<div class="hbox_left" id="hbox_name" >
		<div id="hbox_image"><?php post_pic('40px','auto',$post_id, $user_id, $post_extension, $extension); ?>
		</div>
		<div class="left" style="margin-left:5px;">
			<strong><?php echo substr($post_name,0,25); if(strlen($post_name)>25){echo '...';} ?></strong>
			<br />	
			<?php echo substr($post_description,0,25); if(strlen($post_description)>25){echo '...';} ?>
		</div>
			<br style="clear:both;" />
				<div id="hbox_average" class="left">
					<?php
						$color = "#EDC812";
						if($average>75){$color="#4EAF0E";}else if($average<50){$color="#E22A12";}
					?>
					<span style="color:<?php echo $color; ?>;"><span style="font-weight:bold; font-size:30px;"><?php echo $average; ?></span> 
					Average in <?php echo $count; ?> ratings.</span>
					<br />
				</div>
				<br style="clear:both;" />
				<div id="box_button_holder">
				<a  id="editHead_<?php echo $post_id; ?>" class='editHead' postid="<?php echo $post_id; ?>" data-reveal-id="unlock"><div id="box_button" class="box_button_left" style="width:35px;">Edit</div></a>	
				<a href="/user_data/qr_posts/<?php echo $post_id; ?>.png" class="tooltip" target="_BLANK" style="text-decoration:none;">
				<div id="box_button">Barcode</div>
				<span>
				<strong>Barcode for this aspect</strong><br />
				This barcode will lead to a page where just this aspect of your business can be rated.<br />If you have products listed on Brevada, put the barcode on packaging to allow customers an easy way to give feedback on the item they have purchased.
				</span>
				</a>
				<?php if($active=='yes'){ ?>
 					<a href="/hub/posts/post_activate.php?id=<?php echo $post_id; ?>&yes=no"><div id="box_button" class="box_button" style="width:35px;">Hide</div></a>
 					<?php } else{ ?>
 					<a href="/hub/posts/post_activate.php?id=<?php echo $post_id; ?>&yes=yes"><div id="box_button" class="box_button" style="width:35px;">Show</div></a>
 				<?php } ?>
				<a href="/overall/generic_delete.php?db=posts&id=<?php echo $post_id; ?>"><div id="box_button" class="box_button_right">Delete</div></a>
				<br style="clear:both;" />
			</div>
	</div>
	<div class="hbox_left" style="height:150px; overflow:scroll;">
		<div id="post_activity_main_left">
 					<?php
					$queryDouble = Database::query("SELECT * FROM (SELECT date, value, comment, post_id, reviewer FROM feedback WHERE post_id='{$post_id}' UNION SELECT date, value, comment, post_id, reviewer FROM comments WHERE post_id='{$post_id}') AS a ORDER BY a.date DESC LIMIT 20;");
										
					if($queryDouble->num_rows==0){
						echo '<div class="text_clean" style="padding:5px;">No ratings or comments.</div>';
					}
				
					while($rowsDouble=$queryDouble->fetch_assoc()){
						$value = $rowsDouble['value'];
						$reviewer = $rowsDouble['reviewer'];
						$comment = $rowsDouble['comment'];
						$date = $rowsDouble['date'];
						$post_id = $rowsDouble['post_id'];
						$createDate=new DateTime($date);
						$date=date('F jS', strtotime($createDate->format('d.m.Y')));
							
					 	//get name of post
						$queryPost=Database::query("SELECT `name`, `extension`, `id` FROM posts WHERE id='{$post_id}' LIMIT 1");
						while($rowPost = $queryPost->fetch_assoc()){
							$post_name = $rowPost['name'];
							$post_extension = $rowPost['extension'];
						}
						 
						if($value == 200){ //its a comment
						?>
						<div id="post_activity_single">
							<div id="activity_single_right" class="activity_comment">
								<strong><?php echo substr($comment,0,65); if(strlen($comment)>65){echo '...';} ?></strong>
							</div>
							<br style="clear:both;" />
						</div>
						<?php
						} else{
						//its a rating
						?>
						<div id="post_activity_single">
							 <div id="activity_single_right">							
								<?php
								$color = "#EDC812";
								if($value>75){$color="#4EAF0E";}else if($value<50){$color="#E22A12";}
								?>
								<span style="color:<?php echo $color; ?>">
									<div class="left">rated <strong><?php echo $value; ?></span></strong> <span style="font-size:11px;">on <?php echo $date; ?></span></div>
									<br style="clear:both;" />
								</span><br />
								<span style="color:#888; font-size:11px; display:none;">
								<?php
									if(!empty($reviewer)){
										$queryReviewer=Database::query("SELECT `id`, `email` FROM reviewers WHERE id='{$reviewer}'");
										while($rowReviewer=$queryReviewer->fetch_assoc()){
											echo $rowReviewer['email'];
										}
									}
								?>
								</span>
							  </div>
							  <br style="clear:both;" />
						</div>
						<?php
						}
			}
		?>
		</div>
	</div>
	<div id="color_bar">
		<a class="tooltip" return false>
			<span style="background:#4EAF0E;">
				<p class="resp_big"><?php echo round($good, 2); ?>%</p><br />
				<p class="resp_small">of responses are greater than 80</p>
			</span>
			<div class="bar_color" style="width:100%; height:<?php echo $good; ?>%; background:#4EAF0E;"></div>
		</a>
		<a class="tooltip" return false>
			<span style="background:#EDC812;">
				<p class="resp_big"><?php echo round($ok, 2); ?>%</p><br />
				<p class="resp_small">of responses are between 50 and 80</p>
			</span>
			<div class="bar_color" style="width:100%; height:<?php echo $ok; ?>%; background:#EDC812;"></div>
		</a>
		<a class="tooltip" return false>
			<span style="background:#E22A12;">
				<p class="resp_big"><?php echo round($bad, 2); ?>%</p><br />
				<p class="resp_small">of responses are less than 50</p>
			</span>
			<div  class="bar_color" style="width:100%; height:<?php echo $bad; ?>%; background:#E22A12;"></div>
		</a>
 	</div>
 	<div class="hbox_left" style="float:right;">
		<?php $this->add(new View("../pages/hub/includes/new_chart.php", array('post_id' => $post_id))); ?>
	</div>
	<br style="clear:both;" />
</div>
<br style="clear:both;" />
<div id="editContent_<?php echo $post_id; ?>" style="display:none;margin-top:1px; padding:4px;  ">
	<?php
	$post_d = !empty($post_description);
	?>
	<form action="/hub/posts/post_edit.php" method="post">
		<input id="in" type="text" name="name" value="<?php echo $post_name; ?>" style="width:140px;"></input>
		<input id="in" type="text" name="description" value="<?php echo $post_description; ?>" <?php if($post_d){?> placeholder="Description" <?php } ?> style="width:212px;"></input>
		<input type="hidden" name="post_id" value="<?php echo $post_id; ?>" />
		<input class="button2" type="submit" value="Update" />
	</form>
</div>