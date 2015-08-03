<?php
$this->setTitle('Brevada - Sign Up');
$this->addResource('/css/layout.css');
$this->addResource('/css/signup.css');
$this->addResource('/js/signup.js');

$this->addResource("<meta name='viewport' content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0'/>", true, true);
if(Brevada::IsLoggedIn()){
	Brevada::Redirect('/dashboard');
}

$plan = 0;
if(isset($_GET['l'])){
	switch(strtolower(trim($_GET['l'])))
	{
		case 'basic':
			$plan = 1;
			break;
		case 'premium':
			$plan = 2;
			break;
		default:
			$plan = 0;
	}
}
?>
<div id="signup_box" style='display: none;'>
	<a href="/index.php" ><img src="/images/brevada.png" id='logo' style="margin:0 auto; width:150px; outline:none;" /></a>
	<form method="post" id='frmSignup' action="/overall/insert/insert_user.php" class='part-container'>
		<div class='part'>
			<div class="signup_instruction"><?php _e("Signing up is easy. We just need a bit of information to get started."); ?></div>
			<input class="in" type="text" name="txtCompanyName" id="txtCompanyName" placeholder="<?php _e('What is the name of your company?'); ?>" style="color:#555;" />
			<input class="in" type="text" name="txtWebsite" id="txtWebsite" placeholder="<?php _e("What is your company's website? (ignore if you don't have one)"); ?>" style="color:#555;" />
			<div class="submit-next"><?php _e('Next'); ?> <i class="fa fa-chevron-right"></i></div>
		</div>
		<div class='part'>
			<div class="signup_instruction"><span class='crawled'><?php _e("We've detected you are a <span id='categoryDetection'></span>. If this is incorrect, choose a different category from the list below."); ?></span><span class='not-crawled'><?php _e("Please choose a category from the list below."); ?></span></div>
			
			<select name='ddCategory' id='ddCategory' style="color:#555;"><?php
				if(($query = Database::query("SELECT `id`, `Title` FROM `company_categories`")) !== false){
					while($row = $query->fetch_assoc()){
						$id = $row['id'];
						$title = __(ucwords($row['Title']));
						echo "<option value='{$id}'>{$title}</option>";
					}
				}
			?></select>
			
			<div class="submit-back"><i class="fa fa-chevron-left"></i> <?php _e('Back'); ?></div>
			<div class="submit-next"><?php _e('Next'); ?> <i class="fa fa-chevron-right"></i></div>
		</div>
		<div class='part'>
			<div class="signup_instruction"><?php _e('Is this how you would describe your business? Feel free to turn keywords on or off.'); ?></div>
			
			<div class='token-container token-keywords'>
				<div class='tokens'>
					<?php
					if(($query = Database::query("SELECT company_keywords.Title, company_keywords.id as CompanyKeywordID FROM company_keywords ORDER BY company_keywords.Title ASC")) !== false){
						while($row = $query->fetch_assoc()){
							echo "<div class='token noselect' data-tokenid='{$row['CompanyKeywordID']}'><span>".__($row['Title'])."</span></div>";
						}
					}
					?>
					<input type='hidden' name='tokensKeywords' class='token-input' />
				</div>
			</div>
			
			<div class="submit-back"><i class="fa fa-chevron-left"></i> <?php _e('Back'); ?></div>
			<div class="submit-next"><?php _e('Next'); ?> <i class="fa fa-chevron-right"></i></div>
		</div>
		<div class='part part1'>
			<div class="signup_instruction"><?php _e('What do you want to get feedback on?'); ?></div>
			<div class='token-container token-aspects'>
				<div class='tokens'>
					<?php
					if(($query = Database::query("SELECT aspect_type.Title, aspect_type.ID as AspectTypeID FROM aspect_type ORDER BY aspect_type.Title ASC")) !== false){
						while($row = $query->fetch_assoc()){
							echo "<div class='token noselect' data-tokenid='{$row['AspectTypeID']}'><span>".__($row['Title'])."</span></div>";
						}
					}
					?>
					<input type='hidden' name='tokensAspects' class='token-input' />
				</div>
			</div>
			<div class="submit-back"><i class="fa fa-chevron-left"></i> <?php _e('Back'); ?></div>
			<div class="submit-next"><?php _e('Next'); ?> <i class="fa fa-chevron-right"></i></div>
			<br style="clear:both;" />
		</div>
		<div class='part'>
			<div class="signup_instruction"><?php echo sprintf(__('Where is your business located? If you have more than one location, please give us a call for a <i>%s</i>.'), __('custom corporate package')); ?></div>
			
			<input class="in" type="text" name="txtStoreName0" id="txtStreetAddress" placeholder="<?php _e('Store Name (leave blank if same as company)'); ?>" style="color:#555;" />
			<input class="in" type="text" name="txtStreetAddress0" id="txtStreetAddress" placeholder="<?php _e('Street Address'); ?>" style="color:#555;" />
			<input class="in" type="text" name="txtCity0" id="txtCity" placeholder="<?php _e('City'); ?>" style="color:#555;" />
			<input class="in" type="text" name="txtProvince0" id="txtProvince" placeholder="<?php _e('Province'); ?>" style="color:#555;" />
			
			<div class="signup_instruction"><?php _e("* We are only available in Canada at the moment."); ?></div>
			
			<div class="submit-back"><i class="fa fa-chevron-left"></i> <?php _e('Back'); ?></div>
			<div class="submit-next"><?php _e('Next'); ?> <i class="fa fa-chevron-right"></i></div>
		</div>
		<div class='part part2'>
			<div class="signup_instruction"><?php _e("Let's set up the login credentials for your administrator account. You can make additional logins later."); ?></div>
			
			<input class="in" type="email" name="txtEmail" placeholder="<?php _e('Email Address'); ?>" style="color:#555;" />
			<input class="in" id="password1" type="password" name="txtPassword"  placeholder="<?php _e('Password'); ?>" style="color:#555;"/>
			<input class="in" id="password2" type="password" name="txtPassword2" placeholder="<?php _e('Retype Password'); ?>"  style="color:#555;" />
			<br /><p class='terms'><input type='checkbox' name='chkAgree' id='chkAgree' /><?php echo sprintf(__("I have read and agree to the %s Terms &amp; Conditions %s."), "<a href='/privacy' target='_blank'>", "</a>"); ?></p>
			<input type="hidden" name="plan" value="<?php echo $plan; ?>" />
			<div class="submit-back"><i class="fa fa-chevron-left"></i> <?php _e('Back'); ?></div>
			<div class="submit-next disabled" id='submit'><?php _e('Sign Up'); ?> <i class="fa fa-chevron-right"></i></div>
		</div>
	</form>
	<br /><br /><br /><br /><br />
	<div class="text_clean">
		<strong><?php _e("We'll help you get signed up"); ?>: <span id="emphasis"><a href='tel:1-844-2738232' style='text-decoration:none;'>1 (844) BREVADA</a></span></strong><br /><?php _e('Have an account?'); ?> <a href="/home/login.php"><span style="color:#bc0101;"><?php _e("Click Here"); ?></span></a>
	</div>
</div>

<br />
<?php
//This is an include
$this->add(new View('../template/footer.php'));
?>

<!-- Pre selected suggestions -->
<script>
$( document ).ready(function() {
    $('.token-aspects > .tokens > div[data-tokenid="1"]').click();
	$('.token-aspects > .tokens > div[data-tokenid="2"]').click();
	$('.token-aspects > .tokens > div[data-tokenid="4"]').click();
	$('.token-aspects > .tokens > div[data-tokenid="5"]').click();
	$('.token-aspects > .tokens > div[data-tokenid="7"]').click();
});
</script>