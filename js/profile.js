$(document).ready(function(){
	$("#imdone").click(function() { 
		$('#aspects, .fixed-toolbar').fadeOut(300, function () {
			$('#email_connect').show();
		});
	});

	$(window).bind('touchmove scroll scrollstart', function() {
		if ($(window).scrollTop() >= 10) $('.topbar').addClass('fixed');
		else $('.topbar').removeClass('fixed');
	});
});