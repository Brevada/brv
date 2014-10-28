<?php
$this->setTitle('Brevada - Sign Up');
$this->addResource('/css/layout.css');
$this->addResource('/css/signup.css');
//$this->addResource('<meta name="" />', false, true); //Literal include

if(Brevada::IsLoggedIn()){
	Brevada::Redirect('/hub');
}

$level = 1;
if(isset($_GET['l'])){
	$level=strtolower($_GET['l']);
	if($level=="free"){
		$level=1;
	}
	else if($level=="personal"){
		$level=2;
	}
	else if($level=="professional"){
		$level=3;
	}
	else if($level=="enterprise"){
		$level=4;
	}
}

$existing_address=false;
if(isset($_GET['email']) && $_GET['email'] != 'false'){
	$existing_address=true;
}
?>
<div style="width:100%; margin-top:100px; height:370px;  background-repeat:repeat-y; background-repeat:repeat-x;">
		<div id="signup_box" style="width:500px; margin:0 auto;">
		<a href="/index.php" ><img src="/images/brevada.png" style="margin:0 auto; width:150px; outline:none;" /></a>
			<form method="post" action="/overall/insert/insert_user.php">
				<input class="in" type="email" name="email" <?php if($existing_address){ ?> value="<?php echo $_GET['email']; ?>"<?php } else{ ?>  value="Email" <?php } ?> onfocus="if(this.value == 'Email'){this.value=''; }" onblur="if(this.value == ''){this.value='Email'; }"  style="color:#555;" />
				<input class="in" id="password1" type="text" name="password"  value="Password" onfocus="if(this.value == 'Password'){this.value='';this.type='password' }" onblur="if(this.value == ''){this.value='Password'; this.type='text';}" style="color:#555;"/>
				<input class="in" id="password2" type="text" name="password2" value="Retype Password" onfocus="if(this.value == 'Retype Password'){this.value='';this.type='password' }" onblur="if(this.value == ''){this.value='Retype Password'; this.typ=text';}" style="color:#555;" />
				<input class="in" type="text" name="name" placeholder="Your Company Name"  style="background:#eee; opacity:1;"  required>
				<input type="hidden" name="level" value="<?php echo $level; ?>" />
				<input id="submit" class="button4" type="submit" name="submit" value="Sign Up" />
			</form>
			<br />
			<div class="text_clean">
            <strong> We'll help you get signed up: <span id="emphasis">1 (844) BREVADA</span> </strong>
               <br />
			Have an account? <a href="/home/login.php"><span style="color:#bc0101;">Click Here</span></a>
			</div>
		</div>
	</div>
	<br style="clear:both;" />
</div>

<script type="text/javascript">
window.onload=function () {
 document.getElementById("password1").onchange=validatePassword;
 document.getElementById("password2").onchange=validatePassword;
}
function validatePassword(){
var pass=document.getElementById("password2").value;
var pass2=document.getElementById("password1").value;
if(pass1==pass2)
	document.getElementById("password2").setCustomValidity("Passwords Don't Match");
else
	document.getElementById("password2").setCustomValidity('');  
//empty string means no validation error
}
</script>
<?php
//This is an include
$this->add(new View('../template/footer.php'));
?>