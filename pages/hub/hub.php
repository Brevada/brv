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



<!-- DROP DOWN LIST -->
<div class="more_list">
	<?php if(isset($_SESSION['corporate']) && $_SESSION['corporate']=='active' && $_SESSION['corporate_id']==$_SESSION['user_id']){ ?>
	<form action="/corporate/hub/corporate_login.php" method="POST">
	<input type="hidden" name="user_id" value="<?php echo $_SESSION['corporate_id']; ?>" />
	<input class="list_button" value="Return To Corporate" type="submit" style="float:right;" />
	</form>
	<?php } ?>
	<a class="open_modal" id="modal_updateinfo"><div class="list_button">Update Info</div></a>
	<a class="open_modal" id="modal_changepic"><div class="list_button">Change Picture</div></a>
	
	<a href="/home/logout.php"><div class="list_button" style="float:right;">Logout</div></a>
	<div class="list_button" id="takeTheTour" style="float:right;">Tour</div>
	
	
	<div class="right_pad"> 
		<div class="head3">
			Referral Code: <strong><?php echo $referral_code; ?></strong>
			<br />
			Referral Credit: <strong><?php echo $referral_credits; ?></strong>
		</div>
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
            <div class="head2.06">
            	Your QR Code:
            	<a href="/user_data/qr/<?php echo $user_id; ?>.png" target="_TOP">View</a>
            </div>
        </div>
        <br style="clear:both;" />
  	 </div>
</div>






<div class="hub_container"> 

<!-- LEFT SIDE -->
<div class="hub_left">

<div class="hub_left_bar hub_left_bar_top">

	<div style="height:50px; cursor:default; opacity:1; background:#333; border-bottom:1px solid #000; background:url('/user_data/user_images/<?php  echo $user_id; ?>.<?php echo $user_extension; ?>'); background-size:100%; background:#FF2B2B;
	background-image:       linear-gradient(0deg, transparent 24%, rgba(0, 0, 0, .1) 25%, rgba(0, 0, 0, .1) 26%, transparent 27%, transparent 74%, rgba(0, 0, 0, .1) 75%, rgba(0, 0, 0, .1) 76%, transparent 77%, transparent), linear-gradient(90deg, transparent 24%, rgba(0, 0, 0, .1) 25%, rgba(0, 0, 0, .1) 26%, transparent 27%, transparent 74%, rgba(0, 0, 0, .1) 75%, rgba(0, 0, 0, .1) 76%, transparent 77%, transparent);
  background-size:30px 30px;">
		<!-- LEAVE EMPTY -->
	</div>
	
		
	<div style="padding:5px 5px; height:50px; cursor:default; opacity:1; background:rgba(0,0,0,0.3); margin-top:-50px;">
		<div class="info_holder">
        	<div class="head1" style="height:20px; overflow:hidden;"><?php echo $name; ?></div>
        </div>
    	<br />
        <div class="info_holder">
        	<div class="button2">brevada.com/<?php echo $url_name; ?></div>
        </div>
		<br style="clear:both;" />
	</div>

	
	<div class="leftside_button">
			<img class="leftside_button_img" src="<?php echo p('HTML','path_hubicons','paper.png'); ?>" />
			<br />
			Share by Print
	</div>
	
	
	<a href="http://www.facebook.com/sharer/sharer.php?u=http%3A%2F%2Fwww.brevada.com%2F<?php echo $url_name; ?>" target="_blank">
	<div class="leftside_button">
			<img class="leftside_button_img" src="<?php echo p('HTML','path_hubicons','facebook.png'); ?>" />
			<br />
			Share to Facebook
	</div>
	</a>
	
	<a href="http://twitter.com/share?text=Give us feedback!&url=http://brevada.com/<?php echo $url_name; ?>" title="Share on Twitter" rel="nofollow" target="_blank">
	<div class="leftside_button">
			<img class="leftside_button_img" src="<?php echo p('HTML','path_hubicons','twitter.png'); ?>" />
			<br />
			Share to Twitter
	</div>
	</a>
	
	<div class="leftside_button">
			<img class="leftside_button_img" src="<?php echo p('HTML','path_hubicons','widgets.png'); ?>"/>
			<br />
			Advanced Tools
	</div>
	
	<br />
	
	<div class="promo_img"><img  style="width:100%;" src="/images/promo_bounce.png" /></div>
	
	<div class="promo_img"><img style="width:100%;" src="/user_data/qr/<?php echo $user_id; ?>.png" /></div>
	
	
</div>

</div>

<!-- END LEFT SIDE -->


<!-- RIGHT SIDE -->
<div class="hub_right_section">

	<div id="logo_banner">
				<img id="logo" src="/images/quote.png" />
				<div id="more_open">
					<img class="more" src="/images/more_lines.png" />
				</div>
				<br style="clear:both;" />
	 </div>
	 
	 <div class="hub_right_toolbar">
	 	
	 	<div class="hub_right_toolbar_button" style="color:#FF2b2b;">
	 		+ Add New
	 	</div>
	 
	 	<div  class="hub_right_toolbar_button">
	 		Print Stats
	 	</div>
	 	
	 	<div  class="hub_right_toolbar_button">
	 		Another Button
	 	</div>
	 	
	 	<div id="toggle" class="hub_right_toolbar_button">
	 		Expand View
	 	</div>
	 	
	 	
	 </div>
	 
	 
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
	<div class="add_new">+</div>
     <br style="clear:both;" />
     </div>
</div>

<br style="clear:both;" />

</div>




<?php $this->add(new View('../pages/hub/includes/tour.php', array('url_name' => $url_name, 'logins' => $logins))); ?>
<br style="clear:both;" />