$(document).ready(function(){
	$('body').on('change', '.graph-toggle', function() {
		$graphs = $(this).parent().siblings('.graphs');
	    if( $(this).is(':checked')) {
	    	$graphs.find('.line-graph').css({'visibility': 'visible'});
	        $graphs.find('.bar-graph').hide();
	    } else {
	        $graphs.find('.bar-graph').show();
	        $graphs.find('.line-graph').css({'visibility': 'hidden'});
	    }
	}); 
});

Chart.Scale.prototype.buildYLabels = function () {
  this.yLabelWidth = 0;
};

Chart.defaults.global.maintainAspectRatio = false;

Chart.defaults.global.pointHitDetectionRadius = 3;
Chart.defaults.global.tooltips.enabled = true;
Chart.defaults.global.tooltips.custom = function(tooltip){	
	$(document).mouseover();
	
	var el = $('#chartjs-tooltip');
	if (el.length == 0){
		el = $("<div id='chartjs-tooltip'></div>").appendTo($('body'));
	}
	
	if(!tooltip || !tooltip.opacity){
		el.brevadaTooltip('hide', {
			className: 'chart-tooltip',
			bind: false,
			keepalive: true,
			one: true
		});
		return;
	}
	
	var content = tooltip.title && tooltip.title.length > 0 ? "<span>" + tooltip.title[0] + "</span>" : '';
	for(var i = 0; i < tooltip.body.length; i++){
		var colour = '';
		if(tooltip.labelColors && i < tooltip.labelColors.length){
			var col = tooltip.labelColors[i];
			if(col.borderColor){
				colour = "<div class='label-colour' style='background-color:"+col.borderColor+"'></div>";
			}
		}
		
		content += '<span>' + colour + tooltip.body[i] + '</span>';
	}
	
	el.brevadaTooltip('show', {
		className: 'chart-tooltip',
		x: 'mouse',
		y: 'mouse',
		content: content,
		bind: false,
		keepalive: true,
		one: true
	});
	
};



function build_line_graph(bucket, id) {
	$pod = $('#' + id);
	var graph_color = 'rgb(242, 96, 106)',
		data = {
		    labels: bucket.dates,
		    datasets: [
		        {
		            label: "Aspect",
					fill: true,
					backgroundColor: graph_color,
		            borderColor: graph_color,
		            pointBackgroundColor: graph_color,
		            pointBorderColor: "#FFFFFF",
		            pointHoverBackgroundColor: "#FFFFFF",
		            pointHoverBorderColor: graph_color,
					borderWidth: 0.5,
					tension: 0.3,
		            data: bucket.data
		        }
		    ]
		};
	var ctx = $pod.find('.line-graph canvas').get(0).getContext("2d");
	return new Chart(ctx, {
		type: 'line',
		'data': data,
		'options': {
			legend: {
				display: false
			},
			scales: {
				xAxes: [{
					display: false
				}],
				yAxes: [{
					display: false,
					ticks : {
						beginAtZero: true,
						autoSkip: false,
						min: 0,
						max: 100
					}
				}]
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
				},
				enabled: false
			}
		}
	});
}

