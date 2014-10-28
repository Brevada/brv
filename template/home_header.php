<?php
$this->addResource('/css/home_header.css');
$this->addResource('/css/layout.css');
?>

<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<script type='text/javascript'>
	$(window).scroll(function(){
		if(window.scrollY > 100){
			$('#fixed_bar').slideDown();
		} else {
			$('#fixed_bar').slideUp();
		}
	});
</script>

<div id="fixed_bar">
	<div class="container">
		<div class="top_bar_left">
			<a href="/index.php"><img src="/images/brevada.png" style="height:35px;" /></a>
		</div>
		<div class="top_bar_left" style="float:right;">
			<a href="/home/signup.php"><div class="top_bar_button2">Get Started</div></a>
		</div>
	</div>
</div>

<div id="top_bar">
	<div class="container">
		<div class="top_bar_left">
			<a href="/index.php"><img src="/images/nosurvey_logo.png" style="height:45px; margin-top:-5px;" /></a>
		</div>
		<div class="top_bar_left" ></div>
		<div class="top_bar_left" id="social">
			<div class="fb-like" data-href="https://facebook.com/brevadafeedback" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div>
		</div>
		<div class="top_bar_left" id="social">
			<a href="https://twitter.com/BrevadaFeeback" class="twitter-follow-button" data-width="150px" data-show-screen-name="false" data-show-count="true" data-lang="en">Follow @twitterapi</a>
			<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
		</div>
		<div class="top_bar_left" style="float:right;">
			<a href="/home/signup.php"><div class="top_bar_button2">Get Started</div></a>
		</div>
		<div class="top_bar_left" style="float:right;">
			<a href="/home/login.php"><div class="top_bar_button">Log In</div></a>
		</div>
		<br style="clear:both;" />
	</div>
</div>