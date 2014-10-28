<?php
$this->addResource('/css/promopopup_content.css');
$user_id = $_SESSION['user_id'];
?>
<div id="promo" >
 		<div id="p_holder">
 			<div id="p_left">
 				<?php $this->add(new View('../pages/overall/public/cert1.php')); ?>
 			</div>
 			<div id="p_right">
            	<!-- UPDATE IFRAME!!! -->
 				<textarea style="height:200px; resize:none;"><iframe src="http://brevada.com/cert1.php?u=<?php echo $user_id; ?>" height="180px" width="100px"></iframe></textarea>
 			</div>
 			<br style="clear:both;" />
 		</div>
 		<img id="promo_image" src="/images/giveusfeedback.png" />
 		<div id="promo_text">Classic Promo</div>
 		<a href="/hub/includes/marketing/promo_3.php" target="_BLANK">
 		<div id="promo_open">
			<img class="view_picture" src="/images/view.png" />
		</div>
		</a>
 		<img id="promo_image" src="/images/giveusfeedback_black.png" />
 			<div id="promo_text">Classic Promo - White</div>			
 			<a href="/hub/includes/marketing/promo_4.php" target="_BLANK">
 			<div id="promo_open">
			<img class="view_picture" src="/images/view.png" />
			</div>
 		</a>
 		<a href="/hub/includes/marketing/promo_clean.php" target="_BLANK">
			<img id="promo_image" src="/images/promo_clean.png" />
			<div id="promo_text">Clean Promo</div>
			<div id="promo_open">
				<img class="view_picture" src="/images/view.png" />
			</div>
 		</a>
</div>