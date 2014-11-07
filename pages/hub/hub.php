<?php
$this->addResource('/css/layout.css');
$this->addResource('/css/hub.css');
$this->addResource('/pages/overall/packages/dygraph-combined.js');
$this->addResource('/js/hub.js');

if(Brevada::IsMobile()){
	Brevada::Redirect('/mobile/hub_mobile.php');
}

if(!Brevada::IsLoggedIn()){
	Brevada::Redirect('/home/logout');
}

$user_id = Brevada::validate($_SESSION['user_id'], VALIDATE_DATABASE);

$query=Database::query("SELECT * FROM users WHERE id='{$user_id}' LIMIT 1");


if($query->num_rows==0){
	Brevada::Redirect('/home/logout');
}

$name = ''; $email = ''; $type = ''; $url_name = ''; $active = ''; $logins = ''; $extension = ''; $type = '';
$trial = ''; $picture = ''; $expiry_date = ''; $corporate = ''; $referral_credits = ''; $level = '';

while($row = $query->fetch_assoc()){
	$name = $row['name'];
	$email = $row['email'];
	$type = $row['type'];
	$url_name = $row['url_name'];
	$active = $row['active'];
	$logins = $row['logins'];
	$extension = $row['extension'];
	$type = $row['type'];
	$trial = $row['trial'];
	$picture = $row['picture'];
	$expiry_date = $row['expiry_date'];
	$user_extension = $row['extension'];
	$corporate = $row['corporate'];
	$referral_credits = $row['referral_credits'];
	$level = $row['level'];
}

$referral_credits = empty($referral_credits) ? 0 : intval($referral_credits);

$this->setTitle("Brevada Hub - {$name}");
	
$logins++;

Database::query("UPDATE users SET logins='{$logins}' WHERE id='{$user_id}';");

if($level == '0' && $level == '1' && $active == 'no'){
	Brevada::Redirect('/hub/payment.php');
}

$query=Database::query("SELECT * FROM codes WHERE referral_user='{$user_id}' LIMIT 1");
$referral_code = 'No Code';
while($row = $query->fetch_assoc()){
	$referral_code = $row['code'];
}

if($corporate == '1'){
	$_SESSION['corporate']='active';
	$_SESSION['corporate_id']=$user_id;
	Brevada::Redirect('/corporate/hub/corporate.php');
}

if($expiry_date < date("Y-m-d")){
	$active='no';
}

$message = $active == 'no' ? "You're Almost There!" : 'Membership Expired';
?>
<!-- MODAL STUFF --> 
<div id="bottom_banner_pic">
	<div id="bottom_right">
		24/7 Customer Service and Feedback Consulting: <strong>1 (844) BREVADA</strong> or <strong>support@brevada.com</strong>
	</div>
</div>

<div id="generic_modal" class="text_clean">
  <div class="closeModal" id="email_close">Close</div>
  <div id="generic_modal_content"></div>
</div>

<div class="modal_bg">
    <div class="modal">
    	<div class="close_modal">Close</div>
        <div id="modal_content"></div>
	</div>
</div>

<!-- Section 1: User Data -->
<div id="logo_banner">
    <div class="container">
		<img id="logo" src="/images/quote.png" />
		<?php if(isset($_SESSION['corporate']) && $_SESSION['corporate']=='active' && $_SESSION['corporate_id']==$_SESSION['user_id']){ ?>
				<form action="/corporate/hub/corporate_login.php" method="POST">
				<input type="hidden" name="user_id" value="<?php echo $_SESSION['corporate_id']; ?>" />
				<input class="button4" value="Return To Corporate" type="submit" style="float:right;" />
				</form>
		<?php } ?>
		<a href="/home/logout.php"><div class="button4" style="float:right;">Logout</div></a>
		<div class="button4" id="takeTheTour" style="float:right;">Tour</div>
		<?php if($logins>20){ ?>
			<div class="button4" id="showsteps" style="float:right;">What Do I Do?</div>
		<?php } ?>
		<br style="clear:both;" />
    </div>
</div>
<div class="hub_top">
    <div class="top_container">
        <div id="pic_holder">
		<?php  if($extension=="none"){ ?>
				<img id="company_pic" src="/user_data/user_images/default.png"  />
				<?php  } else { ?>
				<img id="company_pic" src="/user_data/user_images/<?php  echo $user_id; ?>.<?php echo $user_extension; ?>"  />
		<?php } ?>
        </div>
        <div id="info_holder" class="left_pad">
        	<div class="head1"><?php echo $name; ?></div>
            <div class="head2"><?php echo $type; ?></div>
        </div>
        <div class="right_pad">
            <div class="head2">Your URL: brevada.com/<?php echo $url_name; ?></div>
        </div>
        <br style="clear:both;" />
  	 </div>
