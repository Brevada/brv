/* 
	Live Dashboard App
*/

bdff.create('live', function(canvas, face){

	live = {};
	live.data = {}

	live.datastatus = {};
	live.datastatus.data = {}

	live.responses = {};
	live.responses.data = {}

	live.score = {};
	live.score.data = {}

	live.change = {};
	live.change.data = {}

	live.blank1 = {};
	live.blank1.data = {}

	live.blank2 = {};
	live.blank2.data = {}

	live.breakdown = {};
	live.breakdown.data = {}

	live.newsfeed = {};
	live.newsfeed.data = {}

	live.daily = {};
	live.daily.data = {}


	live.render = function (canvas) {
		canvas.children().not('div.message-container').remove();
		/* Template */
		$('\
			<div class="square square-1 col-md-6"></div>\
			<div class="square square-2 col-md-6"></div>\
			<div class="square square-3 col-md-12"></div>\
			').appendTo(canvas);
		var square_1 = canvas.find('.square-1');
			square_2 = canvas.find('.square-2'),
			square_3 = canvas.find('.square-3');
		
		// TODO: Make some sort of decision as to the
		// timeframe to pass in to these render functions
		// MAYBE: Allow them to choose the timeframe???????? (up to 23H?)

		// Square 1
		live.datastatus.render(square_1, 5);
		live.score.render(square_1, 5);
		live.responses.render(square_1, 5);
		live.change.render(square_1, 5);
		live.blank1.render(square_1, 5);
		live.blank2.render(square_1, 5);

		// Square 2
		live.breakdown.render(square_2, 5);
		live.newsfeed.render(square_2, 5);
		
		// Square 3
		live.daily.render(square_3, 5);
	}

	live.createTicker = function (canvas, size) {
		var el = document.createElement("div");
		el.setAttribute('class', 'col-md-' + size);

		var inner = document.createElement("div");
		inner.setAttribute('class', 'bigticker dashboard-pod');
		
		$(el).appendTo(canvas);
		$(inner).appendTo($(el));
		return inner;
	}

	/* Data Status */

	live.datastatus.fetch = function (hours) {
		// TODO: Fetch based on the number of hours back
		live.datastatus.data = {
			'timeframe': 'Last 4 Hours',
			'current_time': 87
		}
	}

	live.datastatus.render = function (canvas, hours) {
		live.datastatus.element = live.createTicker(canvas, 6);
		$(live.datastatus.element).addClass('datastatus');

		/* Template */
		$('\
			<div class="timeframe">Current Dataset: <span class="data"></span></div>\
			<div class="current-time">As of <span class="data"></span></div>\
			').appendTo($(live.datastatus.element));
		live.datastatus.renderData(hours);
	}

	live.datastatus.renderData = function (hours) {
		live.datastatus.fetch(hours);
		var timeframe = live.datastatus.data['timeframe'],
			current_time = live.datastatus.data['current_time'];
		$(live.datastatus.element).find('.timeframe .data').html(timeframe);
		$(live.datastatus.element).find('.current-time .data').html(current_time);
	}


	/* Responses */

	live.responses.fetch = function (hours) {
		// TODO: Fetch based on the number of hours back
		live.responses.data = {
			'responses': 87
		}
	}

	live.responses.render = function (canvas, hours) {
		live.responses.element = live.createTicker(canvas, 6);
		$(live.responses.element).addClass('responses');

		/* Template */
		$('\
			<div class="data"></div>\
			<div class="text">Responses</div>\
			').appendTo($(live.responses.element));
		live.responses.renderData(hours);
	}

	live.responses.renderData = function (hours) {
		live.responses.fetch(hours);
		var responses = live.responses.data['responses'];
		$(live.responses.element).find('.data').html(responses);
	}


	/* Score */

	live.score.fetch = function (hours) {
		// TODO: Fetch based on the number of hours back
		live.score.data = {
			'score': 74
		}
	}

	live.score.render = function (canvas, hours) {
		live.score.element = live.createTicker(canvas, 6);
		$(live.score.element).addClass('score');
		$(live.score.element).addClass('bad');

		/* Template */
		$('\
			<div class="data"></div>\
			<div class="text">Average</div>\
			').appendTo($(live.score.element));
		live.score.renderData(hours);
	}

	live.score.renderData = function (hours) {
		live.score.fetch(hours);
		var score = live.score.data['score'];
		$(live.score.element).find('.data').html(score);
	}


	/* Tablet Status */

	live.blank1.fetch = function (hours) {
		// TODO: Fetch based on the number of hours back
		live.blank1.data = {
			'responses': 87
		}
	}

	live.blank1.render = function (canvas, hours) {
		live.blank1.element = live.createTicker(canvas, 6);
		$(live.blank1.element).addClass('blank1');

		/* Template */
		$('\
			<div class="data"></div>\
			<div class="text">Responses</div>\
			').appendTo($(live.blank1.element));
		live.blank1.renderData(hours);
	}

	live.blank1.renderData = function (hours) {
		live.blank1.fetch(hours);
		var blank1 = live.blank1.data['responses'];
		$(live.blank1.element).find('.data').html(blank1);
	}


	/* Blank2 */

	live.blank2.fetch = function (hours) {
		// TODO: Fetch based on the number of hours back
		live.blank2.data = {
			'responses': 87
		}
	}

	live.blank2.render = function (canvas, hours) {
		live.blank2.element = live.createTicker(canvas, 6);
		$(live.blank2.element).addClass('blank2');

		/* Template */
		$('\
			<div class="data"></div>\
			<div class="text">Responses</div>\
			').appendTo($(live.blank2.element));
		live.blank2.renderData(hours);
	}

	live.blank2.renderData = function (hours) {
		live.blank2.fetch(hours);
		var blank2 = live.blank2.data['responses'];
		$(live.blank2.element).find('.data').html(blank2);
	}



	/* Change */

	live.change.fetch = function (hours) {
		// TODO: Fetch based on the number of hours back
		live.change.data = {
			'parity': '+',
			'change': 12
		}
	}

	live.change.render = function (canvas, hours) {
		live.change.element = live.createTicker(canvas, 6);
		$(live.change.element).addClass('score');
		$(live.change.element).addClass('bad-text');

		/* Template */
		$('\
			<div class="data"><span class="parity"></span><span class="change"></span></div>\
			<div class="text">Change</div>\
			').appendTo($(live.change.element));
		live.change.renderData(hours);
	}

	live.change.renderData = function (hours) {
		live.change.fetch(hours);
		var parity = live.change.data['parity'],
			change = live.change.data['change'];
		$(live.change.element).find('.parity').html(parity);
		$(live.change.element).find('.change').html(change);
	}


	/* Breakdown */

	live.breakdown.fetch = function (hours) {
		// TODO: Fetch based on the number of hours back
		live.breakdown.data = {
			'responses': 87
		}
	}

	live.breakdown.render = function (canvas, hours) {
		live.breakdown.element = live.createTicker(canvas, 12);
		$(live.breakdown.element).addClass('breakdown');

		/* Template */
		$('\
			<div class="pie-graph"></div>\
			<div class="bar-graph"></div>\
			<div class="text">Breakdown</div>\
			').appendTo($(live.breakdown.element));
		live.breakdown.renderPie(hours);
	}

	live.breakdown.renderPie = function (hours) {
		live.breakdown.fetch(hours);
		$(live.breakdown.element).find('.pie-graph').html('<canvas></canvas>');
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
		var ctx = $(live.breakdown.element).find(".pie-graph canvas").get(0).getContext("2d"),
			chart = new Chart(ctx).Doughnut(doughnutData, {responsive : false});
	}

	/* Newsfeed */

	live.newsfeed.fetch = function (hours) {
		// TODO: Fetch based on the number of hours back
		live.newsfeed.data = {
			'responses': 87
		}
	}

	live.newsfeed.render = function (canvas, hours) {
		live.newsfeed.element = live.createTicker(canvas, 12);
		$(live.newsfeed.element).addClass('newsfeed');

		/* Template */
		$('\
			<div class="header"></div>\
			<div class="text">Breakdown</div>\
			').appendTo($(live.newsfeed.element));
		live.newsfeed.renderData(hours);
	}

	live.newsfeed.renderData = function (hours) {
		live.newsfeed.fetch(hours);
		$(live.newsfeed.element).find('.header').html('Robbie');
	}



	/* Daily */

	live.daily.fetch = function () {
		// Fetch data for last 24 hours
		live.daily.data = {
			'score': 92,
			'change': '+7',
			'responses': 436
		}
	}

	live.daily.render = function (canvas) {
		live.daily.element = live.createTicker(canvas, 12);
		$(live.daily.element).addClass('daily');

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
			').appendTo($(live.daily.element));
		live.daily.renderData();
	}

	live.daily.renderData = function () {
		live.daily.fetch();
		var score = live.daily.data['score'],
			change = live.daily.data['change'],
			responses = live.daily.data['responses'];

		$(live.daily.element).find('.score .data').html(score);
		$(live.daily.element).find('.responses .data').html(responses);
		$(live.daily.element).find('.change .data').html(change);
	}
	
	live.render(canvas);
});
