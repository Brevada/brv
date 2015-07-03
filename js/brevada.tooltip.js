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
			duration : 1250,
			offset : 15,
			text : ''
		};
		
		opts = $.extend(true, defaults, opts);
		
		var tipElement, tmr;
		
		/* TODO: Optimize subClassName assignment. */
		
		$(this).on({
			mouseenter : function(){
				opts.text = $(this).data('tooltip');
				opts.subClassName = typeof opts.subClassName === 'undefined' ? '' : opts.subClassName;
				getTipElement().removeClass(opts.subClassName);
				opts.subClassName = $(this).data('tooltip-class');
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
			var x = e.pageX + opts.offset;
			var y = e.pageY + opts.offset;
			getTipElement().css({ 'top' : y, 'left' : x });
			if(!tipElement.is(':visible')){
				tipElement.children('span').html(opts.text);
				stopTimer();
				tipElement.stop().fadeIn(opts.fadeInDuration, function(){
					resetTimer();
				});
			}
		}
		
		function hide(){
			if(getTipElement().is(':visible')){
				tipElement.stop().fadeOut(opts.fadeOutDuration);
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
	$('div[data-tooltip]').brevadaTooltip();
});