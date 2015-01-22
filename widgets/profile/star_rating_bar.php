<?php
$this->addResource('/css/star_rating_bar.css');
$this->addResource('/js/star_rating_bar.js');

$r = $this->getParameter('row');
$post_id = $r['id'];
$country = $this->getParameter('country');
$ip = $this->getParameter('ip');
$user_id = $this->getParameter('id');
$reviewer = $this->getParameter('reviewer');
?>

<div id="holder<?php echo $post_id; ?>" class='holder' onclick="disappearRating('<?php echo $post_id; ?>')" style="width:100%; white-space:nowrap; overflow:hidden; background: white; text-align:left;">

	<a href="#" onclick="insertRating('20', '<?php echo $post_id; ?>', '<?php echo $ip; ?>', '<?php echo $country; ?>', '<?php echo $user_id; ?>', '<?php echo $reviewer; ?>'); return false;">
		<div class='star star-1' squarenum='1'></div>
	</a>
	<a href="#" onclick="insertRating('40', '<?php echo $post_id; ?>', '<?php echo $ip; ?>', '<?php echo $country; ?>', '<?php echo $user_id; ?>', '<?php echo $reviewer; ?>'); return false;">
		<div class='star star-2' squarenum='2'></div>
	</a>
	<a href="#" onclick="insertRating('60', '<?php echo $post_id; ?>', '<?php echo $ip; ?>', '<?php echo $country; ?>', '<?php echo $user_id; ?>', '<?php echo $reviewer; ?>'); return false;">
		<div class='star star-3' squarenum='3'></div>
	</a>
	<a href="#" onclick="insertRating('80', '<?php echo $post_id; ?>', '<?php echo $ip; ?>', '<?php echo $country; ?>', '<?php echo $user_id; ?>', '<?php echo $reviewer; ?>'); return false;">
		<div class='star star-4' squarenum='4'></div>
	</a>
	<a href="#" onclick="insertRating('100', '<?php echo $post_id; ?>', '<?php echo $ip; ?>', '<?php echo $country; ?>', '<?php echo $user_id; ?>', '<?php echo $reviewer; ?>'); return false;">
		<div class='star star-5' squarenum='5'></div>
	</a>
	
</div>

<div class="appear" id="appear<?php echo $post_id; ?>"  align="center" style="display:none;width:100%; border-top:1px solid #dcdcdc;">
		Thanks for rating.
</div>