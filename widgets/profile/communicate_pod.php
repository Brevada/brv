<?php
$session_id = session_id();
$user_id = $this->getParameter('user_id');
$this->addResource('/js/communicate_pod.js');
?>

<div id="communicate_form">
	 <form action="<?php echo $path; ?>insert_emailTie.php" method="post">
	 	<input type="hidden" name="session_id" value="<?php echo $session_id; ?>" />
	 	<input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
	 	<input id="emailTie" type="email" class="inp" name="emailTie" style="font-size:12px; width:220px; border:0px; margin:0px; text-align:left;" placeholder="Your Email" />
	 	<input class="button4" type="submit" value="Connect" onclick="SubmitEmail('<?php echo $user_id ?>', '<?php echo $session_id; ?>')" style="margin:0px; border-left:0px; border-right:0px; height:40px; line-height:30px; width:100%; text-align:left;">
	 </form>
</div>

<div id="thanks_connect" class="thanks_suggestion">Submitted</div>