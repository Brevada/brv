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

function build_line_graph(bucket, id) {
	$pod = $('#' + id);
	
	var data = {
	    labels: bucket.dates,
	    datasets: [
	        {
	            label: "Aspect",
				fill: true,
				backgroundColor: "rgba(21,187,75,0.7)",
	            borderColor: "rgba(21,187,75,0.7)",
	            pointBackgroundColor: "rgba(151,187,205,1)",
	            pointBorderColor: "#FFFFFF",
	            pointHoverBackgroundColor: "#FFFFFF",
	            pointHoverBorderColor: "rgba(151,187,205,1)",
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
				backgroundColor : '#999',
				color : '#FFFFFF'
			}
		}
	});
}

