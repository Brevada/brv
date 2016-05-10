/* Complete Dashboard App */
var complete = {};

bdff.create('complete', function(canvas, face){
	/*
	DATA: each aspect, financial (revenue), number of responses
	have JS timeline with milestones
	*/
	complete.included = false;
	complete.serverData = {
		aspects : {},
		financials : [],
		milestones : {},
		average : {}
	};
	
	complete.colorOptions = ['#ca60f2', '#f260b6', '#f2606a', '#60b6f2', '#f2c460', '#d98d42'];
	complete.colorFillOptions = ['rgba(202,96,242, 0.4)', 'rgba(242,96,182,0.4)', 'rgba(242,96,106,0.4)', 'rgba(96,182,242,0.4)', 'rgba(242,196,96,0.4)', 'rgba(217,141,66,0.4)']
	// complete.colorOptions = ['#2ecc0e', '#29b60c', '#30e30c', '#36ff0d', '#24a40a', '#197806'];
	// complete.colorFillOptions = ['rgba(46,204,14, 0.1)', 'rgba(41,182,12,0.1)', 'rgba(48,227,12,0.1)', 'rgba(54,255,13,0.1)', 'rgba(36,164,10,0.1)', 'rgba(217,141,66,0.1)']
	complete.styleData = function () {
		for (var p in complete.serverData.aspects) {
			complete.serverData.aspects[p].disabled = !complete.serverData.aspects[p].bucket;
			if (!complete.serverData.aspects[p].bucket) {
				complete.serverData.aspects[p].borderColor = '#666';
			} else {
				complete.serverData.aspects[p].fill = true;
		        complete.serverData.aspects[p].borderColor = complete.colorOptions[p%5];
		        complete.serverData.aspects[p].backgroundColor = complete.colorFillOptions[p%5];
		    	complete.serverData.aspects[p].datasetStrokeWidth = 5;
			}

	    }
	}

	complete.renderAspects = function () {
		for (var i in complete.serverData.aspects) {

			var aspect = complete.serverData.aspects[i],
				background = aspect.borderColor;
				
			var aspectDom = $(complete.el).find('.aspects div.aspect[data-id="'+aspect.id+'"]');
			var add = false;
			if(aspectDom.length == 0){
				// Add at right spot.
				add = true;
				aspectDom = $('<div class="aspect" data-id="'+aspect.id+'" >\
					<div class="aspect-icon"></div>\
					<div class="aspect-data">\
						<div class="aspect-title"></div>\
						<div class="aspect-info"></div>\
					</div>\
					<div class="aspect-visibility"><i class="fa fa-eye"></i></div>\
					</div>\
					');
			}
			
			if(aspect.disabled){
				aspectDom.addClass('aspect-disabled');
				aspectDom.find('.aspect-visibility > i').removeClass('fa-eye').addClass('fa-eye-slash');
			} else {
				aspectDom.removeClass('aspect-disabled');
				aspectDom.find('.aspect-visibility > i').removeClass('fa-eye-slash').addClass('fa-eye');
			}
			
			aspectDom.children('div.aspect-icon').css({ background: background });
			aspectDom.find('div.aspect-data > div.aspect-title').text(aspect.title);
			aspectDom.find('div.aspect-data > div.aspect-info').text(aspect.bucket ? 'Responses: ' + aspect.bucket.size : 'Disabled');
			
			if(add){
				if($(complete.el).find('div.aspect').length > 0){
					$(complete.el).find('div.aspect').each(function(){
						var thisTitle = $(this).find('div.aspect-title').text();
						var nextTitle = $(this).next('div.aspect').length > 0 ? $(this).next().find('div.aspect-title').text() : false;
						
						if(!nextTitle || nextTitle > thisTitle){
							aspectDom.hide().insertAfter($(this)).slideDown(100);
							return true;
						}
					});
				} else {
					aspectDom.hide().appendTo($(complete.el).find('.aspects')).slideDown(100);
				}
			}
		}
	}
	
	complete.render = function () {
		complete.styleData();
		complete.renderAspects();

		complete.renderAspectRelGraph();
		complete.renderResponseAbsGraph();
		complete.renderAspectAbsGraph();
		complete.renderAverageLineGraph();
		complete.renderAverageBarGraph();
		
		complete.renderDateSlider();
	}
	complete.renderDateSlider = function () {
		if(complete.dateSlider || !complete.minDate){ return; }
		
		var boundMinDate = new Date(0);
		boundMinDate.setUTCSeconds(complete.minDate);
		// TODO: The initial min and max dates are off (starting from 2011...)
		complete.dateSlider = $('#slider').dateRangeSlider({
			bounds: {min: boundMinDate, max: new Date()},
			defaultValues: {min: boundMinDate, max: new Date()}
		});
		
		$('.settings .date').html(moment(boundMinDate).format('MMM Do, YYYY') + ' - ' + moment().format('MMM Do, YYYY'));
	}
	complete.updateDateSlider = function (hoursAgo) {
			var d = new Date();
			d.setDate(d.getDate() - hoursAgo/24);

			$("#slider").dateRangeSlider("values", d, new Date());
	}
	
	complete.renderResponseAbsGraph = function () {
		if(!complete.chartResponseAbs){
			var ctx = $(complete.el).find('.graph-response-abs').get(0).getContext("2d");
				complete.chartResponseAbs = new Chart(ctx, {
					type: 'line',
					data: { labels : [], datasets : [] },
					options: {
						responsive: true,
						maintainAspectRatio: false,
						scales: {
							xAxes: [{
								display: false,
								ticks: {
									fontSize: '11',
									fontColor: '#666',
									reverse: true
								},
								gridLines: {
									color: 'rgba(0, 0, 0, 0.02)'
								}
							}],
							yAxes: [{
								display: false,
								ticks : {
									beginAtZero: false,
									autoSkip: false,
									min: -105,
									max: 105
								}
							}]
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
							callbacks: {
								title : function(tooltip){
									return tooltip[0].xLabel;
								}
							},
							backgroundColor : '#999',
							color : '#FFFFFF'
						}
					}
				});
		}
		
		// Update
		var datasets = [];
		var labels = [];
		var max = 0;
		for(var i in complete.serverData.aspects){
			var aspect = complete.serverData.aspects[i];
			if(!aspect.bucket){ continue; }
			if(labels.length == 0){
				labels = aspect.bucket.abs.labels;
			}
			datasets.push({
				data : aspect.bucket.abs.responses.data,
				label : aspect.title,
				borderColor : aspect.borderColor,
				fill : false,
				backgroundColor : aspect.backgroundColor,
				datasetStrokeWidth : 5
			});
			
			max = Math.max(max, aspect.bucket.abs.responses.max);
		}
		complete.chartResponseAbs.data.labels = labels;
		complete.chartResponseAbs.data.datasets = datasets;
		complete.chartResponseAbs.options.scales.yAxes[0].ticks.min = 0;
		complete.chartResponseAbs.options.scales.yAxes[0].ticks.max = Math.round(max) + 5;
		complete.chartResponseAbs.update();
	};
	
	complete.renderAspectRelGraph = function () {
		if(!complete.chartAspectRel){
			var ctx = $(complete.el).find('.graph-aspect-rel').get(0).getContext("2d");
				complete.chartAspectRel = new Chart(ctx, {
					type: 'line',
					data: { labels : [], datasets : [] },
					options: {
						responsive: true,
						maintainAspectRatio: false,
						scales: {
							xAxes: [{
								display: true,
								ticks: {
									fontSize: '11',
									fontColor: '#666',
									reverse: true
								},
								gridLines: {
									color: 'rgba(0, 0, 0, 0.02)'
								}
							}],
							yAxes: [{
								display: false,
								ticks : {
									beginAtZero: false,
									autoSkip: false,
									min: -105,
									max: 105
								}
							}]
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
							callbacks: {
								title : function(tooltip){
									return tooltip[0].xLabel;
								},
								label : function(tooltip){
									var percent = Math.round(parseFloat(tooltip.yLabel),2);
									var sign = percent == 0 ? '' : percent > 0 ? '+' : '-';
									return ' '+complete.chartAspectRel.legend.legendItems[tooltip.datasetIndex].text+': '+sign+Math.abs(percent)+"%";
								}
							},
							backgroundColor : '#999',
							color : '#FFFFFF'
						}
					}
				});
		}
		
		// Update
		var datasets = [];
		var labels = [];
		var min = 100; var max = -100;
		for(var i in complete.serverData.aspects){
			var aspect = complete.serverData.aspects[i];
			if(!aspect.bucket){ continue; }
			if(labels.length == 0){
				labels = aspect.bucket.rel.labels;
			}
			datasets.push({
				data : aspect.bucket.rel.data,
				label : aspect.title,
				borderColor : aspect.borderColor,
				fill : false,
				backgroundColor : aspect.backgroundColor,
				datasetStrokeWidth : 5
			});
			
			min = Math.min(min, aspect.bucket.rel.min);
			max = Math.max(max, aspect.bucket.rel.max);
		}
		complete.chartAspectRel.data.labels = labels;
		complete.chartAspectRel.data.datasets = datasets;
		complete.chartAspectRel.options.scales.yAxes[0].ticks.min = Math.round(min) - 5;
		complete.chartAspectRel.options.scales.yAxes[0].ticks.max = Math.round(max) + 5;
		complete.chartAspectRel.update();
	};
	
	complete.renderAspectAbsGraph = function () {
		if(!complete.chartAspectAbs){
			var ctx = $(complete.el).find('.graph-aspect-abs').get(0).getContext("2d");
				complete.chartAspectAbs = new Chart(ctx, {
					type: 'line',
					data: { labels : [], datasets : [] },
					options: {
						responsive: true,
						maintainAspectRatio: false,
						scales: {
							xAxes: [{
								display: false,
								ticks: {
									fontSize: '11',
									fontColor: '#666',
									reverse: true
								},
								gridLines: {
									color: 'rgba(0, 0, 0, 0.02)'
								}
							}],
							yAxes: [{
								display: false,
								ticks : {
									beginAtZero: false,
									autoSkip: false,
									min: -105,
									max: 105
								}
							}]
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
							callbacks: {
								title : function(tooltip){
									return tooltip[0].xLabel;
								},
								label : function(tooltip){
									var percent = Math.round(parseFloat(tooltip.yLabel),2);
									return ' '+complete.chartAspectAbs.legend.legendItems[tooltip.datasetIndex].text+': '+Math.abs(percent)+"%";
								}
							},
							backgroundColor : '#999',
							color : '#FFFFFF'
						}
					}
				});
		}
		
		// Update
		var datasets = [];
		var labels = [];
		var min = 100; var max = 0;
		for(var i in complete.serverData.aspects){
			var aspect = complete.serverData.aspects[i];
			if(!aspect.bucket){ continue; }
			if(labels.length == 0){
				labels = aspect.bucket.abs.labels;
			}
			datasets.push({
				data : aspect.bucket.abs.data,
				label : aspect.title,
				borderColor : aspect.borderColor,
				fill : false,
				backgroundColor : aspect.backgroundColor,
				datasetStrokeWidth : 5
			});
			
			min = Math.min(min, aspect.bucket.abs.min);
			max = Math.max(max, aspect.bucket.abs.max);
		}
		complete.chartAspectAbs.data.labels = labels;
		complete.chartAspectAbs.data.datasets = datasets;
		complete.chartAspectAbs.options.scales.yAxes[0].ticks.min = min - 5;
		complete.chartAspectAbs.options.scales.yAxes[0].ticks.max = max + 5;
		complete.chartAspectAbs.update();
	};

	complete.renderAverageLineGraph = function () {
		if(!complete.averageChart){
			var ctx = $(complete.el).find('.average-line').get(0).getContext("2d");
			complete.averageChart = new Chart(ctx, {
					type: 'line',
					data: { labels : [], datasets : [] },
					options: {
						responsive: true,
						maintainAspectRatio: false,
						scales: {
							xAxes: [{
								display: false,
								gridLines: {
									color: 'rgba(0, 0, 0, 0.05)'
								}
							}],
							yAxes: [{
								display: false,
								ticks : {
									beginAtZero: false,
									autoSkip: false,
									min: -105,
									max: 105
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
							mode : 'single',
							callbacks: {
								title : function(tooltip){
									return tooltip[0].xLabel;
								},
								label : function(tooltip){
									var percent = Math.round(parseFloat(tooltip.yLabel),2);
									var sign = percent == 0 ? '' : percent > 0 ? '+' : '-';
									return 'Combined Average: '+sign+Math.abs(percent)+"%";
								}
							},
							backgroundColor : '#999',
							color : '#FFFFFF'
						}
					}
				});
		}
		
		// Update
		complete.averageChart.data.labels = complete.serverData.average.labels || [];
		complete.averageChart.data.datasets = [{
			label: "Average",
			data: complete.serverData.average.bucket || [],
			backgroundColor: complete.colorFillOptions[1] || '#666',
			fill: false
		}];
		complete.averageChart.options.scales.yAxes[0].ticks.min = complete.serverData.average.min - 5;
		complete.averageChart.options.scales.yAxes[0].ticks.max = complete.serverData.average.max + 5;
		complete.averageChart.update();
	}

	complete.renderAverageBarGraph = function () {
		if(!complete.aspectBarGraph){
			var ctx = $(complete.el).find('.average-bar').get(0).getContext("2d");
			complete.aspectBarGraph = new Chart(ctx,{
				type:"bar",
				data: { labels : [], datasets: [] },
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
					},
					tooltips: {
						mode : 'single',
						callbacks: {
							title : function(tooltip){
								return tooltip[0].xLabel;
							},
							label : function(tooltip){
								return tooltip.yLabel+"%";
							}
						}
					}
				}
			});
		}
		
		// Update
		var labels = [], averages = [];
		for(var i in complete.serverData.aspects){
			var aspect = complete.serverData.aspects[i];
			if(!aspect.bucket){ continue; }
			
			labels.push(aspect.title);
			averages.push(aspect.bucket.average);
		}
		
		complete.aspectBarGraph.data.labels = labels;
		complete.aspectBarGraph.data.datasets = [{
			label: "Aspects",
		    backgroundColor: "rgba(220,220,220,0.2)",
		    borderColor: "rgba(220,220,220,1)",
		    borderWidth: 1,
		    hoverBackgroundColor: "rgba(220,220,220,0.2)",
		    hoverBorderColor: "rgba(220,220,220,1)",
		    data: averages
		}];
		
		complete.aspectBarGraph.stop();
		complete.aspectBarGraph.update();
	}

	complete.initEvents = function () {
		$(window).resize(complete.adjustSize);
		$('#slider').bind('valuesChanged', function (e, data) {
			var min = data.values.min,
				max = data.values.max;
			$('.settings .date').html(moment(min).format('MMM Do, YYYY') + ' - ' + moment(max).format('MMM Do, YYYY'));
			
			complete.fromDate = Math.floor(data.values.min.getTime()/1000);
			complete.toDate = Math.ceil(data.values.max.getTime()/1000);
			
			complete.update();
		});
		$(complete.el).on('click', '.aspect', function () {
			complete.toggleAspect(parseInt($(this).attr('data-id')));
		});
		$(complete.el).on('mousedown', '#slider', function () {
			$('.default-options .option').removeClass('selected');
		});
		$(complete.el).on('click', '.graph-button', function () {
			complete.graphFullScreen($(this).parent()); 
			// TODO: Show legend here
		});
		$(complete.el).on('click', '.default-options .option', function () {
			complete.setPresetTimeframe($(this).attr('data-value'), this);
		});
	}

	complete.setPresetTimeframe = function (option, el) {
		if (option === 'custom') {
			$(el).remove();
			$('.custom-options').animate({
				height: '75px'
			});
		}
		else if (option === 'all') {
			var difference = (Math.round(new Date()/1000) - complete.initialMinDate) / (60)
			complete.updateDateSlider(difference);
		} else {
			complete.updateDateSlider(option);
		}
		$('.default-options .option').removeClass('selected');
		$(el).addClass('selected');
	}

	complete.graphFullScreen = function (graph) {
		$(document).on('keyup.escape', function(e){
		    if(e.keyCode === 27)
		        complete.graphExitFullScreen(graph);
		    $(document).unbind('keyup.escape');
		});

		$('<div class="screen-overlay"></div>').appendTo($('body'));
		$(graph).addClass('fullscreen');
		$(graph).css({
			'top': ($(window).height() - $(graph).height())/2 + 'px',
		});
		$(graph).find('.graph-button')
			.html('<i class="fa fa-compress"></i>')
			.on('click', function () {
				complete.graphExitFullScreen(graph);
			});
	}

	complete.graphExitFullScreen = function (graph) {
		$('.screen-overlay').remove();
		$(graph).removeClass('fullscreen');
		$(graph).css({
			top: '0px'
		});
		$(graph).find('.graph-button')
			.html('<i class="fa fa-expand"></i>')
			.on('click', function () {
				complete.graphFullScreen(graph);
			});
	}

	complete.toggleGraph = function (graph) {
		graph = $('#'+graph);
		$(graph).css({
			height: '500px'
		});
	}

	complete.toggleAspect = function (id) {
		var aspectPosition = complete.included.indexOf(id);
		if (aspectPosition > -1) {
			complete.included.splice(aspectPosition, 1);
		} else {
			complete.included.push(id);
		}
		
		complete.update();
	}

	complete.adjustSize = function () {
		var available_space = $(window).height()-260;
		$(complete.el).find('.side-control .aspects').css({ 'max-height': available_space-75});
	}

	canvas.children().not('div.message-container').remove();
	
	complete.el = $('<div>').addClass('complete-page col-md-12').appendTo(canvas);
	// TODO: Automate this
	$('<div class="col-md-8 main">\
			<!--<div class="dashboard-pod timeline">Timeline</div>-->\
			<div class="section">\
				<div class="toolbar">\
					<div class="title">Aspects (% Change)</div>\
					<div class="buttons">\
						<!--<div class="toggle" data-id="graph-1"><i class="fa fa-info"></i></div>-->\
					</div>\
					<div class="clear"></div>\
				</div>\
				<div id="graph-1" class="graph-container">\
					<canvas class="dashboard-pod graph graph-aspect-rel"></canvas>\
					<div class="graph-button"><i class="fa fa-expand"></i></div>\
				</div>\
			</div>\
			<div class="section">\
				<div class="toolbar">\
					<div class="title">Responses</div>\
					<div class="buttons">\
						<!--<div class="toggle" data-id="graph-5"><i class="fa fa-info"></i></div>-->\
					</div>\
					<div class="clear"></div>\
				</div>\
				<div id="graph-5" class="graph-container">\
					<canvas class="dashboard-pod graph graph-response-abs"></canvas>\
					<div class="graph-button"><i class="fa fa-expand"></i></div>\
				</div>\
			</div>\
			<div class="section">\
				<div class="toolbar">\
					<div class="title">Aspects</div>\
					<div class="buttons">\
						<!--<div class="toggle" data-id="graph-2"><i class="fa fa-info"></i></div>-->\
					</div>\
					<div class="clear"></div>\
				</div>\
				<div id="graph-2" class="graph-container">\
					<canvas class="dashboard-pod graph graph-aspect-abs"></canvas>\
					<div class="graph-button"><i class="fa fa-expand"></i></div>\
				</div>\
			</div>\
			<div class="section">\
				<div class="toolbar">\
					<div class="title">Combined Aspect Data</div>\
					<div class="buttons">\
						<!--<div class="toggle" data-id="graph-3"><i class="fa fa-info"></i></div>-->\
					</div>\
					<div class="clear"></div>\
				</div>\
				<div id="graph-3" class="sub-graph-container">\
					<canvas class="dashboard-pod average-line"></canvas>\
					<div class="graph-button"><i class="fa fa-expand"></i></div>\
				</div>\
			</div>\
			<div class="section">\
				<div class="toolbar">\
					<div class="title">Overall Averages Per Aspect</div>\
					<div class="buttons">\
						<!--<div class="toggle" data-id="graph-4"><i class="fa fa-info"></i></div>-->\
					</div>\
					<div class="clear"></div>\
				</div>\
				<div id="graph-4" class="sub-graph-container">\
					<canvas class="dashboard-pod average-bar"></canvas>\
				</div>\
			</div>\
		</div>\
		<div class="col-md-4 side-control">\
			<div class="header">Customize Graph</div>\
			<div class="sub-header">Select the <strong>timeframe</strong> for the graphs.</div>\
			<div class="settings">\
				<div class="default-options">\
					<div class="option" data-value="2">Last 2 Hours</div>\
					<div class="option" data-value="24">Last 24 Hours</div>\
					<div class="option" data-value="72">Last 3 Days</div>\
					<div class="option" data-value="168">Last Week</div>\
					<div class="option" data-value="336">Last 2 Weeks</div>\
					<div class="option" data-value="720">Last Month</div>\
					<div class="option" data-value="1440">Last 2 Months</div>\
					<div class="option selected" data-value="all">All Time</div>\
					<div class="option" data-value="custom">Custom</div>\
					<div class="clear"></div>\
				</div>\
				<div class="custom-options">\
					<div class="date">Text</div>\
					<div id="slider"></div>\
				</div>\
			</div>\
			<div class="sub-header after">Choose which <strong>aspects</strong> are included in the graphs.</div>\
			<div class="aspects"></div>\
		</div>\
	  ').appendTo(complete.el);
	  
	  complete.update = function(){
		  face.datahooks[0].request.data.from = complete.fromDate;
		  face.datahooks[0].request.data.to = complete.toDate;
		  face.datahooks[0].request.data.included = complete.included.join(',');
		  face.startHooks();
	  };
	 
	complete.renderDateSlider();
	complete.initEvents();
	 
	complete.adjustSize();
	complete.render();
	
	face.datahook(0, {
			url : '/api/v1/playground/all',
			data : { 'store' : bdff.storeID() }
		}, function(data){
			if(data.hasOwnProperty('error') && data.error.length > 0){
				bdff.log('Uh oh...');
			} else if(data.playground) {
				if(data.playground.aspects){
					var initInclude = complete.included === false;
					for(var i = 0; i < data.playground.aspects.length; i++){
						// Add or Update
						var remoteAspect = data.playground.aspects[i];
						complete.serverData.aspects[remoteAspect.id] = remoteAspect;
						
						if(initInclude){
							if(!complete.included){ complete.included = []; }
							complete.included.push(remoteAspect.id);
						}
					}
				}
				complete.serverData.average = data.playground.average;
				complete.initialMinDate = data.playground.minDate;
				complete.minDate = data.playground.minDate;
				
				complete.render();
			}
		}
	);
		
}, function(){
	if(complete){
		if(complete.dateSlider){ try { complete.dateSlider.dateRangeSlider("destroy"); complete.dateSlider = undefined; } catch (ex){} }
		if(complete.chartAspectRel){ try { complete.chartAspectRel.destroy(); complete.chartAspectRel = undefined; } catch (ex){} }
		if(complete.chartAspectAbs){ try { complete.chartAspectAbs.destroy(); complete.chartAspectAbs = undefined; } catch (ex){} }
		if(complete.averageChart){ try { complete.averageChart.destroy(); complete.averageChart = undefined; } catch (ex){} }
		if(complete.aspectBarGraph){ try { complete.aspectBarGraph.destroy(); complete.aspectBarGraph = undefined; } catch (ex){} }
		complete = {};
	}
});