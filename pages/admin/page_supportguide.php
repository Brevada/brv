<?php
if(!Brevada::IsLoggedIn()){ Brevada::Redirect('/home/logout'); }
if(!Permissions::has(Permissions::VIEW_ADMIN)){ Brevada::Redirect('/404'); }
?>
<h1 class="page-header">Support Guide</h1>

FAQ Index
<ul class='faq faq-index'></ul>

<div class='faq faq-content'>
	<h3>The customer is being logged out of Brevada after a while.</h3>
	<p>For security purposes, the customer is logged out after 2 hours of inactivity.</p>

	<h3>No data is appearing on the online dashboard.</h3>
	<p>The customer should refresh the page. If data is still not showing, tell the customer to clear his/her cache and perform a refresh again.</p>

	<h3>Hourly responses are 0 even though customers have used the tablets in the past hour.</h3>
	<p>You should check the internet connection on the tablet by visiting the Tablets tab in the admin panel. If the tablet has a connection, elevate this issue.</p>

	<h3>Some of the customer's aspects have disappeared.</h3>
	<p>Peform a Data Analysis for the customer. It will inform you whether the customer has disabled aspects with feedback. If this is the case, tell the customer to go to settings &gt; feedback and then re-enable the disabled aspects.</p>

	<h3>I've changed my aspects but the tablet remains the same.</h3>
	<p>Ask the user if the tablet has been restarted. If not, wait for the user to restart the tablet. You should check the internet connection on the tablet by visiting the Tablets tab in the admin panel. If the tablet has a connection, and the tablet has been restarted, elevate this issue.</p>
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