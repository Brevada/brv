<?php
$session_id = session_id();
$user_id = $this->getParameter('user_id');
$this->addResource('/js/communicate_pod.js');
?>
	<h2>
		<b>Thank's for the feedback!</b> Please provide your email address so we can stay in touch.
	</h2>
<div id="communicate_form">
	 <form action="<?php echo $path; ?>insert_emailTie.php" method="post">
	 	<input type="hidden" name="session_id" value="<?php echo $session_id; ?>" />
	 	<input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
	 	<div class="input-group input-group-lg">
	 		<span class="input-group-addon" id="basic-addon1">Email:</span>
	 		<input id="emailTie" class="form-control" type="email" class="inp" name="emailTie"  placeholder="example@gmail.com" />
	 		
	 		<span class="input-group-btn">
		        <input id="email-submit" class="btn btn-success disabled" type="submit" value="Go" onclick="SubmitEmail('<?php echo $user_id ?>', '<?php echo $session_id; ?>')" >
		    </span>
	 	</div>
	 	
	 </form>
	 <a id="reset" class="opt-out">I'd rather not</a>
</div>
