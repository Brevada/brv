<?php
$this->addResource('/css/star_rating_bar.css');
$this->addResource('/js/star_rating_bar.js');

$r = $this->getParameter('row');
$post_id = $r['ID'];
?>

<div id="holder<?php echo $post_id; ?>" class='holder' onclick="disappearRating('<?php echo $post_id; ?>')">

	<a href="#" onclick="insertRating('20', '<?php echo $post_id; ?>'); return false;">
		<div class='star star-1' data-squarenum='1'></div>
	</a>
	<a href="#" onclick="insertRating('40', '<?php echo $post_id; ?>'); return false;">
		<div class='star star-2' data-squarenum='2'></div>
	</a>
	<a href="#" onclick="insertRating('60', '<?php echo $post_id; ?>'); return false;">
		<div class='star star-3' data-squarenum='3'></div>
	</a>
	<a href="#" onclick="insertRating('80', '<?php echo $post_id; ?>'); return false;">
		<div class='star star-4' data-squarenum='4'></div>
	</a>
	<a href="#" onclick="insertRating('100', '<?php echo $post_id; ?>'); return false;">
		<div class='star star-5' data-squarenum='5'></div>
	</a>
	
</div>

<div class="appear" id="appear<?php echo $post_id; ?>">
		<?php _e('Thanks for rating.'); ?>
</div>