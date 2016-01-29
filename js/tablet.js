app.custom.sessionToken = 'not-set';

app.custom.newSessionToken = function(){
	var chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	var result = '';
	for (var i = 32; i > 0; --i) { result += chars[Math.floor(Math.random() * chars.length)]; }
	app.log('Session Token: ' + result);
	app.custom.sessionToken = result;
};

app.custom.initialize = function(){
	app.custom.newSessionToken();

	$(document).click(app.updateInteraction);
	$("#imdone").click(app.custom.imdone);
	$('#reset').click(app.custom.resetAll);
	
	$(window).bind('touchmove scroll scrollstart', function() {
		if ($(window).scrollTop() >= 100) { $('.topbar, .top-spacer').addClass('fixed'); }
		else { $('.topbar, .top-spacer').removeClass('fixed'); }
	});
	$('.topbar i').click(function () {
		$('html,body').animate({
        	scrollTop: $(window).scrollTop() + 100
    	});
	});

	app.custom.resizestars();

	app.custom.inactivity.updateInteraction();

	app.log("Loaded tablet.js");
};

app.custom.imdone = function(){
	$('#aspects, .fixed-toolbar').fadeOut(300, function () {
		$('#email_connect').show();
	});
};

app.custom.resizestars = function(){
	$('div.star').each(function(){
		$(this).css('height', $(this).outerWidth()+'px')
	});
};

app.custom.resetAll = function(){
	app.custom.newSessionToken();
	$('html, body').scrollTop(0);
	$('#email_connect').hide();
	$('.rated').removeClass('rated').show();
	$('#aspects').randomize('div.aspect');
	$('#aspects, .fixed-toolbar').fadeIn(300);
	$('#imdone').hide();
};

function insertRating(val, id) {
	if(!$('#imdone').is(':visible')){
		$('#imdone').slideDown(125);
	}

	var payload = {
		now : Math.floor((new Date()).getTime()/1000),
		rating : val,
		aspectID : id,
		session : app.custom.sessionToken,
		batteryLevel : app.opts.system.battery.level,
		batteryIsPlugged : app.opts.system.battery.isPlugged.toString()
	};

	app.sendPayload(payload);

	$("#aspect_"+id).addClass('rated').slideUp(325, function(){
		app.custom.inactivity.updateInteraction();
		if($('div.aspect:not(.rated)').length == 0 && $('#aspects').is(':visible')){
			app.custom.imdone();
		}
	});
}

function disappearRating(post_id) {}

app.custom.inactivity = {
	inactivityTmr : null,
	inactiveDelayA : 45000,
	inactiveDelayB : 15000,
	inactiveA : null,
	inactiveB : null,
	message : "If you're not done giving feedback, tap anywhere on the screen."
};

app.custom.inactivity.updateInteraction = function(){
	if(app.custom.inactivity.inactiveDelayA > 0){
		clearTimeout(app.custom.inactivity.inactivityTmr);
		app.custom.inactivity.inactivityTmr = setTimeout(
			app.custom.inactivity.inactive,
			app.custom.inactivity.inactiveDelayA
		);
	}
};

app.custom.inactivity.inactive = function(){
	if(typeof app.custom.inactivity.inactiveA === 'function' &&
		typeof app.custom.inactivity.inactiveB === 'function'){
		app.custom.inactivity.inactiveA();

		clearTimeout(app.custom.inactivity.inactivityTmr);
		app.custom.inactivity.inactivityTmr = setTimeout(
			app.custom.inactivity.inactiveB,
			app.custom.inactivity.inactiveDelayB
		);
	}
};

app.custom.inactivity.showInactivityWarning = function(){
	if($('.inactivity').length == 0){
		$('html, body').addClass('locked');
		$('<div>').attr('id', 'inactivity-overlay').addClass('inactivity').appendTo('body');
		$('<div>').attr('id', 'inactivity').addClass('inactivity').append(
			$('<p>').html(app.custom.inactivity.message)
		).appendTo('body');

		$('#inactivity-overlay, #inactivity').click(function(){
			app.custom.inactivity.updateInteraction();
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
	app.log('Inactivity Warning shown.');
}

app.custom.inactivity.inactiveA = function(){
	if($('div.aspect.rated').length > 0){
		app.custom.inactivity.showInactivityWarning();
	}
};

app.custom.inactivity.inactiveB = function(){
	if($('div.aspect.rated').length > 0){
		if($('.inactivity').length > 0){
			$('#inactivity').fadeOut(225, function(){
				$('#inactivity-overlay').fadeOut(125, function(){
					$('html, body').removeClass('locked');
				});
			});

		}
		app.custom.resetAll();
	}
	app.custom.inactivity.updateInteraction();
};

$.fn.randomize = function(childElem) {
	return this.each(function() {
		var $this = $(this);
		var elems = $this.children(childElem);
		elems.sort(function() { return (Math.round(Math.random())-0.5); });
		$this.detach(childElem);
		for(var i=0; i < elems.length; i++){
			$this.prepend(elems[i]);
		}
	});
}

app.custom.initialize();