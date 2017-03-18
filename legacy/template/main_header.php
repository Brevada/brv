<?php
$this->addResource('/css/main_header.css');
$this->addResource('/css/layout.css');
?>
<div style="width:100%; min-width:1050px; position:fixed; height:55px; top:-8px; left:0px; background: rgba(255,255,255,1); -webkit-box-shadow: 0px 2px 2px rgba(50, 50, 50, 0.2); -moz-box-shadow: 0px 2px 2px rgba(50, 50, 50, 0.2); box-shadow: 0px 2px 2px rgba(50, 50, 50, 0.2); padding-top:7px; z-index:999999999;">
	<div style="float:left; padding:11px; border-right:0px solid #dcdcdc;">
		<a href="/"><img itemprop="logo" src="/images/brevada.png" height="33px" style="outline:none; border:none;" /></a>
	</div>
	<div style="float:left; margin-top:1px; margin-left:40px;">
		<a href="/index.php#features" id="ref_left">
			<div id="left_button" style="float:left; background:#fdfdfd; ">
				<div id="left_button_text">Features</div>
			</div>
		</a>
		<a href="/contact.php" id="ref_left">
			<div id="left_button" style="float:left; border-left:none;">
				<div id="left_button_text">Contact</div>
			</div>
		</a>
		<a href="/brevada" id="ref_left">
			<div id="left_button" style="float:left; border-left:none;">
				<div id="left_button_text">Feedback</div>
			</div>
		</a>
		<a href="/index.php#pricing" id="ref_left">
			<div id="left_button" style="float:left; border-left:none; border-right:none; ">
				<div id="left_button_text">Pricing</div>
			</div>
		</a>
		<div style="float:left; padding:9px; margin-left:20px;">
			<form method="post" action="/home/search.php">
				<div style="float:left;">
					<input id="search" class="search" name="needle" value="Search for a page" onFocus="if(this.value == 'Search for a page'){this.value = '';}" onBlur="if(this.value == ''){this.value='Search for a page';}" />
				</div>
				<div style="float:right;">
					<img style="height:24px; opacity:1;margin: 8px 0px 0px -34px;" src="/images/search-2-xxl.png">
				</div>
			</form>
		</div>
		<div class="left" style="margin-top:7px; display:none;">
			<div id="icons" style="font-size:35px;">
				<div class="left">
					<i class="foundicon-graph"></i>
				</div>
				<div class="left" style="margin-left:5px;">
					<i class="foundicon-star"></i>
				</div>
			</div>
		</div>
	</div>
	<!-- 296FBA -->
	<div style="float:right; padding:0px; margin-top:5px; margin-right:0px;">
		<?php if(!Brevada::IsLoggedIn()){ ?>
		<a href="/home/login.php" id="ref">
			<div id="right_button" style="float:right; background:#f1f1f1;">
				<div id="button_text">Login</div>
			</div>
		</a>
		<a href="/home/signup.php" id="ref">
			<div id="right_button" style="float: right; border-right: none; color: #ffffff; background: #cd0000; width:80px;">
				<div id="button_text"><strong>Free Trial</strong></div>
			</div>
		</a>
		<?php } else { ?>
		<a href="/home/logout.php" id="ref">
			<div id="right_button" style="float:right;">
				<div id="button_text">Logout</div>
			</div>
		</a>
		
		<a href="/hub" id="ref">
			<div id="right_button" style="float: right; border-right: none;">
				<div id="button_text">Profile</div>
			</div>
		</a>
		<?php } ?>
		
		<br style="clear:both;" />
		<div class="f" style="float: left; padding: 1px; margin: 0px; margin-left: 11px; text-decoration: underline; font-size: 11px;">
			<div class="left"></div>
		</div>
	</div>
</div>