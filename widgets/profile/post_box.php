<?php
$this->addResource('/css/post_box.css');
$this->addResource('/js/post_box.js');

$r = $this->getParameter('row');

$post_id = $r['ID'];
$post_name = $r['Title'];
$post_description = $r['Description'];

$reviewer = $this->getParameter('reviewer');
$ip = $this->getParameter('ip');
$user_id = $this->getParameter('id');
$user_extension = $this->getParameter('user_extension');

$isMobile = $this->getParameter('mobile');
$isMobile = empty($isMobile) ? '' : '_mobile';

?>

<div class="aspect">
		<div class="aspect-header">
			<div class="title"><?php echo $post_name; ?></div>
			<!-- <div class="subtitle"><?php echo substr($post_description,0,85); if(strlen($post_description)>85){echo '...';} ?></div> -->
		</div>

		<div class="rating-box">
			<?php				
			$checkQuery = Database::query("SELECT 1 FROM feedback WHERE IPAddress = '{$ip}' AND AspectID = {$post_id} ORDER BY feedback.id DESC");
			if($checkQuery->num_rows==0){
			?>
			<div style="padding:0px; background:green;"><?php $this->add(new View('../widgets/profile/star_rating_bar.php', array('row' => $r, 'country' => $this->getParameter('country'), 'ip' => $this->getParameter('ip'), 'id' => $this->getParameter('id'), 'reviewer' => $reviewer))); ?></div>
			<div class="appear" id="appear_bar_<?php echo $post_id; ?>"  align="center" style="display:none;width:100%; border-top:1px solid #dcdcdc;">Thanks for rating.</div>	
			<?php } else { ?>
			<div class="appear" id="appear_bar_<?php echo $post_id; ?>"  align="center" style="width:100%; font-size:12px; color:#dcdcdc; border-top:0px solid #dcdcdc;">Already rated.</div>	
			<?php } ?>
		</div>
		<div class="rate-description">
			<div class="pull-left">Worst</div>
			<div class="pull-right">Best</div>
			<br class="clear" />
		</div>
	</div>