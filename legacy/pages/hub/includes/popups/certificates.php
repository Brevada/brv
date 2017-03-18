<?php
$this->addResource('/css/layout.css');
$this->addResource('/css/popup_style.css');
$user_id = $_SESSION['user_id'];
$user = user($user_id);

$status = "Hidden (Only visible to you)";	
if($user['public'] == 1){
	$status="Public";	
}
?>
<div id="modal_title" class="text_clean"></div>
<br />
	<div style="width:100%;"> 
        <div class="option">
        	<div class="option_title"><br />
            Your public image is: <span style="color:#ee2b2b;"><strong><?php echo $status; ?></strong></span>
             <br />
            </div>
        </div>
        <div class="option">
        	<div class="option_left">	
 				<img class="option_image" src="/images/scores_demo.png" />
            </div>
            <div class="option_right">	
                <div class="option_title">
                	<strong>Your Scores Page</strong>
                </div>
                <br />
                <div class="text_clean">
        			URL: <a href="http://brevada.com/scores/<?php echo $user['url_name']; ?>" target="_BLANK"><strong>www.brevada.com/scores/<?php echo $user['url_name']; ?> </strong></a>
                </div>
 			</div>
            <br style="clear:both;" />
        </div>
        <div class="option">
        <?php $this->add(new View('../pages/hub/includes/popups/includes/search_result.php', array('user_id' => $user_id, 'user' => $user))); ?>
        </div>
        <div class="option">
        	<div class="option_left">	
 				<?php $this->add(new View('../pages/overall/public/cert1.php')); ?>
            </div>
            <div class="option_right">	
                <div class="option_title">
                	<strong>Embed Your Certificate:</strong> Copy and paste this code onto your website.
                </div>
        		<textarea class="option_ta"><iframe src="http://brevada.com/overall/public/cert1.php?u=<?php echo $user_id; ?>" height="180px" width="100px"></iframe></textarea>
 		</div>
            <br style="clear:both;" />
        </div>
</div>