/* 
	Live Dashboard App
*/
var live = { };

bdff.create('live', function(canvas, face){

	canvas.children().not('div.message-container').remove();
	if(live && live.snapshot){
		live.snapshot.cleanup();
	}
	
	live = {};

	var render = function (canvas) {
		canvas.children().not('div.message-container').remove();
		
		var left = $('<div>').addClass('col-xs-12 col-md-9').appendTo(canvas);
		var right = $('<div>').addClass('col-xs-12 col-md-3').appendTo(canvas);
		
		renderSnapshot(left);
		renderPastScores(right);
		renderResponseFeed(left);
		
		canvas.append(
			$('<div>').addClass('full-loader').append(
				$('<div>').addClass('fa fa-spin fa-gear')
			)
		);
	};
	
	var renderSnapshot = function(canvas){
		var snapshot = $("<div>").addClass('snapshot col-xs-12').appendTo(canvas);
		snapshot
			.append($('<span>').addClass('header').text('Snapshot'))
			.append($('<span>').addClass('subtitle').text("Here's an overview of where you stand."));
		
		var createPod = function(type){
			var pod = $("<div>").addClass('snapshot-pod col-xs-12 col-md-4 type-' + type).appendTo(snapshot);
			var stats = $("<div>").addClass('snapshot-stats').appendTo(pod);
			var graph = $("<div>").addClass('snapshot-graph').append("<canvas>").appendTo(pod);
			var bestWorst = $("<div>").addClass('snapshot-best-worst').appendTo(pod);
			
			pod.append(
				$('<span>').addClass('snapshot-label')
				.text(type == 'day' ? 'Last 24 Hours' : (type == 'week' ? 'Last Week' : 'All Time'))
			);
			
			bestWorst
				.append($('<span>').append($('<i>').addClass('fa fa-thumbs-up')).append($('<span>').text("Food Quality, 3 more")))
				.append($('<span>').append($('<i>').addClass('fa fa-thumbs-down')).append($('<span>').text("Pricing, 3 more")));
				
			stats.append($('<div>').addClass('average').append($('<span>').addClass('number').text('50%')).append($('<span>').addClass('label').text('Average')));
			
			
			stats.append($('<div>').addClass('change').append($('<span>').addClass('number').text('+76%')).append($('<span>').addClass('label').text('Change')));
			
			stats.append($('<div>').addClass('responses').append($('<span>').addClass('number').text('2543')).append($('<span>').addClass('label').text('Responses')));
			
			if(type == 'all'){
				stats.find('div.change').css('visibility', 'hidden'); //Easier to hide then not add
				stats.find('div.change').insertAfter(stats.find('div.responses'));
			}
			
			var registerGraph = function(el){
				var ctx = el.children('canvas').get(0).getContext("2d");
				return new Chart(ctx, {
						type: 'doughnut',
						data: { labels : ['Positive', 'Great', 'Neutral', 'Bad', 'Negative'], datasets : [{
							data: [0, 0, 0, 0, 0],
							backgroundColor: [
								"#38cf4a",
								"#7ccf38",
								"#bbcf38",
								"#cfc338",
								"#cf9b38"
							],
							hoverBackgroundColor: [
								"#38cf4a",
								"#7ccf38",
								"#bbcf38",
								"#cfc338",
								"#cf9b38"
							]
						}] },
						options: {
							responsive: true,
							maintainAspectRatio: false,
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
									title : function(tooltip, data){
										return data.labels[tooltip[0].index];
									},
									label : function(tooltip, data){
										return data.datasets[0].data[tooltip.index] + ' responses';
									}
								},
								backgroundColor : '#999',
								color : '#FFFFFF'
							}
						}
				});
			};
			
			var obj = {
				setUp : function(aspects) {
					var row = bestWorst.find('i.fa-thumbs-up').parent();
					if(row.is(':visible') && aspects.length == 0){
						row.slideUp();
					}
					
					if(aspects.length > 0){
						row.find('span').text(aspects[0] + (aspects.length > 1 ? ", "+(aspects.length-1)+" more" : ''));
						if(!row.is(':visible')){
							row.slideDown();
						}
					} else {
						row.find('span').text('');
					}
				},
				setDown : function(aspects) {
					var row = bestWorst.find('i.fa-thumbs-down').parent();
					if(row.is(':visible') && aspects.length == 0){
						row.slideUp();
					}
					
					if(aspects.length > 0){
						row.find('span').text(aspects[0] + (aspects.length > 1 ? ", "+(aspects.length-1)+" more" : ''));
						if(!row.is(':visible')){
							row.slideDown();
						}
					} else {
						row.find('span').text('');
					}
				},
				setAverage : function(avg) {
					stats.find('div.average').children('span.number').removeClass('positive great neutral bad negative').addClass(bdff.mood(avg)).text(avg+'%');
				},
				setChange : function(change) {
					var sign = change == 0 ? '' : change > 0 ? '+' : '-';
					stats.find('div.change').children('span.number').removeClass('positive great neutral bad negative').addClass(bdff.mood((parseFloat(Math.abs(change))+100.0)/2)).text(sign+Math.abs(change)+'%');
				},
				setResponses : function(resp) {
					var magnitude = Math.floor(Math.log10(parseInt(resp)))+1;
					stats.find('div.responses').children('span.number').addClass('mag-'+magnitude).text(resp);
				},
				graph: undefined
			};
			
			obj.setGraph = function(data) {
				if(!obj.graph){
					obj.graph = registerGraph(graph);
				}
				
				// update graoh , update/render
				obj.graph.data.datasets[0].data = data.data;
				obj.graph.update();
			};
			
			return obj;
		};
		
		live.snapshot = {
			day : createPod('day'),
			week : createPod('week'),
			all : createPod('all'),
			cleanup : function(){
				if(live.snapshot.day && live.snapshot.day.graph){
					live.snapshot.day.graph.destroy();
				}
				if(live.snapshot.week && live.snapshot.week.graph){
					live.snapshot.week.graph.destroy();
				}
				if(live.snapshot.all && live.snapshot.all.graph){
					live.snapshot.all.graph.destroy();
				}
			}
		};
		
	};
	
	var renderResponseFeed = function(canvas){
		var feed = $("<div>").addClass('feed col-xs-12').appendTo(canvas);
		feed
			.append($('<span>').addClass('header').text('Live Response Feed'))
			.append($('<span>').addClass('subtitle').text("Responses will appear as they come in."));
			
		var feedList = $('<div>').addClass('feed-list').appendTo(feed);
		
		live.feed = { };
		live.feed.add = function(data){
			if(data.percent && data.aspect && data.date && data.medium){
				var feedItem = $('<div>').addClass('feed-item').hide();
				feedItem.append($('<span>').addClass('number').addClass(bdff.mood(data.percent)).text(Math.round(data.percent) + '%'));
				feedItem.append($('<span>').addClass('feed-label').text(data.aspect));
				feedItem.append($('<span>').addClass('medium').addClass('medium-' + data.medium));
				feedItem.append($('<span>').addClass('date').text(data.date));
				feedItem.prependTo(feedList).slideDown(100, function(){
					feedList.children('div.feed-item:gt(4)').slideUp(200, function(){
						$(this).remove();
					});
				});
			}
		};
		
	};
	
	var renderPastScores = function(canvas){
		var weeksScores = $("<div>").addClass('weeks-scores col-xs-12').appendTo(canvas);
		weeksScores
			.append($('<span>').addClass('header').text('This Week\'s Scores'))
			.append($('<span>').addClass('subtitle').text("Scores from the past 7 days. For more details, use the tabs on the left."));
			
		var aspectList = $('<div>').addClass('scores-list').appendTo(weeksScores);
		
		live.scores = { aspects: {} };
		live.scores.update = function(aspectLabel, percent, id){
			var aspect;
			
			if(live.scores.aspects.hasOwnProperty(id) && aspectList.children('div[data-id='+id+']').length > 0){
				// Update
				aspect = aspectList.children('div[data-id='+id+']');
				aspect.find('span.scores-label').text(aspectLabel);
				aspect.find('span.scores-percent').text(percent + '%');
			} else {
				aspect = $('<div>').attr('data-id', id).addClass('scores-item').hide().appendTo(aspectList);
				aspect.append(
					$('<div>').addClass('score-bar').attr('data-percent', percent)
						.append($('<div>')).append($('<span>').addClass('scores-percent').text(percent + '%'))
				);
				aspect.append($('<span>').addClass('scores-label').text(aspectLabel));
				aspect.find('div.score-bar > div').width(10);
			}
			
			var targetWidth = percent;
			aspect.slideDown(function(){
				$(this).find('div.score-bar > div').animate({
					width: targetWidth+'%'
				}, 1000);
			});
			
			live.scores.aspects[id] = {id: id, percent: percent, label: aspectLabel};
		};
	};
	
	render(canvas);
	
	face.datahook(10000, {
			url : '/api/v1/live/all',
			data : { 'store' : bdff.storeID(), 'latest': 0 }
		}, function(data){
		if(data.hasOwnProperty('error') && data.error.length > 0){
			bdff.log('Uh oh...');
		} else if(data.hasOwnProperty('live')) {
			
			var processData = function(data){
				if(data.snapshot){
					live.snapshot.day.setUp(data.snapshot.day.up);
					live.snapshot.day.setDown(data.snapshot.day.down);
					live.snapshot.day.setAverage(data.snapshot.day.average);
					live.snapshot.day.setChange(data.snapshot.day.change);
					live.snapshot.day.setResponses(data.snapshot.day.responses);
					live.snapshot.day.setGraph(data.snapshot.day.bucket);
					
					live.snapshot.week.setUp(data.snapshot.week.up);
					live.snapshot.week.setDown(data.snapshot.week.down);
					live.snapshot.week.setAverage(data.snapshot.week.average);
					live.snapshot.week.setChange(data.snapshot.week.change);
					live.snapshot.week.setResponses(data.snapshot.week.responses);
					live.snapshot.week.setGraph(data.snapshot.week.bucket);
					
					live.snapshot.all.setUp(data.snapshot.all.up);
					live.snapshot.all.setDown(data.snapshot.all.down);
					live.snapshot.all.setAverage(data.snapshot.all.average);
					live.snapshot.all.setResponses(data.snapshot.all.responses);
					live.snapshot.all.setGraph(data.snapshot.all.bucket);
				}
				
				if(data.feed){
					for(var i = 0; i < data.feed.length; i++){
						face.datahooks[0].request.data.latest = Math.max(face.datahooks[0].request.data.latest, data.feed[i].id);
						live.feed.add(data.feed[i]);
					}
				}				
				
				if(data.scores){
					for(var i = 0; i < data.scores.length; i++){
						live.scores.update(data.scores[i].title, data.scores[i].percent, data.scores[i].id);
					}
				}
				
				$('div[data-tooltip]').brevadaTooltip();
			};
			
			if(canvas.find('.full-loader').length > 0){
				canvas.find('.full-loader').fadeOut(10, function(){
					processData(data.live);
					$(this).remove();
				});
			} else {
				processData(data.live);
			}
		} else {
			bdff.log('Uh oh...');
		}
	});
	
});
