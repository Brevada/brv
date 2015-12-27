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
	dashboards.hoverpod.render($('#main-container').parent());
	
	// Initial State
	//$('#main-container').html(...default (aspects) template...);
	$('.toggle-button[data-id="aspects"]').addClass('selected');
	window.current_face = 'aspects';
	// Toggle Behaviour
	$('.toggle-button').click(function(){
		var new_face = $(this).attr('data-id');

		if (window.current_face == 'aspects') {
			aspects_holder = aspects_holder || $('#main-container').html();
		}

		dashboards.changeFace(new_face);
		
	});
});

dashboards.changeFace = function (new_face) {
	$('.toggle-button').removeClass('selected');
	$('.toggle-button[data-id=' + new_face + "]").addClass('selected');

	if (new_face == 'aspects') {
		dashboards.aspects.render($('#main-container'));
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
}

dashboards.createTemplate = function (structure, canvas) {
	var structure = [
		['div', 'testclass', 'other class'],
		[
			['testclass', 'other class'],
			['testclass', 'other class']
		]
	]
	// Traverse the array and continue to append to an element

	// Append the element to a canvas
}

/* Alerts */

dashboards.alert = function (alert, type) {
	 $('\
	 	<div class="alert '+type+'">\
		' + alert + '\
		</div>\
		').appendTo($('#alert-holder'));
	 setTimeout(dashboards.clearAlert, 3000);
}
dashboards.clearAlert = function () {
	$('#alert-holder .alert').fadeOut(500);
}