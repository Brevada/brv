/* Milestone Dashboard App */

dashboards.milestones = {};
dashboards.milestones.data = {}

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

	/* Events */
	$(canvas).find('button').click(function () {
		dashboards.milestones.createMilestone($(canvas.find('.milestone-form')));		
 	});
 	
}

dashboards.milestones.createMilestone = function (form) {
	var title = $(form).find('.title').val(),
		date = $(form).find('.date').val();

	if (title && date) {
		dashboards.changeFace('milestones');
		dashboards.alert('New milestone created.', 'success');
	}

}
