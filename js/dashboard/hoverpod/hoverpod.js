/* 
	Live Dashboard App
	Brevada Dashboard Frontend Framework (BDFF)
*/

/* Hoverpod App */

bdff.create('hoverpod', function(canvas, face){
	var hoverpod = $('<div>');
 	hoverpod.addClass('hoverpod toggle-button');
 	hoverpod.attr('data-id', 'live');
 	hoverpod.appendTo(canvas);

 	$('\
 		<div class="body">\
 			<div class="status"></div>\
 			<div class="responses"></div>\
 			<div class="tablets"></div>\
 		</div>\
 		').appendTo(hoverpod);
		
	var renderResponses = function(responses){
		$('\
		<div class="number">87</div>\
		<div class="text">Hourly Responses</div>\
		').appendTo(responses);
	};
	
	var renderStatus = function (status) {
		$('\
			<div class="bulb"></div>\
			<div class="text">Current Status</div>\
			').appendTo(status);
	};

	var renderTablets = function (status) {
		$('\
			<div class="number">2</div>\
			<div class="text">Active Tablets</div>\
			').appendTo(status);
	};
		
 	renderStatus(hoverpod.find('.status'));
 	renderResponses(hoverpod.find('.responses'));
 	renderTablets(hoverpod.find('.tablets'));
});