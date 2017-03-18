<?php
$this->addResource('/css/new_rating_bar.css');
$this->addResource('/js/new_rating_bar.js');

$r = $this->getParameter('row');
$post_id = $r['id'];
$country = $this->getParameter('country');
$ip = $this->getParameter('ip');
$user_id = $this->getParameter('id');
$reviewer = $this->getParameter('reviewer');
?>

<div id="holder<?php echo $post_id; ?>" onclick="disappearRating('<?php echo $post_id; ?>')" style="background:rgb(0,255,0); width:100%; white-space:nowrap; overflow:hidden;">
	<?php
	for($num=0;$num<=100;$num++){
		$g=floor((225*($num))/100);
		$r=floor((225*(100-($num)))/100); 
		$b=40;
	?>
	<a href="#" onclick="insertRating('<?php echo $num; ?>', '<?php echo $post_id; ?>', '<?php echo $ip; ?>', '<?php echo $country; ?>', '<?php echo $user_id; ?>', '<?php echo $reviewer; ?>'); return false;">
	<div id="square" class="square ratingsquare" squarenum="<?php echo $num; ?>" style="background:rgba(<?php echo $r; ?>, <?php echo $g; ?>,<?php echo $b; ?>, 1);">
		<div class="overholder">
			<div class="text_holder" style="background:rgba(<?php echo $r; ?>, <?php echo $g; ?>,<?php echo $b; ?>, 0.85);"></div>
		</div>
	</div>
	</a>
	<?php
	}
	?>
</div>

<div class="appear" id="appear<?php echo $post_id; ?>"  align="center" style="display:none;width:100%; border-top:0px solid #dcdcdc;">
		Thanks for rating.
</div>