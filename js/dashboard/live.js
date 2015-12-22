/* 
	Live Dashboard App
	Brevada Dashboard Frontend Framework (BDFF)
*/

/* Setup */

// Live App
dashboards.live = {};
dashboards.live.data = {}

// Big Ticker
dashboards.live.bigticker = {};
dashboards.live.bigticker.data = {}


/* Live App */

dashboards.live.fetch = function (id) {
}

dashboards.live.render = function (canvas) {
	$(canvas).empty();
	dashboards.live.bigticker.render(canvas);
}

/* Big Ticker */

dashboards.live.bigticker.fetch = function () {
	dashboards.live.bigticker.data = {
		'score': 87
	};
}

dashboards.live.bigticker.render = function (canvas) {
	dashboards.live.bigticker.fetch();

	var bigticker = document.createElement("div");
 	bigticker.setAttribute('class', 'bigticker dashboard-pod col-md-3');
 	$(bigticker).appendTo($(canvas));

	$('\
		<div class="score"></div>\
		').appendTo($(bigticker));
	dashboards.live.bigticker.renderScore($(bigticker).find('.score'));
}

dashboards.live.bigticker.renderScore = function (scoreCanvas) {
	var score = dashboards.live.bigticker.data['score'];
	$(scoreCanvas).html(score);
}