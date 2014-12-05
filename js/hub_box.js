$(document).ready(function(){
	$(".editHead").click(function(){
		var postid = $(this).attr('postid');
		$("#editContent_" + postid).slideToggle();
		if ($("#editSign_" + postid).text() == "+"){
			$("#editSign_" + postid).text("-")
		} else {
			$("#editSign_" + postid).text("+")
		}
	});
	
    $('.toggle_width').click( function() {
		var postid = $(this).attr('post-id');
        var toggleWidth = $("#hbox"+postid).width() == 250 ? "543px" : "250px";
        var toggleContent = $("#hbox"+postid).width() == 250 ? "&lt;" : "&gt;";
        $(this).html(toggleContent);
        $('#hbox'+postid).animate({ width: toggleWidth},80);
    });
	
	$('.editHead').click(function(){
		var postid = $(this).attr('post-id');
		toggle_visibility("#editContent_"+postid);
	});
	
	$('.delete_post').click(function(){
		var postid = $(this).attr('post-id');
		$('#dialog-confirm-delete').attr('post-id', postid);
		$('#dialog-confirm-delete').dialog('open');
	});
});

function toggle_visibility(id) {
	var el = $(id);
	var postid = el.attr('post-id');
	if(el.is(':visible')){
		el.hide();
		$("#hbox_titles"+postid).show(100);
	} else {
		el.show();
		$("#hbox_titles"+postid).hide(100);
	}
}
