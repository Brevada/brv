$(document).ready(function(){
	$('.slide-down-trigger').click(function(){
		if($('.slide-down').is(':visible')){
			$('.slide-down').slideUp(50);
			$('#email-display').css({'opacity' : '1'});
		} else {
			$('.slide-down').slideDown()
			$('.email-display-container').show();
			$('#email-display').css({'opacity' : '0.5'});
		}
	});
	$('#email-close').click(function(){
		$('.slide-down').slideUp(50);
		$('#email-display').css({'opacity' : '1'});
	});
});

