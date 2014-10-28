<?php 
$this->addResource('/css/layout.css');
$this->addResource('/css/mobile/profile.css');

//GET COUNTRY
$geo = Brevada::GetGeo();
$country = $geo['country'];
$ip = $geo['ip'];

$this->addResource("<meta name='viewport' content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0'/>", true, true);

$url_name =$_GET['name'];
$query=Database::query("SELECT * FROM users WHERE url_name = '{$url_name}' LIMIT 1");
if($query->num_rows==0){
	//No user exists
}
while($rows=$query->fetch_assoc()){
   $user_id=$rows['id'];
   $name = $rows['name'];
   $type = $rows['type'];
   $user_extension = $rows['extension'];
}
?>
<div id="top_bar">
	<div class="container">
	 <img id="logo" src="/images/quote.png" />
    </div>
</div>
<div id="info_bar">
		<div id="user_logo"><?php post_pic('100px','auto','0', $user_id, 'NONE', $user_extension); ?></div>
        <div id="name"><?php echo $name; ?></div>
        <div id="description"><?php echo $type; ?></div>
        <div id="buttons">
        	<div class="button4" id="suggestions_button">Suggestions</div>
        </div>
        <!-- SUGGESTIONS -->
				<script>
                    $(document).ready(
                        function(){
                            $("#suggestions_button").click(function () {
                                $("#suggestion_box").show("slow");
                            });
                    
                        });
                    </script>
        
					<div id="suggestion_box" style="display:none;">
						<form  action="/overall/insert/insert_message.php"  method="post">
							<input type="hidden" name="userid" id="ipaddress<?php echo $post_id; ?>" value="<?php echo $user_id; ?>" />
							<textarea class="inp" id="suggestion" placeholder="General suggestions or comments" class="ta" style="width:100%;"></textarea>
							<div class="button4" onclick="SubmitFormSuggestion(), close_suggestion(), thanks_suggestion();" style="width:100%; height:30px; line-height:30px;">Submit Suggestions</div>
						</form>
                    </div> 
                   <script>

					function SubmitFormSuggestion() {
					var suggestion=$("#suggestion").val();
					$.post("/overall/insert/insert_message.php?userid=<?php echo $user_id; ?>", { message: suggestion},
					   function(data) {
							close_suggestion();
					   });
					}

					function clearContents(element) {
					  element.value='';
					}
					
					function close_suggestion() {
						document.getElementById("suggestion_box").style.display='none';
						
					}
					function thanks_suggestion() {
						document.getElementById("thanks_suggestion").style.display='block';
						
					}
					</script>
