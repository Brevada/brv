$(document).ready(function(){
	$('.slide-down-trigger').click(function(){
		$('.slide-down').fadeIn()
	});
	$('#email-display').click(function(){
		$('.email-display-container').fadeIn();
		$('#email-display').fadeOut();
	});
	$('#email-close').click(function(){
		$('.slide-down').fadeOut(50);
		$('#email-display').fadeIn(50);
	});
});

