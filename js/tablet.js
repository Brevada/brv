console.log("Loaded tablet.js");

$("#imdone").click(function() { 
	$('#aspects, .fixed-toolbar').fadeOut(300, function () {
		$('#email_connect').show();
	});
});

$('div.star').each(function(){
	$(this).css('height', $(this).outerWidth()+'px')
});

$('#reset').click(function(){
	resetAll();
});

function resetAll(){
	app.newSessionToken();
	$('html, body').scrollTop(0);
	$('#email_connect').hide();
	$('.rated').removeClass('rated');
	$('#aspects, .fixed-toolbar').fadeIn(300);
	$('#imdone').hide();
}

function insertRating(val, id) {
	if(!$('#imdone').is(':visible')){
		$('#imdone').slideDown(125);
	}
	
	var payload = {
		serial : globals.uuid,
		now : Math.floor((new Date()).getTime()/1000),
		rating : val,
		aspectID : id,
		session : globals.sessionToken,
		batteryLevel : globals.battery.level,
		batteryIsPlugged : globals.battery.isPlugged.toString()
	};
	
	app.sendPayload(payload);
}

function disappearRating(post_id) {
	$("#aspect_"+post_id).addClass('rated');
}

function showInactivityWarning(){
	if($('.inactivity').length == 0){
		var message = "If you're not done giving feedback, tap anywhere on the screen.";
		$('html, body').addClass('locked');
		$('<div>').attr('id', 'inactivity-overlay').addClass('inactivity').appendTo('body');
		$('<div>').attr('id', 'inactivity').addClass('inactivity').append($('<p>').html(message)).appendTo('body');
		
		$('#inactivity-overlay, #inactivity').click(function(){
			app.updateInteraction();
			$('#inactivity').fadeOut(225, function(){
				$('#inactivity-overlay').fadeOut(125, function(){
					$('html, body').removeClass('locked');
				});
			});
		});
	}
	$('#inactivity-overlay').fadeIn(200, function(){
		$('#inactivity').fadeIn(350);
	});
	console.log('Inactivity Warning shown.');
}

globals.inactiveA = function(){
	if($('div.aspect.rated').length > 0){
		showInactivityWarning();
	}
};

globals.inactiveB = function(){
	if($('div.aspect.rated').length > 0){
		if($('.inactivity').length > 0){
			$('#inactivity').fadeOut(225, function(){
				$('#inactivity-overlay').fadeOut(125, function(){
					$('html, body').removeClass('locked');
				});
			});
			
		}
		resetAll();
	}
	app.updateInteraction();
};

$(document).click(app.updateInteraction);

app.updateInteraction();