app.custom.sessionToken = 'not-set';

if (!app.session.create) {
	app.custom.newSessionToken = function(){
		var chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		var result = '';
		for (var i = 32; i > 0; --i) { result += chars[Math.floor(Math.random() * chars.length)]; }
		app.log('Session Token: ' + result);
		app.custom.sessionToken = result;
		return app.custom.sessionToken;
	};
}

/* Hooks for each event where we might show post-feedback. */

app.custom.initialize = function(){
	(app.session.create || app.custom.newSessionToken)();

	$(document).click(app.custom.inactivity.updateInteraction);
	$('input, select').change(app.custom.inactivity.updateInteraction);
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
	$('#aspects').randomize('div.aspect');
	
	app.custom.inactivity.updateInteraction();

	app.custom.initPostPreData();
	
	app.log("Loaded tablet.js");
};

app.custom.initPostPreData = function(){
	// Register app.events.* depending on #data-collect[data-location]
	var $dataCollect = $('#data-collect');
	
	if ($dataCollect.length == 0){ return; }
	
	var dataLocation = parseInt($dataCollect.attr('data-location'));
	if (dataLocation == 1){
		// After poor response.
		app.events.onDone(function(e){
			if(app.session.poorResponse){
				app.custom.showPostPre(e);
				return false;
			}
			return true;
		});
	} else if (dataLocation == 2){
		// After survey.
		app.events.onDone(function(e){
			app.custom.showPostPre(e);
			return false;
		});
	} else if (dataLocation == 3){
		// Before survey.
		app.events.onNewSession(function(){
			app.custom.showPostPre();
		});
	}
	
	if($('.pp-email-options', $dataCollect).length > 0){
		var $target = $($('.pp-email-options', $dataCollect).attr('data-for'), $dataCollect);
		
		$target.find('.data-email input').hide();
		try {
			$target.find('.data-email > span').text(
				$('.pp-email-options .pp-email-option.selected', $dataCollect).attr('data-domain')
			);
		} catch (err){	}
		
		$target.children('input[type=text]').change(function(){
			$target.children('input[type=hidden]').val($target.children('input[type=text]').val() + '@' + (
				$target.find('.data-email > span').is(':visible') ?
				$target.find('.data-email > span').text() :
				$target.find('.data-email > input').val()
			));
		});
		
		$target.find('.data-email > input').change(function(){
			$target.children('input[type=text]').change();
		});
		
		$('.pp-email-options .pp-email-option', $dataCollect).click(function(){
			if ($('.pp-email-options .pp-email-option.selected', $dataCollect).length > 0 &&
				$(this).is($('.pp-email-options .pp-email-option.selected', $dataCollect))){
				return false;
			}
			
			$('.pp-email-options .pp-email-option', $dataCollect).removeClass('selected');
			$(this).addClass('selected');
			
			var val = $(this).attr('data-domain');
			if(val == 'other'){
				$target.find('.data-email > span').hide();
				$target.find('.data-email > input').val('').show().focus();
			} else {
				$target.find('.data-email > span').text(val);
				$target.find('.data-email > input').hide();
				$target.find('.data-email > span').show();
			}
			
			$target.children('input[type=text]').change();
		});
	}
	
	if (dataLocation == 3){
		// Before survey.
		app.custom.resetAll();
	}
}

app.custom.showPostPre = function(e){
	var $dataCollect = $('#data-collect');

	if(!$dataCollect || $dataCollect.length === 0){
		return false;
	}
	
	$('html, body').addClass('locked');
	
	// This is specific to a template so should be replaced with generic reset, controlled through DOM.
	$('#data-collect').find('input').val('');
	$('.pp-email-options .pp-email-option', $dataCollect).first().click();
	
	
	$('#data-collect-overlay').stop().fadeIn(200, function(){
		$dataCollect.stop().fadeIn(350, function(){
			$('.content .auto-focus', $dataCollect).length > 0 &&
				$('.content .auto-focus', $dataCollect).focus();
		});
	});
	
	app.log('Post/Pre form shown.');
	
	if($('[data-type=submit]', $dataCollect).length > 0){
		$('[data-type=submit]', $dataCollect).one('click', function(){
			
			if($('.pp-require', $dataCollect).length > 0){
				var failed = false;
				$('.pp-require', $dataCollect).each(function(){
					if($(this).val().length == 0){
						console.log("Failed @ " + $(this).attr('class'));
						failed = true;
					}
				});
				
				if(failed){
					return false;
				}
			}
			
			var fields = {};
			$('[data-pp-key]', $dataCollect).each(function(){
				fields[$(this).attr('data-pp-key')] = {
					value: $(this).val(),
					label: $(this).attr('data-pp-label') || $(this).attr('data-pp-key')
				};
			});
			
			var payload = {
				now : Math.floor((new Date()).getTime()/1000),
				session : app.session.token || app.custom.sessionToken,
				fields : JSON.stringify(fields)
			};

			app.sendPayload(payload);
			
			
			$dataCollect.stop().fadeOut(200, function(){
				$('#data-collect-overlay').stop().fadeOut();
			});
			
			if(e.thanks){
				e.thanks();
			}
		});
	}

}

