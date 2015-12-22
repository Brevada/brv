$(document).ready(function(){
	$('.graph-toggle').change(function() {
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

function build_line_graph(bucket, id) {
	$pod = $('#' + id);
	$pod.find('.line-graph').html('<canvas></canvas>');
	var data = {
	    labels: bucket.dates,
	    datasets: [
	        /*{
	            label: "Industry",
	            fillColor: "rgba(220,220,220,0.6)",
	            strokeColor: "rgba(220,220,220,1)",
	            pointColor: "rgba(220,220,220,1)",
	            pointStrokeColor: "#FFFFFF",
	            pointHighlightFill: "#FFFFFF",
	            pointHighlightStroke: "rgba(220,220,220,1)",
	            data: bucket.data
	        },*/
	        {
	            label: "Aspect",
	            fillColor: "rgba(21,187,75,0.7)",
	            strokeColor: "rgba(151,187,205,1)",
	            pointColor: "rgba(151,187,205,1)",
	            pointStrokeColor: "#FFFFFF",
	            pointHighlightFill: "#FFFFFF",
	            pointHighlightStroke: "rgba(151,187,205,1)",
	            data: bucket.data
	        }
	    ]
	};
	var options = {
		scaleShowGridLines : true,
		showScale : false,
	    bezierCurve : true,
	    bezierCurveTension : 0.8,
	    pointDot : true,
	    pointDotRadius : 5,
	    pointDotStrokeWidth : 1,
	    pointHitDetectionRadius : 20,
	    datasetStrokeWidth : 2,
		scaleBeginAtZero: true,
	    datasetFill : true,
		responsive : true,
		showTooltips: true,
		scaleOverride : true,
        scaleSteps : 100,
        scaleStepWidth : 1,
        scaleStartValue : 0,
		tooltipTemplate: "<%if (label){%><%= '<span class=\"tooltip-label\">' + label + '</span>' %> <%}%><%= '<span class=\"tooltip-value\">' + value + '%</span>' %>",
		customTooltips: function(tooltip) {
			// Tooltip Element
			var tooltipEl = $('#chartjs-customtooltip');

			// Make the element if not available
			if (!tooltipEl[0]) {
				$('body').append('<div id="chartjs-customtooltip"></div>');
				tooltipEl = $('#chartjs-customtooltip');
			}

			// Hide if no tooltip
			if (!tooltip) {
				tooltipEl.css({
					opacity: 0
				});
				return;
			}

			// Set caret Position
			tooltipEl.removeClass('above below no-transform');
			if (tooltip.yAlign) {
				tooltipEl.addClass(tooltip.yAlign);
			} else {
				tooltipEl.addClass('no-transform');
			}

			// Set Text
			if (tooltip.text) {
				tooltipEl.html(tooltip.text);
			}

			// Find Y Location on page
			var top = 0;
			if (tooltip.yAlign) {
				top = tooltip.y - tooltip.caretHeight - tooltip.caretPadding;
			}

			var offset = $(tooltip.chart.canvas).offset();

			// Display, position, and set styles for font
			tooltipEl.css({
				opacity: 1,
				width: tooltip.width ? (tooltip.width + 'px') : 'auto',
				left: offset.left + tooltip.x + 'px',
				top: offset.top + top + 'px',
				fontFamily: tooltip.fontFamily,
				fontSize: tooltip.fontSize,
				fontStyle: tooltip.fontStyle,
			});

		}
	};
	var ctx = $pod.find('.line-graph canvas').get(0).getContext("2d");
	var myLineChart = new Chart(ctx).Line(data, options);
	window.onresize = function(event){
		var width = $('canvas').parent().width();
		$('canvas').attr("width", width);
		new Chart(ctx).Line(data, options);
	};
}

