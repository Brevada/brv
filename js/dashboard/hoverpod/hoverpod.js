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
		<div class="number"></div>\
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
			<div class="number"></div>\
			<div class="text">Active Tablets</div>\
			').appendTo(status);
	};
		
 	renderStatus(hoverpod.find('.status'));
 	renderResponses(hoverpod.find('.responses'));
 	renderTablets(hoverpod.find('.tablets'));
	
	var setResponses = function(num){
		hoverpod.find('div.responses > div.number').text(num);
	};
	
	var setTablets = function(num){
		hoverpod.find('div.tablets > div.number').text(num);
	};
	
	var setStatus = function(num){
		hoverpod.find('div.status > div.bulb')
		.removeClass('positive great neutral bad negative')
		.addClass(bdff.mood(num));
	};
	
	face.datahook(10000, {
			url : '/api/v1/bdff/hoverpod',
			data : { 'store' : bdff.storeID() }
		}, function(data){
		if(data.hasOwnProperty('error') && data.error.length > 0){
			bdff.log('Uh oh...');
		} else if(data.hasOwnProperty('hoverpod')) {
			setResponses(data.hoverpod.responses);
			setTablets(data.hoverpod.tablets);
			setStatus(data.hoverpod.mood);
		} else {
			bdff.log('Uh oh...');
		}
	});	
	
});