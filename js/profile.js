$(document).ready(function(){
	$("#imdone").click(function() { 
		$('#aspects, .fixed-toolbar').fadeOut(300, function () {
			$('#email_connect').show();
		});
	});

	$(window).bind('touchmove scroll scrollstart', function() {
		if ($(window).scrollTop() >= 100) $('.topbar, .top-spacer').addClass('fixed');
		else $('.topbar, .top-spacer').removeClass('fixed');
	});
	$('.topbar i').click(function () {
		$('html,body').animate({
        	scrollTop: $(window).scrollTop() + 100
    	});
	});
});