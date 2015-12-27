/* Overall Aspects Dashboard App */

dashboards.aspects = {};
dashboards.aspects.data = {};


dashboards.aspects.render = function (canvas) {
	// Temporary hack before we convert the aspects page into
	// proper JS format.
	$(canvas).html(aspects_holder);
	$(canvas).find('.line-graph').each( function () {
		var pod_id = 'pod' + $(this).attr('data-id'),
			bucket = $.parseJSON($(this).attr('graph-json'));
		$(this).html('<canvas></canvas>');
		build_line_graph(bucket, pod_id);
	});
}

