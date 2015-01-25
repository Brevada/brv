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
		var hbox = $("#hbox"+postid);
		if(hbox.hasClass('hbox_open')){
			hbox.removeClass('hbox_open').addClass('hbox_closed');
			hbox.children('.hbox_left').removeClass('hide_mobile');
		} else {
			hbox.removeClass('hbox_closed').addClass('hbox_open');
			hbox.children('.hbox_left').addClass('hide_mobile');
		}
		
        var toggleContent = hbox.width() == 230 ? "&lt;" : "&gt;";
        $(this).html(toggleContent);
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
