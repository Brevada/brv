$(document).ready(function(){
	$(".editHead").click(function(){
		var postid = $(this).attr('postid');
		$("#editContent_" + postid).slideToggle("fast", function() {
   			 // Animation complete.
  		});
		if ($("#editSign_" + postid).text() == "+"){
			$("#editSign_" + postid).text("-")
		} else {
			$("#editSign_" + postid).text("+")
		}
	});
	
    $('.toggle_width').click( function() {
		var postid = $(this).attr('post-id');
        var toggleWidth = $("#hbox"+postid).width() == 230 ? "503px" : "230px";
        var toggleContent = $("#hbox"+postid).width() == 230 ? "&lt;" : "&gt;";
        $(this).html(toggleContent);
        $('#hbox'+postid).animate({ width: toggleWidth},80);
    });
	
	$('.editHead').click(function(){
		var postid = $(this).attr('post-id');
		toggle_visibility("#editContent_"+postid);
	});
	
	$('.delete_post').click(function(){
		var postid = $(this).attr('post-id');
		confirmDeleteModal.attr('post-id', postid);
		confirmDeleteModal.dialog('open');
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
