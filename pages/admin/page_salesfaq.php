<?php
if(!Brevada::IsLoggedIn()){ Brevada::Redirect('/home/logout'); }
if(!Permissions::has(Permissions::VIEW_ADMIN)){ Brevada::Redirect('/404'); }
?>
<h1 class="page-header">Sales FAQ</h1>

FAQ Index
<ul class='faq faq-index'></ul>

<div class='faq faq-content'>
	<h3>Do I need to be tech-savvy?</h3>
	<p>Not at all. Brevada is focused on a simple and easy experience for both you and your customers. The dashboard is simple to navigate with bar charts indicating your company’s performance on the areas you are tracking. Changing your feedback criteria is as easy as clicking a button. The tablets are updated automatically so there is no inputting of information. Any issue you experience will be handled by our support team who are available at on our company phone number or service email address.</p>

	<h3>Do I need to download any software?</h3>
	<p>Nope. Brevada's platform is accessible online. It can be viewed from a computer, tablet or even your mobile device.</p>

	<h3>Will the service work if I do not have WiFi in my restaurant?</h3>
	<p>aving WIFI allows for the tablets to instantly sync with your dashboard. This means you can have up to the minute feedback coming to your account. However, not having WIFI is not an issue. The tablets are able to store all of the feedback data in the device. At the end of the day (or once a week) simply take the tablets home, or to an area where they can connect to the internet. This will allow them to sync with the dashboard and let you see all of the data collected.</p>

	<h3>What are the key benefits of Brevada?</h3>
	<p>Improving customer satisfaction (customers appreciate when restaurants are making the effort to collect feedback), improving service (when employees know they are being reviewed constantly it creates an awareness of the importance to be consistently positive and providing the highest quality of service), reducing negative broadcasted reviews (an study we conducted showed that customers were far less likely to bash a restaurant on social media if the restaurant provided an internal review system to collect feedback), benchmarking (we allow you to compare your results to similar restaurants in close proximity), monitoring (allows the owner a new insight into what is working and what is not, on a macro level).</p>

	<h3>What is the warranty on the Tablets?</h3>
	<p>The tablets are owned by Brevada and rented to you for the duration of the subscription. We provide new tablets every year to make sure that you have the latest technology and that there is no breakdowns of equipment. In addition, Brevada will cover any manufacturers defect to the product. Simply contact us and we will send out a new tablet immediately. Unfortunately if the tablet is broken by physical/water damage you will have to replace it. Replacing a tablet is simple and easy being paid in a small monthly fee. The fee depends on the plan you select (the Premium plan charges less per additional tablet).</p>

	<h3>What is the ROI on this Investment?</h3>
	<p>If being able to spot negative areas of your operations and fix them in order to satisfy the customer will help make you more money then yes. Like most investments (think marketing campaigns, CSR, etc) a definitive ROI is hard to come by, but the value in feedback has become apparent. We live in an age where everyone's voice can matter and a restaurant puts their reputation on the line every time it opens it doors. With those high stakes, a feedback platform that helps me monitor how I am doing sounds valuable to me.
	</p>

	<h3>How will Brevada save me money?</h3>
	<p>Many restaurants understand the value of feedback and have even begun to collect their own feedback. This usually entails a paper survey and requires an employee to log hours of data into a software solution to keep track of everything. Our solution requires no additional employee work, instant results and is going to be significantly cheaper than that. Printing those pages + employee wage to input + software = 100s of dollars a month.</p>

	<h3>How do I get started?</h3>
	<p>It's simple! Go to www.Brevada.com and click Sign Up. Then you choose what you want to start gathering feedback on (you can change these choices at any time). After that you will create a username and password. Once you are directed to the dashboard you may choose which pricing plan fits your needs best. Once you have paid, the tablets will be sent to you and a customer service representative will contact you for further details.</p>
</div>

<script type='text/javascript'>
$(document).ready(function(){
	$('.faq.faq-content > h3').each(function(){
		var h = $(this);
		$('ul.faq.faq-index').append(
			$('<li>').text($(this).text()).click(function(){
				$(window).scrollTop(h.offset().top - $('#navbar').height() - 10);
				var oldSize = parseFloat(h.css('font-size'));
				h.animate({ 'font-size' : (oldSize + 2) + 'px' }, 500, function(){
					h.animate({ 'font-size' : oldSize }, 500);
				});
			})
		);
	});
});
</script>