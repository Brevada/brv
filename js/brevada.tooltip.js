/*
	Written by Noah Negin-Ulster
	http://noahnu.com
	
	The code contained in this file (excluding the name 'brevada' in all forms) is licensed under a Creative Commons Attribution 4.0 International License.
	The license can be found at http://creativecommons.org/licenses/by/4.0/legalcode
	A human-readable summary can be found at http://creativecommons.org/licenses/by/4.0/
*/

var currentTooltip = false;
var currentTooltipOpts = false;

(function($){
	var currentMouse = { x: -1, y: -1 };
	$(document).mousemove(function(e){
		currentMouse.x = e.pageX;
		currentMouse.y = e.pageY;
		
		if(currentTooltip && currentTooltipOpts){
			if(currentTooltip.is(':visible')){
				var x = calcX(e.pageX || e.x, currentTooltip, currentTooltipOpts);
				var y = calcY(e.pageY || e.y, currentTooltip, currentTooltipOpts);
				currentTooltip.css({ 'top' : y, 'left' : x });
			}
		}
	}).mouseover();
	
	$.fn.brevadaTooltip = function(action, opts, tipEl){
		if (typeof action !== 'string'){
			opts = action;
			action = false;
		}
		
		opts = typeof opts === 'undefined' ? {} : opts;
		
		var defaults = {
			className : 'brevada-tooltip',
			subClassName : '',
			fadeInDuration : 10,
			fadeOutDuration : 100,
			duration : 1550,
			offset : 10,
			text : '',
			bind: true,
			keepalive: false,
			one: false
		};
		
		opts = $.extend(true, defaults, opts);
		
		var that = this;
		
		var tipElement, tmr;
		tipElement = tipEl;
		
		var textChanged = false;
		
		if(opts.bind && opts.action != 'hide'){
			$(this).not('.brv-tp-enabled').on({
				mouseenter : function(e){
					opts.text = $(this).attr('data-tooltip');
					opts.subClassName = typeof opts.subClassName === 'undefined' ? '' : opts.subClassName;
					getTipElement().removeClass(opts.subClassName);
					opts.subClassName = $(this).attr('data-tooltip-class');
					opts.subClassName = typeof opts.subClassName === 'undefined' ? '' : opts.subClassName;
					tipElement.addClass(opts.subClassName);
					$(this).mousemove(e);
				},
				mousemove : function(e){
					show(e);
				},
				mouseleave : function(){
					hide();
				}
			});
		}
		
		$(this).addClass('brv-tp-enabled');
		
		if (action == 'show'){
			if(opts.text != opts.content){
				textChanged = true;
			}
			opts.text = opts.content;
			
			show({
				x: opts.x === 'mouse' ? currentMouse.x : opts.x,
				y: opts.y === 'mouse' ? currentMouse.y : opts.y
			});
			
			if(!opts.bind){
				currentTooltip = tipElement;
				currentTooltipOpts = opts;
			}
		} else if(action == 'hide'){
			hide();
		}
		
		function followMouse(e){
			show(e);
		}
		
		function getTipElement(){
			tipElement = tipElement || $(that).data('tipElement') || undefined;
			
			if(typeof tipElement !== 'undefined'){
				return tipElement;
			} else {
				if(opts.one && $('div.'+opts.className).length > 0){
					tipElement = $('div.'+opts.className).first().hide();
				} else {
					tipElement = $('<div>').addClass(opts.className)
										   .addClass(opts.subClassName).hide();
					tipElement.css('position', 'absolute');
					tipElement.append($('<span>'));
					tipElement.appendTo($('body'));
				}
				
				$(that).data('tipElement', tipElement);
				return tipElement;
			}
		}
		
		function show(e){
			resetTimer();
			var x = calcX(e.pageX || e.x, getTipElement(), opts);
			var y = calcY(e.pageY || e.y, getTipElement(), opts);
			getTipElement().css({ 'top' : y, 'left' : x });
			
			var invisible = !tipElement.is(':visible');
			if(invisible || (opts.one && textChanged)){
				tipElement.children('span').html(opts.text);
				textChanged = false;
			}
			
			if(invisible){
				stopTimer();
				tipElement.stop().fadeIn(opts.fadeInDuration, function(){
					resetTimer();
				});
			}
		}
		
		function hide(){
			if(getTipElement().is(':visible')){
				$(that).removeData('tipElement');
				tipElement.stop().fadeOut(opts.fadeOutDuration, function(){
					stopTimer();
					tipElement.remove();
					tipElement = undefined;
				});
			}
		}
		
		function resetTimer(){
			stopTimer();
			if(!opts.keepalive){
				tmr = setTimeout(hide, opts.duration);
			}
		}
		
		function stopTimer(){
			clearTimeout(tmr);
		}
		
	};
	
	function calcX(x, tp, opts){
		var w = tp.outerWidth();
		
		if (x + w + opts.offset > $(window).width()) {
			return Math.max(0, x - opts.offset - w);
		} else {
			return x + opts.offset;
		}
	}
	
	function calcY(y, tp, opts){
		var h = tp.outerHeight();
		
		if (y + h + opts.offset > $(window).height() + $(window).scrollTop()) {
			//getTipElement().removeClass('above').addClass('below');
			return y - h - opts.offset;
		} else {
			//getTipElement().removeClass('below').addClass('above');
			return y + opts.offset;
		}
	}
}(jQuery));

$.fn.isBound = function(type, fn) {
    var data = jQuery._data(this[0], 'events')[type];

    if (data === undefined || data.length === 0) {
        return false;
    }

    return (-1 !== $.inArray(fn, data));
};

$(document).ready(function(){
	$('[data-tooltip]').each(function(){
		$(this).brevadaTooltip();
	});
});