</div>
<div class="section_bottom">
	<div class="top_container">
		<a class="open_modal" id="modal_changepic"><div class="section_bottom_button">Change Picture</div></a>
		<a class="open_modal" id="modal_updateinfo"><div class="section_bottom_button">Update Info</div></a>
		<div class="right_pad"> <div class="head3">Referral Code: <strong><?php echo $referral_code; ?></strong>&nbsp;&nbsp;Referral Credit: <strong><?php echo $referral_credits; ?></strong></div></div>
		<br style="clear:both;" />
	</div>
</div>
<div class="container">
<!--Section 1.5 HOW IT WORKS TABS -->
  <div id="works_section" class="hub_section">
    <div class="section_container">
    	<div id="works_holder">
			<div class="works1" id="works_single">
				<div id="works_single_title">Upload Picture</div>
				<div id="works_single_description">Click 'Change Picture' to upload your company logo or picture.</div>
			</div>
			<div class="works2" id="works_arrow">&rarr;</div>
			<a href="#expanderHeadAspects">
				<div class="works3" id="works_single" style="-moz-transform: scale(1.1);
				-ms-transform: scale(1.1);
				-o-transform: scale(1.1);
				-webkit-transform: scale(1.1);
				transform: scale(1.1);
				">
					<div id="works_single_title" style="-webkit-box-shadow: 0px 0px 5px 0px rgba(50, 50, 50, 0.75);-moz-box-shadow:0px 0px 5px 0px rgba(50, 50, 50, 0.75);
				box-shadow:0px 0px 5px 0px rgba(50, 50, 50, 0.75); background:#ee2b2b;">Set Up</div>
					<div id="works_single_description">
						Specify the different <span id="emphasis">products, services, or other aspects</span> of your business that you want feedback on.
						<div class="top_bar_button2" style="margin-top:10px;">Get Started</div>
					</div>
				</div>
			</a>
			<div class="works4" id="works_arrow">&rarr;</div>
			<a href="#expanderHeadMarketing">
			<div class="works5" id="works_single">
				<div id="works_single_title">Share</div>
				<div id="works_single_description">Share your Brevada Page using your <span id="emphasis">Feedback Marketing</span> tools.</div>
			</div>
			</a>
			<div class="works6" id="works_arrow">&rarr;</div>
			<a href="#expanderHeadManage">
			<div class="works7" id="works_single">
				<div id="works_single_title">View Results</div>
				<div id="works_single_description">View, analyze, respond, and share to feedback using your <span id="emphasis">Feedback Management</span> tools.</div>
			</div>
			</a>
			<br style="clear:both;" />
		</div>
	</div>
</div>
<?php if($logins==0){ ?>
<script type='text/javascript'>
$(document).ready(showSteps);
</script>
<?php } ?>
<!-- Section 2: Feedback Gathering -->
<div id="hub_title">
	Currently Gathering Feedback On: <a class="hide_section" id="expanderHeadAspects"><span id="expanderSignAspects">Hide</a>
    <br />
    <span id="sub_title">The Products, Services, and Other Aspects of your business</span>
</div>
<div id="expanderContentAspects" class="hub_section">
    <div class="section_container">
        <?php $this->add(new View('../pages/hub/posts/new_post.php', array('user_id' => $user_id))); ?>
    	<br style="clear:both;" />
    </div>
</div>
<div id="sized_containerHub"> 
<div id="hub_title">
	Feedback Marketing  <a class="hide_section" id="expanderHeadMarketing"><span id="expanderSignMarketing">Hide</a><br />
    <span id="sub_title">Tell Customers To Give You Feedback</span>
