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

function build_line_graph (data, id) {
	$pod = $('#' + id);
	$pod.find('.line-graph').html('\
		<canvas style="width: 100%; height: 250px;"></canvas>');
	var data = {
		// take in 12 points for now (optionally include labels)
	    labels: ["", "", "", "", "", "", "", "", "", "", "", ""],
	    datasets: [
	        {
	            label: "My First dataset",
	            fillColor: "rgba(220,220,220,0.6)",
	            strokeColor: "rgba(220,220,220,1)",
	            pointColor: "rgba(220,220,220,1)",
	            pointStrokeColor: "#fff",
	            pointHighlightFill: "#fff",
	            pointHighlightStroke: "rgba(220,220,220,1)",
	            data: [87, 84, 89, 92, 83, 88, 90]
	        },
	        {
	            label: "My Second dataset",
	            fillColor: "rgba(21,187,75,0.9)",
	            strokeColor: "rgba(151,187,205,1)",
	            pointColor: "rgba(151,187,205,1)",
	            pointStrokeColor: "#fff",
	            pointHighlightFill: "#fff",
	            pointHighlightStroke: "rgba(151,187,205,1)",
	            data: [78, 87, 84, 89, 92, 83, 90, 88, 90]
	        }
	    ]
	};
	var options = {

	    ///Boolean - Whether grid lines are shown across the chart
	    scaleShowGridLines : false,

	    scaleShowLabels : false,

	    //String - Colour of the grid lines
	    scaleGridLineColor : "rgba(0,0,0,.1)",

	    //Number - Width of the grid lines
	    scaleGridLineWidth : 1,

	    //Boolean - Whether to show horizontal lines (except X axis)
	    scaleShowHorizontalLines: false,

	    //Boolean - Whether to show vertical lines (except Y axis)
	    scaleShowVerticalLines: false,

	    //Boolean - Whether the line is curved between points
	    bezierCurve : true,

	    //Number - Tension of the bezier curve between points
	    bezierCurveTension : 0.4,

	    //Boolean - Whether to show a dot for each point
	    pointDot : true,

	    //Number - Radius of each point dot in pixels
	    pointDotRadius : 4,

	    //Number - Pixel width of point dot stroke
	    pointDotStrokeWidth : 1,

	    //Number - amount extra to add to the radius to cater for hit detection outside the drawn point
	    pointHitDetectionRadius : 20,

	    //Boolean - Whether to show a stroke for datasets
	    datasetStroke : true,

	    //Number - Pixel width of dataset stroke
	    datasetStrokeWidth : 2,

	    //Boolean - Whether to fill the dataset with a colour
	    datasetFill : true,

	    //String - A legend template
	    // legendTemplate : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].strokeColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>"
	};
	var ctx = $pod.find('.line-graph canvas').get(0).getContext("2d");
	// This will get the first returned node in the jQuery collection.
	var myLineChart = new Chart(ctx).Line(data, options);
	// $pod.find('canvas').attr({width: '100%'});
}

