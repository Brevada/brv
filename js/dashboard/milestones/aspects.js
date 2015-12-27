/* Milestone Aspect */

dashboards.milestones.milestone.aspect = {};
dashboards.milestones.milestone.aspect.data = {};

dashboards.milestones.milestone.aspect.fetch = function (id, aspect_id) {
	dashboards.milestones.milestone.aspect.data = {
		'percent-change': 27,
		'parity': '+',
		'responses': 358,
		'score': 83
	}
}

dashboards.milestones.milestone.aspect.render = function (body, id, aspect_id) {

	var aspect = document.createElement("div");
 	aspect.setAttribute('class', 'milestone-aspect');
 	aspect.setAttribute('milestone-aspect-data-id', id);
 	$(aspect).appendTo($(body));
 	/* Template */
	$('\
	   <div class="bulb"></div>\
	   <div class="title">\
	   Taste\
	   </div>\
	   <div class="details">\
	   </div>\
	   <div class="clear">\
	   <i class="fa fa-times"></i>\
	   </div>\
	   ').appendTo($(aspect)); 
	dashboards.milestones.milestone.aspect.renderStats($(aspect), id, 1);
}

dashboards.milestones.milestone.aspect.renderStats = function (aspect, id, aspect_id) {
	dashboards.milestones.milestone.aspect.fetch(id, aspect_id);
	var percent_change = dashboards.milestones.milestone.aspect.data['percent-change'],
		parity = dashboards.milestones.milestone.aspect.data['parity'],
		responses = dashboards.milestones.milestone.aspect.data['responses'];

	$(aspect).find('.details').html(parity + percent_change + '% after ' + responses + ' responses');
}