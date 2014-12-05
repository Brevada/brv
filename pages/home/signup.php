<?php
$this->setTitle('Brevada - Sign Up');
$this->addResource('/css/layout.css');
$this->addResource('/css/signup.css');
$this->addResource('/js/jquery.tokeninput.js');
$this->addResource('/js/signup.js');
$this->addResource('/css/token-input.css');
$this->addResource('/css/token-input-facebook.css');
$this->addResource('/css/token-input-mac.css');
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

$existing_address = isset($_GET['email']) && $_GET['email'] != 'false';
?>
<div style="width:100%; margin-top:100px; height:370px;  background-repeat:repeat-y; background-repeat:repeat-x;">
		<div id="signup_box">
		<a href="/index.php" ><img src="/images/brevada.png" style="margin:0 auto; width:150px; outline:none;" /></a>
			<form method="post" action="/overall/insert/insert_user.php">
				<div id='part1'>
					<div class="signup_instruction">What do you want to get feedback on?</div>
				
						<div class='token-container'>
							<input type="text" id="posts-token" name="posts-token" />
							<div id="next" class="button4">Next</div>
						</div>
				
						<br style="clear:both;" />
				</div>
				<div id='part2' style='display:none;'>
					<input class="in" type="email" name="email" <?php if($existing_address){ ?> placeholder="<?php echo $_GET['email']; ?>"<?php } else { ?>  placeholder="Email" <?php } ?>  style="color:#555;" />
					<input class="in" id="password1" type="password" name="password"  placeholder="Password" style="color:#555;"/>
					<input class="in" id="password2" type="password" name="password2" placeholder="Retype Password"  style="color:#555;" />
					<input class="in" type="text" name="name" placeholder="Your Company Name"  style="background:#eee; opacity:1;">
					<input type="hidden" name="level" value="<?php echo $level; ?>" />
					<button id="back" class="button4">Back</button><input id="submit" class="button4" type="submit" name="submit" value="Sign Up" />
				</div>
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
<br />
<?php
//This is an include
$this->add(new View('../template/footer.php'));
?>