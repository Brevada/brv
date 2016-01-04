/* Overall Aspects Dashboard App */

bdff.create('aspects', function(canvas, face){
	canvas.empty();
	
	var aspects = {};
	
	var renderAspect = function(id){
		
		var aspect = { 'id' : id };
		
		var aspectDom = $('<div>').addClass('col-sm-6 col-md-4 col-lg-3 pod-holder').append(
			$('<div>').addClass('pod').attr('id', 'pod'+id).append(
				$('<div>').addClass('body')
				.append(
					$('<div>').addClass('header').append($('<span>').addClass('aspect-title').text('Ambience'))
				)
				.append(
					$('<div>').addClass('pull-left col-md-6 pod-body-left')
					.append(
						$('<div>').addClass('top').append(
							$('<i>').addClass('pull-left fa fa-arrow-circle-up')
						).append(
							$('<span>').addClass('pull-left percent').text('32%')
						).append(
							$('<span>').addClass('duration').text('24H')
						)
					).append(
						$('<div>').addClass('top').append(
							$('<i>').addClass('pull-left fa fa-arrow-circle-down')
						).append(
							$('<span>').addClass('pull-left percent').text('26%')
						).append(
							$('<span>').addClass('duration').text('4W')
						)		
					)
				)
				.append(
					$('<div>').addClass('pull-right col-md-6 pod-body-right').append(
						$('<div>').addClass('pod-body-rating positive-text').text('80%')
					).append(
						$('<div>').addClass('rating-text').text('in 2 responses.')
					).append(
						$('<div>').addClass('pod-body-rating external').text('80%')
					).append(
						$('<div>').addClass('rating-text external').text('industry average')
					)
				)
				.append(
					$('<div>').addClass('col-md-12 pod-body-bottom').append(
						$('<input>').addClass('graph-toggle').attr('type', 'checkbox').attr('checked', '')
						.attr('data-toggle', 'toggle').attr('data-onstyle', 'default').attr('data-on', 'Line')
						.attr('data-off', 'Bar').attr('data-size', 'mini').attr('data-width', '100').attr('data-height', '25')
					).append(
						$('<div>').addClass('graphs')
						.append(
							$('<div>').addClass('bar-graph')
							.append(
								$('<div>').addClass('left-graph graph positive').data('percent', '80')
								.append(
									$('<div>').addClass('percent').text('80%')
								)
							)
							.append(
								$('<div>').addClass('right-graph graph').data('percent', '80').data('tooltip', 'Market Benchmark (80%)')
							)
						)
						.append(
							$('<div>').addClass('line-graph').attr('data-id', id).append('<canvas>')
						)
					)
				)
			)
		);
		
		aspectDom.appendTo(canvas);
		
		aspectDom.find('input.graph-toggle').bootstrapToggle();
		
		aspect.setTitle = function(val){
			aspectDom.find('span.aspect-title').text(val);
		};
		
		aspect.setRating = function(val){
			aspectDom.find('div.pod-body-rating').first()
				.removeClass('positive-text great-text neutral-text bad-text negative-text')
				.addClass(bdff.mood(parseFloat(val))+'-text')
				.text(val + "%");
				
			aspectDom.find('div.left-graph').attr('data-percent', val)
				.removeClass('positive great neutral bad negative')
				.addClass(bdff.mood(val))
				.children('div.percent').text(val+"%");
				
			aspect.animateBarGraphs();
		};
		
		aspect.setIndustryRating = function(val){
			aspectDom.find('div.pod-body-rating.external').text(val + "%");
			
			aspectDom.find('div.right-graph').attr('data-percent', val)
				.attr('data-tooltip', 'Market Benchmark ('+val+'%)')
				.children('div.percent').text(val+"%");
				
			aspect.animateBarGraphs();
		};
		
		aspect.setNumResponses = function(num){
			aspectDom.find('div.rating-text').first().text("in " + num.toString() + " responses.");
		};
		
		aspect.setTopTicker = function(val){
			aspectDom.find('div.top > span.percent').first().text(val+"%");
			aspectDom.find('div.top > i').first()
				.removeClass('fa-arrow-circle-up fa-arrow-circle-down fa-minus-circle')
				.addClass(bdff.tickerIcon(val));
		};
		
		aspect.setBottomTicker = function(val){
			aspectDom.find('div.top > span.percent').last().text(val+"%");
			aspectDom.find('div.top > i').last()
				.removeClass('fa-arrow-circle-up fa-arrow-circle-down fa-minus-circle')
				.addClass(bdff.tickerIcon(val));
		};
		
		aspect.animateBarGraphs = function(){
			aspectDom.find('.graph').each(function(){
				var percent = $(this).attr('data-percent');
				var original = $(this).height();
				var target = (parseFloat(percent)/100)*($(this).parent().height() - original);
				$(this).animate({ height : Math.min(Math.floor(original+target), $(this).parent().height()) }, 1500);
			});
		};
		
		aspect.lineGraph = build_line_graph({"dates":[],"data":[]}, 'pod'+id);
	
		return aspect;
	};
	
	face.datahook(0, {
			url : '/api/v1/bdff/aspects',
			data : { 'store' : bdff.storeID() }
		}, function(data){
		if(data.hasOwnProperty('error') && data.error.length > 0){
			bdff.log('Uh oh...');
		} else if(data.hasOwnProperty('aspects')) {
			for(var i = 0; i < data.aspects.length; i++){
				var aspect;
				if(aspects.hasOwnProperty(data.aspects[i].id)){
					aspect = aspects[data.aspects[i].id];
				} else {
					aspect = renderAspect(data.aspects[i].id);
				}
				
				aspect.setTitle(data.aspects[i].title);
				aspect.setRating(data.aspects[i].rating);
				aspect.setIndustryRating(data.aspects[i].industry);
				aspect.setNumResponses(data.aspects[i]['size']);
				aspect.setTopTicker(data.aspects[i].change.day);
				aspect.setBottomTicker(data.aspects[i].change.month);
				
				aspect.lineGraph = build_line_graph({"dates": data.aspects[i].bucket.labels, "data": data.aspects[i].bucket.data }, 'pod'+data.aspects[i].id);
			}
		} else {
			bdff.log('Uh oh...');
		}
	});	

});