<?php
$this->addResource('/css/corporate_hub_banner.css');
$this->addResource('/js/corporate_hub_banner.js');

$user_id = $this->getParameter('user_id');
$user_extension = $this->getParameter('user_extension');
$active = $this->getParameter('active');
$logins = $this->getParameter('logins');
$picture = $this->getParameter('picture');
$name = $this->getParameter('name');
$url_name = $this->getParameter('url_name');

Database::query("UPDATE users SET `logins` = `logins` + 1 WHERE id='{$user_id}'");
?>

<div style="position:fixed; top:0px; left:0px; width:100%; z-index:19;">
	<div id="banner" style="background: #ffffff; border: 0px solid #dcdcdc; position:relative; z-index:9; height:30px;">
		<div style="float:left; padding:5px;">
			<img src="/images/brevada.png" height="20px" />
		</div>
		<div id="names" style="width:300px; float:right; margin-left:15px; overflow:hidden;" >
			<a href="/home/logout.php">
				<div style="float:right; padding:5px;">
					<div class="button4" style=" padding:2px; background:none; border:none;">Logout</div>
				</div>
			</a>
			<?php if(Brevada::validate($_SESSION['corporate']) == 'active' && Brevada::validate($_SESSION['corporate_id']) != Brevada::validate($_SESSION['user_id'])){ ?>
			<div id="corporate_floater" style="float:right; padding:5px;">
					<form action="/corporate/corporate_login.php" method="POST">
						<input type="hidden" name="user_id" value="<?php echo $_SESSION['corporate_id']; ?>" />
						<input class="button4" value="Return To Corporate" type="submit" style=" padding:2px; background:none; border:none; color:#bc0101;" />
					</form>
			</div>
			<?php } ?>
		</div>
	</div>
</div>

<div id="banner" style="background:url('/user_data/user_images/<?php echo $user_id; ?>.<?php echo $user_extension; ?>'); position:relative; z-index:8; background-size:100%; height:85px; margin-top:30px; min-width:1200px;"></div>

<div id="banner" style="margin-top:-95px; padding-top:10px; height:85px; position:relative; z-index:10; min-width:1200px;">
	<div style="width:100px; float:left; margin-left:10px; margin-top:10px;">
		<div class="left" id="names" style="height:50px; overflow:hidden;">
			<div style="max-height:120px; overflow:hidden;">
				<img id="banner_pic" src="/user_data/user_images/<?php echo $user_extension == 'none' ? 'default.jpg' : "{$user_id}.{$user_extension}"; ?>"  />
			</div>
			<?php if(($active == 'yes' && $logins < 3) || ($active == 'yes' && $picture != 'yes')){ ?>
			<div id="pic_outer_modal">
				<div id="pic_modal">
					<strong>Welcome to Brevada!</strong><br />Please upload your profile image by clicking the <strong>'Change Picture'</strong> Button. This is extremely important in ensuring your Brevada profile looks attractive and professional.<div class="pic_close">Got It!</div>
				</div>
			</div>
			<?php 
			}
			?>
		</div>
	</div>
	<div id="names" style="float:left; width:200px; margin-top:10px; height:50px;"><?php echo $name; ?><br /><span style="font-size:11px;"><a href="http://brevada.com/<?php  echo $url_name; ?>" style="color:#fbfbfb;">brevada.com/<?php echo $url_name; ?></a></span><br /></div>
	<br style="clear:both;" />
	<div id="options">
		<a id="expanderHead5Products" data-reveal-id="unlock" style="color:#f8f8f8; font-size:12px;">
			<div class="Optionsbutton" style="background: #bc0101; float:left; box-shadow:none; color:#f9f9f9; text-decoration:none;  margin:0px; text-shadow:none;" >
				<span id="expanderSign5Products">+</span>&nbsp;Add New Accounts
			</div>
		</a> 
		<a id="expanderHead9Products" data-reveal-id="unlock">
			<div class="Optionsbutton" style="margin:0px; box-shadow:none; margin-left:-6px;">Edit Information</div>
		</a>
		<a id="expanderHead6Products" data-reveal-id="unlock">
			<div class="Optionsbutton" style="margin:0px; box-shadow:none; margin-left:-6px;">Change Picture</div>
		</a>
		<a  id="expanderHead7Products" data-reveal-id="unlock"><div class="Optionsbutton" style="margin:0px; box-shadow:none; margin-left:-6px;">Change Password</div></a>
	</div>
</div>
<div style="display:none;margin-left:0px; margin-top:0px;">
<a id="expanderHead5Products" data-reveal-id="unlock" style="color:#f8f8f8; font-size:12px;"><div class="button2" style="width:500px; border:0px; border-radius:0px;"><span id="expanderSign5Products">+</span>&nbsp;Add A New Aspect Of Your Business</div></a> 
</div>