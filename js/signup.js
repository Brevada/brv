var scannedWebsite = categoryChanged = false;
var website = '';

$(document).ready(function(){
	
	$('.in').keypress(function(){
		if($(this).hasClass('invalid')){
			$(this).removeClass('invalid');
		}
	});
	
	$('div.submit-next').click(function(e){
		advancePart($(this));
	});
	
	$('div.submit-back').click(function(e){
		var $self = $(this);
		$self.parent().fadeOut(100, function(){
			$self.parent().prev('div').fadeIn();
		});
	});
	
    $('#logo').each(function(i) {
        if (this.complete) {
            $('#signup_box').fadeIn(2000);
        } else {
            $(this).load(function() {
				$('#signup_box').fadeIn(2000);
            });
        }
    });
	
	$('div.token-aspects div.tokens > div.token').click(tokenClicked);
	
	$('#chkAgree').change(function(){
		if(this.checked){
			$('#submit').removeClass('disabled');
		} else {
			$('#submit').addClass('disabled');
		}
	});
	
	$('#ddCategory').change(function(){
		categoryChanged = true;
	});
	
});

function tokenClicked(){
	if($(this).hasClass('selected')){
		$(this).removeClass('selected');
	} else {
		$(this).addClass('selected');
	}
	updateTokens($(this).parent());
}

function updateTokens(container){
	var tokens = [];
	container.children('div.token').each(function(){
		if($(this).hasClass('selected')){
			tokens.push($(this).data('tokenid'));
		}
	});
	container.children('input.token-input').val(tokens.join(','));
}

function validateForm(){
	if($('#password1').val().length == 0 || $('#password1').val() != $('#password2').val()){
		$('#password1').addClass('invalid');
		$('#password2').addClass('invalid');
		return false;
	}
	
	return true;
}

function resetNext($self){
	var icon = $self.children('i').removeClass('fa-spin').removeClass('fa-spinner').addClass('fa-chevron-right')[0].outerHTML;
	$self.html("Next " + icon).removeClass('button-loading');
}

function advancePart($self){
	if($self.hasClass('disabled') || $self.children('i').hasClass('fa-spinner')) return;
	
	if($self.parent().next('div').length == 0){
		if(validateForm()){
			$('#frmSignup').submit();
		}
	} else {
		var partN = $self.parent().parent().find('div.part').index($self.parent());
		
		if(partN == 0 && $('#txtWebsite').val().length > 0 && (!scannedWebsite || $('#txtWebsite').val() != website)){
			scannedWebsite = true;
			website = $('#txtWebsite').val();
			var icon = $self.children('i').removeClass('fa-chevron-right').addClass('fa-spinner').addClass('fa-spin')[0].outerHTML;
			$self.html("Loading " + icon).addClass('button-loading');
			
			$.getJSON('/home/crawl.php', { website : $('#txtWebsite').val() }, function(data){
				if(data.hasOwnProperty('error')){
					$self.parent().fadeOut(100, function(){
						resetNext($self);
						$self.parent().next('div').fadeIn();
					});
				} else {
					$('#categoryDetection').text(data.category.title);
					$('#ddCategory').val(data.category.id);
					
					$('.not-crawled').hide();
					$('.crawled').show();
					
					$.post('/home/keywords.php', { category: data.category.id }, function(cont){
						$('.token-keywords > .tokens').html(cont);
						
						$('div.token-keywords div.tokens > div.token').click(tokenClicked);
						
						for(var i = 0; i < data.keywords.length; i++){
							$('.token-keywords > .tokens > div[data-tokenid="'+(data.keywords[i])+'"]').click();
						}
						updateTokens($('div.token-keywords'));
						
						$self.parent().fadeOut(100, function(){
							resetNext($self);
							$self.parent().next('div').fadeIn();
						});
					});
				}
			});
		} else if(partN == 1 && categoryChanged){
			categoryChanged = false;
			
			var icon = $self.children('i').removeClass('fa-chevron-right').addClass('fa-spinner').addClass('fa-spin')[0].outerHTML;
			$self.html("Loading " + icon).addClass('button-loading');
			
			$.post('/home/keywords.php', { category: $('#ddCategory').val() }, function(cont){
				$('.token-keywords > .tokens').html(cont);
				
				$('div.token-keywords div.tokens > div.token').click(tokenClicked);
				
				updateTokens($('div.tokens-keywords'));
				
				$self.parent().fadeOut(100, function(){
					$self.parent().next('div').fadeIn();
					
					resetNext($self);
				});
			});
			
		} else {
			$self.parent().fadeOut(100, function(){
				$self.parent().next('div').fadeIn();
			});
		}
	}
}