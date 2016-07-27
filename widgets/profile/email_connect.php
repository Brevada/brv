<?php
$store_id = $this->getParameter('store_id');
$this->addResource('/js/communicate_pod.js');

$store_id = @intval($store_id);
?>

<div class="thanks-header">
	<h1><?php _e("Thanks for the feedback!"); ?></h1> 
</div>

<div id="reset" class="refresh">
	<i class="fa fa-refresh"></i>
</div>