<?php
$this->add(new View('../template/home_header.php'));
$this->addResource('/css/index.css');
$this->addResource('/css/howitworks.css');
$this->setTitle('How It Works - Brevada');
?>
<div>
  <div id="complete_title">
  	<h1>How The Brevada Complete Feedback System Works</h1>
    <h2>Brevada let's you gather feedback to grow your business in four simple steps.</h2>
  </div>
  <div id="works_holder">
  	<div class="works1" id="works_single">
    	<div id="works_single_title">Set Up</div>
        <div id="works_single_description">Sign up and specify the different <span id="emphasis">products, services, or other aspects</span> of your business that you want feedback on.</div>
        <a href="/home/signup.php"><div id="works_single_learn">Sign Up</div></a>
    </div>
    <div class="works2" id="works_arrow">&rarr;</div>
    <div class="works3" id="works_single">
    	<div id="works_single_title">Marketing</div>
        <div id="works_single_description">Share your Brevada Page using Brevada's <span id="emphasis">Feedback Marketing</span> tools.</div>
    	<a href="<?php echo p('HTML','path_home', 'complete.php#tab1'); ?>"><div id="works_single_learn">Learn More</div></a>
    </div>
    <div class="works4" id="works_arrow">&rarr;</div>
    <div class="works5" id="works_single">
    	<div id="works_single_title">Gathering</div>
        <div id="works_single_description">Sit back and recieve feedback and communication through your Brevada Page and the other <span id="emphasis">Feedback Gathering</span> tools.</div>
    	<a href="<?php echo p('HTML','path_home', 'complete.php#tab2'); ?>"><div id="works_single_learn">Learn More</div></a>
    </div>
    <div class="works6" id="works_arrow">&rarr;</div>
    <div class="works7" id="works_single">
    	<div id="works_single_title">Managing</div>
        <div id="works_single_description">View, analyze, share, and respond to feedback using Brevada's <span id="emphasis">Feedback Management</span> tools.</div>
    	<a href="<?php echo p('HTML','path_home', 'complete.php#tab3'); ?>"><div id="works_single_learn">Learn More</div></a>
    </div>
    <br style="clear:both;" />
  </div>
  <a href="/home/signup.php"><div class="top_bar_button2" style="width:170px; margin:0 auto;">Try It Out!</div></a>
  <br style="clear:both;" />
  <br style="clear:both;" />
</div>

<script type='text/javascript'>
  $(document).ready(function() {
		$(".works1").fadeIn("fast",function(){
			  $(".works2").fadeIn("fast", function(){
				  $(".works3").fadeIn("fast", function(){
				  		$(".works4").fadeIn("fast", function(){
				   			$(".works5").fadeIn("fast", function(){
				   				$(".works6").fadeIn("fast", function(){
				   					$(".works7").fadeIn("fast", function(){
				   						$(".works8").fadeIn("fast");
			 	   					});
			 	   				});
			 	   			});
			 	   		});
			 	   });
			  });
		});
    });
</script>

<?php $this->add(new View('../template/long_footer.php')); ?>