</div>
<div id="expanderContentMarketing" class="hub_section">
    <div class="section_container">
    	<a class="open_modal" id="modal_url">
    	<div class="app">
        	<div class="app_box">
				<img class="app_icon" src="<?php echo p('HTML','path_hubicons','share.png'); ?>" />
				<div class="app_description">URL To Brevada Page</div>
			</div>
        </div>
        </a>
        <a class="open_modal" id="modal_qr">
        <div class="app">
        	<div class="app_box">
				<img class="app_icon" src="<?php echo p('HTML','path_hubicons','qr.png'); ?>" />
				<div class="app_description">QR Code To Brevada Page</div>
			</div>
        </div>
        </a>
        <a class="open_modal" id="modal_print">
        <div class="app">
        	<div class="app_box">
				<img class="app_icon" src="<?php echo p('HTML','path_hubicons','print.png'); ?>" />
				<div class="app_description">Printables</div>
			</div>
        </div>
        </a>
        <a href="http://www.facebook.com/sharer/sharer.php?u=http%3A%2F%2Fwww.brevada.com%2F<?php echo $url_name; ?>" target="_blank">
        <div class="app">
        	<div class="app_box" style='background:#3B5998;'>
				<img class="app_icon" src="<?php echo p('HTML','path_hubicons','facebook.png'); ?>" />
				<div class="app_description">Tell Customers On Facebook</div>
			</div>
        </div>
        </a>
        <a href="http://twitter.com/share?text=Give us feedback!&url=http://brevada.com/<?php echo $url_name; ?>" title="Share on Twitter" ref="nofollow" target="_blank">
        <div class="app">
        	<div class="app_box" style='background:#4099FF;'>
				<img class="app_icon" src="<?php echo p('HTML','path_hubicons','twitter.png'); ?>" />
				<div class="app_description">Tell Your Customers On Twitter</div>
			</div>
        </div>
        </a>
        <br style="clear:both;" />
    </div>
</div>
<div id="hub_title">
	Feedback Gathering <a class="hide_section" id="expanderHeadGather"><span id="expanderSignGather">Hide</a>
    <br />
    <span id="sub_title">'One Click' Feedback</span>
</div>
<div id="expanderContentGather" class="hub_section">
    <div class="section_container"> 
		<a href="http://brevada.com/<?php echo $url_name; ?>" target="_blank"> 
			<div class="app">
				<div class="app_box" id="app_page">
					<img class="app_icon" src="<?php echo p('HTML','path_hubicons','page.png'); ?>" />
					<div class="app_description"><strong>View Your Brevada Page</strong></div>
				</div>
			</div>
		</a>
        <a class="open_modal" id="modal_email">
			<div class="app">
				<div class="app_box" id="app_email">
					<img class="app_icon" src="<?php echo p('HTML','path_hubicons','email.png'); ?>" />
					<div class="app_description">Email Gathering</div>
				</div>
			</div>
        </a>
        
        <a class="open_modal" id="modal_widgets">
			<div class="app">
				<div class="app_box" id="app_widgets">
					<img class="app_icon" src="<?php echo p('HTML','path_hubicons','widgets.png'); ?>" />
					<div class="app_description">Widgets and Integration</div>
				</div>
			</div>
        </a>
        <a href="/hub/voting" target="_blank"> 
			<div class="app">
				<div class="app_box" id="app_station">
					<img class="app_icon" src="<?php echo p('HTML','path_hubicons','tablet.png'); ?>" />
					<div class="app_description">Voting Station Login</div>
				</div>
			</div>
        </a>
        <br style="clear:both;" />
    </div>
</div>
<div id="hub_title">
	Feedback Management <a class="hide_section" id="expanderHeadManage"><span id="expanderSignManage">Hide</a>
    <br />
    <span id="sub_title">View And Share Your Feedback</span>
