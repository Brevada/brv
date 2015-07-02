$(document).ready(function(){
	setTimeout(function(){ $(".dis").fadeOut(); }, 3000);
	$("#imdone").click(function() { 
		location.reload(true);
	});
});

function message_show() {
	$("#message_box").css('display', 'block');
}

$(window).load(function() {
  $('#loading').hide();
});

$(document).ready(function() {  
 var offset = $('#far_left').offset();  

 $(window).scroll(function () {  
   var scrollTop = $(window).scrollTop(); 

   if (offset.top<scrollTop) $('#far_left').addClass('fixedL');  
   else $('#far_left').removeClass('fixedL');  
  });  
});