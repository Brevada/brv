<?php
$this->addResource('/css/mobile/index_mobile.css');
$this->addResource("<link ref='shortcut icon' type='image/x-icon' href='/images/check.png'>", true, true);
$this->addResource("<meta name='viewport' content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=1'/>", true, true);

if(!isset($_SESSION['user_id'])){
	$_SESSION['user_id']='none';
}
?>

<script>
$(document).ready(function() { 

    $('#main_container').hide();

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

<body>

<div align="center" id="main_container">

	<img id="logo" src="/images/brevada.png" width="250px" />

	
	
	<div id="second"  style="width:100%; margin-top:16px;">
	
		<form method="post" action="/home/process_login.php">
			<input id="in" type="text" name="email" placeholder="Email" style="" /> 
			<input id="in" type="password" name="password" placeholder="Password" style="margin-top:6px;" /> 
			<input id="sub" name="submit" type="submit" value="Login" />
		</form>
	
	</div>

</div>


<center>
	<div id="bottom">
		Don't have an account? <a href="/home/signup.php"><span style="color:#bc0101;">Click Here</span></a>
		<br />
		<br />
		
		<a href="/index.php?mobile_overwrite=true" style="color:red;">Desktop Version</a>
		
		<br />
		
		<span style="font-size:11px;">Toll free: 1 (855) 484-7451 <br />   &copy; 2013 brevada.com </span>
	</div>
</center>

</html>