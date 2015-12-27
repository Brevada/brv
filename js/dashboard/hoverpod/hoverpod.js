/* 
	Live Dashboard App
	Brevada Dashboard Frontend Framework (BDFF)
*/

/* Setup */

// Hoverpod App
dashboards.hoverpod = {};
dashboards.hoverpod.data = {};

/* Hoverpod App */

dashboards.hoverpod.fetch = function (id) {
}

dashboards.hoverpod.render = function (canvas) {
	var hoverpod = document.createElement("div");
 	hoverpod.setAttribute('class', 'hoverpod toggle-button');
 	hoverpod.setAttribute('data-id', 'live');
 	$(hoverpod).appendTo($(canvas));

 	$('\
 		<div class="body">\
 			<div class="status"></div>\
 			<div class="responses"></div>\
 			<div class="tablets"></div>\
 		</div>\
 		').appendTo($(hoverpod));
 	dashboards.hoverpod.renderStatus($(hoverpod).find('.status'));
 	dashboards.hoverpod.renderResponses($(hoverpod).find('.responses'));
 	dashboards.hoverpod.renderTablets($(hoverpod).find('.tablets'));
}

dashboards.hoverpod.renderResponses = function (responses) {
	$('\
		<div class="number">87</div>\
		<div class="text">Hourly Responses</div>\
		').appendTo($(responses));
}

dashboards.hoverpod.renderStatus = function (status) {
	$('\
		<div class="bulb"></div>\
		<div class="text">Current Status</div>\
		').appendTo($(status));
}

dashboards.hoverpod.renderTablets = function (status) {
	$('\
		<div class="number">2</div>\
		<div class="text">Active Tablets</div>\
		').appendTo($(status));
}
