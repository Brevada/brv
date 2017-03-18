var dialogModal; var confirmDeleteModal;

$(document).ready(function(){
	$("#showsteps").click( function() {
		showSteps();
	});
	
	$("#showNew").click(function() {
		$("#showNew").fadeOut("fast");
		$("#newPost").fadeIn("fast");
	});
	
	$("#advanced").click(function() {
		$("#advanced").fadeOut(0);
		$("#advancedIntegration").fadeIn("fast");
		$("#advancedEmail").fadeIn("fast");
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

	/*
	EXPAND BAR ON HOVER
    $('.hub_left_bar').hover( function() {
        var toggleWidth;
		if ($(".hub_left_bar").width() == 300) {
			$('.hub_left_bar').animate({ width: '150px'}, 400);
		} else {
			$('.hub_left_bar').animate({ width: '300px'}, 400);
		}
    });
	*/
	
	$("#more_open").hover(function(){
	  $( ".more_list" ).toggle();
	});

	$(".more_list").hover(function(){
	  $( ".more_list" ).toggle();
	});

	$('#more_open').hover(function(){
		this.style.opacity = this.style.opacity == 0.3 ? 1 : 0.3;
	});
	
	confirmDeleteModal = $('#dialog-confirm-delete').dialog({
		autoOpen: false,
		resizable: false,
		width: 'auto',
		minHeight: 0,
		maxHeight: $(window).height(),
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
				confirmDeleteModal.dialog('close');
			}
		}
	});
	
	dialogModal = $('#dialog-modal').dialog({
		autoOpen: false,
		resizable: false,
		width: 'auto',
		minHeight: 0,
		maxHeight: ($(window).height() - (0.1*$(window).height())),
		modal: true,
		open: function(event, ui){
			dialogModal.dialog('option', 'maxHeight', ($(window).height() - (0.1*$(window).height())));
			dialogModal.dialog('widget').position({my: 'center', at : 'center', of : window});
			dialogModal.position({my: 'center', at : 'center', of : window});
		}
	});
	
	bindModals();
	
	$(document).tooltip({ items : '[tooltip]', content: function(){
		var el = $(this);
		if(el.is("[tooltip]")){
			return el.attr('tooltip');
		}
	}});
});

function bindModals(){	
	$("#modal_qr" ).click(function() {	
		showModalDialog('/widget/hub/qr.php');
	});
 
	$("#modal_email").click(function() {	
		showModalDialog('/widget/hub/email.php');
	});
  
	$("#modal_widgets").click(function() {	
		showModalDialog('/widget/hub/widgets.php');
	});

	$("#modal_changepic").click(function() {	
		showModalDialog('/widget/hub/change_pic.php');
	});

	$("#modal_updateinfo").click(function() {	
		showModalDialog('/widget/hub/update_info.php');
	});
}
function showModalDialog(url){
	$.get(url).success(function(data){
		$('#dialog-modal-content').html(data);
		dialogModal.dialog('open');
	});
}

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
