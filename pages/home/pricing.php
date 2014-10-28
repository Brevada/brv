<?php 
$this->add(new View('../template/home_header.php'));
$this->addResource('/css/pricing.css');
?>

<div class="home_section" style="background:#fff; margin-top:0px; padding-bottom:100px;">
	<div class="container">
		<div style="width:100%; text-align:center;">
			<div id="home_text">
				You're only <strong>one step</strong> away!
			</div>
			
			<div id="home_text2" style="float:none; width:500px; margin:0 auto; text-align:center;">
				In <span class="emphasis">less than 2 minutes</span> you can be fully set up with a premium customer feedback and communication platform. 
			</div>
			
			<br />
			<div id="pricing_holder">
				
				<div id="pricing_box">
					
					<div id="pricing_top" style="background:#888;">
						
						<div class="pricing_title">
							<div  id="home_text" style="color:#fff;">
							FREE
							</div>
						</div>
						
						<div id="pricing_under">
							<i>No credit card required</i>
							<br />
							<strong>Get set up for free</strong>
						
							
						</div>
						
					</div>
					
					<div id="pricing_bottom">
						<a href="/home/signup.php?l=free"><div id="pricing_button">Get Started</div></a>
					</div>
					
					<div id="pricing_info">
						1 aspect
					</div>
					
					<div id="pricing_info" class="pricing_grey">
						Brevada Page <span class="lighter">Desktop and Mobile</span>
		
						
					</div>
					
					<div id="pricing_info">
						Email Support
					</div>
					
				
				</div>
				
				
			
				<div id="pricing_box" >
					
					<div id="pricing_top" style="background:#12a5f4;">
						
						<div class="pricing_title">
							<div  id="home_text" style="color:#fff;">
							PERSONAL
							</div>
							<div  id="pricing_price">
							<span id="price">$14</span>/month
							</div>
							
						</div>
						
						<div id="pricing_under">
							<i>Billed at $168 for one year</i>
							<br />
							<strong>The basic package.</strong>
						</div>
						
					</div>
					
					<div id="pricing_bottom">
						<a href="/home/signup.php?l=personal"><div id="pricing_button">Get Started</div></a>
					</div>
					
					<div id="pricing_info">
						<span class="emphasis">50 responses</span>/month
						<br />
						Unlimited Aspects
					</div>
					
					<div id="pricing_info" class="pricing_grey">
						Brevada Page <span class="lighter">Desktop and Mobile</span> &nbsp;<a href="../..#page" target="_BLANK">&bull;</a>
						<br />
						Custom URL and Barcode
						<br />
						Marketing Material &nbsp;<a href="../..#marketing" target="_BLANK"> &bull;</a>
						<br />
						Charts and Data Analysis
						
					</div>
					
					<div id="pricing_info">
						Email Support
					</div>
					
				
				</div>
				
				<div id="pricing_box" style="width:260px;  margin-top:-15px;
				
				-webkit-box-shadow: 0px 0px 20px 0px rgba(50, 50, 50, 0.75);
