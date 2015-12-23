/* 
	Live Dashboard App
	Brevada Dashboard Frontend Framework (BDFF)
*/

/* Setup */



// Live App
dashboards.live = {};
dashboards.live.data = {}

dashboards.live.hourly = {};
dashboards.live.hourly.data = {}

dashboards.live.current = {};
dashboards.live.current.data = {}


/* Live App */



dashboards.live.render = function (canvas) {
	$(canvas).empty();
	dashboards.live.hourly.render(canvas);
	dashboards.live.current.render(canvas);
}

dashboards.live.createTicker = function (canvas, size) {
	var el = document.createElement("div");
 	el.setAttribute('class', 'col-md-' + size);

 	var inner = document.createElement("div");
 	inner.setAttribute('class', 'bigticker dashboard-pod');
 	
 	$(el).appendTo($(canvas));
 	$(inner).appendTo($(el));
 	return inner;
}

/* Hourly */

dashboards.live.hourly.fetch = function () {
	dashboards.live.hourly.data = {
		'hourly': 87
	}
}

dashboards.live.hourly.render = function (canvas) {
	dashboards.live.hourly.element = dashboards.live.createTicker(canvas, 3);
	$(dashboards.live.hourly.element).addClass('hourly');

	$('\
		<div class="data"></div>\
		<div class="text">Hourly Responses</div>\
		').appendTo($(dashboards.live.hourly.element));
	dashboards.live.hourly.renderData();
}

dashboards.live.hourly.renderData = function () {
	dashboards.live.hourly.fetch();
	var hourly = dashboards.live.hourly.data['hourly'];
	$(dashboards.live.hourly.element).find('.data').html(hourly);
}


/* Current */

dashboards.live.current.fetch = function () {
	dashboards.live.current.data = {
		'score': 92
	}
}

dashboards.live.current.render = function (canvas) {
	dashboards.live.current.element = dashboards.live.createTicker(canvas, 9);
	$(dashboards.live.current.element).addClass('current');

	$('\
		<div class="bulb"></div>\
		<div class="text"><span class="data"></span> Hourly Responses</div>\
		').appendTo($(dashboards.live.current.element));
	dashboards.live.current.renderData();
}

dashboards.live.current.renderData = function () {
	dashboards.live.current.fetch();
	var score = dashboards.live.current.data['score'];
	$(dashboards.live.current.element).find('.data').html(score);
}