</div>
<div id="expanderContentManage">
<?php  if($level>1){ //ACTIVE ACCOUNT ?>
<div style="width:100%; background:#fff; padding: 20px; margin-bottom:20px;">
	<a class="open_modal" id="modal_certificates">
	<div class="app">
		<div class="app_box">
			<img class="app_icon" src="<?php echo p('HTML','path_hubicons','certificate.png'); ?>" />
			<div class="app_description">Certificates</div>
		</div>
	</div>
	</a>
	<a class="open_modal" id="modal_approved">
		<div class="app">
			<div class="app_box">
				<img class="app_icon" src="<?php echo p('HTML','path_hubicons','approved.png'); ?>" />
				<div class="app_description">Approved</div>
			</div>
		</div>
	</a>
	<br style="clear:both;" />
</div>
<div style="float:left; width:850px; margin-top:10px;"> 
<!-- ACTIVITY -->
<div style="float:left;">
<?php $this->add(new View('../pages/hub/includes/hub_activity.php', array('user_id' => $user_id, 'extension' => $extension))); ?>
</div>
<br style="clear:both;" />
<!-- ASPECTS -->
<div style="float:left; width:840px; margin-top:5px; padding-top:5px; border-top:0px solid #dcdcdc;">
	<div id="aspect_header" style="display:none;"> <a id="expanderHead5Products" data-reveal-id="unlock">
		<div class="new_aspect_button" id="aspect_header_left" style="float:left; border:1px solid #dcdcdc;"> <span id="expanderSign5Products">+</span> New Aspect </div>
		</a> <br style="clear:both;" />
		</div>
        <a id="aspects"></a>
        <div id="box_holder">
<?php
	$query=Database::query("SELECT * FROM posts WHERE user_id='{$user_id}' ORDER BY id DESC");
	
	if($query->num_rows==0){
		echo "<br /><span style='font-size:12px; color:#777; font-family:helvetica;'><center>Click 'New Post' to specify a product, service or aspect of your business that you would like feedback on.</center></span>";
	}

	while($rows=$query->fetch_assoc()){
		$post_id=$rows['id'];
		$active = $rows['active'];
		$post_name=$rows['name'];
		$post_extension=$rows['extension'];
		$post_description=$rows['description'];

		$this->add(new View('../pages/hub/posts/hub_box.php', array('post_id' => $post_id, 'active' => $active, 'post_name' => $post_name, 'post_extension' => $post_extension, 'post_description' => $post_description, 'extension' => $extension)));
	}
?>
          <br style="clear:both;" />
          <br style="clear:both;" />
        </div>
      </div>
      <br style="clear:both;" />
    </div>
    
    <!-- FAR RIGHT --> 
    <div id="far_right" style="float:right; width:300px; margin-top:10px;">
      <div class="right" style="float:right; margin-right:5px;">
        <div id="activity_box" style="width:277px; min-height:30px; max-height:307px;  overflow:auto; ">
          <div id="activity_header" style="border-bottom:1px solid #ededed;">
            <div style="float:left;">
              <div id="activity_head_text" style="font-size:12px;" >Reviewers</div>
              <br style="clear:both;" />
            </div>
             <br style="clear:both;" />
          </div>
          <?php $this->add(new View('../pages/hub/includes/reviewers_display.php', array('user_id' => $user_id))); ?>
        </div>
        <div id="activity_box" style="width:277px; min-height:30px; max-height:200px;  overflow:scroll; margin-top:10px;">
          <div id="activity_header" style="border-bottom:1px solid #ededed;">
            <div style="float:left;">
              <div id="activity_head_text" style="font-size:12px;" >Suggestions</div>
              <br style="clear:both;" />
            </div>
            <br style="clear:both;" />
          </div>
          <?php $this->add(new View('../pages/hub/includes/message_display.php', array('user_id' => $user_id))); ?>
        </div>
      </div>
    </div>
<?php } else { //FREE TRIAL ?>
<img src="/images/hub_blur.png" style="width:100%; margin-top:10px; " />
<div id="locked_bar">
	<div id="locked_center">
		<div id="locked_title"></div>
	</div>
	<div id="locked_content" align="center" >
		<div align="center">
			<div style="color:#555;"><strong>Brevada Free Trial - Activate Account To View Your Feedback</strong></div>
			<div style="margin-top:4px;"><span style="font-size:12px; color:#777;"> You need to activate your Brevada Services to view, analyze, and respond to feedback.<br /><br />
			<?php $this->add(new View('../hub/includes/upgrade_button.php', array('upgrade_message' => "Upgrade Brevada"))); ?>
			<div style="margin-top:10px;">Or</div>
			<br />
			<div style="margin-top:0px; font-size:12px;">Enter a promo code:
				<form action="promo_validate.php" method="post">
				  <input type="text" name="promo"  style="width:80px; margin-top:10px; padding:10px; font-size:12px; outline:none; border:1px solid #f7f7f7;" />
				  <input type="submit" value="&rarr;" name="submit" style="padding:11px; cursor:pointer; background:#f9f9f9; border:1px solid #f3f3f3; font-weight:bold; width:30px; margin-left:5px; outline:none;">
				</form>
			</div>
			</span>
			</div>
		</div>
	</div>
</div>
<?php } ?>
</div>  
</div>
<?php $this->add(new View('../pages/hub/includes/tour.php', array('url_name' => $url_name, 'logins' => $logins))); ?>
<br style="clear:both;" />