-moz-box-shadow:    0px 0px 20px 0px rgba(50, 50, 50, 0.75);
box-shadow:         0px 0px 20px 0px rgba(50, 50, 50, 0.75);
				">
				
					<div id="pricing_top" style="background:#3369e8;">
						
						<div class="pricing_title">
							<div  id="home_text" style="color:#fff;">
							PROFESSIONAL
							</div>
							<div  id="pricing_price">
							<span id="price">$30</span>/month
							</div>
							
						</div>
						
						<div id="pricing_under">
							<i>Billed at $360 for one year</i>
							<br />
							<strong>The complete package.</strong>
						</div>
						
					
						
						
					</div>
					
						<div id="pricing_bottom">
							<a href="/home/signup.php?l=professional"><div id="pricing_button">Get Started</div></a>
						</div>
						
					<div id="pricing_info">
						<span class="emphasis">Unlimited</span> Responses
						<br />
						Unlimited Aspects
						<br />
						1 Additional User
					</div>
					
					<div id="pricing_info" class="pricing_grey">
						Brevada Page <span class="lighter">Desktop and Mobile</span> &nbsp;<a href="../..#page" target="_BLANK">&bull;</a>
						<br />
						Custom URL and Barcode 
						<br />
						Marketing Material &nbsp;<a href="<?php echo p('HTML','path_home','complete.php#tab1'); ?>" target="_BLANK">&bull;</a>
						<br />
						Charts and Data Analysis 
						<br />
						2-Way Communication &nbsp;<a href="<?php echo p('HTML','path_home','complete.php#tab3'); ?>" target="_BLANK">&bull;</a>
						<br />
						E-mail Feedback Acquisition &nbsp;<a href="<?php echo p('HTML','path_home','complete.php#tab2'); ?>" target="_BLANK">&bull;</a>
						<br />
						Website Integration &nbsp;<a href="<?php echo p('HTML','path_home','complete.php#tab2'); ?>" target="_BLANK">&bull;</a>
						<br />
						Tablet Voting Station &nbsp;<a href="<?php echo p('HTML','path_home','complete.php#tab2'); ?>" target="_BLANK">&bull;</a>
						<br />
						Brevada Approved &nbsp;<a href="/home/approved" target="_BLANK">&bull;</a>
					</div>
					
					<div id="pricing_info">
						<span class="emphasis">24/7</span> Email and Phone Support
					</div>
				
				</div>
				
				<div id="pricing_box">
				
					<div id="pricing_top" style="background:#3b5998;">
						
						<div class="pricing_title">
							<div  id="home_text" style="color:#fff;">
							ENTERPRISE
							</div>
							<div  id="pricing_price">
							<span id="price">$90</span>/month
							</div>
							
						</div>
						
						<div id="pricing_under">
							<i>Billed at $1080 for one year</i>
							<br />
							<strong>The ultimate package.</strong>
						</div>
						
						
					</div>
					
					<div id="pricing_bottom">
						<a href="/home/signup.php?l=enterprise"><div id="pricing_button">Get Started</div></a>
					</div>
					
							
					<div id="pricing_info">
						<span class="emphasis">Unlimited</span> Responses
						<br />
						Unlimited Aspects
						<br />
						4 Additional Users
						<br />
						<span class="emphasis">Personal Brevada Consultant</span>
						<ul style="font-size:10px; margin-left:20px;">
							<li>Account Set Up</li>
							<li>Custom Generated Marketing Material</li>
							<li>Feedback Presence Consulting</li>
							<li>Monthly Phone Meetings</li>
						</ul>
					</div>
					
					<div id="pricing_info" class="pricing_grey">
						Brevada Page <span class="lighter">Desktop and Mobile</span> &nbsp;<a href="../..#page" target="_BLANK">&bull;</a>
						<br />
						Custom URL and Barcode 
						<br />
						Marketing Material &nbsp;<a href="../..#marketing" target="_BLANK">&bull;</a>
						<br />
						Charts and Data Analysis 
						<br />
						2-Way Communication &nbsp;<a href="../..#communicate" target="_BLANK">&bull;</a>
						<br />
						E-mail Feedback Acquisition &nbsp;<a href="../..#email" target="_BLANK">&bull;</a>
						<br />
						Website Integration &nbsp;<a href="../..#widgets" target="_BLANK">&bull;</a>
						<br />
						Tablet Voting Station &nbsp;<a href="../..#voting" target="_BLANK">&bull;</a>
						<br />
						Brevada Approved &nbsp;<a href="/home/approved" target="_BLANK">&bull;</a>
			
						
					</div>
					
					<div id="pricing_info">
						<span class="emphasis">24/7</span> Email and Phone Support
					</div>
					
				</div>
				
				<br style="clear:both;" />
				
			</div>
						
		</div>
		
	
		<br style="clear:both;" />
	</div>
</div>

<?php $this->add(new View('../template/long_footer.php')); ?>