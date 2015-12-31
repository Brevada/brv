/* Individual Milestone */

milestones.milestone = {};
milestones.milestone.data = {};

milestones.milestone.fetch = function (id) {
	milestones.milestone.data[id] = {
		'title': 'Hired Chef Sheff Jones',
		'date': 'November ' + id + ' 2015'
	};
}

milestones.milestone.render = function (canvas, id) {
	

	// Create the milestone
	var milestone = document.createElement("div");
 	milestone.setAttribute('class', 'milestone col-md-12');
 	milestone.setAttribute('milestone-data-id', id);
 	$(milestone).appendTo(canvas);
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
 	milestones.milestone.renderTitle(milestone, id);
 	milestones.milestone.renderDate(milestone, id);
 	milestones.milestone.renderBody(milestone, id);

 	/* Events */
 	$(milestone).find('.header').click(function () {
 		$(milestone).find('.body, .add').toggle(100);
 	});
 	
}

milestones.milestone.renderTitle = function (milestone, id) {
	milestones.milestone.fetch(id);
	$(milestone).find('.title').html(
		milestones.milestone.data[id]['title']
	);
}

milestones.milestone.renderDate = function (milestone, id) {
	milestones.milestone.fetch(id);
	$(milestone).find('.date').html(
		milestones.milestone.data[id]['date']
	);
}

milestones.milestone.renderBody = function (milestone, id) {
	milestones.milestone.fetch(id);
	// TODO: Loop through each of the milestone's chosen aspects
	var body = $(milestone).find('.body');
	milestones.milestone.aspect.render($(body), id, 1);
	milestones.milestone.aspect.render($(body), id, 2);
	milestones.milestone.aspect.render($(body), id, 3);
}
