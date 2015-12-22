/* 
	Live Dashboard App
	Brevada Dashboard Frontend Framework (BDFF)
*/

/* Setup */

// Support App
dashboards.support = {};
dashboards.support.data = {};

dashboards.support.form = {};

dashboards.support.resources = {};

/* Support App */

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

 	$('\
 		<div class="dashboard-pod">\
 		<textarea class="issue" placeholder="What can we help you with?"></textarea>\
 		</div>\
 		').appendTo($(support_form));
}

/* Support Resources */

dashboards.support.resources.render = function (canvas) {
	var support_resources = document.createElement("div");
 	support_resources.setAttribute('class', 'support-resources col-md-3');
 	$(support_resources).appendTo($(canvas));

 	$('\
 		<div class="dashboard-pod">Dashboard Support, Tablet Support</div>\
 		').appendTo($(support_resources));
}