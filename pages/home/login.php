<?php
if(Brevada::IsLoggedIn()){ Brevada::Redirect('/dashboard'); }

$this->setTitle('Brevada - Login');
$this->addResource('/css/layout.css');
$this->addResource('/css/login.css');

if(Brevada::IsMobile()){
	$this->addResource("<meta name='viewport' content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0'/>", true, true);
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
			<input class="in" type="text" name="email" placeholder="<?php _e('Email'); ?>" style="margin-top:16px;" /> 
			<input class="in" type="password" name="password" placeholder="<?php _e('Password'); ?>" style="margin-top:6px;" /> 
			<input class="sub button4" name="submit" type="submit" value="<?php _e('Login'); ?>" />
		</form>
	</div>
</div>

<div id="bottom">
	<center>
		<span class="text_clean"><?php _e("Don't have an account?"); ?></span>&nbsp;<a href="/home/signup.php"><span style="color:#bc0101;"><?php _e('Click Here'); ?></span></a>
		<br /><br /><br />
		<span style="font-size:11px;"><?php _e('Toll free'); ?>: 1 (855) 484-7451 <br /> &copy; <?php echo date('Y'); ?> brevada.com</span>
	</center>
</div>