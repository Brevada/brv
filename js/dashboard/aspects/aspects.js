/* Overall Aspects Dashboard App */

bdff.create('aspects', function(canvas, face){
	canvas.children().not('div.message-container').remove();
	
	canvas.append(
		$('<div>').addClass('full-loader').append(
			$('<div>').addClass('fa fa-spin fa-gear')
		)
	);
	
	var aspects = {};
	
	var renderAspect = function(id){
		
		var aspect = { 'id' : id };
		
		var aspectDom = $('<div>').addClass('col-sm-6 col-md-4 col-lg-3 pod-holder').append(
			$('<div>').addClass('pod').attr('id', 'pod'+id).append(
				$('<div>').addClass('body')
				.append(
					$('<div>').addClass('header').append($('<span>').addClass('aspect-title').text(''))
				)
				.append(
					$('<div>').addClass('pull-left col-md-6 pod-body-left')
					.append(
						$('<div>').addClass('top').append(
							$('<i>').addClass('pull-left fa fa-arrow-circle-up')
						).append(
							$('<span>').addClass('pull-left percent').text('')
						).append(
							$('<span>').addClass('duration').text('24H')
						)
					).append(
						$('<div>').addClass('top').append(
							$('<i>').addClass('pull-left fa fa-arrow-circle-down')
						).append(
							$('<span>').addClass('pull-left percent').text('')
						).append(
							$('<span>').addClass('duration').text('4W')
						)		
					)
				)
				.append(
					$('<div>').addClass('pull-right col-md-6 pod-body-right').append(
						$('<div>').addClass('pod-body-rating positive-text').text('')
					).append(
						$('<div>').addClass('rating-text').text('in 2 responses.')
					).append(
						$('<div>').addClass('pod-body-rating external').text('')
					).append(
						$('<div>').addClass('rating-text external').text('industry average')
					)
				)
				.append(
					$('<div>').addClass('col-md-12 pod-body-bottom')
					// .append(
						// $('<input>').addClass('graph-toggle').attr('type', 'checkbox').attr('checked', '')
						// .attr('data-toggle', 'toggle').attr('data-onstyle', 'default').attr('data-on', 'Line')
						// .attr('data-off', 'Bar').attr('data-size', 'mini').attr('data-width', '100').attr('data-height', '25'))
					.append(
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
								$('<div>').addClass('right-graph graph').data('percent', '0').data('tooltip', 'Market Benchmark (0)')
							)
						)
						.append(
							$('<div>').addClass('line-graph').attr('data-id', id).append(
								$('<canvas>')
							).append(
								$('<div>').addClass('no-data').append(
									$('<i>').addClass('fa fa-line-chart')
								).append(
									$('<span>').text('Insufficient Data')
								).attr({ 'data-tooltip': "There's not enough data<br/>to make a meaningful graph." })
							)
						)
					)
				)
			)
		);
		
		aspectDom.hide().appendTo(canvas).fadeIn();
		
		aspectDom.find('input.graph-toggle').bootstrapToggle();
		
		aspect.setTitle = function(val){
			aspectDom.find('span.aspect-title').text(val);
		};
		
		aspect.setRating = function(val){
			val = Math.round(val * 10)/10;
			
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
			val = Math.round(val * 10) / 10;
			
			aspectDom.find('div.pod-body-rating.external').text(val + "%");
			
			aspectDom.find('div.right-graph').attr('data-percent', val)
				.attr('data-tooltip', 'Market Benchmark ('+val+'%)')
				.children('div.percent').text(val+"%");
				
			aspect.animateBarGraphs();
		};
		
		aspect.setNumResponses = function(num){
			aspectDom.find('div.rating-text').first().text(num.toString() + " responses.");
		};
		
		aspect.setTopTicker = function(val){
			if(val && val.toString().length > 4){
				val = Math.round(val * 10) / 10;
			}
			
			aspectDom.find('div.top > span.percent').first().text(val == null ? 'N/A' : val+"%");
			aspectDom.find('div.top > i').first()
				.removeClass('fa-arrow-circle-up fa-arrow-circle-down fa-minus-circle')
				.addClass(bdff.tickerIcon(val));
		};
		
		aspect.setBottomTicker = function(val){
			if(val && val.toString().length > 4){
				val = Math.round(val * 10) / 10;
			}
			
			aspectDom.find('div.top > span.percent').last().text(val == null ? 'N/A' : val+"%");
			aspectDom.find('div.top > i').last()
				.removeClass('fa-arrow-circle-up fa-arrow-circle-down fa-minus-circle')
				.addClass(bdff.tickerIcon(val));
		};
		
		aspect.animateBarGraphs = function(){
			aspectDom.find('.graph').each(function(){
				var percent = $(this).attr('data-percent');
				var original = $(this).children('div.percent').outerHeight();
				var target = (parseFloat(percent)/100)*($(this).parent().height() - original);
				$(this).stop().animate({ height : Math.min(Math.floor(original+target), $(this).parent().height()) }, 1500);
			});
		};
		
		aspect.remove = function(){
			aspectDom.remove();
		};
	
		return aspect;
	};
	
	face.datahook(15000, {
			url : '/api/v1/aspects/list',
			data : { 'store' : bdff.storeID() }
		}, function(data){
		if(data.hasOwnProperty('error') && data.error.length > 0){
			bdff.log('Uh oh...');
		} else if(data.hasOwnProperty('aspects')) {
			var processData = function(data){
				var ids = [];
				for(var i = 0; i < data.aspects.length; i++){
					var aspect;
					if(aspects.hasOwnProperty(data.aspects[i].id)){
						aspect = aspects[data.aspects[i].id];
					} else {
						aspect = renderAspect(data.aspects[i].id);
						aspects[data.aspects[i].id] = aspect;
					}
					
					ids.push(data.aspects[i].id);
					
					aspect.setTitle(data.aspects[i].title);
					aspect.setRating(data.aspects[i].rating);
					aspect.setIndustryRating(data.aspects[i].industry);
					aspect.setNumResponses(data.aspects[i]['size']);
					aspect.setTopTicker(data.aspects[i].change.day);
					aspect.setBottomTicker(data.aspects[i].change.month);
					
					if(!aspect.data){ aspect.data = {}; }
					
					if(!aspect.data.data || !bdff.equal(aspect.data.labels, data.aspects[i].bucket.labels) || !bdff.equal(aspect.data.data, data.aspects[i].bucket.data)){
						aspect.data.labels = data.aspects[i].bucket.labels;
						aspect.data.data = data.aspects[i].bucket.data;
						if(!aspect.lineGraph){
							aspect.lineGraph = build_line_graph({"dates": aspect.data.labels, "data": aspect.data.data }, 'pod'+data.aspects[i].id);
						}
						
						aspect.lineGraph.data.labels = aspect.data.labels;
						aspect.lineGraph.data.datasets[0].data = aspect.data.data;
						
						// Adds padding for label.
						var padding = 0.23 * (data.aspects[i].bucket.max - data.aspects[i].bucket.min);
						aspect.lineGraph.options.scales.yAxes[0].ticks.min = data.aspects[i].bucket.min - padding;
						aspect.lineGraph.options.scales.yAxes[0].ticks.max = data.aspects[i].bucket.max;
						aspect.lineGraph.update();
						
						var domGraph = $('#pod'+data.aspects[i].id).find('div.line-graph');
						if(aspect.data.data.length == 1){
							domGraph.addClass('no-data');
						} else {
							domGraph.removeClass('no-data');
						}
					}
					
					$('[data-tooltip]').each(function(){
						$(this).brevadaTooltip();
					});
				}
				/* Check deletes. */
				var keys = Object.keys(aspects);
				for(var i = 0; i < keys.length; i++){
					var key = parseInt(keys[i]);
					if($.inArray(key, ids) < 0){
						aspects[key].remove();
						delete aspects[key];
					}
				}
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