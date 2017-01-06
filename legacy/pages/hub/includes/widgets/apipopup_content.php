<?php
$this->addResource('/css/apipopup_content.css');

$url_name=$this->getParameter('url_name');
$user_id=$this->getParameter('user_id');
?>
<br style="clear:both;" />
		<div style="width:100%;">
		<div class="option_holder" style="float:left;">
			<iframe id="frame1" onload="iframeLoaded1()" src="http://brevada.com/mobile/profile_mobile.php?name=<?php echo $url_name; ?>" frameBorder="0" style=""></iframe>
			<script type="text/javascript">
  			function iframeLoaded1() {
				var iFrameID=$('#frame1');
				if(iFrameID) {
					// here you can make the height, I delete it first, then I make it again
					iFrameID.height="";
					iFrameID.height=iFrameID.contentWindow.document.body.scrollHeight + "px";
				}  
			}
			</script>
		</div>
		
		<div id="api_side_text">
		Copy and paste this code onto your website to include the rating widget for all your posts.
		<textarea id="api_frame"><iframe id="frame1" onload="iframeLoaded1()" src="http://brevada.com/mobile/profile_mobile.php?name=<?php echo $url_name; ?>" frameBorder="0"></iframe></textarea>
		</div>	
		
		<br style="clear:both;" />
		
		</div>
		
		<div style="width:100%;">
		
		<div class="option_holder" style="float:left; width:10px;">
		</div>
		
		<div id="api_side_text">
		Copy and paste this code into an HTML email, this Widget is <strong>compatible with email clients</strong>.
		
		<textarea id="api_frame" style="width:500px;">
					
					<style>
					#rad{
						width:9px;
					}
					input[type=radio]:checked + span { 
						opacity:0.7;
					}
					.overflow{
						text-overflow: ellipsis;
						width: 250px;
						height:60px;
						white-space: nowrap;
						overflow: hidden;
					}
					</style>
					
					<form id="form" method="get" action="http://brevada.com/email_friendly_insert.php" >
						
						<!-- EDIT HERE -->
						<input name="email" value="PUT EMAIL HERE" type="hidden" />
						<!-- -->
						
						
						<input name="user_id" value="<?php echo $user_id; ?>" type="hidden" />
						
						<div id="whole_container" style="padding:6px; border:1px solid #dcdcdc; background:#f8f8f8;">
					
							
							<!-- Loop for each post -->
							<?php
								$query=Database::query("SELECT * FROM posts WHERE user_id='{$user_id}' AND active='yes' ORDER BY id DESC");
								
								while($rows=$query->fetch_assoc()){
								  $post_id=$rows['id'];
								  $post_name=$rows['name'];
								  $post_description=$rows['description'];
								  
							?>
							<div id="line_holder" style="padding:5px; margin:5px; background:#eee; font-size:12px; color:#555; font-family:helvetica;">
						
							<div style="float:left; padding:6px; 
								text-overflow: ellipsis;
								width: 150px;
								white-space: nowrap;
								overflow: hidden;
							">
								<strong><?php echo $post_name; ?></strong>
								<br />
								<?php echo $post_description; ?>
							</div>
							
							
								<input id="rad" type="radio" name="r<?php echo $post_id; ?>" value="101" style="display:none;"/>
							
								<div style="float:left; padding:6px; line-height:25px; ">
							<label style="cursor:pointer; margin-left:10px;">
								<input id="rad" type="radio" name="r<?php echo $post_id; ?>" value="20" style="width:6px; background:#cd0000;"/>
								<span style="
								padding:5px; margin-left:3px; font-size:12px; font-family:helvetica; color:#555;
								border:1px solid #dcdcdc;
							
								text-align:center;
							
								-webkit-border-radius: 2px;
								-moz-border-radius: 2px;
								border-radius: 2px;
							
								background: #f8f8f8;">
								Very Bad
								</span>
							</label>
							</div>
								<div style="float:left; padding:6px; line-height:25px; ">
								<label style="cursor:pointer; margin-left:6px;">
									<input id="rad" type="radio" name="r<?php echo $post_id; ?>" value="50" style="width:6px; background:#cd0000;"/>
									<span style="
									padding:5px; margin-left:3px; font-size:12px; font-family:helvetica; color:#555;
									border:1px solid #dcdcdc;
							
									text-align:center;
							
									-webkit-border-radius: 2px;
									-moz-border-radius: 2px;
									border-radius: 2px;
							
									background: #f8f8f8;">
									Bad
									</span>
								</label>
								</div>
								<div style="float:left; padding:6px; line-height:25px; ">
								<label style="cursor:pointer; margin-left:6px;">
									<input id="rad" type="radio" name="r<?php echo $post_id; ?>" value="65" style="width:6px; background:#cd0000;"/>
									<span style="
									padding:5px; margin-left:3px; font-size:12px; font-family:helvetica; color:#555;
									border:1px solid #dcdcdc;
							
									text-align:center;
							
									-webkit-border-radius: 2px;
									-moz-border-radius: 2px;
									border-radius: 2px;
							
									background: #f8f8f8;">
									Ok
									</span>
								</label>
								</div>
								<div style="float:left; padding:6px; line-height:25px; ">
								<label style="cursor:pointer; margin-left:6px;">
									<input id="rad" type="radio" name="r<?php echo $post_id; ?>" value="80" style="width:6px; background:#cd0000;"/>
									<span style="
									padding:5px; margin-left:3px; font-size:12px; font-family:helvetica; color:#555;
									border:1px solid #dcdcdc;
							
									text-align:center;
							
									-webkit-border-radius: 2px;
									-moz-border-radius: 2px;
									border-radius: 2px;
							
									background: #f8f8f8;">
									Good
									</span>
								</label>
								</div>
								<div style="float:left; padding:6px; line-height:25px; ">
								<label style="cursor:pointer; margin-left:6px;">
									<input id="rad" type="radio" name="r<?php echo $post_id; ?>" value="100" style="width:6px; background:#cd0000;"/>
									<span style="
									padding:5px; margin-left:3px; font-size:12px; font-family:helvetica; color:#555;
									border:1px solid #dcdcdc;
							
									text-align:center;
							
									-webkit-border-radius: 2px;
									-moz-border-radius: 2px;
									border-radius: 2px;
							
									background: #f8f8f8;">
									Excellent
									</span>
								</label>
								</div>
						
						
							<br style="clear:both;" />
						
							</div>
							 
							
							<?php
								
								}
							
							?>
							
						
							<!-- -->
							
							
							<input type="submit" value="Submit Your Ratings" style="padding:5px; margin:5px; border:1px solid #cd0000; background:#f8f8f8; color:#cd0000; font-family:helvetica; font-size:12px; font-weight:bold; cursor:pointer; width:140px; text-align:center;" />
					
							</form>
							
							<div class="text_clean" style="margin:5px;">
								<!-- EDIT HERE -->
								<a href="http://brevada.com/profile.php?name=<?php echo $url_name; ?>&reviewer=PUT EMAIL HERE" target="_TOP">Not Working?</a>
								<!-- -->	
							</div>
							
							
							<div style="width:150px; text-align:center; margin:0 auto; font-size:11px; font-family:helvetica; padding:5px;">
							Powered by <a href="http://brevada.com"><span style="color:#cd0000; font-weight:bold;">Brevada</span></a>
							</div>
							
						
						</div>
						
						
			</textarea>
		
		</div>	
		
		<br style="clear:both;" />
		
		</div>
		
		<br style="clear:both;" />
		
		<div style="width:100%; margin-top:10px;">
		
		<div class="option_holder" style="float:left; width:320px;">
			<a href="http://brevada.com/<?php echo $url_name; ?>" target="_top"><img src="http://brevada.com/give_feedback.png" style="width:280px;" /></a>
		</div>
		
		
		<div id="api_side_text">
		Copy and paste this code onto your website to include this link to your page.
		<textarea id="api_frame"><a href="http://brevada.com/<?php echo $url_name; ?>" target="_top"><img src="http://brevada.com/give_feedback.png" style="width:280px;" /></a></textarea>
		</div>	
		
		<br style="clear:both;" />
		
		</div>
		
		<br />
		
		<?php
		$query=Database::query("SELECT * FROM posts WHERE user_id='{$user_id}' ORDER BY id DESC");
		while($rows=$query->fetch_assoc()){
		$post_id=$rows['id'];
		$active=$rows['active'];
		$post_name=$rows['name'];
		$post_extension=$rows['extension'];
		$post_description=$rows['description'];
		?>
		<div style="width:100%;">
		
		<div class="option_holder" style="float:left;">
			<iframe id="frame<?php echo $post_id; ?>" class='frame' onload="iframeLoaded<?php echo $post_id; ?>()" src="http://brevada.com/mobile_single.php?id=<?php echo $post_id; ?>" frameBorder="0" style=""></iframe>
<script type="text/javascript">
  	function iframeLoaded<?php echo $post_id; ?>() {
    		var iFrameID=$('#frame<?php echo $post_id; ?>');
    	if(iFrameID) {
        // here you can make the height, I delete it first, then I make it again
        iFrameID.height="";
        iFrameID.height=iFrameID.contentWindow.document.body.scrollHeight + "px";
    }  
  }
</script>
		</div>
		
		<div id="api_side_text">
		Copy and paste this code onto your website to include the rating widget for <strong><?php echo $post_name; ?></strong>.
		<textarea id="api_frame"><iframe id="frame<?php echo $post_id; ?>" onload="iframeLoaded<?php echo $post_id; ?>()" src="http://brevada.com/mobile_single.php?id=<?php echo $post_id; ?>" frameBorder="0" style=""></iframe></textarea>
		</div>
		
		
		</div>
		<br />
		
		<?php
	}
	?>
		<br style="clear:both;" />