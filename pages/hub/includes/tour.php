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
	<li data-id="logo_banner" data-text="Next" data-option="tipLocation:bottom;tipAdjustmentY:25">
		<p><span id="emphasis">Welcome to Brevada!</span> This quick tour will take you through how everything works!</p>
	</li>
	<li data-id="expanderContentAspects" data-text="Next" data-option="tipLocation:top;tipAdjustmentY:-35">
		<p>These are the <span id="emphasis">products, services, and aspects</span> of your business that you are currently gathering feedback on. Eg. Customer Service, Pricing, Location, etc...</p>
	</li>
	<li data-id="expanderContentMarketing" data-text="Next" data-option="tipLocation:top;tipAdjustmentY:-35">
		<p>These are your <span id="emphasis">feedback marketing tools</span>. These are designed to help lead your customers to your Brevada Page to give you feedback.</p>
	</li>
	<li data-id="expanderContentGather" data-text="Next" data-option="tipLocation:top;tipAdjustmentY:-35">
		<p>These are your <span id="emphasis">feedback gathering tools</span>.</p>
	</li>
	<li data-id="app_page" data-text="Next" data-option="tipLocation:top;tipAdjustmentY:-35">
		<p>This is a link to your <span id="emphasis">Brevada page</span>. It can be found at <span id="emphasis">brevada.com/<?php echo $url_name; ?></span> This page is what your URL, barcode, and promo material link to. This is your <strong>ultimate place for gathering feedback</strong>.</p>
	</li>
	<li data-id="app_email" data-text="Next" data-option="tipLocation:top;tipAdjustmentY:-35">
		<p>The <span id="emphasis">Email Gathering</span> tool allows you to send feedback email. Allowing your customers to give you feedback right from within the email.</p>
	</li>
	<li data-id="app_widgets" data-text="Next" data-option="tipLocation:top;tipAdjustmentY:-35">
		<p><span id="emphasis">Widgets</span> allow you to integrate Brevada feedback tools into your website, apps or third party email systems.</p>
	</li>
	<li data-id="app_station" data-text="Next" data-option="tipLocation:top;tipAdjustmentY:-35">
		<p>Login to the <span id="emphasis">Voting Station</span> on a tablet in your store and allow your customers to give you feedback right there.</p>
	</li>
	<li data-id="expanderContentManage" data-text="Next" data-option="tipLocation:top;tipAdjustmentY:-35">
		<p>The <span id="emphasis">feedback management</span> tools let you view, analyze, organize, and share your feedback.</p>
	</li>
	<li data-id="locked_bar" data-text="Next" data-option="tipLocation:top;tipAdjustmentY:-35">
		<p>The <span id="emphasis">feedback management</span> tools let you view, analyze, organize, and share your feedback. You must upgrade Brevada to view your feedback.</p>
	</li>
	<li data-id="expanderContentAspects" data-text="Finish Tour and Get Started" data-option="tipLocation:top;tipAdjustmentY:-20">
		<p>Get started by specifying the <span id="emphasis">products, services, or other aspects</span> of your business that you would like feedback on.<br /><span style="font-size:11px; font-weight:bold;">Then share your Brevada Page to your customers using the <span id="emphasis">Feedback Marketing Tools</span> above.</span></p>
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