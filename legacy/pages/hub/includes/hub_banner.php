<?php
// This file is never used.

$this->addResource('/css/hub_banner.css');
$this->addResource("<style type='text/css'>#container_image { background:url('user_images/{$user_id}.{$user_extension}'); }</style>", false, true);

$user_id = $this->getParameter('user_id');
$referral_credits = $this->getParameter('referral_credits');
$referral_code = $this->getParameter('referral_code');
$url_name = $this->getParameter('url_name');
?>
<div id="container_image"></div>
<div id="banner_main">
	<div id="banner_main_content">
		<div id="banner_main_logo"><img src="/images/quote.png" style="height:30px; margin:0 auto; margin-top:5px;" /></div>
		<br style="clear:both;" />
	</div>	
</div>
<div id="container_white">
	<div id="white_holder" >
			<a href="http://brevada.com/<?php  echo $url_name; ?>" class="tooltip" target="_BLANK" style="text-decoration:none; outline:none; border:none;">
			<div id="white_button" style="-webkit-border-top-left-radius: 3px;-moz-border-top-left-radius: 3px;border-top-left-radius: 3px;-webkit-border-bottom-left-radius: 3px;-moz-border-bottom-left-radius: 3px;border-bottom-left-radius: 3px;margin-left:0px;padding-left:0px;">
		<strong>Your Page</strong>
		<span>brevada.com/<?php  echo $url_name; ?></span>
	</div>
	</a>
	<a id="widgets" style="text-decoration:none;">
		<div id="white_button">Widgets</div>
	</a>
	<a class="openModal" id="email_modal" userid='<?php echo $user_id; ?>'>
		<div id="white_button">Get Email Feedback</div>
	</a>
	<a href="../../voting" class="tooltip" target="_BLANK" style="text-decoration:none;">
		<div id="white_button">Voting Station Login</div>
		<span><strong>Allow people to vote right at your location!</strong><br />
		This version of your page allows unlimited votes from a single ip address and is compatible on computers, tablets and mobile phones.<br />Have this open in your store or office and allow people to give you feedback right on the spot!</span>
	</a>
	<div id="promos">
	<div id="white_button">Feedback Marketing</div>
	</div>
	<a href="<?php echo p('HTML','path_qr', "{$user_id}.png"); ?>" target="_BLANK" class="tooltip" style="text-decoration:none;">
		<div id="white_button" style="width:130px;">View Profile Barcode</div>
	</a>
	<div id="banner_right">
		Referral Code: <strong><?php echo $referral_code; ?></strong> &nbsp;&nbsp; Referral Credit: <strong>$<?php echo $referral_credits; ?></strong>
	</div>
		<br style="clear:both;" />
	</div>
	<br style="clear:both;" />
	<br style="clear:both;" />
</div>
<div id="bottom_banner_pic">
	<div id="bottom_right">
		24/7 Customer Service: <strong>1 (855) 484-7451</strong> or <strong>support@brevada.com</strong>
	</div>
</div>