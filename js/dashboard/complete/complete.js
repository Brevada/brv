/* Complete Dashboard App */
var complete = {};

bdff.create('complete', function(canvas, face){
	/*
	DATA: each aspect, financial (revenue), number of responses
	have JS timeline with milestones
	*/

	// 1. Create JS object with all data points (for now)
	// A timeframe is sent to the server and data points are returned in buckets (we should aim to have 15 buckets per timeframe)
	// TIMEFRAME: START DATE, END DATE (eg. Feb 1 - Feb 18)
	complete.fetch = function (start, end) {
		return server_data = {
			labels: ['Feb 1', 'Feb 3', 'Feb 5', 'Feb 7', 'Feb 9', 'Feb 11', 'Feb 13', 'Feb 15', 'Feb 18'],
			aspects: [
				{
					label: 'Customer Service',
					data: [86, 77, 90, 83, 89, 67, 78, 89, 80]
				},
				{
					label: 'Wait Time',
					data: [86, 77, 90, 83, 80, 89, 67, 78, 89]
				},
				{
					label: 'Pricing',
					data: [90, 83, 89, 67, 86, 77, 78, 89, 80]
				},
				{
					label: 'Food Presentation',
					data: [86, 77, 90, 67, 78, 89, 83, 89, 80]
				},
				{
					label: 'Location',
					data: [86, 77, 71, 83, 89, 67, 78, 83, 80]
				}
			],
			minValue: 50,
			financial: [34500, 44500, 2389, 34500, 34500, 38600, 34500, 32940, 33500],
			// Milestones are for rendering the timeline on top
			milestones: [
				{
					name: 'Hired a new chef',
					date: 'Feb 13'
				},
				{
					name: 'Hired a new chef',
					date: 'Feb 13'
				}
			]
		};
	}
	complete.colorOptions = ['#ca60f2', '#f260b6', '#f2606a', '#60b6f2', '#f2c460', '#d98d42']
	complete.styleData = function (data) {
		for (var p in data) {
	        complete.serverData.aspects[p].fill = false;
	        complete.serverData.aspects[p].borderColor = complete.serverData.aspects[p].backgroundColor = complete.colorOptions[p%5];
	    	complete.serverData.aspects[p].datasetStrokeWidth = 5;
	    }
	    return complete.serverData.aspects;
	}

	complete.render = function () {
		// Fetch data
		complete.serverData = complete.fetch(12, 14);
		complete.serverData.aspects = complete.styleData(complete.serverData.aspects);

		$('#slider').dateRangeSlider();
		
		var data = {
		    labels: complete.serverData.labels,
		    datasets: complete.serverData.aspects
		};
		var ctx = $(complete.el).find('.graph').get(0).getContext("2d");
		return new Chart(ctx, {
		    type: 'line',
		    data: data,
		    options: {
		    	responsive: true,
		        scales: {
					xAxes: [{
						display: true
					}],
					yAxes: [{
						display: false,
						ticks : {
							beginAtZero: true,
							autoSkip: false,
							min: complete.serverData.minValue,
							max: 100
						}
					}]
				},
		        legend: {
					display: true
				},
				tooltips: {
					mode : 'label',
					backgroundColor : '#999',
					color : '#FFFFFF'
				}
		    }
		});
	}

	canvas.children().not('div.message-container').remove();
	
	complete.el = $('<div>').addClass('complete-page col-md-12').appendTo(canvas);
	
	$(' <div class="dashboard-pod settings"><div id="slider"></div>Settings</div>\
		<div class="dashboard-pod timeline">Timeline</div>\
		<canvas class="dashboard-pod graph">HI</canvas>\
	  ').appendTo(complete.el);

	

	complete.render();
});