/* Individual Milestone */

dashboards.milestones.milestone = {};
dashboards.milestones.milestone.data = {};

dashboards.milestones.milestone.fetch = function (id) {
	dashboards.milestones.milestone.data[id] = {
		'title': 'Hired Chef Sheff Jones',
		'date': 'November ' + id + ' 2015'
	};
}

dashboards.milestones.milestone.render = function (canvas, id) {
	

	// Create the milestone
	var milestone = document.createElement("div");
 	milestone.setAttribute('class', 'milestone col-md-12');
 	milestone.setAttribute('milestone-data-id', id);
 	$(milestone).appendTo($(canvas));
 	/* Template */
 	$(' \
 		<div class="milestone-body">\
 		<div class="header">\
 			<div class="bulb">\
 			</div>\
 			<div class="header-content">\
	 			<div class="title">\
	 			</div>\
	 			<div class="date"></div>\
 			</div>\
 		</div>\
 		<div class="body">\
 		</div>\
 		<div class="add">+ Add an Aspect</div>\
 		<div class="footer">\
 			<div class="delete">Delete</div>\
 		</div>\
 		</div>\
 		').appendTo($(milestone));

 	// Populate the milestone
 	dashboards.milestones.milestone.renderTitle(milestone, id);
 	dashboards.milestones.milestone.renderDate(milestone, id);
 	dashboards.milestones.milestone.renderBody(milestone, id);

 	/* Events */
 	$(milestone).find('.header').click(function () {
 		$(milestone).find('.body, .add').toggle(100);
 	});
 	
}

dashboards.milestones.milestone.renderTitle = function (milestone, id) {
	dashboards.milestones.milestone.fetch(id);
	$(milestone).find('.title').html(
		dashboards.milestones.milestone.data[id]['title']
	);
}

dashboards.milestones.milestone.renderDate = function (milestone, id) {
	dashboards.milestones.milestone.fetch(id);
	$(milestone).find('.date').html(
		dashboards.milestones.milestone.data[id]['date']
	);
}

dashboards.milestones.milestone.renderBody = function (milestone, id) {
	dashboards.milestones.milestone.fetch(id);
	// TODO: Loop through each of the milestone's chosen aspects
	var body = $(milestone).find('.body');
	dashboards.milestones.milestone.aspect.render($(body), id, 1);
	dashboards.milestones.milestone.aspect.render($(body), id, 2);
	dashboards.milestones.milestone.aspect.render($(body), id, 3);
}
