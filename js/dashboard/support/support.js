/* Support Dashboard App */

dashboards.support = {};
dashboards.support.data = {};

dashboards.support.form = {};

dashboards.support.resources = {};

dashboards.support.fetch = function (id) {
}

dashboards.support.render = function (canvas) {
	$(canvas).empty();
	dashboards.support.form.render(canvas);
	dashboards.support.resources.render(canvas);
}

/* Support Form */

dashboards.support.form.render = function (canvas) {
	var support_form = document.createElement("div");
 	support_form.setAttribute('class', 'support-form col-md-9');
 	$(support_form).appendTo($(canvas));
 	dashboards.support.form.element = support_form;

 	/* Template */
 	$('\
 		<div class="dashboard-pod">\
 		<textarea class="issue" placeholder="What can we help you with?"></textarea>\
 		<div class="submit">Submit</div>\
 		</div>\
 		').appendTo($(dashboards.support.form.element));

 	/* Events */
 	$(dashboards.support.form.element).find('.submit').click(function () {
 		dashboards.support.form.submit($(dashboards.support.form.element).find('.issue').val());
 	});
}

dashboards.support.form.submit = function (message) {
	if (message) {
		// TODO: Submit the message through the API
		$(dashboards.support.form.element).find('textarea, .submit').hide();
		dashboards.support.form.renderSumbission();
	}
}

dashboards.support.form.renderSumbission = function () {
	dashboards.changeFace('aspects');
	dashboards.alert('Your response has been recieved, \
		you will be contacted shortly.', 'success');
}


/* Support Resources */

dashboards.support.resources.render = function (canvas) {
	var support_resources = document.createElement("div");
 	support_resources.setAttribute('class', 'support-resources col-md-3');
 	$(support_resources).appendTo($(canvas));

 	/* Template */
 	$('\
 		<div class="dashboard-pod">Dashboard Support, Tablet Support</div>\
 		').appendTo($(support_resources));
}