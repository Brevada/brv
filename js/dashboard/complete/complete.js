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
	complete.fetch = function (start, end, excluded) {
		var server_data = {
			labels: ['Feb 1', 'Feb 3', 'Feb 5', 'Feb 7', 'Feb 9', 'Feb 11', 'Feb 13', 'Feb 15', 'Feb 18'],
			excluded: [
				{
					label: 'Exc Test',
					id: 23
				}
			],
			average: [
				{
					label: 'Average',
					data: [86, 77, 90, 83, 89, 67, 78, 89, 80],
					responses: 45
				}
			],
			aspects: [
				{
					position: 1,
					average: 89,
					label: 'Customer Service',
					data: [86, 77, 90, 83, 89, 67, 78, 89, 80],
					responses: 45
				},
				{
					position: 2,
					average: 67,
					label: 'Wait Time',
					data: [86, 77, 90, 83, 80, 89, 67, 78, 89],
					responses: 45
				},
				{
					position: 3,
					average: 74,
					label: 'Pricing',
					data: [90, 83, 89, 67, 86, 77, 78, 89, 80],
					responses: 45
				},
				{
					position: 4,
					average: 53,
					label: 'Food Presentation',
					data: [86, 77, 90, 67, 78, 89, 83, 89, 80],
					responses: 45
				},
				{
					position: 5,
					average: 88,
					label: 'Location',
					data: [86, 77, 71, 83, 89, 67, 66, 77, 80],
					responses: 45
				},
				{
					position: 6,
					average: 99,
					label: 'Authenticity',
					data: [86, 77, 90, 67, 78, 89, 89, 89, 76],
					responses: 45
				},
				{
					position: 7,
					average: 22,
					label: 'Parking',
					data: [86, 77, 71, 76, 89, 67, 78, 83, 80],
					responses: 45
				}
			],
			minValue: 50,
			maxValue: 95,
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

		// Temp: Exclude certain aspects on front-ed
		server_data.aspects = server_data.aspects.filter(function( obj ) {
		    return excluded.indexOf(obj.position) < 0;
		});

		return server_data;
	}
	complete.colorOptions = ['#ca60f2', '#f260b6', '#f2606a', '#60b6f2', '#f2c460', '#d98d42'];
	// complete.colorOptions = ['#2ecc0e', '#29b60c', '#30e30c', '#36ff0d', '#24a40a', '#197806'];
	complete.colorFillOptions = ['rgba(202,96,242, 0.1)', 'rgba(242,96,182,0.1)', 'rgba(242,96,106,0.1)', 'rgba(96,182,242,0.1)', 'rgba(242,196,96,0.1)', 'rgba(217,141,66,0.1)']
	complete.styleData = function (data) {
		for (var p in data) {
	        complete.serverData.aspects[p].fill = true;
	        complete.serverData.aspects[p].borderColor = complete.colorOptions[p%5];
	        complete.serverData.aspects[p].backgroundColor = complete.colorFillOptions[p%5];
	    	complete.serverData.aspects[p].datasetStrokeWidth = 5;
	    }
	    complete.serverData.average[0].fill = true;
	    complete.serverData.average[0].backgroundColor = complete.colorFillOptions[0];
	}

	// complete.excludeData = function (excluded) {
	// 	return complete.serverData.aspects.filter(function( obj ) {
	// 	    return excluded.indexOf(obj.position) < 0;
	// 	});
	// }

	complete.renderAspects = function () {
		$(complete.el).find('.aspects').html('');
		for (var aspects = 0; aspects < complete.serverData.aspects.length; aspects++ ) {

			var aspect = complete.serverData.aspects[aspects],
				position = aspect.position,
				label = aspect.label,
				responses = aspect.responses,
				average = aspect.average,
				background = aspect.borderColor;

			$('<div class="aspect" data-position="'+position+'" >\
					<div class="aspect-icon" style="background: '+background+';"></div>\
					<div class="aspect-data">\
						<div class="aspect-title">'+label+'</div>\
						<div class="aspect-info">Responses: '+responses+'</div>\
						<div class="aspect-info">Average: '+average+'</div>\
					</div>\
					</div>\
					').appendTo($(complete.el).find('.aspects'));
		}
	}
	
	complete.render = function () {
		// Fetch data
		complete.serverData = complete.fetch(12, 14, complete.excluded);
		
		// var	refinedAspects = complete.excludeData(complete.excluded);
		complete.styleData(complete.serverData.aspects);

		complete.renderAspects();
		complete.renderDateSlider();
		complete.renderCompleteGraph(complete.serverData.aspects);
		complete.renderAverageLineGraph(complete.serverData.average);
		complete.renderAverageBarGraph(complete.serverData.average);
		complete.initEvents();
		
	}
	complete.renderDateSlider = function () {
		$('#slider').dateRangeSlider({
			bounds: {min: new Date(2012, 0, 1), max: new Date(2015, 11, 31, 12, 59, 59)},
			defaultValues: {min: new Date(2014, 1, 10), max: new Date(2014, 4, 22)}
		});
	}
	complete.renderCompleteGraph = function (aspects) {
		var data = {
		    labels: complete.serverData.labels,
		    datasets: aspects
		};
		var ctx = $(complete.el).find('.graph').get(0).getContext("2d"),
			chart =  new Chart(ctx, {
			    type: 'line',
			    data: data,
			    options: {
			    	responsive: true,
			    	fillOpacity: '.3',
			    	maintainAspectRatio: false,
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
								max: complete.serverData.maxValue
							}
						}],
						gridLines: {
							color: 'rgba(0, 0, 0, 0)'
						}
					},
			        legend: {
						display: false,
						labels: {
							boxWidth: 20,
							fontColor: '#333'
						}
					},
					tooltips: {
						mode : 'label',
						backgroundColor : '#999',
						color : '#FFFFFF'
					}
			    }
			});
	}

	complete.renderAverageLineGraph = function (average) {
		var data = {
		    labels: complete.serverData.labels,
		    datasets: average
		};
		var ctx = $(complete.el).find('.average-line').get(0).getContext("2d"),
			chart =  new Chart(ctx, {
			    type: 'line',
			    data: data,
			    options: {
			    	responsive: true,
			    	maintainAspectRatio: false,
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
								max: complete.serverData.maxValue
							}
						}],
						gridLines: {
							color: 'rgba(0, 0, 0, 0)'
						}
					},
			        legend: {
						display: false,
						labels: {
							boxWidth: 20,
							fontColor: '#333'
						}
					},
					tooltips: {
						mode : 'label',
						backgroundColor : '#999',
						color : '#FFFFFF'
					}
			    }
			});
			console.log(ctx);
	}

	complete.renderAverageBarGraph = function (average) {
		var labels = [],
			averages = [];
		for (var aspects = 0; aspects < complete.serverData.aspects.length; aspects++) {
			var aspect = complete.serverData.aspects[aspects],
				label = aspect.label,
				average = aspect.average,
				color = aspect.borderColor;

			labels.push(aspect.label);
			averages.push(average);
		}
		var data = {
		    labels: labels,
		    datasets: [
		        {
		            label: "My Second dataset",
		            backgroundColor: "rgba(220,220,220,0.2)",
		            borderColor: "rgba(220,220,220,1)",
		            borderWidth: 1,
		            hoverBackgroundColor: "rgba(220,220,220,0.2)",
		            hoverBorderColor: "rgba(220,220,220,1)",
		            data: averages
		        }
		    ]
		};
		var ctx = $(complete.el).find('.average-bar').get(0).getContext("2d");
		var b = new Chart(ctx,{
		    type:"bar",
		    data: data,
		    options: {
		        scales: {
	                xAxes: [{
	                	// display: false,
                        stacked: true
	                }],
	                yAxes: [{
	                	display: false,
                        stacked: true
	                }]
		        },
		        legend: {
		        	display: false
		        }
		    }
		});
	}

	complete.initEvents = function () {
		// $(window).resize(complete.sizeGraph);
		$('#slider').bind('valuesChanging', function (e, data) {
			var min = data.values.min.toString().split(" "),
				max = data.values.max.toString().split(" ");
				min_date = min[1] + ' ' + min[2] + ' ' + min[3],
				max_date = max[1] + ' ' + max[2] + ' ' + max[3];
			$('.settings .header').html(min_date + ' - ' + max_date);
		});
		$(complete.el).find('.aspect').click(function () {
			complete.toggleAspect(parseInt($(this).attr('data-position')));
		})
	}

	// complete.toggleAspect = function (position) {
	// 	var excludedPosition = complete.excluded.indexOf(position);
	// 	if (excludedPosition > -1) {
	// 		complete.excluded.splice(excludedPosition, 1);	
	// 	} else {
	// 		complete.excluded.push(position);
	// 	}
	// 	// console.log(complete.excluded);
	// 	complete.render();
	// }

	complete.sizeGraph = function () {
		var available_space = $(window).height()-260;
		$(complete.el).find('.graph-container').height(available_space*0.8);
		$(complete.el).find('.sub-graph-container').height(available_space*0.2);
	}

	canvas.children().not('div.message-container').remove();
	
	complete.el = $('<div>').addClass('complete-page col-md-12').appendTo(canvas);
	
	$('<div class="col-md-9 main">\
			<!--<div class="dashboard-pod timeline">Timeline</div>-->\
			<div class="settings">\
				<div class="header">Text</div>\
				<div id="slider"></div>\
			</div>\
			<div class="graph-container">\
				<canvas class="dashboard-pod graph"></canvas>\
			</div>\
			<div class="sub-graph-container col-md-12">\
				<div class="sub-graph-container col-md-12">\
					<canvas class="dashboard-pod average-line"></canvas>\
				</div>\
				<div class="sub-graph-container col-md-12">\
					<canvas class="dashboard-pod average-bar"></canvas>\
				</div>\
			</div>\
		</div>\
		<div class="col-md-3 side-control">\
			<div class="aspects"></div>\
		</div>\
	  ').appendTo(complete.el);
	complete.excluded = [2, 3, 4];
	// complete.sizeGraph();
	complete.render();
});