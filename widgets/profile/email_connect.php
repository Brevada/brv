<?php
$store_id = $this->getParameter('store_id');
$this->addResource('/js/communicate_pod.js');
?>

<div class="thanks-header">
	<h1><?php _e("Thank's for the feedback!"); ?></h1> 
	<h2><?php _e("Let's stay in touch."); ?></h2>
</div>

<div id="communicate_form">
	 	<input type="hidden" name="store_id" value="<?php echo $store_id; ?>" />
	 	<div class="input-group input-group-lg">
	 		<span class="input-group-addon" id="basic-addon1"><?php _e("Email:"); ?></span>
	 		<input id="emailTie" class="form-control" type="email" class="inp" name="emailTie" placeholder="example@gmail.com" />
	 		
	 		<span class="input-group-btn">
		        <input id="email-submit" class="btn btn-success disabled" type="submit" value="Go" />
		    </span>
	 	</div>
	 <div id="finished" class="btn opt-out"><?php _e("I'd rather not."); ?></div>
</div>

 <div id="reset" class="refresh">
 	<i class="fa fa-refresh"></i>
 </div>