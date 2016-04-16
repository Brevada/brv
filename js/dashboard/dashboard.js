$(document).ready(function(){
	$('.link').css({'cursor' : 'pointer'}).click(function(){
		window.location = '/'+$(this).data('link');
	});

	$('body').on('click', 'div.message-container div.close', function(){
		$(this).parent().slideUp();
	});
});

if(typeof bdff !== 'undefined'){
	$(document).ready(function(){
		var hashMappings = {
			play: 'complete',
			details: 'aspects',
			live: 'live',
			events: 'milestones'
		};
		
		var mappingsToHash = {
			complete: 'play',
			aspects: 'details',
			live: 'live',
			milestones: 'events'
		};
		
		$('.toggle-button').click(function(){
			bdff.face($(this).attr('data-id'));
		});
		
		bdff.callbacks.rendered = function(face){
			$('.toggle-button').removeClass('selected');
			$('.toggle-button[data-id=' + face.label + "]").addClass('selected');
			$(window).scrollTop(0);
			
			if(face.label && face.label != 'hoverpod'){
				window.location.hash = '#' + mappingsToHash[face.label];
			}
		};
		
		bdff.canvas('#main-container');
		
		bdff.faces['hoverpod'].attach('body');
		bdff.faces['complete'].attach();
		bdff.faces['live'].attach();
		bdff.faces['milestones'].attach();
		bdff.faces['aspects'].attach();
		
		bdff.persistent('hoverpod');
		
		if(window.location.hash.length > 1 && hashMappings.hasOwnProperty(window.location.hash.substring(1))){
			bdff.face(hashMappings[window.location.hash.substring(1)]);
		} else {
			bdff.face('live');
		}
	});
}