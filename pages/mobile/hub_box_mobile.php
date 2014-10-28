<?php
$active = $this->getParameter('active');
$post_id = $this->getParameter('post_id');
$post_name = $this->getParameter('post_name');
$post_extension = $this->getParameter('post_extension');
$post_description = $this->getParameter('post_description');
$user_id = $this->getParameter('user_id');
$extension = $this->getParameter('extension');
?>
<style>
#comment_box{
	border-bottom:1px solid #f5f5f5;
	padding-bottom:4px;
}
	
#buttonGraph{
	background: #f9f9f9;
}
.h1_light{
	font-family:helvetica;
}
</style>

<div align="center" id="graphContent_<?php echo $post_id; ?>" style="display:none; margin-top:7px; padding:0px; padding-top:10px; background:#fcfcfc; border:1px solid #dcdcdc;   width:100%; "><?php /*$this->add(new View('../pages/mobile/new_chart_mobile.php')); //Doesn't exist. */ ?></div>

<?php if($active=='yes'){ ?>
<div id="hub_box" style="padding:0px;background:#fff;border: 1px solid #dcdcdc;margin-top:5px; width:100%;">
<?php } else{ ?>
<div id="hub_box" style="padding:0px;background:#fff;border: 1px solid #dcdcdc;margin-top:5px; width:100%; opacity:0.5;">
<?php } ?>
 			<div style="width:100%; background:#f6f6f6; border-bottom:1px solid #dcdcdc; height:40px; padding-bottom:4px;">
 				
 				<center>
 				
 				<div style="float:left; max-width:100px; overflow:hidden;">
                    
                    
                   <?php post_pic('40px','50px',$post_id, $user_id, $post_extension, $extension); ?>
                    
                    
 				</div>
 			
 			
 				<div style="float:left; margin-top:10px; width:90px; margin-top:2px; margin-left:10px; font-family:helvetica;">
 					<span class="h1_light" style="font-size:15px; font-family:helvetica; "><?php echo $post_name; ?></span><br>
 				</div>
 				
 				 <div style="float:left; width:100px; margin-left:15px; margin-top:0px;">
 							<?php
 							//Calculate Average
 							$query2=Database::query("SELECT * FROM feedback WHERE post_id='{$post_id}'");
							
							$count=0;
							$total=0;
							while($rows2=sql_fetch_array($query2)){
  							 $value = @intval($rows2['value']);
  							 $count+=1;
  							 $total=$total+$value;
							}
							$average = 0;
							if($count > 0){
							 $average=$total/$count;
							 $average=round($average, 2);
							}
							?>
 					<span class="h1_light" style="font-size:13px; font-family:helvetica;">Average:</span> <span class="h1_light" style="font-size:15px; color:#bc0101;"><br /><strong><?php echo $average; ?></strong>/100</span>
 				</div>
 				
 				 <div style="float:left; ">
 					<span class="h1_light" style="font-size:13px; font-family:helvetica;">Ratings:<br></span> <span class="h1_light" style="font-size:15px; color:#bc0101; font-weight:bold;"><?php echo $count; ?></span>
 				</div>
 				
 				
 				</center>
 				
 				
 			
 				
 			
 			</div>
 			
 				<div style="background:#f8f8f8; border-top:1px solid #dcdcdc; border-bottom: 1px solid #dcdcdc; width:100%; overflow:hidden;">
 				<center>
 				 <div style="margin-top:3px;">
 				 
 				 	
 				 	<a  id="graphHead_<?php echo $post_id; ?>" data-reveal-id="unlock"><div class="button3" id="buttonGraph"><span id="graphSign_<?php echo $post_id; ?>">+</span> Graph</div></a>
 				  			<script type="text/javascript">
			$(document).ready(function(){
			$("#graphHead_<?php echo $post_id; ?>").click(function(){
			$("#graphContent_<?php echo $post_id; ?>").slideToggle();
			if ($("#graphSign_<?php echo $post_id; ?>").text() == "+"){
			$("#graphSign_<?php echo $post_id; ?>").html("-")
			}
			else {
			$("#graphSign_<?php echo $post_id; ?>").text("+")
			}
			});
			});
			</script>
 				 	
 				
 					
 				
 					<a  id="editHead_<?php echo $post_id; ?>" data-reveal-id="unlock"><div class="button3">Edit</div></a>
 				  			<script type="text/javascript">
			$(document).ready(function(){
			$("#editHead_<?php echo $post_id; ?>").click(function(){
			$("#editContent_<?php echo $post_id; ?>").slideToggle();
			if ($("#editSign_<?php echo $post_id; ?>").text() == "+"){
			$("#editSign_<?php echo $post_id; ?>").html("-")
			}
			else {
			$("#editSign_<?php echo $post_id; ?>").text("+")
			}
			});
			});
			</script>
		
 				
 	
 					
 					
 					<a href="/profile/profile.php"><div class="button3" style="display:none;">Edit</div></a>
			<?php if($active=='yes'){ ?>
 					<a href="/hub/posts/post_activate.php?id=<?php echo $post_id; ?>&yes=no"><div class="button3" style="">Hide</div></a>
 					<?php } else{ ?>
 					<a href="/hub/posts/post_activate.php?id=<?php echo $post_id; ?>&yes=yes"><div class="buttonglow2" style="">Show</div></a>
 					<?php } ?>
 					<?php if($count!=0){ ?>
 					<div class="button2" style="opacity:0.25; background:#dc0101;display:none;">Reset</div>
 					<?php } else{ ?>
 					
 				
 					
 					
 					<?php } ?>
 					<a href="/overall/generic_delete.php?db=posts&id=<?php echo $post_id; ?>"><div class="buttonglow" style="">Delete</div></a>	
 				</div>
 				</center>
 				
 				</div>
 			
		
 			<div style="float:left; margin-left:10px; font-size:12px; color:#444; padding-bottom:10px; height:150px; overflow:scroll;">
			
			
			 	<div id="editContent_<?php echo $post_id; ?>" style="display:none; margin-top:10px; padding:4px; border:1px solid #dcdcdc; border-top:0px; background:#f7f7f7; width:692px; margin-top:-3px;">
				<?php
				$post_d=!empty($post_description);
				?>
				<form action="/hub/posts/post_edit.php" method="post">
				<input id="in" type="text" name="name" value="<?php echo $post_name; ?>" style="width:100px;"></input>
				<input id="in" type="text" name="description" value="<?php echo $post_description; ?>" <?php if($post_d){?> placeholder="Description" <?php } ?> style="width:100px;"></input>
				<input type="hidden" name="post_id" value="<?php echo $post_id; ?>" />
				<input class="button2" type="submit" value="Update" />
				</form>
			</div>
			
 			<div style="float:left; margin-top:4px; width:100%; display:none;">
 				<span class="h1_light" style="font-size:13px;">Resets:</span> <br>
 				</div>
 							<?php
 							/*
 							//Comments
 							$query3=Database::query("SELECT * FROM resets WHERE post_id='{$post_id}' ORDER BY id DESC");
							if(sql_num_rows($query3=0){
							echo "No Resets";
							}
							while($rows=sql_fetch_array($query3)){
  							 $value = $rows3['value'];
  							 $vote=$rows3['votes'];
  							 $date = $rows3['date'];
  							 */
							?>

 				<div id="comment_box" style="float:left; width:100%; margin-top:5px; display:none; ">
 					<div style="float:left; width:80px; color:#777;">
 					<?php echo $date; ?>
 					</div>
 					<div style="float:left; width:395px; margin-left:15px;">
 					Average of <?php echo $value; ?> in <?php echo $votes; ?> votes.
 					</div>
 				</div>
 				
 				  		<?php //}
 				  		?>
 	
 	
 				<div style="float:left; margin-top:4px; width:100%;">
 				<span class="h1_light" style="font-size:13px; font-family:helvetica;">Comments:</span> <br>
 				</div>
 							<?php
 							//Comments
 							$query3=Database::query("SELECT * FROM comments WHERE post_id='{$post_id}' ORDER BY id DESC");
							if($query3->num_rows==0){
								echo "<br style='clear:both;'>No Comments";
							}
							while($rows3=$query3->fetch_assoc()){
  							 $c_id=$rows3['id'];
  							 $date = $rows3['date'];
  							 $comment = $rows3['comment'];
  							 $country=$rows3['country'];
							?>

 				<div id="comment_box" style="float:left; width:100%; margin-top:5px; font-family:helvetica;">
 					<div style="float:left; width:70%; margin-left:5px;">
 					<?php echo $comment; ?> &nbsp;
 					</div>
 					 <div style="float:left; width:20px; margin-left:15px;">
 					<a href="/overall/generic_delete.php?db=comments&id=<?php echo $c_id; ?>" style="text-decoration:none; font-size:13px;"><strong>x</strong></a>
 					</div>
 				</div>
 				
 				  		<?php }?>
 				  		
 				  		
 				  		 				<div style="float:left; margin-top:4px; width:100%;">
 				<span class="h1_light" style="font-size:13px; font-family:helvetica;">Rating History:</span> <br>
 				</div>
 							<?php
 							//Comments
 							$query3=Database::query("SELECT * FROM feedback WHERE post_id='{$post_id}' ORDER BY id DESC");
							if($query3->num_rows==0){
							echo "<br style='clear:both;'>No Ratings";
							}
							while($rows=$query3->fetch_assoc()){
  							 $f_id=$rows3['id'];
  							 $date = $rows3['date'];
  							 //$date = trtotime(date('Y-m-d', $date));
  							 $value = $rows3['value'];
  							 $country=$rows3['country'];
							?>

 				<div id="comment_box" style="float:left; width:100%; margin-top:5px; border-bottom:0px;">
 					
 					<div style="float:left; width:90px; margin-left:0px;">
 					<strong><?php echo $value; ?></strong>/100 &nbsp;
 					</div>
 					<div style="float:left; width:150px; color:#777;">
 					<?php echo $date; ?>
 					</div>
 					

 				</div>
 				
 				  		<?php }?>
 				  		
 				  		
 				  		
 				
 			</div>
 			
 			
 			
 			
 			<br style="clear:both;" />
 		
 		</div>

 			<br style="clear:both;" />