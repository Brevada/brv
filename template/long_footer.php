<?php $this->addResource('/css/long_footer.css'); ?>
<div id="holder" class='footer-holder'>
	<div class="container">
			<div style="margin:0 auto;  border-bottom: 0px solid #FFAEA5; padding:20px 5px 5px 5px;">
				<div class="left" >
					<img src="/images/quote.png" style="height:30px;"/>
				</div>
				<br style="clear:both;" />
			</div>
			<div style="margin:0 auto; padding:5px;">
				<div class="left footer-column">
					<div class="bottom_link_big">
					<?php _e('Account'); ?>
					</div>
					<div>
						<a href="/home/login.php"><div class="bottom_link"><?php _e('Login'); ?></div></a>
						<a href="/home/signup.php"><div class="bottom_link"><?php _e('Signup'); ?></div></a>
					</div>
				</div>
				<div class="left footer-column">
					<div class="bottom_link_big" style=" ">
					<?php _e('Contact'); ?>
					</div>
					<div>
						<a href="/brevada"><div class="bottom_link"><?php _e('Feedback'); ?></div></a>
						<a href="mailto:contact@brevada.com"><div class="bottom_link"><?php _e('Email'); ?></div></a>
						<a href="http://facebook.com/brevadafeedback" target="_BLANK"><div class="bottom_link">Facebook</div></a>
						<a href="https://twitter.com/BrevadaFeedback" target="_BLANK"><div class="bottom_link">Twitter</div></a>
					</div>
				</div>
				<div class="left footer-column">
					<div class="bottom_link_big" style=" ">
					<?php _e('Social'); ?>
					</div>
					<div>
						<div style="border-left:none;background:none;margin-top:6px;">
						<div class="fb-like" data-href="https://facebook.com/brevadafeedback" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div>
						</div>
						<div  style=" border-left:none;background:none; margin-top:10px;">
						<a href="https://twitter.com/BrevadaFeeback" class="twitter-follow-button" data-width="150px" data-show-screen-name="false" data-show-count="true" data-lang="en"><?php _e('Follow'); ?> @twitterapi</a>
						<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
						</div>	
					</div>
				</div>
				<div class="left footer-column">
					<div class="bottom_link_big">
					<?php _e('Resources'); ?>
					</div>
					<div>
						<?php if(isset($_SESSION['lang']) && $_SESSION['lang'] == 'fr_CA') { ?>
						<a href="/index?lang=en"><div class="bottom_link">Speak English?</div></a>
						<?php } else { ?>
						<a href="/index?lang=fr"><div class="bottom_link">Parlez-vous français?</div></a>
						<?php } ?>
					</div>
				</div>

				<br style="clear:both;" />
				<br style="clear:both;" />
				<div style="width:100%; text-align:center; font-size:12px; color:#FFAEA5;">
				&copy; <?php echo date('Y'); ?> brevada.com. <?php _e('All rights reserved.'); ?> <?php _e('Questions?'); ?> <a href='mailto:<?php _e('contact@brevada.com'); ?>' style='color:#FFAEA5;'><?php _e('contact@brevada.com'); ?></a> <?php _e('or'); ?> <strong>1 (844) BREVADA</strong>
				</div>
			</div>
	</div>
</div>