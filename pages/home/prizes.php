<?php
$this->add(new View('../template/home_header.php'));
$this->addResource('/css/prizes.css');
?>
	
<div  style="width:100%; background:#4EAF0E; height:500px; background-size:100%;"></div>

<div  style="width:100%; background:rgba(0,0,0,0.3); height:500px; margin-top:-500px;-webkit-box-shadow: 0px 0px 5px 0px rgba(50, 50, 50, 0.75);-moz-box-shadow:0px 0px 5px 0px rgba(50, 50, 50, 0.75);box-shadow:0px 0px 5px 0px rgba(50, 50, 50, 0.75); position:relative; z-index:9;">

	<div class="slideshow" style="width:1000px; margin:0 auto; margin-top:-500px;">
	  	<div class="a_slide">

	  		<div class="slide_image" style="height:500px; overflow:hidden;" >

	  		</div>
	  		<div class="slide_overlay" >
	  		 	<div class="container" style="width:1000px; margin:0 auto;">
					<div class="text_left">
                    <img src="<?php echo p('HTML','path_images','brevada_prizes.png'); ?>" style="width:200px; margin-bottom:5px;"/>
                    <br />
					
					
					<div style="margin-top:6px; font-size:15px;">
					 Giving feedback through Brevada gives you a chance to win one of several <strong>cash prizes</strong>.
					</div>
										
					<a href="/"><div class="top_bar_button3" style="margin-top:10px; width:250px; margin-left:0px;">Learn more about Brevada</div></a>
					
					</div>
					
					<div class="text_right">
							<img src="<?php echo p('HTML','path_images','logo_prizes.png'); ?>" style="width:550px; margin-right:0px;" />
					</div>
					<br style="clear:both;" />
	  			
	  			</div>
	  		</div>
	  		<br style="clear:both;" />
	  	</div>
	</div>
</div>

<div class="home_section">
	<div class="container" style="text-align:center;">
		<img src="<?php echo p('HTML','path_images','prizes_demo.png'); ?>" style="width:1000px; margin: 0 auto; border:0px solid #333;"/>
	</div>
</div>

<?php $this->add(new View('../template/long_footer.php')); ?>