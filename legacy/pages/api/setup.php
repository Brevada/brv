<?php
$this->IsScript = true;
?>

<style type='text/css'>
div.main_container {
	display: block;
	margin: 20px auto 0 auto;
	padding: 0.625em;
	width: 75%;
}

.in {
	font-family:helvetica;
	color:#444444;
	-webkit-appearance: none;

	width:100%;
	background:rgba(227,227,227,0.15);
	outline:none;
	border:1px solid #dcdcdc;
	padding:0.875em;
	font-size:1.5em;
	border-radius:2px;
	margin-top:0.625em;
}

div.bottom {
	font-size:1em;
	font-family:helvetica;
	color:#444444;
	margin: 1em auto 0 auto;
	text-align: center;
	max-width: 80%;
	padding: 5px;
}

.sub {
	width:100%;
	color:#666;
	padding:1em;
	margin-top:0.625em;
	-webkit-appearance: none;
    border-radius: 0;
	background: #f9f9f9;
	border: 1px solid #dcdcdc;
	font-family: arial;
	font-size: 12px;
	text-decoration: none;
}

#msg { color: #000000; }
img.logo { height: auto; max-width: 80%; margin: 10% auto 25px auto; text-align: center; }

::-webkit-input-placeholder { /* WebKit browsers */
    color:    #999;
}
:-moz-placeholder { /* Mozilla Firefox 4 to 18 */
    color:    #999;
    opacity:  1;
}
::-moz-placeholder { /* Mozilla Firefox 19+ */
    color:    #999;
    opacity:  1;
}
:-ms-input-placeholder { /* Internet Explorer 10+ */
    color:    #999;
}
</style>

<div class="main_container">
	<img src='images/brevada.png' class='logo' />
	<div>
		<div>
			<p id='msg'></p>
			<input class="in" type="text" id="email" placeholder="<?php _e('Email'); ?>" style="margin-top:16px;" /> 
			<input class="in" type="password" id="password" placeholder="<?php _e('Password'); ?>" style="margin-top:6px;" />
			<select class="in" id="store" style="margin-top:6px;display: none;"></select>
			<input class="sub" name="submit" type="submit" id='btnSetup' value="<?php _e('Connect Device'); ?>" />
			<input class="sub" name="submit" type="submit" id='btnComplete' value="<?php _e('Select Store'); ?>" style='display:none;' />
		</div>
	</div>
</div>

<div class="bottom">
	<span class="text_clean">Don't have an account? Sign up on your computer at brevada.com.</span></a>
	<br /><br /><br />
	<span style="font-size:11px;"><?php _e('Toll free'); ?>: 1 (855) 484-7451 <br /> &copy; <?php echo date('Y'); ?> brevada.com</span>
</div>

<script type='text/javascript'>
$('#btnSetup').click(function(){
	$('#msg').text('');
	if (app && $('#email').val().length > 0 && $('#password').val().length > 0) {
		app.send('tablet/setup', {
			'email': $('#email').val(),
			'password': $('#password').val()
		},
		function(data){
			if (data && data.stores) {
				$('#store').children().remove();
				for(var i = 0; i < data.stores.length; i++){
					$('#store').append($('<option>').attr('value', data.stores[i].id).text(data.stores[i].title));
				}
				
				$('#email').hide();
				$('#password').hide();
				$('#btnSetup').hide();
				$('#btnComplete').show();
				$('#store').show();
				
				$('#msg').text('Select a store.');
			}
		}, function(err){
			console.log("Failed to setup tablet.");
			$('#msg').text('Email or password are invalid.');
		});
	}
});

$('#btnComplete').click(function(){
	$('#msg').text('');
	if (app && $('#store option:selected').length > 0) {
		app.send('tablet/setup', {
			'store': $('#store').val()
		},
		function(data){
			// Restart
			app.doCommand('restart');
		}, function(err){
			console.log("Failed to setup tablet.");
			$('#msg').text('An error has occured. Try again or contact support.');
		});
	}
});
</script>