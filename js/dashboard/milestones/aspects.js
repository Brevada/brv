/* Milestone Aspect */

milestones.milestone.aspect = {};
milestones.milestone.aspect.data = {};

milestones.milestone.aspect.fetch = function (id, aspect_id) {
	milestones.milestone.aspect.data = {
		'percent-change': 27,
		'parity': '+',
		'responses': 358,
		'score': 83
	}
}

milestones.milestone.aspect.render = function (body, id, aspect_id) {

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
	milestones.milestone.aspect.renderStats($(aspect), id, 1);
}

milestones.milestone.aspect.renderStats = function (aspect, id, aspect_id) {
	milestones.milestone.aspect.fetch(id, aspect_id);
	var percent_change = milestones.milestone.aspect.data['percent-change'],
		parity = milestones.milestone.aspect.data['parity'],
		responses = milestones.milestone.aspect.data['responses'];

	$(aspect).find('.details').html(parity + percent_change + '% after ' + responses + ' responses');
}