app.custom.imdone = function(){
	if(app.events.callbacks.onDone.length == 0 ||
		(app.events.callbacks.onDone.length > 0 &&
			app.events.fireDone({ timestamp: (new Date()).getTime(), thanks: function(){
				$('#aspects, .fixed-toolbar').stop().fadeOut(300, function () {
					$('#email_connect').show();
				});
			} }))){
			
		$('#aspects, .fixed-toolbar').stop().fadeOut(300, function () {
			$('#email_connect').show();
		});
	}
};

app.custom.resizestars = function(){
	$('div.star').each(function(){
		$(this).css('height', $(this).outerWidth()+'px')
	});
};

app.custom.resetAll = function(){
	$('html, body').scrollTop(0);
	
	$('#data-collect').hide();
	$('#data-collect-overlay').hide();
	
	$('#email_connect').hide();
	$('.rated').removeClass('rated').show();
	$('#aspects').randomize('div.aspect');
	$('#aspects, .fixed-toolbar').stop().fadeIn(300);
	$('#imdone').hide();
	
	(app.session.create || app.custom.newSessionToken)();
};

function insertRating(val, id) {
	if(!$('#imdone').is(':visible')){
		$('#imdone').stop().slideDown(125);
	}

	var payload = {
		now : Math.floor((new Date()).getTime()/1000),
		rating : val,
		aspectID : id,
		session : app.session.token || app.custom.sessionToken,
		batteryLevel : app.opts.system.battery.level,
		batteryIsPlugged : app.opts.system.battery.isPlugged.toString()
	};

	app.sendPayload(payload);

	$("#aspect_"+id).addClass('rated').stop().slideUp(325, function(){
		app.custom.inactivity.updateInteraction();
		app.events.fireRating({ rating: val, timestamp: (new Date()).getTime() });
		if($('div.aspect:not(.rated)').length == 0 && $('#aspects').is(':visible')){
			app.custom.imdone();
		}
	});
}

function disappearRating(post_id) {}

app.custom.inactivity = {
	inactivityTmr : null,
	inactiveDelayA : 60000,
	inactiveDelayB : 15000,
	inactiveA : null,
	inactiveB : null,
	message : "If you're not done giving feedback, tap anywhere on the screen.<br /><i class='fa fa-hand-pointer-o'></i>"
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
			
		// If on Thanks / I'm Done screen, don't prompt, just redirect.

		if($('#aspects').is(':visible') || $('#data-collect').is(':visible')){
			// On Aspects screen.
			app.custom.inactivity.inactiveA();

			clearTimeout(app.custom.inactivity.inactivityTmr);
			app.custom.inactivity.inactivityTmr = setTimeout(
				app.custom.inactivity.inactiveB,
				app.custom.inactivity.inactiveDelayB
			);
		} else {
			// On Thanks screen.
			app.custom.inactivity.inactiveB();
		}
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
			$('#inactivity').stop().fadeOut(225, function(){
				$('#inactivity-overlay').stop().fadeOut(125, function(){
					$('html, body').removeClass('locked');
				});
			});
		});
	}
	$('#inactivity-overlay').stop().fadeIn(200, function(){
		$('#inactivity').stop().fadeIn(350);
	});
	app.log('Inactivity Warning shown.');
}

app.custom.inactivity.inactiveA = function(){
	if($('div.aspect.rated').length > 0){
		app.custom.inactivity.showInactivityWarning();
	}
};

app.custom.inactivity.inactiveB = function(){
	if($('div.aspect.rated').length > 0 || !$('#aspects').is(':visible')){
		if($('.inactivity').length > 0){
			$('#inactivity').stop().fadeOut(225, function(){
				$('#inactivity-overlay').stop().fadeOut(125, function(){
					$('html, body').removeClass('locked');
				});
			});

		}
		app.custom.resetAll();
	}
	app.custom.inactivity.updateInteraction();
};

if(!$.fn.randomize){
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
}

app.custom.initialize();