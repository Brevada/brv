/* Overall Aspects Dashboard App */

bdff.create('aspects', function(canvas, face){
	console.log(canvas);
	
	canvas.html('Aspects face.');
	
	/*canvas.find('.line-graph').each( function () {
		var pod_id = 'pod' + $(this).attr('data-id'),
			bucket = $.parseJSON($(this).attr('graph-json'));
		$(this).html('<canvas></canvas>');
		build_line_graph(bucket, pod_id);
	});*/
	
	face.attach();
});