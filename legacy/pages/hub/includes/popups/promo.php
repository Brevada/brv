<?php
$this->addResource('/css/layout.css');
$this->addResource('/css/promo.css');
$user_id = $_SESSION['user_id'];
$user=user($user_id);
?>		
<div id="promo">
 		<a href="/hub/includes/marketing/promo_red.php" target="_BLANK">
 		<img id="promo_image" src="/images/promo_rectangle.png" />
 			<div id="promo_open" style="background:#333;">
				<span class="text_clean" style="color:#fff;">Generate this promo</span>
			</div>
 		</a>
      
        <a href="/hub/includes/marketing/promo_white.php" target="_BLANK">
 		<img id="promo_image" src="/images/promo_rectangleWhite.png" />		
 			<div id="promo_open" style="background:#333;">
				<span class="text_clean" style="color:#fff;">Generate this promo</span>
			</div>
 		</a>
</div>