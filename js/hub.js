$(document).ready(function(){
	$("#showsteps").click( function() {
		showSteps();
	});

	$(".openModal").click(function() {
		$("#generic_modal").fadeIn("fast");
	});

	$(".closeModal").click(function() {
		$("#generic_modal").fadeOut("fast");
	});

	$("#expanderHeadGather").click(function(){
		$("#expanderContentGather").slideToggle();
		if ($("#expanderSignGather").text() == "Show"){
			$("#expanderSignGather").text("Hide")
		} else {
			$("#expanderSignGather").text("Show")
		}
	});
	
	$("#expanderHeadManage").click(function(){
		$("#expanderContentManage").slideToggle();
		if ($("#expanderSignManage").text() == "Show"){
			$("#expanderSignManage").text("Hide")
		} else {
			$("#expanderSignManage").text("Show")
		}
	});
	
	$("#expanderHeadMarketing").click(function(){
		$("#expanderContentMarketing").slideToggle();
		if ($("#expanderSignMarketing").text() == "Show"){
			$("#expanderSignMarketing").text("Hide")
		} else {
			$("#expanderSignMarketing").text("Show")
		}
	});
	
	$("#expanderHeadAspects").click(function(){
		$("#expanderContentAspects").slideToggle();
		if ($("#expanderSignAspects").text() == "Show"){
			$("#expanderSignAspects").text("Hide")
		} else {
			$("#expanderSignAspects").text("Show")
		}
	});
	
	$( ".open_modal" ).click(function() {
	  $( ".modal_bg" ).fadeIn( "fast", function() {
		   $("body").addClass("no_scroll");
	  });
	});
	
	$( ".close_modal" ).click(function() {
	  $( ".modal_bg" ).fadeOut( "fast", function() {
		  $("body").removeClass("no_scroll");
	  });
	});
	
	/* MODALS */
	$("#email_modal").click(function() {
		var user_id = $(this).attr('userid');
		$('#generic_modal_content').html('<center>Loading...</center>');
		$("#generic_modal_content").load("/hub/includes/email/email_feedback.php?user_id="+user_id);
	});
	
	$("#modal_url").click(function() {	
		$('#modal_content').html('<center>Loading...</center>');
		$("#modal_content").load('/hub/includes/popups/url.php');
	});
 
	$("#modal_qr" ).click(function() {	
		$('#modal_content').html('<center>Loading...</center>');
		$("#modal_content").load('/hub/includes/popups/qr.php');
	});
 
	$("#modal_print").click(function() {	
		$('#modal_content').html('<center>Loading...</center>');
		$("#modal_content").load('/hub/includes/popups/promo.php');
	});
 
	$("#modal_certificates").click(function() {	
		$('#modal_content').html('<center>Loading...</center>');
		$("#modal_content").load('/hub/includes/popups/certificates.php');
	});
 
	$("#modal_email").click(function() {	
		$('#modal_content').html('<center>Loading...</center>');
		$("#modal_content").load('/hub/includes/popups/email.php');
	});
  
	$("#modal_widgets").click(function() {	
		$('#modal_content').html('<center>Loading...</center>');
		$("#modal_content").load('/hub/includes/popups/widgets.php');
	});
  
	$("#modal_approved").click(function() {	
		$('#modal_content').html('<center>Loading...</center>');
		$("#modal_content").load('/hub/includes/popups/approved.php');
	});

	$("#modal_changepic").click(function() {	
		$('#modal_content').html('<center>Loading...</center>');
		$("#modal_content").load('/hub/includes/popups/change_pic.php');
	});

	$("#modal_updateinfo").click(function() {	
		$('#modal_content').html('<center>Loading...</center>');
		$("#modal_content").load('/hub/includes/popups/update_info.php');
	});
	
	// check where the shoppingcart-div is  
	var offset=$('#far_right').offset();
	if(typeof offset !== 'undefined'){
		$(window).scroll(function () {  
			var scrollTop=$(window).scrollTop(); // check the visible top of the browser  
			if (offset.top<scrollTop){
				$('#far_right').addClass('fixed'); 
			} else {
				$('#far_right').removeClass('fixed'); 
			}
		});
	}

	
    $('.hub_left_bar').hover( function() {
        var toggleWidth;
		if ($(".hub_left_bar").width() == 300) {
			$('.hub_left_bar').animate({ width: '150px'}, 400);
		} else {
			$('.hub_left_bar').animate({ width: '300px'}, 400);
		}
    });
	
	$("#more_open").hover(function(){
	  $( ".more_list" ).toggle();
	});

	$(".more_list").hover(function(){
	  $( ".more_list" ).toggle();
	});

	$('#more_open').hover(function(){
		this.style.opacity = this.style.opacity == 0.3 ? 1 : 0.3;
	});
	
	$('#dialog-confirm-delete').dialog({
		autoOpen: false,
		resizable: false,
		height: 230,
		modal: true,
		buttons: {
			"Delete": function(){
				var postid = $(this).attr('post-id');
				if(typeof postid !== 'undefined' && postid.length > 0){
					$.get('/overall/generic_delete.php?db=posts&nr=nr&id='+postid, function(data){
						if(data == '1'){
							//You can do an animation here.
							$('#hbox'+postid).remove();
						}
					});
				}
				$(this).attr('post-id', '');
				$(this).dialog('close');
			},
			Cancel: function(){
				$(this).attr('post-id', '');
				$(this).dialog('close');
			}
		}
	});
	
});

function openPopup() {
    $('#test').show();
}
function closePopup() {
    $('#test').hide();
}

function showSteps(){
	$("#works_section").fadeIn("fast",function(){
		$(".works1").fadeIn("fast",function(){
			$(".works2").fadeIn("fast", function(){
				$(".works3").fadeIn("fast", function(){
					$(".works4").fadeIn("fast", function(){
						$(".works5").fadeIn("fast", function(){
							$(".works6").fadeIn("fast", function(){
								$(".works7").fadeIn("fast", function(){
									$(".works8").fadeIn("fast");
								});
							});
						});
					});
				});
			});
		});
	});
}
