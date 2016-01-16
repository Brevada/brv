/* Individual Milestone */

milestones.milestone = {};
milestones.milestone.data = {};

milestones.milestone.fetch = function (id) {
	milestones.milestone.data[id] = {
		'title': 'Hired Chef Sheff Jones',
		'date': 'November ' + id + ' 2015',
		'completed': id == 1 ? false : 'November 30 2015' 
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
	 			<div class="data">\
		 			<div class="date"></div>\
		 			<div class="completion"></div>\
	 			</div>\
 			</div>\
 		</div>\
 		<div class="body">\
 		</div>\
 		<div class="add">+ Add an Aspect</div>\
 		<div class="footer">\
 			<div class="delete">Delete</div>\
 			<div class="complete-button" >Complete Milestone</div>\
 		</div>\
 		</div>\
 		').appendTo($(milestone));

 	// Populate the milestone
 	milestones.milestone.renderTitle(milestone, id);
 	milestones.milestone.renderDate(milestone, id);
 	milestones.milestone.renderCompletion(milestone, id);
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
	$(milestone).find('.data .date').html(
		milestones.milestone.data[id]['date']
	);
}

milestones.milestone.renderCompletion = function (milestone, id) {
	milestones.milestone.fetch(id);
	var completed = milestones.milestone.data[id]['completed'];
	if (completed) {
		// The milestone is complete
		$(milestone).find('.data .completion').html('&nbsp;- ' +
			milestones.milestone.data[id]['completed']
		);
		$('<span class="complete" >&nbsp;MILESTONE COMPLETE</span>').appendTo($(milestone).find('.title'));
	} else {
		// The milestone is still in progress
		$(milestone).find('.complete-button').css({
			'display': 'inline-block'
		}).click(function () {
			milestones.milestone.completeMilestone(milestone, id);
		});
	}
}

milestones.milestone.renderBody = function (milestone, id) {
	milestones.milestone.fetch(id);
	// TODO: Loop through each of the milestone's chosen aspects
	var body = $(milestone).find('.body');
	milestones.milestone.aspect.render($(body), id, 1);
	milestones.milestone.aspect.render($(body), id, 2);
	milestones.milestone.aspect.render($(body), id, 3);
}

milestones.milestone.completeMilestone = function (milestone, id) {
	console.log('Completing Milestone');
	// TODO: Complete the mielstone then run renderCompletion
}
