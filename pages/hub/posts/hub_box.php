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
$level = $this->getParameter('level');
?>
<div class="hbox<?php echo $active=='yes' ? '' : ' hbox-inactive'; ?> hbox_closed" id="hbox<?php echo $post_id; ?>">
<?php
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
	<div class="hbox_left">
		<a id="editHead" class='editHead' post-id='<?php print $post_id; ?>' data-reveal-id="unlock">
		<div class="hubbox_button_left">
			<img class="hubbox_button_img" src="<?php echo p('HTML','path_hubicons','edit.png'); ?>" />
		</div>
		</a>
		
		<a href="<?php echo f('HTML','path_hub_posts'); ?>post_activate.php?id=<?php print $post_id; ?>&yes=<?php echo $active=='yes' ? 'no' : 'yes'; ?>">
		<div class="hubbox_button_left">
			<img class="hubbox_button_img" src="<?php echo p('HTML','path_hubicons','eye.png'); ?>" />
		</div>
		</a>
		
		<a href="/user_data/qr_posts/<?php echo $post_id; ?>.png" class="tooltip" target="_BLANK" style="text-decoration:none;">
		<div class="hubbox_button_left">
			<img class="hubbox_button_img" src="<?php echo p('HTML','path_hubicons','qr.png'); ?>" />
		</div>
		</a>
		<div class="hubbox_button_left delete_post" post-id='<?php echo $post_id; ?>' style="background:#ff2b2b;">
			<img class="hubbox_button_img" src="<?php echo p('HTML','path_hubicons','x.png'); ?>" />
		</div>
	</div>
		
	<div class="hbox_left hbox_name">
		
		<div id="hbox_titles<?php echo $post_id; ?>" class='hbox_titles' post-id='<?php echo $post_id; ?>' class="left" style="margin-left:5px;">
			<strong><?php echo substr($post_name,0,25); if(strlen($post_name)>25){echo '...';} ?></strong>
			<br />	
			<font class="hbox_description"><?php echo substr($post_description,0,25); if(strlen($post_description)>25){echo '...';} ?></font>
		</div>
		
		<!-- EDIT POST -->
		<div id="editContent_<?php echo $post_id; ?>" post-id='<?php echo $post_id; ?>' style="display:none;">
			<?php
			$post_d = !empty($post_description);
			?>
			<form action="/hub/posts/post_edit.php" method="post">
				<input class="in" type="text" name="name" value="<?php echo $post_name; ?>" style="width:150px; font-weight:bold;"></input>
				<br />
				<input class="in" class="hbox_description" type="text" name="description" value="<?php echo $post_description; ?>" <?php if($post_d){?> placeholder="Description" <?php } ?> style="width:150px;"></input>
				<input type="hidden" name="post_id" value="<?php echo $post_id; ?>" />
				<input class="button2" type="submit" value="Update" />
			</form>
		</div>
		<!-- END EDIT POST -->
			<br style="clear:both;" />
			<?php if($level>1){ ?>
				<!-- ACTIVE ACCOUNT, DISPLAY RESULTS -->	
				<div class="hbox_average" class="left">
					<?php
						$color = "#EDC812";
						if($average>75){$color="#4EAF0E";}else if($average<50){$color="#E22A12";}
					?>
					<span style="color:<?php echo $color; ?>;"><span style="font-weight:bold; font-size:50px;"><?php echo $average; ?></span> 
					<br />
					Average in <?php echo $count; ?> ratings.</span>
					<br />
					
				</div>
			<?php } else { ?>
				<!-- INACTIVE ACCOUNT, HIDE RESULTS -->
				<div class="hbox_average" class="left">
					<strong>Upgrade account</strong> to view your results.
				</div>
			<?php } ?>
			<br style="clear:both;" />
	</div>
	
	<?php if($level>1){ ?>
			<!-- ACTIVE ACCOUNT, DISPLAY EXPAND -->	
			<div class="toggle_width" post-id='<?php echo $post_id; ?>'>&gt;</div>
	<?php } else { ?>
			<!-- INACTIVE ACCOUNT, HIDE EXPAND -->
			<div class="toggle_width" style="cursor:not-allowed;">&gt;</div>
	<?php } ?>
	<div class='hbox_right'>
	<?php
	$queryDouble = Database::query("SELECT * FROM (SELECT date, value, comment, post_id, reviewer FROM feedback WHERE post_id='{$post_id}' UNION SELECT date, value, comment, post_id, reviewer FROM comments WHERE post_id='{$post_id}') AS a ORDER BY a.date DESC LIMIT 20;");
	if($queryDouble->num_rows==0){
		echo '<div class="no_feedback" style="padding:5px;">No ratings or comments.</div>';
	} else { ?>
	<div class="hbox_news">
		<div class="post_activity_main_left">
 					<?php
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
						if(empty($comment)){continue;}
						?>
						<div class="post_activity_single">
							<div class="activity_comment activity_single_right">
								<strong><?php echo substr($comment,0,65); if(strlen($comment)>65){echo '...';} ?></strong>
							</div>
						</div>
						<?php
						} else{
						if(empty($value)){continue;}
						//it's a rating
						?>
						<div class="post_activity_single">
							 <div class="activity_single_right">						
								<?php
								$color = "#EDC812";
								if($value>75){$color="#4EAF0E";}else if($value<50){$color="#E22A12";}
								?>
								<span style="color:<?php echo $color; ?>">
									<div class="left">rated <strong><?php echo $value; ?></span></strong> <span style="font-size:11px;">on <?php echo $date; ?></span></div>
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
						</div>
						<?php
						}
			}
		?>
		</div>
	</div>
	<?php } ?>
	<?php if($count > 0){ ?>
	<div class="color_bar">
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
 	<!--<div class="hbox_left" style="float:right; display:none;">
		<?php /*$this->add(new View("../pages/hub/includes/new_chart.php", array('post_id' => $post_id)));*/ ?>
	</div>-->
	<?php } ?>
	</div>
</div>
