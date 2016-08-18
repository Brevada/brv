$(document).ready(function(){
	$('table.tablesorter').tablesorter();
	
	$('table.editable td').dblclick(function(){
		var td = $(this);
		var th = $(this).parent().parent().parent().find('thead > tr > th:eq('+(td.parent().children().index(td))+')');
		
		if(!th.hasClass('editable') || td.hasClass('not-editable')){ return; }
		
		if(td.hasClass('editing') || td.hasClass('saving')){ return; }
		td.addClass('editing');
		
		var input;
		if(th.hasClass('editable-dropdown')){
			input = $('<select>');
			var options = th.data('dropdown-options').split(',');
			input.append($('<option value="">Choose a Status...</option>'));
			for(var i = 0; i < options.length; i++){
				input.append($('<option value="'+options[i].trim()+'">'+options[i].trim().toUpperCase()+'</option>'));
			}
			input.val(td.text());
		} else {
			input = $('<input>').attr('type', 'text').val(td.text());
			if(!!th.attr('placeholder')){
				input.attr('placeholder', th.attr('placeholder'));
			}
		}
		input.on('keypress change', function(e){
			if((e.type == 'keypress' && e.which == 13) || e.type == 'change'){
				var change = input.val();
				td.html(change);
				td.removeClass('editing').addClass('saving');
				td.append($('<i>').addClass('fa').addClass('fa-spinner').addClass('fa-spin'));
				
				if(typeof submitChange !== 'undefined'){
					submitChange(td.parent().children().index(td), td.parent().data('id'), change);
				}
				return false;
			}
		});
		td.data('previous-value', td.text());
		td.html('');
		td.append(input);
		input.focus();
	});

	$('[data-tooltip]').each(function(){
		$(this).brevadaTooltip();
	});
});