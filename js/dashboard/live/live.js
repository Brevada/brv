/* 
	Live Dashboard App
*/

bdff.create('live', function(canvas, face){

	canvas.children().not('div.message-container').remove();
	
	var live = { };

	var render = function (canvas) {
		canvas.children().not('div.message-container').remove();
		
		renderSnapshot(canvas);
		renderPastScores(canvas);
		renderResponseFeed(canvas);
		
		canvas.append(
			$('<div>').addClass('full-loader').append(
				$('<div>').addClass('fa fa-spin fa-gear')
			)
		);
	};
	
	var renderSnapshot = function(canvas){
		var snapshot = $("<div>").addClass('snapshot col-xs-12 col-md-9').appendTo(canvas);
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
							data: [30, 20, 40, 23, 8],
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
			
			return {
				graph : registerGraph(graph)
			};
		};
		
		live.snapshot = {
			day : createPod('day'),
			week : createPod('week'),
			all : createPod('all')
		};
	};
	
	var renderResponseFeed = function(canvas){
		var feed = $("<div>").addClass('feed col-xs-12 col-md-9').appendTo(canvas);
		feed
			.append($('<span>').addClass('header').text('Live Response Feed'))
			.append($('<span>').addClass('subtitle').text("Responses will appear as they come in."));
			
		var feedList = $('<div>').addClass('feed-list').appendTo(feed);
		
		live.feed = {};
		live.feed.add = function(data){
			if(data.percent && data.aspect && data.date && data.medium){
				if(feedList.children('div.feed-item').length > 5){
					feedList.children('div.feed-item').last().slideUp(100, function(){
						$(this).remove();
					});
				}
				
				var feedItem = $('<div>').addClass('feed-item').hide();
				feedItem.append($('<span>').addClass('number').text(Math.round(data.percent) + '%'));
				feedItem.append($('<span>').addClass('feed-label').text(data.aspect));
				feedItem.append($('<span>').addClass('medium').addClass('medium-' + data.medium));
				feedItem.append($('<span>').addClass('date').text(data.date));
				feedItem.prependTo(feedList).slideDown();
			}
		};
		
		live.feed.add({ percent: '60', aspect: 'Customer Service', date: 'March 17th, 12:25PM', medium: 'tablet' });
		live.feed.add({ percent: '80', aspect: 'Pricing', date: 'March 17th, 12:22PM', medium: 'desktop' });
		live.feed.add({ percent: '20', aspect: 'Customer Service', date: 'March 17th, 12:20PM', medium: 'desktop' });
		live.feed.add({ percent: '40', aspect: 'Food Quality', date: 'March 17th, 12:18PM', medium: 'tablet' });
		live.feed.add({ percent: '60', aspect: 'Taste', date: 'March 17th, 12:13PM', medium: 'desktop' });
		
	};
	
	var renderPastScores = function(canvas){
		var weeksScores = $("<div>").addClass('weeks-scores col-xs-12 col-md-3').appendTo(canvas);
		weeksScores
			.append($('<span>').addClass('header').text('Past Week\'s Scores'))
			.append($('<span>').addClass('subtitle').text("Scores from the past X days. For more details, use the tabs on the left."));
			
		var aspectList = $('<div>').addClass('scores-list').appendTo(weeksScores);
		
		live.past = {};
		live.past.update = function(aspectLabel, percent){
			var aspect = $('<div>').addClass('scores-item').hide().appendTo(aspectList);
			aspect.append(
				$('<div>').addClass('score-bar').attr('data-percent', percent)
					.append($('<div>')).append($('<span>').text(percent+'%'))
			);
			aspect.append($('<span>').text(aspectLabel));
			var targetWidth = percent;
			aspect.find('div.score-bar > div').width(10);
			aspect.slideDown(function(){
				$(this).find('div.score-bar > div').animate({
					width: targetWidth+'%'
				}, 1000);
			});
		};
		
		live.past.update('Customer Service', 70);
		live.past.update('Pricing', 98);
		live.past.update('Taste', 73);
		live.past.update('Parking', 82);
		live.past.update('Ambience', 40);
	};
	
	render(canvas);
	
	face.datahook(10000, {
			url : '/api/v1/bdff/live',
			data : { 'store' : bdff.storeID(), 'hours' : 10 }
		}, function(data){
		if(data.hasOwnProperty('error') && data.error.length > 0){
			bdff.log('Uh oh...');
		} else if(data.hasOwnProperty('live')) {
			
			var processData = function(data){
				
				$('div[data-tooltip]').brevadaTooltip();
			};
			
			if(canvas.find('.full-loader').length > 0){
				canvas.find('.full-loader').fadeOut(10, function(){
					processData(data);
					$(this).remove();
				});
			} else {
				processData(data);
			}
		} else {
			bdff.log('Uh oh...');
		}
	});
	
});
