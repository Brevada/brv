<?php
$this->addResource('/css/post_box.css');
$this->addResource('/js/post_box.js');

$r = $this->getParameter('row');
$post_id = $r['ID'];
$post_name = $r['Title'];
$post_description = $r['Description'];

$alreadyRated = false;
if($this->getParameter('tablet') !== true){
	$alreadyRated = isset($_SESSION['feedback']) && !empty($_SESSION['feedback']) && in_array($post_id, $_SESSION['feedback']);
}
?>

<div id="aspect_<?php echo $post_id; ?>" class="aspect<?php echo $alreadyRated ? ' rated' : ''; ?>">
	<div class="aspect-header">
		<div class="title"><?php _e($post_name); ?></div>
		<?php echo substr(__($post_description),0,85); if(strlen(__($post_description))>85){echo '...';} ?>
	</div>
	<div class="rating-box">
		<?php				
		if(!$alreadyRated){
		?>
		<div><?php $this->add(new View('../widgets/profile/star_rating_bar.php', array('row' => $r))); ?></div>
		<div class="rate-description">
			<div class="pull-left"><?php _e('Worst'); ?></div>
			<div class="pull-right"><?php _e('Best'); ?></div>
			<br class="clear" />
		</div>
		<?php } else { ?>
		<div class="appear" id="appear_bar_<?php echo $post_id; ?>"  align="center" style="width:100%;"><?php _e('Already rated.'); ?></div>	
		<?php } ?>
	</div>
</div>