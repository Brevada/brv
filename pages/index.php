<?php
if(Brevada::IsLoggedIn()){ Brevada::Redirect('/hub'); }

$this->add(new View('../template/home_header.php'));
$this->addResource('css/index.css');
$this->addResource('css/pricing.css');
?>

<div id="second_bar">
	<div class="container">
    	<a href="/home/pricing.php"><div id="second_bar_button" class="top_bar_button_red" style="margin-left:0px; padding-left:0px;">Pricing</div></a>
        <a href="/home/approved.php"><div id="second_bar_button" class="top_bar_button_red">Approved</div></a>
        <a href="/home/howitworks.php"><div id="second_bar_button" class="top_bar_button_red">How It Works</div></a>
        <a href="/home/complete.php"><div id="second_bar_button" class="top_bar_button_red">Complete Systems</div></a>
        <a href="brevada"><div id="second_bar_button" class="top_bar_button_red">Feedback</div></a>
        <div id="second_bar_button" class="top_bar_button_red" style="float:right;"><span id="emphasis">Free</span> Customer Service Consulting: <strong>1 (844) BREVADA</strong></div>
        <br style="clear:both;" />
    </div>
</div>

<div  style="width:100%; background:url('images/applause1.png');"></div>

<div style="width:100%; background:rgba(0,0,0,0.3); height:500px; margin-top:-500px;-webkit-box-shadow: 0px 0px 5px 0px rgba(50, 50, 50, 0.75);-moz-box-shadow: 0px 0px 5px 0px rgba(50, 50, 50, 0.75);box-shadow: 0px 0px 5px 0px rgba(50, 50, 50, 0.75);position:relative; z-index:9;">
	<div class="slideshow" style="width:1000px; margin:0 auto; margin-top:-500px;">
	  	<div class="a_slide">
	  		<div class="slide_image" style="height:500px; overflow:hidden;"></div>
	  		<div class="slide_overlay" >
	  		 	<div class="container" style="width:1000px; margin:0 auto;">
					<div class="text_left">
						<strong>Complete</strong> customer feedback and communication.
						<div style="margin-top:6px; font-size:15px;">
							Revolutionary yet <span style="color:#ee2b2b;">affordable</span> feedback solutions to <strong>make your business succeed</strong>.
						</div>
						<br />
						<a href="signup"><div class="top_bar_button2" style="margin-top:8px; margin-left:0px;">Get Started</div></a>
						<a href="home/complete.php"><div class="top_bar_button3" style="margin-top:8px; margin-left:0px;">Learn More</div></a>
					</div>
					<div class="screen_video">
						<img src="images/2014_page.png" style="width:550px; margin-right:0px;" />
						<br />
						<img src="images/screenvid.gif" style="width:419px; margin-right:65px; position: relative; top:-316px;" />
					</div>
					<br style="clear:both;" />
	  			</div>
	  		</div>
	  		<br style="clear:both;" />
	  	</div>
	</div>
</div>

<div class="home_section" id="main_grey" style="padding-top:20px; padding-bottom:20px; position:relative; z-index:8;">
	<div class="container">
		<div class="left">
			<form action="home/search.php" method="post">
				<div class="left">
					<input class="inp" name="needle" id="home_search" placeholder="Search Brevada pages" />		
				</div>
				<div class="left" style="margin-left:-40px;">
					<img style="height:28px; opacity:1;" src="http://www.iconsdb.com/icons/preview/color/4b4b4b/search-2-xxl.png" />
				</div>
			</form>
		</div>
		<br style="clear:both;" />
	</div>
</div>

