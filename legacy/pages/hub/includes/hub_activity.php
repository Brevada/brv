<?php
$this->addResource('/css/hub_activity.css');

$user_id = $this->getParameter('user_id');
$extension = $this->getParameter('extension');
?>
<!-- Recent Activity -->
 		<div id="activity_box" style="float:left; height:315px;">
 			<div id="activity_header" style="">
 			 	 <div style="float:left;">	
 					<div id="activity_head_text" style="font-size:12px;">News Feed</div>
 				 </div>
 			 	<br style="clear:both;" />
 			</div>
 			<?php
				//Calculate Average
				$query2 = Database::query("SELECT `value`, `user_id` FROM `feedback` WHERE `user_id`='{$user_id}'");
				
				$count=0; $total=0; $good=0; $ok=0; $bad=0;
				
				while($rows2=$query2->fetch_assoc()){
					$value = @intval($rows2['value']);
					$count++;
					$total+=$value;
					if($value>=80){$good++;}else if($value<50){$bad++;}else{$ok++;}
				}
				
				$average = 0;
				
				if($count > 0){
					$user_average=round($total/$count, 2);
					$user_good=($good/$count)*100;
					$user_ok=($ok/$count)*100;
					$user_bad=($bad/$count)*100;
				 }
 			?>
 			
 			
 			<div id="activity_main_left">
 					<?php
					$query = Database::query("SELECT * FROM (SELECT date, value, comment, post_id, reviewer FROM feedback WHERE user_id='{$user_id}' UNION SELECT date, value, comment, post_id, reviewer FROM comments WHERE user_id='{$user_id}') AS a ORDER BY a.date DESC LIMIT 20;");
										
					if($query->num_rows==0){
						echo '<div class="text_clean" style="padding:5px;">No recent activity.</div>';
					}
									
					while($row=$query->fetch_assoc()){
						$value = $row['value'];
						$reviewer = $row['reviewer'];
						$comment = $row['comment'];
						$date = $row['date'];
						$post_id = $row['post_id'];
						$createDate=new DateTime($date);
						$date=date('F jS', strtotime($createDate->format('d.m.Y')));
							
					 	//get name of post
						$queryPost=Database::query("SELECT `id`, `name`, `extension` FROM posts WHERE id='{$post_id}' LIMIT 1");
						
						$post_name = '';
						$post_extension = '';
						
						while($rowPost=$queryPost->fetch_assoc()){
							$post_name=$rowPost['name'];
							$post_extension=$rowPost['extension'];
						}
						 
						if($value==200){
						//its a comment
					?>
					<div id="activity_single">
					<div id="activity_single_left" class="activity_pic">	
						<?php post_pic('30px','auto', $post_id, $user_id, $post_extension, $extension); ?>
					</div>
					<div id="activity_single_left" class="activity_name">	
						<?php echo substr($post_name,0,15); if(strlen($post_name)>15){echo '...';} ?>
					</div>
					  <div id="activity_single_right" class="activity_comment">							
						<strong><?php echo substr($comment,0,75); if(strlen($comment)>75){echo '...';} ?></strong>
					  </div>
					   <div id="activity_single_date">							
						<?php echo $date; ?>
					  </div>
					  <br style="clear:both;" />
					</div>
					<?php
					} else {
					//its a rating
					?>
					<div id="activity_single">
						  	<div id="activity_single_left" class="activity_pic">	
								<?php post_pic('30px','auto',$post_id, $user_id, $post_extension, $extension); ?>	
							</div>
							<div  id="activity_single_left" class="activity_name">	
								<?php echo substr($post_name,0,15); if(strlen($post_name)>15){echo '...';} ?>
							</div>
						 <div id="activity_single_right">							
							<?php
							if($value>75){$color="#4EAF0E";}else if($value<50){$color="#E22A12";}else{$color="#EDC812";}
							?>
							<span style="color:<?php echo $color; ?>">
									<div class="left">was rated <strong><?php echo $value; ?></strong></div>
									<br style="clear:both;" />
							</span>
							<br />
							<span style="color:#888; font-size:11px; display:none;">
							<?php
								if(!empty($reviewer)){
									$queryReviewer=Database::query("SELECT `id`, `email` FROM reviewers WHERE `id`='{$reviewer}'");
									while($rowsReviewer=$queryReviewer->fetch_assoc()){
										echo $rowsReviewer['email'];
									}
								}
							?>
							</span>
						  </div>
						  <div id="activity_single_date"><?php echo $date; ?></div>
						  <br style="clear:both;" />
					</div>
					<?php
				}
			}
		?>
				</div>
			<br style="clear:both;" />
 		</div>
 		<div id="activity_box" style="float:left; height:315px; width:10px; margin-left:2px;">
			<a class="tooltip" return false>
			<span style="background:#4EAF0E;">
				<p class="resp_big"><?php echo round($user_good, 2); ?>%</p><br />
				<p class="resp_small">of all responses are greater than 80</p>
			</span>
			<div class="bar_color" style="width:100%; height:<?php echo $user_good; ?>%; background:#4EAF0E;"></div>
			</a>
			<a class="tooltip" return false>
			<span style="background:#EDC812;">
				<p class="resp_big"><?php echo round($user_ok, 2); ?>%</p>
				<br />
				<p class="resp_small">of all responses are between 50 and 80</p>
			</span>
			<div class="bar_color" style="width:100%; height:<?php echo $user_ok; ?>%; background:#EDC812;">
			</div>
			</a>
			<a class="tooltip" return false>
			<span style="background:#E22A12;">
				<p class="resp_big"><?php echo round($user_bad, 2); ?>%</p><br />
				<p class="resp_small">of all responses are less than 50</p>
			</span>
			<div  class="bar_color" style="width:100%; height:<?php echo $user_bad; ?>%; background:#E22A12;"></div>
			</a>
 		</div>
 		<br style="clear:both;" />
 		<br style="clear:both;" />