<?php
$this->addResource('/pages/overall/packages/joyride-master/jquery.cookie.js');
$this->addResource('/pages/overall/packages/joyride-master/modernizr.mq.js');
$this->addResource('/pages/overall/packages/joyride-master/jquery.joyride-2.1.js');
$this->addResource('/pages/overall/packages/joyride-master/joyride-2.1.css');
$this->addResource('/js/tour.js');

$url_name = $this->getParameter('url_name');
$logins = $this->getParameter('logins');
?>
<ol class="joyride-list" id="joyRideTipContent" data-joyride>
	<li data-id="tourStart" data-text="Next" data-option="tipLocation:bottom;tipAdjustmentY:125">
		<p><span id="emphasis">Welcome!</span> <br /> Your Brevada Page can be found at <strong>brevada.com/<?php echo $url_name; ?></strong><br /> Listed below are ways to share your page to your customers.</p>
	</li>
	<li data-id="box_holder" data-text="Next" data-option="tipLocation:bottom;">
		<p><span id="emphasis">This is where you will view your data.</span> <br /> If your account is not yet payed for, you must do so to view your data.</p>
	</li>
	<li data-id="logo_banner" data-text="Finish Tour and Get Started" data-option="tipLocation:top;tipAdjustmentY:-20">
		<p>Get started by <span id="emphasis">sharing your page using the tools on the left.</span> <br /> Let us know if you need anything!</p>
	</li>
</ol>
<script type='text/javascript'>
  $(document).ready(function() {
	$('#joyRideTipContent').joyride({
		tip_location: 'top',
		modal: true,
		expose: true
    });
	<?php if($logins<2){ ?>
	$("#joyRideTipContent").joyride({ autoStart : true });
	<?php } ?>
  });
</script>