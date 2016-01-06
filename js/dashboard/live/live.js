/* 
	Live Dashboard App
*/

bdff.create('live', function(canvas, face){

	live = { data: {} };
	live.datastatus = { data: {} };
	live.responses = { data: {} };
	live.score = { data: {} };
	live.change = { data: {} };
	live.blank1 = { data: {} };
	live.blank2 = { data: {} };
	live.breakdown = { data: {} };
	live.newsfeed = { data: {} };
	live.daily = { data: {} };

	live.render = function (mcanvas) {
		mcanvas.children().not('div.message-container').remove();
		
		var square_1 = $('<div>').addClass('square square-1 col-md-6').appendTo(mcanvas);
		var square_2 = $('<div>').addClass('square square-2 col-md-6').appendTo(mcanvas);
		var square_3 = $('<div>').addClass('square square-3 col-md-12').appendTo(mcanvas);

		// Square 1
		live.datastatus.render(square_1);
		live.score.render(square_1);
		live.responses.render(square_1);
		live.change.render(square_1);
		live.blank1.render(square_1);
		live.blank2.render(square_1);

		// Square 2
		live.breakdown.render(square_2);
		live.newsfeed.render(square_2);
		
		// Square 3
		live.daily.render(square_3);
	}

	live.createTicker = function (canvas, size) {
		var ticker = $('<div>').addClass('col-md-'+size).append(
				$('<div>').addClass('bigticker dashboard-pod')
			).appendTo(canvas).children('div');
			
		return ticker;
	}

	/* Data Status */

	live.datastatus.render = function (canvas) {
		live.datastatus.element = live.createTicker(canvas, 6);
		$(live.datastatus.element).addClass('datastatus');

		$('\
			<div class="timeframe">Current Dataset: <span class="data"></span></div>\
			<div class="current-time">As of <span class="data"></span></div>\
			').appendTo(live.datastatus.element);
	}

	live.datastatus.update = function (data) {
		var timeframe = data['timeframe'],
			current_time = data['current_time'];
		live.datastatus.element.find('.timeframe .data').html(timeframe);
		live.datastatus.element.find('.current-time .data').html(current_time);
	}

	/* Responses */

	live.responses.render = function (canvas) {
		live.responses.element = live.createTicker(canvas, 6);
		live.responses.element.addClass('responses');
		
		$('\
			<div class="data"></div>\
			<div class="text">Responses</div>\
			').appendTo(live.responses.element);
	}

	live.responses.update = function (data) {
		var responses = data['responses'];
		live.responses.element.find('.data').html(responses);
	}

	/* Score */

	live.score.render = function (canvas) {
		live.score.element = live.createTicker(canvas, 6);
		live.score.element.addClass('score');
		live.score.element.addClass('bad');

		/* Template */
		$('\
			<div class="data"></div>\
			<div class="text">Average</div>\
			').appendTo(live.score.element);
	}

	live.score.update = function (data) {
		var score = data['score'];
		live.score.element.find('.data')
		.removeClass('positive-text great-text neutral-text bad-text negative-text')
		.addClass(bdff.mood(parseFloat(score))+'-text')
		.text(score + "%");
	}

	/* Tablet Status */

	live.blank1.render = function (canvas) {
		live.blank1.element = live.createTicker(canvas, 6);
		live.blank1.element.addClass('blank1');

		/* Template */
		$('\
			<div class="data"></div>\
			<div class="text">Responses</div>\
			').appendTo(live.blank1.element);
	}

	live.blank1.update = function (data) {
		var blank1 = data['responses'];
		live.blank1.element.find('.data').html(blank1);
	}

	/* Blank2 */

	live.blank2.render = function (canvas) {
		live.blank2.element = live.createTicker(canvas, 6);
		live.blank2.element.addClass('blank2');

		/* Template */
		$('\
			<div class="data"></div>\
			<div class="text">Responses</div>\
			').appendTo(live.blank2.element);
	}

	live.blank2.update = function (data) {
		var blank2 = data['responses'];
		live.blank2.element.find('.data').html(blank2);
	}

	/* Change */

	live.change.render = function (canvas) {
		live.change.element = live.createTicker(canvas, 6);
		live.change.element.addClass('score');
		live.change.element.addClass('bad-text');

		/* Template */
		$('\
			<div class="data"><span class="parity"></span><span class="change"></span></div>\
			<div class="text">Change</div>\
			').appendTo(live.change.element);
	}

	live.change.update = function (data) {
		var parity = data['parity'],
			change = data['change'];
		live.change.element.find('.parity').html(parity);
		live.change.element.find('.change').html(change);
	}

	/* Breakdown */

	live.breakdown.render = function (canvas) {
		live.breakdown.element = live.createTicker(canvas, 12);
		live.breakdown.element.addClass('breakdown');

		/* Template */
		$('\
			<div class="pie-graph"></div>\
			<div class="bar-graph"></div>\
			<div class="text">Breakdown</div>\
			').appendTo(live.breakdown.element);
	}

	live.breakdown.update = function (data) {
		if(!live.breakdown.chart){
			live.breakdown.element.find('.pie-graph').html('<canvas></canvas>');
			var doughnutData = data;
			var ctx = live.breakdown.element.find(".pie-graph canvas").get(0).getContext("2d");
			
			live.breakdown.chart = new Chart(ctx, {
				type: 'doughnut',
				data: doughnutData,
				options: {
					responsive: true,
					maintainAspectRatio: false,
					legend: {
						display: false
					}
				}
			});
		} else {
			live.breakdown.chart.data.datasets[0].data = data.datasets[0].data;
			live.breakdown.chart.update();
		}
	}

	/* Newsfeed */

	live.newsfeed.render = function (canvas) {
		live.newsfeed.element = live.createTicker(canvas, 12);
		live.newsfeed.element.addClass('newsfeed');

		/* Template */
		$('\
			<div class="header"></div>\
			<div class="text">Breakdown</div>\
			').appendTo(live.newsfeed.element);
	}

	live.newsfeed.update = function (data) {
		live.newsfeed.element.find('.header').html(data);
	}


	/* Daily */

	live.daily.render = function (canvas) {
		live.daily.element = live.createTicker(canvas, 12);
		live.daily.element.addClass('daily');

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
			').appendTo(live.daily.element);
	}

	live.daily.update = function (data) {
		var score = data['score'],
			change = data['change'],
			responses = data['responses'];

		live.daily.element.find('.score > div')
		.removeClass('positive great neutral bad negative')
		.addClass(bdff.mood(parseFloat(score)))
		.find('.data').text(score);
		
		live.daily.element.find('.responses .data').text(responses);
		
		live.daily.element.find('.change .data').text(change);
	}
	
	live.render(canvas);
	
	face.datahook(10000, {
			url : '/api/v1/bdff/live',
			data : { 'store' : bdff.storeID(), 'hours' : 5 }
		}, function(data){
		if(data.hasOwnProperty('error') && data.error.length > 0){
			bdff.log('Uh oh...');
		} else if(data.hasOwnProperty('live')) {
			
			live.datastatus.update(data.live.datastatus);
			live.responses.update(data.live);
			live.score.update(data.live);
			live.change.update(data.live.change);
			//live.blank1.update();
			//live.blank2.update();
			live.breakdown.update(data.live.breakdown);
			live.newsfeed.update(data.live.newsfeed);
			live.daily.update(data.live.daily);
			
			$('div[data-tooltip]').brevadaTooltip();
		} else {
			bdff.log('Uh oh...');
		}
	});
	
});
