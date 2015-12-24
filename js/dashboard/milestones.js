/* 
	Milestone Dashboard App
	Brevada Dashboard Frontend Framework (BDFF)
*/

/* Setup */

// Milestones App
dashboards.milestones = {};
dashboards.milestones.data = {}

// Individual Milestone (TODO: Separate into other file)
dashboards.milestones.milestone = {};
dashboards.milestones.milestone.data = {};

// Individual Milestone Aspect (TODO: Separate into other file)
dashboards.milestones.milestone.aspect = {};
dashboards.milestones.milestone.aspect.data = {};



/* Milestones App */

dashboards.milestones.render = function (canvas) {
	$(canvas).empty();
	// TODO: Create timeline illusion with line in the background
	dashboards.milestones.renderForm(canvas);
	// TODO: Load the static stuff (eg. creation form)
	// TODO: Loop through an AJAX call to all the milestones and then call
	//		 renderMilestone on each
	dashboards.milestones.milestone.render(canvas, 1);
	dashboards.milestones.milestone.render(canvas, 2);
	dashboards.milestones.milestone.render(canvas, 3);
	dashboards.milestones.milestone.render(canvas, 4);
	dashboards.milestones.milestone.render(canvas, 5);
}

dashboards.milestones.renderForm = function (canvas) {
	/* Template */
	$('\
		<div class="milestone-form">\
		<input class="title" placeholder="New Milestone Title" />\
		<input class="date" placeholder="Date" />\
		<button type="submit">Add</button>\
		</div>\
		').appendTo($(canvas));
}



/* Milestone */

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
 		<div class="footer">\
 			<div class="add">Add Aspect</div>\
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
 		$(milestone).find('.body').toggle(100);
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



/* Milestone Aspect */

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
	$('\
	   <div class="bulb"></div>\
	   <div class="title">\
	   Taste\
	   </div>\
	   <div class="details">\
	   </div>\
	   <div class="clear">\
	   x\
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