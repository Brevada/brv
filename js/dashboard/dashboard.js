var dashboards = {},
	aspects_holder;

$(document).ready(function(){
	$('.link').css({'cursor' : 'pointer'});

	$('.link').click(function(){
		window.location = '/'+$(this).data('link');
	});

	$('div.message-container div.close').click(function(){
		$(this).parent().slideUp();
	});
	
	$('.pod .graph').each(function(){
		var percent = $(this).data('percent');
		var original = $(this).height();
		var target = (parseFloat(percent)/100)*($(this).parent().height() - original);
		$(this).animate({ height : Math.min(Math.floor(original+target), $(this).parent().height()) }, 1500);
	});

	// Load a live-data pod that hovers

	// Toggle Behaviour
	//$('#main-container').html(...default (aspects) template...);
	window.current_face = 'aspects';
	$('.toggle-button').click(function(){
		var new_face = $(this).attr('data-id');

		if (window.current_face == 'aspects') {
			aspects_holder = aspects_holder || $('#main-container').html();
		}

		if (new_face == 'aspects') {
			// TODO: Setup the aspects template with BDFF
			$('#main-container').html(aspects_holder);
			window.current_face = 'aspects';
		} else if (new_face == 'milestones') {
			dashboards.milestones.render($('#main-container'));	
			window.current_face = 'milestones';
		} else if (new_face == 'live') {
			dashboards.live.render($('#main-container'));	
			window.current_face = 'live';
		} else if (new_face == 'support') {
			dashboards.support.render($('#main-container'));	
			window.current_face = 'support';
		}
		
	});
});