<a id="works"></a>
<div class="home_section" id="sec2"  style="border-top:1px solid #dcdcdc; height:550px; background:#f3f3f3; border-bottom:0px solid #dcdcdc; margin-top:0px; padding-bottom:50px;">
	<div class="container">
		<div id="t_title">
			<br style="clear:both;"/>
			<strong>The Brevada Complete Feedback System</strong>
			 <br />
			<span id="t_desc">The 3 components of a <span id="emphasis">complete feedback system</span> help you <strong>gather feedback, build your business, and win over your customers.</strong></span>
			<br />
		</div>
		<br style="clear:both;" />
		<a href="home/complete.php#tab1" id="t_href">
		<div id="t_holder_top" style="margin-left:0px;">
			<div id="t_bg">
				<div id="t_image"></div>
			</div>
			<div id="t_text">
				<img id="t_logo" src="images/logo_marketing.png" />
				<br />
				1. Feedback Marketing
				<hr id="t_hr"/>
				<span id="t_small">
				Show your customers where to go <br /> to communicate with you.
				</span>
				<br />
				<a href="home/complete.php#tab1"><div class="top_bar_button2" id="works_button">Learn More</div></a>
			</div>
			
			<br style="clear:both;" />
			<div id="t_triangle_top"></div>
			
			<br style="clear:both;" />
			<br style="clear:both;" />
			
			<div id="t_circle"></div>
		</div>
		</a>
		
		<a href="home/complete.php#tab2" id="t_href">
			<div id="t_holder_top" style="margin-left:40px;">
				<div id="t_bg">
					<div id="t_image"></div>
				</div>
				<div id="t_text">
					<img id="t_logo" src="images/logo_page.png" /><br />2. Feedback Gathering
					<hr id="t_hr" />
					<span id="t_small">Listen to your customers using your <span id="emphasis">Brevada Page</span> <br /> and other gathering features.</span>
					<br />
					<a href="home/complete.php#tab2"><div class="top_bar_button2" id="works_button">Learn More</div></a>
				</div>
				<br style="clear:both;" />
				<div id="t_triangle_top"></div>
				<br style="clear:both;" />
				<br style="clear:both;" />
				<div id="t_circle"></div>
			</div>
		</a>
		
		<a href="home/complete.php#tab3" id="t_href">
		<div id="t_holder_top" style="margin-left:50px;">
			<div id="t_bg">
			<div id="t_image"></div>
			</div>
			<div id="t_text">
				<img id="t_logo" src="/images/logo_communicate.png" />
				<br />3. Feedback Management
				<hr id="t_hr"/>
				<span id="t_small">View, analyze, respond, and learn from your feedback<br /> using Brevada's management features.</span>
				<br />
				<a href="home/complete.php#tab3"><div class="top_bar_button2" id="works_button">Learn More</div></a>
			</div>
			<br style="clear:both;" />
			<div id="t_triangle_top"></div>
			<br style="clear:both;" />
			<br style="clear:both;" />
			<div id="t_circle"></div>
		</div>
		</a>
		<br style="clear:both;" />
		<div id="timeline"></div>
		<br style="clear:both;" />
	</div>
</div>
    
