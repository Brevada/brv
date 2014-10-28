<?php
$this->setTitle('Brevada - Login');
$this->addResource('/css/layout.css');
$this->addResource('/css/login.css');

if(!isset($_SESSION['user_id'])){
	$_SESSION['user_id']='none';
}

if(Brevada::IsMobile()){
	$this->addResource("<meta name='viewport' content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=1'/>", true, true);
}
?>

<script type="text/javascript">
$(document).ready(function() {
    $('#logo').each(function(i) {
        if (this.complete) {
            $('#main_container').fadeIn(2000);
        } else {
            $(this).load(function() {
                $('#main_container').fadeIn(2000);
            });
        }
    });
});
</script>

<div id="main_container" style='display:none;'>
	<a href="/index.php"><img id="logo" src="/images/brevada.png" width="150px" style="outline:none;" /></a>
	<div id="second"  style="width:100%;">
		<form method="post" action="process_login.php">
			<input class="in" type="text" name="email" placeholder="Email" style="margin-top:16px;" /> 
			<input class="in" type="password" name="password" placeholder="Password" style="margin-top:6px;" /> 
			<input class="sub button4" name="submit" type="submit" value="Login" />
		</form>
	</div>
</div>

<div id="bottom">
	<center>
		<span class="text_clean">Don't have an account?</span>&nbsp;<a href="/home/signup.php"><span style="color:#bc0101;">Click Here</span></a>
		<br /><br /><br />
		<span style="font-size:11px;">Toll free: 1 (855) 484-7451 <br /> &copy; 2013 brevada.com</span>
	</center>
</div>