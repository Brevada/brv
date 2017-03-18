$(document).ready(function(){
	$('.corpHead').click(function(){
		var company_id = $(this).attr('companyid');
		$('#corpContent' + company_id).slideToggle();
		var corpSign = $('#corpSign' + company_id);
		if (corpSign.text() == 'View'){
			corpSign.text('Hide');
		} else {
			corpSign.text('View');
		}
	});
});