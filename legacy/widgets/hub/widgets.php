<?php
$user_id = $_SESSION['user_id'];

$user = user($user_id);
$user_id = $user['id'];
$url_name = $user['url_name'];
$level = $user['level'];

if($level>2){ 
?>
<br style="clear:both;" />
		<div style="width:100%;">
        
        <div class="option">
        
        	<div class="option_left">
            	<img class="option_image" src="/images/widgets/widget1.png" />
            </div>
            
            <div class="option_right">	
                <div class="option_title">
                	<strong>The Feedback Page:</strong> Copy and paste this code onto your website.
                </div>
                <br />
		<textarea class="option_ta" onclick='this.select();'><iframe id="frame1" onload="iframeLoaded1()" src="http://brevada.com/mobile/profile/profile.php?name=<?php echo $url_name; ?>" frameBorder="0"></iframe></textarea>	
            </div>
            <br style="clear:both;" />
        </div>
        <div class="option">
        	<div class="option_left">
            	<img class="option_image" src="/images/widgets/widget2.png" />
            </div>
            
            <div class="option_right">	
                <div class="option_title">
                	<strong>Email Gathering:</strong> Copy and paste this code into an HTML email - those who recieve it can give you feedback on the products, services, or other aspects of your business right from the email!
                </div>
                <br />
		<textarea class="option_ta" onclick='this.select();'><style>#rad{width:9px;} input[type=radio]:checked + span { opacity:0.7;} .overflow{text-overflow: ellipsis;width: 250px;height:60px;white-space: nowrap;overflow: hidden;}</style><form id="form" method="get" action="http://brevada.com/overall/public/email_friendly_insert.php" ><!-- EDIT HERE --><input name="email" value="PUT EMAIL HERE" type="hidden" /><!-- --><input name="user_id" value="<?php echo $user_id; ?>" type="hidden" /><div id="whole_container" style="padding:6px; border:1px solid #dcdcdc; background:#f8f8f8;"><!-- Loop for each post --><?php $query=Database::query("SELECT * FROM posts WHERE user_id='{$user_id}' AND active='yes' ORDER BY id DESC"); while($rows=$query->fetch_assoc()){ $post_id=$rows['id']; $post_name=$rows['name']; $post_description=$rows['description']; ?><div id="line_holder" style="padding:5px; margin:5px; background:#eee; font-size:12px; color:#555; font-family:helvetica;"><div style="float:left; padding:6px; text-overflow: ellipsis;width: 150px;white-space: nowrap;overflow: hidden;"><strong><?php echo $post_name; ?></strong><br /><?php echo $post_description; ?></div><input id="rad" type="radio" name="r<?php echo $post_id; ?>" value="101" style="display:none;"/><div style="float:left; padding:6px; line-height:25px; "><label style="cursor:pointer; margin-left:10px;"><input id="rad" type="radio" name="r<?php echo $post_id; ?>" value="20" style="width:6px; background:#cd0000;"/><span style="padding:5px; margin-left:3px; font-size:12px; font-family:helvetica; color:#555;border:1px solid #dcdcdc;text-align:center;-webkit-border-radius: 2px;-moz-border-radius: 2px;border-radius: 2px;background: #f8f8f8;">Very Bad</span></label></div><div style="float:left; padding:6px; line-height:25px; "><label style="cursor:pointer; margin-left:6px;"><input id="rad" type="radio" name="r<?php echo $post_id; ?>" value="50" style="width:6px; background:#cd0000;"/><span style="padding:5px; margin-left:3px; font-size:12px; font-family:helvetica; color:#555;border:1px solid #dcdcdc;text-align:center;-webkit-border-radius: 2px;-moz-border-radius: 2px;border-radius: 2px;background: #f8f8f8;">Bad</span></label></div><div style="float:left; padding:6px; line-height:25px; "><label style="cursor:pointer; margin-left:6px;"><input id="rad" type="radio" name="r<?php echo $post_id; ?>" value="65" style="width:6px; background:#cd0000;"/><span style="padding:5px; margin-left:3px; font-size:12px; font-family:helvetica; color:#555;border:1px solid #dcdcdc;text-align:center;-webkit-border-radius: 2px;-moz-border-radius: 2px;border-radius: 2px;background: #f8f8f8;">Ok</span></label></div><div style="float:left; padding:6px; line-height:25px; "><label style="cursor:pointer; margin-left:6px;"><input id="rad" type="radio" name="r<?php echo $post_id; ?>" value="80" style="width:6px; background:#cd0000;"/><span style="padding:5px; margin-left:3px; font-size:12px; font-family:helvetica; color:#555;border:1px solid #dcdcdc;text-align:center;-webkit-border-radius: 2px;-moz-border-radius: 2px;border-radius: 2px;background: #f8f8f8;">Good</span></label></div><div style="float:left; padding:6px; line-height:25px; "><label style="cursor:pointer; margin-left:6px;"><input id="rad" type="radio" name="r<?php echo $post_id; ?>" value="100" style="width:6px; background:#cd0000;"/><span style="padding:5px; margin-left:3px; font-size:12px; font-family:helvetica; color:#555;border:1px solid #dcdcdc;text-align:center;-webkit-border-radius: 2px;-moz-border-radius: 2px;border-radius: 2px;background: #f8f8f8;">Excellent</span></label></div><br style="clear:both;" /></div> <?php }?><!-- --><input type="submit" value="Submit Your Ratings" style="padding:5px; margin:5px; border:1px solid #cd0000; background:#f8f8f8; color:#cd0000; font-family:helvetica; font-size:12px; font-weight:bold; cursor:pointer; width:140px; text-align:center;" /></form><div class="text_clean" style="margin:5px;"><!-- EDIT HERE --><a href="http://brevada.com/profile/profile.php?name=<?php echo $url_name; ?>&reviewer=PUT EMAIL HERE" target="_TOP">Not Working?</a><!-- --></div><div style="width:150px; text-align:center; margin:0 auto; font-size:11px; font-family:helvetica; padding:5px;">Powered by <a href="http://brevada.com"><span style="color:#cd0000; font-weight:bold;">Brevada</span></a></div></div></textarea>	
            </div>
            
            <br style="clear:both;" />
        
        </div>
        
        <div class="option">
        
        	<div class="option_left">
            	<a href="http://brevada.com/<?php echo $url_name; ?>" target="_top">
                <img class="option_image" src="/images/giveusfeedback.png" />
                </a>
             </div>
 
            
            <div class="option_right">	
                <div class="option_title">
                	<strong>The Feedback Badge:</strong> Copy and paste this anywhere on your website to link customers to your Brevada Page.
                </div>
                <br />
		<textarea class="option_ta" onclick='this.select();'><a href="http://brevada.com/<?php echo $url_name; ?>" target="_top"><img src="http://brevada.com/images/giveusfeedback.png" style="width:150px;" /></a></textarea>	
            </div>
            
            <br style="clear:both;" />
        
        </div>
		
		<br style="clear:both;" />
		
		</div>


<?php
} else { 
	$this->add(new View('../hub/includes/upgrade_button.php', array('upgrade_message' => "Upgrade To Use Widgets And Integration")));
}
?>