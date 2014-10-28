<?php
$this->add(new View('../template/home_header.php'));
$this->addResource('/css/approved.css');
?>

<style style='text/css'>
<?php $placeholder="666"; ?>
::-webkit-input-placeholder { /* WebKit browsers */
    color:    #<?php echo $placeholder; ?>;
}
:-moz-placeholder { /* Mozilla Firefox 4 to 18 */
    color:    #<?php echo $placeholder; ?>;
    opacity:  1;
}
::-moz-placeholder { /* Mozilla Firefox 19+ */
    color:    #<?php echo $placeholder; ?>;
    opacity:  1;
}
:-ms-input-placeholder { /* Internet Explorer 10+ */
    color:    #<?php echo $placeholder; ?>;
}
</style>

	<!-- include jquery cycle (.all.js) -->
<script type="text/javascript" src="http://malsup.github.com/jquery.cycle.all.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $('.slideshow').cycle({
		fx: 'fade', // choose your transition type, ex: fade, scrollUp, shuffle, etc...
		timeout: 12000 
	});
});
</script>

<div  style="width:100%; background:#ee2b2b; height:500px; background-size:100%;"></div>

<div  style="width:100%; background:rgba(0,0,0,0.3); height:500px; margin-top:-500px;
	 	 -webkit-box-shadow: 0px 0px 5px 0px rgba(50, 50, 50, 0.75);
-moz-box-shadow:    0px 0px 5px 0px rgba(50, 50, 50, 0.75);
box-shadow:         0px 0px 5px 0px rgba(50, 50, 50, 0.75); 

position:relative; z-index:9;

">

	<div class="slideshow" style="width:1000px; margin:0 auto; margin-top:-500px;">
	  	<div class="a_slide">

	  		<div class="slide_image" style="height:500px; overflow:hidden;" >

	  		</div>
	  		<div class="slide_overlay" >
	  		 	<div class="container" style="width:1000px; margin:0 auto;">
					<div class="text_left">
					<strong>Brevada</strong> approved businesses.
					
					<div style="margin-top:6px; font-size:15px;">
					 Businesses using the Brevada platform are taking the necessary  steps to ensure that their customers are satisfied.
					</div>
					
					<br />
					
					<a href="#learnmore"><div class="top_bar_button2" style="width:300px; margin-top:8px; margin-left:0px;">Become A Certified Business</div></a>
										
					<a href="#"><div class="top_bar_button3" style="margin-top:8px; margin-left:0px;">Learn More</div></a>
					
					</div>
					
					<div class="text_right">
							<img src="/images/logo_approved.png" style="width:550px; margin-right:0px;" />
					</div>
					<br style="clear:both;" />
	  			
	  			</div>
	  		
	  		</div>
	  		<br style="clear:both;" />
	  	
	  	</div>
	  	
	</div>

</div>


<div class="home_section">
	<div class="container">
		<div style="width:100%; text-align:center;">
			<img src="/images/2014_combo.png" style="width:600px;"/>
			<br />
			<img src="/images/brevada_flow.png" style="width:580px;"/>
			<br  style="clear:both;"/>
			<img src="/images/2014_hub.png" style="width:580px;"/>
			<br  style="clear:both;"/>
			<div style="margin-top:-324px; margin-left:-4px;">
				<img src="/images/hub_vid.gif" style="width:424px;"/>
			</div>
			<br  style="clear:both;"/>
			<br  style="clear:both;"/>
			<a id="learnmore"></a>
			<div id="home_text">
			Analyze. Listen. <strong>Respond.</strong> 
			<span id="home_text2"><br />Businesses using the Brevada platform utilize an advanced suite of features to <span style="color:#ee2b2b;">listen to their customers</span>, ensure satisfaction, and right any wrongs.</span>
			<br />
			<a href="/index.php"><div class="top_bar_button2" style="width:270px; margin:0 auto; margin-top:10px;">Learn More about Brevada</div></a>
			
			</div>
			
			
		</div>
		<br style="clear:both;" />
	</div>
</div>

<?php $this->add(new View('../template/long_footer.php')); ?>