</div>
			<?php
			$query=Database::query("SELECT * FROM posts WHERE user_id='{$user_id}' AND active='yes' ORDER BY id DESC");
			
			$num=0;
			while($row=$query->fetch_assoc()){
  			$post_id=$rows['id'];
   			$post_name=$rows['name'];
   			$post_extension=$rows['extension'];
   			$post_description=$rows['description'];
   			
			$check=Database::query("SELECT * FROM feedback WHERE ip_addres = '{$ip}' AND post_id='{$post_id}' ORDER BY id DESC");
  			$check_rate=$check->num_rows;
			?>
            <div id="info_bar">
                    <div id="post_logo">
						<?php post_pic('50px','auto',$post_id, $user_id, $post_extension, $user_extension); ?>
                    </div>
                    <div id="post_info">
                        <div id="name"><?php echo $post_name; ?></div>
                        <div id="description"><?php echo $post_description; ?></div>
                    </div>
                    <br style="clear:both;" />
                    <div id="face_holder" style="padding-top:12px;">
									<?php if($check_rate==0){ ?>
                                        <div id="buttons<?php echo $post_id; ?>">
                                        <div onclick="rate<?php echo $post_id; ?>('20')"  class="button4" id="face_button" style="">
                                        <img id="face" src="<?php echo p('HTML','path_images', 'cry.png'); ?>" />
                                        </div>
                                        
                                        <div onclick="rate<?php echo $post_id; ?>('45')" class="button4" id="face_button" style="">
                                        <img id="face" src="<?php echo p('HTML','path_images', 'sad.png'); ?>" />
                                        </div>
                                        
                                        <div onclick="rate<?php echo $post_id; ?>('63')" class="button4" id="face_button" style="">
                                        <img id="face" src="<?php echo p('HTML','path_images', 'medium.png'); ?>" />
                                        </div>
                                        
                                        <div onclick="rate<?php echo $post_id; ?>('84')" class="button4" id="face_button" style="">
                                        <img id="face" src="<?php echo p('HTML','path_images', 'happy.png'); ?>" />
                                        </div>
                                        
                                        <div onclick="rate<?php echo $post_id; ?>('100')" class="button4" id="face_button" style="">
                                        <img id="face" src="<?php echo p('HTML','path_images', 'love.png'); ?>" />
                                        </div>
                                        
                                        <br style="clear:both;" />
                                        </div>
                                        
                                        <div id="thanks_<?php echo $post_id; ?>"  style="display:none;	 width:100%; font-size:11px; color:#dc0101; font-weight:bold; font-family:verdana;">Thanks for rating.</div>
                    
                                    <?php } else{ ?>
                                        <div id="thanks_<?php echo $post_id; ?>"  style="width:100%; font-size:11px; color:#dc0101; font-weight:bold; font-family:verdana;">Thanks for rating.</div>
                                    <?php } ?>
                                    
                                </div>
                     
                     <br style="clear:both;" />
                    
                    <div class="commentButton" id="commentButton<?php echo $post_id; ?>">Comment</div>
                    
                    <br style="clear:both;" />
                    
                    <!-- COMMENT BOX -->
                    
                     		<script>
							$(document).ready(
								function(){
									$("#commentButton<?php echo $post_id; ?>").click(function () {
										$("#post_box_comment_<?php echo $post_id; ?>").show("slow");
									});
							
								});
							</script>
                            
                            
                    <div id="post_box_comment_<?php echo $post_id; ?>" style="display:none;">
                    
							<script>
                            
                            function SubmitForm<?php echo $post_id; ?>() {
                                var comment<?php echo $post_id; ?>=$("#comment<?php echo $post_id; ?>").val();
                                if (comment<?php echo $post_id; ?> != "Comment (type here)") {
                                    $.post("/overall/insert/insert_comment.php?postid=<?php echo $post_id; ?>&country=<?php echo $country; ?>&ipaddress=<?php echo $ip; ?>&user_id=<?php echo $user_id; ?>", { comment: comment<?php echo $post_id; ?>},
                                    function(data) {});
                                    comment_disappear_<?php echo $post_id; ?>();
                                }
                            }
                            
                            function clearContents(element) {
                              element.value='';
                            }
                            </script>
                            
                                <form  action="<?php echo $path; ?>insert_comment.php"  method="post">
                                    <textarea name="comment" align="left" id="comment<?php echo $post_id; ?>" class="ta" placeholder="Comment on <?php echo $post_name; ?>"></textarea>
                                    <input type="hidden" name="ipaddress" id="ipaddress<?php echo $post_id; ?>" value="11111" />
                                
                                    <input id="submit_button"  type="button" onclick="SubmitForm<?php echo $post_id; ?>()"  value="Submit Comment" class="button4"  style="width:100%; height:30px; line-height:20px; outline:none; margin-top:-3px;  text-align:left;">
                                </form>
                                
                            <script type="text/javascript">
                            
                            function comment_disappear_<?php echo $post_id; ?>() {
                                document.getElementById("post_box_comment_<?php echo $post_id; ?>").style.display='none';
                            }
                            </script>
            		
            		</div>
                    
            </div>
            
            
            
            <!-- SCRIPTS -->
            
<script type="text/javascript">

function hideRating_<?php echo $post_id; ?>() {
	document.getElementById("buttons<?php echo $post_id; ?>").style.display='none';
}
function showThanks_<?php echo $post_id; ?>() {
	document.getElementById("thanks_<?php echo $post_id; ?>").style.display='block';
}
				
				function rate<?php echo $post_id; ?>(val){
					 hideRating_<?php echo $post_id; ?>(),
					 showThanks_<?php echo $post_id; ?>(),
					 <?php $path='/overall/insert/'; ?>
					$.post("<?php echo $path; ?>insert_rating.php?postid=<?php echo $post_id; ?>&ipaddress=<?php echo $ip; ?>&country=<?php echo $country; ?>", { value: val, user_id: '<?php echo $user_id; ?>'},
				  function(data) {
				
				   });
				
				}
				
				function submitRating<?php echo $post_id; ?>() {
				var rating<?php echo $post_id; ?>=$("#rating_<?php echo $post_id; ?>").val();
				
				if(rating<?php echo $post_id; ?>!=101){
				 hideRating_<?php echo $post_id; ?>(),
				 showThanks_<?php echo $post_id; ?>(),
				$.post("/overall/insert/insert_rating.php?postid=<?php echo $post_id; ?>&ipaddress=<?php echo $ip; ?>&country=<?php echo $country; ?>", { value: rating<?php echo $post_id; ?>, user_id: '<?php echo $user_id; ?>'},
				  function(data) {
				
				   });
				 }  
				  
				}
</script>
            <?php } ?>