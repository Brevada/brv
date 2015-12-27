/* 
	Live Dashboard App
*/

dashboards.live = {};
dashboards.live.data = {}

dashboards.live.datastatus = {};
dashboards.live.datastatus.data = {}

dashboards.live.responses = {};
dashboards.live.responses.data = {}

dashboards.live.score = {};
dashboards.live.score.data = {}

dashboards.live.change = {};
dashboards.live.change.data = {}

dashboards.live.blank1 = {};
dashboards.live.blank1.data = {}

dashboards.live.blank2 = {};
dashboards.live.blank2.data = {}

dashboards.live.breakdown = {};
dashboards.live.breakdown.data = {}

dashboards.live.newsfeed = {};
dashboards.live.newsfeed.data = {}

dashboards.live.daily = {};
dashboards.live.daily.data = {}


/* Live App */

dashboards.live.render = function (canvas) {
	$(canvas).empty();
	/* Template */
	$('\
		<div class="square square-1 col-md-6"></div>\
		<div class="square square-2 col-md-6"></div>\
		<div class="square square-3 col-md-12"></div>\
		').appendTo($(canvas));
	var square_1 = $(canvas).find('.square-1');
		square_2 = $(canvas).find('.square-2'),
		square_3 = $(canvas).find('.square-3');
	
	// TODO: Make some sort of decision as to the
	// timeframe to pass in to these render functions
	// MAYBE: Allow them to choose the timeframe???????? (up to 23H?)

	// Square 1
	dashboards.live.datastatus.render(square_1, 5);
	dashboards.live.score.render(square_1, 5);
	dashboards.live.responses.render(square_1, 5);
	dashboards.live.change.render(square_1, 5);
	dashboards.live.blank1.render(square_1, 5);
	dashboards.live.blank2.render(square_1, 5);

	// Square 2
	dashboards.live.breakdown.render(square_2, 5);
	dashboards.live.newsfeed.render(square_2, 5);
	
	// Square 3
	dashboards.live.daily.render(square_3, 5);
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

/* Data Status */

dashboards.live.datastatus.fetch = function (hours) {
	// TODO: Fetch based on the number of hours back
	dashboards.live.datastatus.data = {
		'timeframe': 'Last 4 Hours',
		'current_time': 87
	}
}

dashboards.live.datastatus.render = function (canvas, hours) {
	dashboards.live.datastatus.element = dashboards.live.createTicker(canvas, 6);
	$(dashboards.live.datastatus.element).addClass('datastatus');

	/* Template */
	$('\
		<div class="timeframe">Current Dataset: <span class="data"></span></div>\
		<div class="current-time">As of <span class="data"></span></div>\
		').appendTo($(dashboards.live.datastatus.element));
	dashboards.live.datastatus.renderData(hours);
}

dashboards.live.datastatus.renderData = function (hours) {
	dashboards.live.datastatus.fetch(hours);
	var timeframe = dashboards.live.datastatus.data['timeframe'],
		current_time = dashboards.live.datastatus.data['current_time'];
	$(dashboards.live.datastatus.element).find('.timeframe .data').html(timeframe);
	$(dashboards.live.datastatus.element).find('.current-time .data').html(current_time);
}


/* Responses */

dashboards.live.responses.fetch = function (hours) {
	// TODO: Fetch based on the number of hours back
	dashboards.live.responses.data = {
		'responses': 87
	}
}

dashboards.live.responses.render = function (canvas, hours) {
	dashboards.live.responses.element = dashboards.live.createTicker(canvas, 6);
	$(dashboards.live.responses.element).addClass('responses');

	/* Template */
	$('\
		<div class="data"></div>\
		<div class="text">Responses</div>\
		').appendTo($(dashboards.live.responses.element));
	dashboards.live.responses.renderData(hours);
}

dashboards.live.responses.renderData = function (hours) {
	dashboards.live.responses.fetch(hours);
	var responses = dashboards.live.responses.data['responses'];
	$(dashboards.live.responses.element).find('.data').html(responses);
}


/* Score */

dashboards.live.score.fetch = function (hours) {
	// TODO: Fetch based on the number of hours back
	dashboards.live.score.data = {
		'score': 74
	}
}

dashboards.live.score.render = function (canvas, hours) {
	dashboards.live.score.element = dashboards.live.createTicker(canvas, 6);
	$(dashboards.live.score.element).addClass('score');
	$(dashboards.live.score.element).addClass('bad');

	/* Template */
	$('\
		<div class="data"></div>\
		<div class="text">Average</div>\
		').appendTo($(dashboards.live.score.element));
	dashboards.live.score.renderData(hours);
}

dashboards.live.score.renderData = function (hours) {
	dashboards.live.score.fetch(hours);
	var score = dashboards.live.score.data['score'];
	$(dashboards.live.score.element).find('.data').html(score);
}


/* Tablet Status */

dashboards.live.blank1.fetch = function (hours) {
	// TODO: Fetch based on the number of hours back
	dashboards.live.blank1.data = {
		'responses': 87
	}
}

dashboards.live.blank1.render = function (canvas, hours) {
	dashboards.live.blank1.element = dashboards.live.createTicker(canvas, 6);
	$(dashboards.live.blank1.element).addClass('blank1');

	/* Template */
	$('\
		<div class="data"></div>\
		<div class="text">Responses</div>\
		').appendTo($(dashboards.live.blank1.element));
	dashboards.live.blank1.renderData(hours);
}

dashboards.live.blank1.renderData = function (hours) {
	dashboards.live.blank1.fetch(hours);
	var blank1 = dashboards.live.blank1.data['responses'];
	$(dashboards.live.blank1.element).find('.data').html(blank1);
}


/* Blank2 */

dashboards.live.blank2.fetch = function (hours) {
	// TODO: Fetch based on the number of hours back
	dashboards.live.blank2.data = {
		'responses': 87
	}
}

dashboards.live.blank2.render = function (canvas, hours) {
	dashboards.live.blank2.element = dashboards.live.createTicker(canvas, 6);
	$(dashboards.live.blank2.element).addClass('blank2');

	/* Template */
	$('\
		<div class="data"></div>\
		<div class="text">Responses</div>\
		').appendTo($(dashboards.live.blank2.element));
	dashboards.live.blank2.renderData(hours);
}

dashboards.live.blank2.renderData = function (hours) {
	dashboards.live.blank2.fetch(hours);
	var blank2 = dashboards.live.blank2.data['responses'];
	$(dashboards.live.blank2.element).find('.data').html(blank2);
}



/* Change */

dashboards.live.change.fetch = function (hours) {
	// TODO: Fetch based on the number of hours back
	dashboards.live.change.data = {
		'parity': '+',
		'change': 12
	}
}

dashboards.live.change.render = function (canvas, hours) {
	dashboards.live.change.element = dashboards.live.createTicker(canvas, 6);
	$(dashboards.live.change.element).addClass('score');
	$(dashboards.live.change.element).addClass('bad-text');

	/* Template */
	$('\
		<div class="data"><span class="parity"></span><span class="change"></span></div>\
		<div class="text">Change</div>\
		').appendTo($(dashboards.live.change.element));
	dashboards.live.change.renderData(hours);
}

dashboards.live.change.renderData = function (hours) {
	dashboards.live.change.fetch(hours);
	var parity = dashboards.live.change.data['parity'],
		change = dashboards.live.change.data['change'];
	$(dashboards.live.change.element).find('.parity').html(parity);
	$(dashboards.live.change.element).find('.change').html(change);
}


/* Breakdown */

dashboards.live.breakdown.fetch = function (hours) {
	// TODO: Fetch based on the number of hours back
	dashboards.live.breakdown.data = {
		'responses': 87
	}
}

dashboards.live.breakdown.render = function (canvas, hours) {
	dashboards.live.breakdown.element = dashboards.live.createTicker(canvas, 12);
	$(dashboards.live.breakdown.element).addClass('breakdown');

	/* Template */
	$('\
		<div class="pie-graph"></div>\
		<div class="bar-graph"></div>\
		<div class="text">Breakdown</div>\
		').appendTo($(dashboards.live.breakdown.element));
	dashboards.live.breakdown.renderPie(hours);
}

dashboards.live.breakdown.renderPie = function (hours) {
	dashboards.live.breakdown.fetch(hours);
	$(dashboards.live.breakdown.element).find('.pie-graph').html('<canvas></canvas>');
	var doughnutData = [
				{
					value: 300,
					color:"#2ecc0e",
					label: "Positive"
				},
				{
					value: 50,
					color: "#82cc0e",
					label: "Great"
				},
				{
					value: 100,
					color: "#afcc0e",
					label: "Neutral"
				},
				{
					value: 40,
					color: "#ccc10e",
					label: "Bad"
				},
				{
					value: 120,
					color: "#cc750e",
					label: "Negative"
				}

			];
	var ctx = $(dashboards.live.breakdown.element).find(".pie-graph canvas").get(0).getContext("2d"),
    	chart = new Chart(ctx).Doughnut(doughnutData, {responsive : false});
}

/* Newsfeed */

dashboards.live.newsfeed.fetch = function (hours) {
	// TODO: Fetch based on the number of hours back
	dashboards.live.newsfeed.data = {
		'responses': 87
	}
}

dashboards.live.newsfeed.render = function (canvas, hours) {
	dashboards.live.newsfeed.element = dashboards.live.createTicker(canvas, 12);
	$(dashboards.live.newsfeed.element).addClass('newsfeed');

	/* Template */
	$('\
		<div class="header"></div>\
		<div class="text">Breakdown</div>\
		').appendTo($(dashboards.live.newsfeed.element));
	dashboards.live.newsfeed.renderData(hours);
}

dashboards.live.newsfeed.renderData = function (hours) {
	dashboards.live.newsfeed.fetch(hours);
	$(dashboards.live.newsfeed.element).find('.header').html('Robbie');
}



/* Daily */

dashboards.live.daily.fetch = function () {
	// Fetch data for last 24 hours
	dashboards.live.daily.data = {
		'score': 92,
		'change': '+7',
		'responses': 436
	}
}

dashboards.live.daily.render = function (canvas) {
	dashboards.live.daily.element = dashboards.live.createTicker(canvas, 12);
	$(dashboards.live.daily.element).addClass('daily');

	/* Template */
	$('\
		<div class="title">Last 24 Hours</div>\
		<div class="col-md-4 score">\
			<div class="bulb positive"><div class="all-data"><span class="data"></span>%</div> Average</div>\
		</div>\
		<div class="col-md-4 change">\
			<div class="bulb great"><div class="all-data"><span class="data"></span>%</div> Change</div>\
		</div>\
		<div class="col-md-4 responses">\
			<div class="bulb neutral"><div class="all-data"><span class="data"></span></div> Responses</div>\
		</div>\
		').appendTo($(dashboards.live.daily.element));
	dashboards.live.daily.renderData();
}

dashboards.live.daily.renderData = function () {
	dashboards.live.daily.fetch();
	var score = dashboards.live.daily.data['score'],
		change = dashboards.live.daily.data['change'],
		responses = dashboards.live.daily.data['responses'];

	$(dashboards.live.daily.element).find('.score .data').html(score);
	$(dashboards.live.daily.element).find('.responses .data').html(responses);
	$(dashboards.live.daily.element).find('.change .data').html(change);
	
}

