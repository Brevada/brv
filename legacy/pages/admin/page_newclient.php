<?php
if(!Brevada::IsLoggedIn()){ Brevada::Redirect('/home/logout'); }
if(!Permissions::has(Permissions::VIEW_ADMIN)){ Brevada::Redirect('/404'); }

$this->addResource('/css/signup.css');
$this->addResource('/js/signup.js');
?>
<h1 class="page-header">New Client</h1>

<?php if(isset($_GET['done'])){ ?>
<div class='attention'>
<h2 class='sub-header'>The client has been signed up.</h2>
<p>An account has been made for the client. The client can log into his/her account, however the account must be activated via transaction to view feedback analytics. It is recommended that the client pay for his account on his/her own device (for privacy/security) by clicking "Activate Your Account" in his/her dashboard.</p>
</div>
<?php } ?>

<h2 class='sub-header'>Setup A New Client</h2>
<p>If the client has already made an account and you would like to modify or upgrade it, please use the Companies tab.</p>

<hr />

<form method="post" id='frmSignup' action="/overall/insert/insert_user.php?corporate=1" class='part-container'>
		<div class='part'>
			<div class="signup_instruction">Part of the process can be automated by entering the client's website. If the client does not have a website, just leave the field blank.</div>
			<input class="in" type="text" name="txtCompanyName" id="txtCompanyName" placeholder="What is the name of the client's company?" style="color:#555;" />
			<input class="in" type="text" name="txtWebsite" id="txtWebsite" placeholder="What is the client's website? (ignore if they don't have one)" style="color:#555;" />
			<div class="submit-next"><?php _e('Next'); ?> <i class="fa fa-chevron-right"></i></div>
		</div>
		<div class='part'>
			<div class="signup_instruction"><span class='crawled'>We've detected the client is a <span id='categoryDetection'></span>. If this is incorrect, choose a different category from the list below.</span><span class='not-crawled'>Please choose a category from the list below.</span></div>
			
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
			<div class="signup_instruction">Is this how you would describe the client's business? Feel free to turn keywords on or off.</div>
			
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
			<div class="signup_instruction">What does the client want to get feedback on?</div>
			<div class='token-container token-aspects'>
				<div class='tokens'>
					<?php
					if(($query = Database::query("SELECT aspect_type.Title, aspect_type.ID as AspectTypeID FROM aspect_type WHERE aspect_type.CompanyID IS NULL ORDER BY aspect_type.Title ASC")) !== false){
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
			<div class="signup_instruction">Please enter the name and location of each store connected to the client's account. You can add more locations later if necessary.</div>
			
			<div id='storeList'>
			<div class='store-list-item'>
				<input class="in" type="text" name="txtStoreName0" id="txtStoreName" placeholder="<?php _e('Store Name e.g. McRonalds - London'); ?>" style="color:#555;" />
				<input class="in" type="text" name="txtStreetAddress0" id="txtStreetAddress" placeholder="<?php _e('Street Address'); ?>" style="color:#555;" />
				<input class="in" type="text" name="txtCity0" id="txtCity" placeholder="<?php _e('City'); ?>" style="color:#555;" />
				<input class="in" type="text" name="txtProvince0" id="txtProvince" placeholder="<?php _e('Province'); ?>" style="color:#555;" />
			</div>
			</div>
			
			<br /><div><a id='linkAddStore'>Add another store...</a></div>
			
			<div class="submit-back"><i class="fa fa-chevron-left"></i> <?php _e('Back'); ?></div>
			<div class="submit-next"><?php _e('Next'); ?> <i class="fa fa-chevron-right"></i></div>
		</div>
		<div class='part part2'>
			<div class="signup_instruction">Let's set up the login credentials for the client's administrator account. The client can make additional logins later.</div>
			
			<input class="in" type="email" name="txtEmail" placeholder="<?php _e('Email Address'); ?>" style="color:#555;" />
			<input class="in" id="password1" type="password" name="txtPassword"  placeholder="<?php _e('Password'); ?>" style="color:#555;"/>
			<input class="in" id="password2" type="password" name="txtPassword2" placeholder="<?php _e('Retype Password'); ?>"  style="color:#555;" />
			<br /><p class='terms'><input type='checkbox' name='chkAgree' id='chkAgree' />Check this on if the client has read and agreed to the <a href='/privacy' target='_blank'>Terms &amp; Conditions</a>.</p>
			<input type="hidden" name="plan" value="<?php echo $plan; ?>" />
			<div class="submit-back"><i class="fa fa-chevron-left"></i> <?php _e('Back'); ?></div>
			<div class="submit-next disabled" id='submit'>Sign Up Client <i class="fa fa-chevron-right"></i></div>
		</div>
	</form>	
	
<!-- Pre selected suggestions -->
<script>
$(document).ready(function() {
    $('.token-aspects > .tokens > div[data-tokenid="1"]').click();
	$('.token-aspects > .tokens > div[data-tokenid="2"]').click();
	$('.token-aspects > .tokens > div[data-tokenid="4"]').click();
	$('.token-aspects > .tokens > div[data-tokenid="5"]').click();
	$('.token-aspects > .tokens > div[data-tokenid="7"]').click();
	
	$('#linkAddStore').click(function(){
		var count = $('div#storeList > div.store-list-item').length;
		var storeListItem = $('<div>').addClass('store-list-item');
		storeListItem.append($('<input>').addClass('in').attr('type', 'text').attr('name', 'txtStoreName'+count).attr('placeholder', 'Store Name e.g. McRonalds - Store #' + count).css('color', '#555'));
		storeListItem.append($('<input>').addClass('in').attr('type', 'text').attr('name', 'txtStreetAddress'+count).attr('placeholder', 'Street Address').css('color', '#555'));
		storeListItem.append($('<input>').addClass('in').attr('type', 'text').attr('name', 'txtCity'+count).attr('placeholder', 'City').css('color', '#555'));
		storeListItem.append($('<input>').addClass('in').attr('type', 'text').attr('name', 'txtProvince'+count).attr('placeholder', 'Province').css('color', '#555'));
		storeListItem.hide();
		$('div#storeList').append(storeListItem);
		storeListItem.slideDown(100, function(){
			$("html, body").animate({ scrollTop: $(this).offset().top }, 250);
		});
	});
});
</script>