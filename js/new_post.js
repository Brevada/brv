$(document).ready(function(){
	$("#expanderHeadPost").click(function(){
		$("#new_post").slideToggle();
		if ($("#expanderSignPost").text() == "+"){
			$("#expanderSignPost").text("-")
		} else {
			$("#expanderSignPost").text("+")
		}
	});
});

$(".description_input").on('keydown', function(event) {
	var currentString=$(".description_input").val()
	if (currentString.length <= 60 )  {  /*or whatever your number is*/
		$( this ).css( "background", "white" );
		$( this ).css( "color", "#333333" );
	} else {
		$( this ).css( "background", "red" );
		$( this ).css( "color", "white" );
	}
});

function chooseFile() {
	$("#fileInput").click();
}

function clearButton() {
	document.getElementById("but").style.display='none';
	document.getElementById("prev").style.display='block';
}
			
function readURL(input) {
    if (input.files && input.files[0]) {
        var reader=new FileReader();
        reader.onload=function (e) {
            $('#prev').attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
    }
}

$("#fileInput").change(function(){
    readURL(this);
});