<ul id="tiles">
	<li>
		<a id="benefits"></a>
		<div class="home_section" id="sec1">
			<div class="container">
				<div id="t_title">
					The Benefits of Brevada <strong>#TheBOB</strong><br />
					<span id="t_desc">How <span id="emphasis">Brevada's</span> complete feedback platform will help you <strong>Win Your Customers</strong></span><br />
					<span id="t_mini">Feedback Marketing. Feedback Gathering. Feedback Management.</span>
				</div>
				<br style="clear:both;" />
				<div id="b_left">
					<img id="b_logo" src="images/logo_customer.png" /><br />
					<div id="b_title">Customer Service</div>
					<div id="b_subtitle">Have an ear for your customers. <strong>Without the hassle.</strong></div>
					<div id="b_graphs">
						<div id="b_graph">
							<div id="stat_holder"> 
								<div id="big_stat">24x</div>
							</div>
							<div class="text_clean" id="graph_label">
								Retain up to <span id="emphasis">24x</span> more dissatisfied customers.
							</div>
							<br style="clear:both;" />
						</div>
						<div id="b_graph">
							<div id="stat_holder"> 
								<img class="piechart" src="/images/pie/81.png" />
							</div>
							<div class="text_clean" id="graph_label">
								<span id="emphasis">81%</span> of customers are willing to pay more for Brevada levels of customer service.
							</div>
							<br style="clear:both;" />
						</div>
						 <div id="b_graph">
							<div id="stat_holder"> 
								<img class="piechart" src="/images/pie/71.png" />
							</div>
							<div class="text_clean" id="graph_label">
								<span id="emphasis">71%</span> of customers first look for online outlets when experiencing issues.
							</div>
							<br style="clear:both;" />
						</div>
					</div>
				</div>
				<div id="b_left">
					<img id="b_logo" src="images/logo_business.png" /><br />
					<div id="b_title">Business Development</div>
					<div id="b_subtitle">Learn from your customers and make <strong>the right moves.</strong></div>
					<div id="b_graphs">
						<div class="left" id="b_graph">
							<div id="stat_holder"> 
								<div id="big_stat">7x</div>
							</div>
							<div class="text_clean" id="graph_label">
								<span id="emphasis">7x</span> less expensive to retain customers using Brevada than to aquire new ones altogether.
							</div>
						</div>
						<div class="left" id="b_graph">
							<div id="stat_holder"> 
								<img class="piechart" src="/images/pie/25.png" />
							</div>
							<div class="text_clean" id="graph_label">Increase profitability up to <span id="emphasis">25%</span> through customer retention.</div>
						</div>
						<div id="b_graph">
							<div id="stat_holder"> 
								<div id="big_stat">10x</div>
							</div>
							<div class="text_clean" id="graph_label">Create a Brevada customer base - worth up to <span id="emphasis">10x</span> more than an un-engaged customer base.</div>
							<br style="clear:both;" />
						</div>
						<br style="clear:both;" />
					</div>
				</div>
				<div id="b_left">
					<img id="b_logo" src="images/logo_pr.png" /><br />
					<div id="b_title">Marketing and PR</div>
					<div id="b_subtitle">Show off your strengths and get <strong>feedback in style.</strong></div>
					<div id="b_graph">
						<div id="stat_holder"> 
							<div id="big_stat">&uarr;</div>
						</div>
						<div class="text_clean" id="graph_label">
							<span id="emphasis">Increased</span> recognition and respect - our clients are considered: <br /><a href="/home/approved.php" style="font-weight:bold; text-decoration:underline;">Brevada Approved Business</a>.
						</div>
						<br style="clear:both;" />
					</div>
					<div id="b_graph">
						<div id="stat_holder"> 
							<div id="big_stat">&uarr;</div>
						</div>
						<div class="text_clean" id="graph_label"><span id="emphasis">Increased</span> web presence for SEO and online recognition.</div>
						<br style="clear:both;" />
					</div>
					<div id="b_graph">
						<div id="stat_holder"> 
							<div id="big_stat">&uarr;</div>
						</div>
						<div class="text_clean" id="graph_label"><span id="emphasis">Increased</span> consumer loyalty by showing you <span id="emphasis">care for your customers</span>.</div>
						<br style="clear:both;" />
					</div>
				</div>
				<br style="clear:both;" />
			</div>
		</div>
	</li>
	<li>
		<div class="home_section" id="sec2">
			<div class="container" style="width:950px;">
				<div id="pricing_holder">
					<a href="/home/pricing.php">
						<div id="pricing_box">
							<div id="pricing_top" style="background:#888;">
								<div class="pricing_title">
									<div id="home_text" style="color:#fff;">FREE</div>
								</div>
								<div id="pricing_under">
									<i>No credit card required</i><br />
									<strong>Get set up for free</strong>
								</div>
							</div>
						 </div>
					</a>
				</div> 
				<a href="/home/pricing.php">         
					<div id="pricing_box">
						<div id="pricing_top" style="background:#12a5f4;">
							<div class="pricing_title">
								<div  id="home_text" style="color:#fff;">PERSONAL</div>
								<div  id="pricing_price"><span id="price">$14</span>/month</div>
							</div>
							<div id="pricing_under">
								<i>Billed at $168 for one year</i><br />
								<strong>The basic package.</strong>
							</div>
						</div>
					</div>
				</a>
				<a href="/home/pricing.php">
					<div id="pricing_box">
						<div id="pricing_top" style="background:#3369e8;">
							<div class="pricing_title">
								<div id="home_text" style="color:#fff;">
								PROFESSIONAL
								</div>
								<div id="pricing_price"><span id="price">$30</span>/month</div>
							</div>
							<div id="pricing_under">
								<i>Billed at $360 for one year</i>
								<br />
								<strong>The complete package.</strong>
							</div>
						</div>
					</div>
				</a>
				<a href="/home/pricing.php">
					<div id="pricing_box">
						<div id="pricing_top" style="background:#3b5998;">
							<div class="pricing_title">
								<div id="home_text" style="color:#fff;">ENTERPRISE</div>
								<div id="pricing_price"><span id="price">$90</span>/month</div>
							</div>
							<div id="pricing_under">
								<i>Billed at $1080 for one year</i><br />
								<strong>The ultimate package.</strong>
							</div>
						</div>
					</div>
				</a>
				<br style="clear:both;" />
				<a href="/home/howitworks.php">
					<div class="top_bar_button2" style="width:200px; margin:0 auto; margin-top:35px;">See How It Works!</div>
				</a>
			</div>
		</div>
		</li>
	<li>
		<a id="examples"></a>
		<div class="home_section" id="sec3">
			<div id="t_title">
				<strong>How Companies are using Brevada's <span id="emphasis">Feedback Gathering</span> Features</strong><br />
				<span id="t_desc">Brevada's feedback gathering features ensure that you can listen to your customers</span>
			</div>
		</div>
	</li>
</ul>

<script type='text/javascript'>
tiles=$("ul#tiles li").fadeTo(0, 0);
$(window).scroll(function(d,h) {
    tiles.each(function(i) {
        a=$(this).offset().top + ($(this).height()/4);
        b=$(window).scrollTop() + $(window).height();
        if (a < b) $(this).fadeTo(600,1);
    });
});
</script>

<?php $this->add(new View('../template/long_footer.php')); ?>