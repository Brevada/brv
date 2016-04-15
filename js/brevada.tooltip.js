/*
	Written by Noah Negin-Ulster
	http://noahnu.com
	
	The code contained in this file (excluding the name 'brevada' in all forms) is licensed under a Creative Commons Attribution 4.0 International License.
	The license can be found at http://creativecommons.org/licenses/by/4.0/legalcode
	A human-readable summary can be found at http://creativecommons.org/licenses/by/4.0/
*/

(function($){
	$.fn.brevadaTooltip = function(opts){
		opts = typeof opts === 'undefined' ? {} : opts;
		
		var defaults = {
			className : 'brevada-tooltip',
			subClassName : '',
			fadeInDuration : 10,
			fadeOutDuration : 500,
			duration : 1550,
			offset : 10,
			text : ''
		};
		
		opts = $.extend(true, defaults, opts);
		
		var tipElement, tmr;
		
		$(this).not('.brv-tp-enabled').on({
			mouseenter : function(){
				opts.text = $(this).attr('data-tooltip');
				opts.subClassName = typeof opts.subClassName === 'undefined' ? '' : opts.subClassName;
				getTipElement().removeClass(opts.subClassName);
				opts.subClassName = $(this).attr('data-tooltip-class');
				opts.subClassName = typeof opts.subClassName === 'undefined' ? '' : opts.subClassName;
				tipElement.addClass(opts.subClassName);
				$(this).mousemove();
			},
			mousemove : function(e){
				show(e);
			},
			mouseleave : function(){
				hide();
			}
		});
		
		$(this).addClass('brv-tp-enabled');
		
		function getTipElement(){
			if(typeof tipElement !== 'undefined'){
				return tipElement;
			} else {
				tipElement = $('<div>').addClass(opts.className).hide();
				tipElement.css('position', 'absolute');
				tipElement.append($('<span>'));
				tipElement.appendTo($('body'));
				return tipElement;
			}
		}
		
		function show(e){
			resetTimer();
			var x = calcX(e.pageX);
			var y = calcY(e.pageY);
			getTipElement().css({ 'top' : y, 'left' : x });
			if(!tipElement.is(':visible')){
				tipElement.children('span').html(opts.text);
				stopTimer();
				tipElement.stop().fadeIn(opts.fadeInDuration, function(){
					resetTimer();
				});
			}
		}
		
		function calcX(x){
			var w = getTipElement().outerWidth();
			
			if (x + w + opts.offset > $(window).width()) {
				return Math.max(0, x - opts.offset - w);
			} else {
				return x + opts.offset;
			}
		}
		
		function calcY(y){
			var h = getTipElement().outerHeight();
			
			if (y + h + opts.offset > $(window).height()) {
				return y - h - opts.offset;
			} else {
				return y + opts.offset;
			}
		}
		
		function hide(){
			if(getTipElement().is(':visible')){
				tipElement.stop().fadeOut(opts.fadeOutDuration, function(){
					stopTimer();
					$(this).remove();
					tipElement = undefined;
				});
			}
		}
		
		function resetTimer(){
			stopTimer();
			tmr = setTimeout(hide, opts.duration);
		}
		
		function stopTimer(){
			clearTimeout(tmr);
		}
		
	};
}(jQuery));

$(document).ready(function(){
	$('[data-tooltip]').brevadaTooltip();
});