/* Milestone Dashboard App */
var milestones = {};

bdff.create('milestones', function(canvas, face){

	milestones.data = {}

	milestones.render = function (canvas) {
		canvas.empty();
		// TODO: Create timeline illusion with line in the background
		milestones.renderForm(canvas);
		// TODO: Load the static stuff (eg. creation form)
		// TODO: Loop through an AJAX call to all the milestones and then call
		//		 renderMilestone on each
		milestones.milestone.render(canvas, 1);
		milestones.milestone.render(canvas, 2);
		milestones.milestone.render(canvas, 3);
		milestones.milestone.render(canvas, 4);
		milestones.milestone.render(canvas, 5);
	}

	milestones.renderForm = function (canvas) {
		/* Template */
		$('\
			<div class="milestone-form">\
				<input class="title" placeholder="New Milestone Title" />\
				<input class="date" placeholder="Date" />\
				<button type="submit">Add</button>\
			</div>\
			').appendTo(canvas);

		/* Events */
		canvas.find('button').click(function () {
			milestones.createMilestone($(canvas.find('.milestone-form')));		
		});
		
	}

	milestones.createMilestone = function (form) {
		var title = $(form).find('.title').val(),
			date = $(form).find('.date').val();

		if (title && date) {
			dashboards.changeFace('milestones');
			dashboards.alert('New milestone created.', 'success');
		}

	}
	
	milestones.render(canvas);

});