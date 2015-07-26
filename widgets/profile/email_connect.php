<?php
$session_id = session_id();
$user_id = $this->getParameter('user_id');
$this->addResource('/js/communicate_pod.js');
?>

<div class="thanks-header">
	<h1>Thank's for the feedback!</h1> 
	<h2>Let's stay in touch.</h2>
</div>

<div id="communicate_form">
	 	<input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
	 	<div class="input-group input-group-lg">
	 		<span class="input-group-addon" id="basic-addon1">Email:</span>
	 		<input id="emailTie" class="form-control" type="email" class="inp" name="emailTie"  placeholder="example@gmail.com" />
	 		
	 		<span class="input-group-btn">
		        <input id="email-submit" class="btn btn-success disabled" type="submit" value="Go" />
		    </span>
	 	</div>
	 <div id="finished" class="btn opt-out">I'd rather not.</div>
</div>

 <div id="reset" class="refresh">
 	<i class="fa fa-refresh"></i>
 </div>