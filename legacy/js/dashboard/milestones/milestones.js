/* Milestone Dashboard App */

bdff.create('milestones', function(canvas, face){

	var milestones = {};
	var aspects = [];

	var render = function (canvas) {
		canvas.children().not('div.message-container').remove();
		renderForm(canvas);
		renderDialog(canvas);
		canvas.append(
			$('<div>').addClass('full-loader').append(
				$('<div>').addClass('fa fa-spin fa-gear')
			)
		);
	};

	var renderDialog = function (canvas) {
		var dialog = $('<div>')
					.addClass('modal fade')
					.attr({
						id: 'confirm-delete',
						tabindex: '-1',
						role: 'dialog'
					})
					.appendTo(canvas);
					
		dialog.append(
			$('<div>').addClass('modal-dialog').append(
				$('<div>').addClass('modal-content').append($('<div>').addClass('modal-header').html('<h2>Delete <b><span></span></b></h2>')).append($('<div>').addClass('modal-body').html("<p>Are you sure you want to delete this event?</p>")).append(
					$('<div>').addClass('modal-footer').append(
						$('<button>').addClass('btn btn-default').attr({ type: 'button', 'data-dismiss':'modal' }).text('Cancel')
					).append(
						$('<button>').addClass('btn btn-danger btn-ok').attr({ type: 'button', 'data-dismiss':'modal' }).text('Delete')
					)
				)
			)
		);
		
		dialog.find('.btn-ok').click(function(){
			$.post('/api/v1/milestones/delete', { 'store' : bdff.storeID(), 'id' : $(this).attr('data-ms-id'), 'localtime' : Math.ceil(((new Date()).getTime()/1000)) }, function(data){
				if(data.error && data.error.length > 0){
					bdff.notify(data.error[0], '', 'error');
				} else {
					face.startHooks();
				}
			});
		});
		
	};
	
	var renderForm = function (canvas) {
		
		var formType = function(e){
			var group = canvas.find('.milestone-form > .form-group').last();
			if(!group.is(':visible')){
				group.slideDown();
			}
		};
		
		canvas.append(
			$('<form>').addClass('form-horizontal milestone-form col-lg-12').attr('role', 'form').append(
				$('<div>').addClass('form-group row').append(
					$('<div>').addClass('col-lg-6 col-md-6 col-xs-12 nopadding').append(
						$('<input>').addClass('form-control title').attr({
							type: 'text',
							placeholder: 'New Event Title'
						}).keyup(formType).change(formType)
					)
				).append(
					$('<div>').addClass('col-lg-3 col-md-3 col-xs-6 nopadding').append(
						$('<div>').addClass('input-group date').attr('id', 'dtpFrom').append(
							$('<input>').attr({
								type: 'text',
								placeholder: 'From Date'
							}).addClass('form-control')
						).append(
							$('<span>').addClass('input-group-addon').append(
								$('<span>').addClass('glyphicon glyphicon-calendar')
							)
						)
					)
				).append(
					$('<div>').addClass('col-lg-3 col-md-3 col-xs-6 nopadding').append(
						$('<div>').addClass('input-group date').attr('id', 'dtpTo').append(
							$('<input>').attr({
								type: 'text',
								placeholder: 'To Date (optional)'
							}).addClass('form-control')
						).append(
							$('<span>').addClass('input-group-addon').append(
								$('<span>').addClass('glyphicon glyphicon-calendar')
							)
						)
					)
				)
			).append(
				$('<div>').addClass('form-group').append(
					$('<button>').addClass('btn btn-default submit add-event').attr({
						'type': 'button'
					}).text('Add Event').click(function(){
						var title = canvas.find('.milestone-form .title').val();
						var dateFrom = canvas.find('#dtpFrom > input').val();
						var dateTo = canvas.find('#dtpTo > input').val();
						if(title.length > 0 && dateFrom.length > 0){
							dateFrom = parseInt(moment(dateFrom, 'MMM. D, YYYY').format('X'));
							dateTo = parseInt(moment(dateTo, 'MMM. D, YYYY').format('X'));
							
							canvas.find('.milestone-form input').val('');
							canvas.find('.milestone-form > .form-group').last().slideUp();
							$.post('/api/v1/milestones/create', { 'store' : bdff.storeID(), 'title' : title, 'from' : dateFrom, 'to' : dateTo, 'localtime' : Math.ceil(((new Date()).getTime()/1000)) }, function(data){
								if(data.error && data.error.length > 0){
									bdff.notify(data.error[0], '', 'error');
								} else {
									face.startHooks();
								}
							});
						}
					})
				)
			)
		);
			
		canvas.find('#dtpFrom').datetimepicker({
			format: 'MMM. D, YYYY'
		});
		canvas.find('#dtpTo').datetimepicker({
			format: 'MMM. D, YYYY'
		});
	}
	
	var renderMilestone = function (id) {
		var milestone = { 'id' : id, 'aspects' : {} };
		
		var ms = $('<div>').addClass('milestone col-md-12').attr('milestone-data-id', id).insertAfter(canvas.find('.milestone-form'));
		
		$(' \
			<div class="milestone-body">\
			<div class="header">\
				<div class="bulb">\
				</div>\
				<div class="header-content">\
					<div class="title">\
					</div>\
					<div class="data">\
						<div class="date"></div>\
						<div class="completion"></div>\
					</div>\
				</div>\
			</div>\
			<div class="body">\
			</div>\
			<div class="add">+ Add an Aspect</div>\
			<div class="footer">\
				<div class="delete" data-toggle="modal" data-target="#confirm-delete">Delete</div>\
				<div class="complete-button" >Complete Milestone</div>\
			</div>\
			</div>\
			').appendTo(ms);

		ms.find('div.delete').attr('data-ms-id', id);
			
		ms.find('div.delete').click(function(){
			$('#confirm-delete').find('.btn-ok').attr('data-ms-id', $(this).attr('data-ms-id'));
			$('#confirm-delete').find('div.modal-header h2 span').text(ms.find('div.header-content div.title').text());
		});
		
		ms.find('div.complete-button').click(function(){
			$.post('/api/v1/milestones/complete', { 'store' : bdff.storeID(), 'id' : id, 'localtime' : Math.ceil(((new Date()).getTime()/1000)) }, function(data){
				if(data.error && data.error.length > 0){
					bdff.notify(data.error[0], '', 'error');
				} else {
					face.startHooks();
				}
			});
		});
		
		ms.find('div.add').click(function(){
			if($(this).children('select').length == 0){
				var sel = $('<select>').addClass('form-control').change(function(){
					if($(this).val() > 0){
						$.post('/api/v1/milestones/aspect', { 'store' : bdff.storeID(), 'id' : id, 'aid' : $(this).val(), 'localtime' : Math.ceil(((new Date()).getTime()/1000)) }, function(data){
							ms.find('div.add').empty().text('+ Add an Aspect');
							if(data.error && data.error.length > 0){
								bdff.notify(data.error[0], '', 'error');
							} else {
								face.startHooks();
							}
						});
					}
				});
				sel.append($('<option>').attr('value', '-1').text('Select an aspect...'));
				for(var i = 0; i < aspects.length; i++){
					if(ms.find('div.milestone-aspect div.title').filter(function(){
						return $(this).text() == aspects[i]['title'];
					}).length == 0){
						sel.append($('<option>').attr('value', aspects[i]['id']).text(aspects[i]['title']));
					}
				}
				$(this).empty().append(sel);
			}
		});
		
		milestone.setTitle = function(title){
			ms.find('.title').text(title);
		};
		
		milestone.setDate = function(start, end){
			var str;
			start = moment(start, 'X').format('MMM. D, YYYY');
			
			if(end > 0){
				end = moment(end, 'X').format('MMM. D, YYYY');
			} else {
				end = "Still Active";
			}
			
			if(start == end){
				str = start;
			} else {
				str = start + ' - ' + end;
			}
			ms.find('.data .date').text(str);
		};
		
		milestone.setCompletion = function(end){
			if(end == 0){
				ms.find('div.complete-button').show();
			} else {
				ms.find('div.complete-button').hide();
			}
			
			var now = (new Date().getTime())/1000;
			if(now > end){
				$('<span class="complete" >&nbsp;MILESTONE COMPLETE</span>').appendTo(ms.find('.title'));
			}
		};
		
		milestone.setMood = function(mood){
			ms.find('div.header > div.bulb').removeClass('positive great neutral bad negative').addClass(	
				bdff.mood(parseFloat((parseInt(mood)+80)/2))
			);
		};
		
		milestone.addAspect = function(maID){
			
			var aspect = { 'id' : maID };
			
			var dom = $('<div>').addClass('milestone-aspect').attr('milestone-aspect-data-id', maID);
	 
			$('\
			   <div class="bulb"></div>\
			   <div class="title">\
			   Taste\
			   </div>\
			   <div class="details">\
			   </div>\
			   <div class="clear">\
			   <i class="fa fa-times"></i>\
			   </div>\
			   ').appendTo(dom);
			
			dom.find('div.clear > i').click(function(){
				dom.fadeOut(100, function(){ $(this).remove(); });
				$.post('/api/v1/milestones/aspect', { 'store' : bdff.storeID(), 'delete' : true, 'id' : id, 'aid' : maID, 'localtime' : Math.ceil(((new Date()).getTime()/1000)) }, function(data){
					if(data.error && data.error.length > 0){
						bdff.notify(data.error[0], '', 'error');
					} else {
						face.startHooks();
					}
				});
			});
			
			aspect.setTitle = function(title){
				dom.find('.title').text(title);
			};
			
			aspect.setDetails = function(change, responses){
				if(responses == 0){
					dom.find('.details').text("no responses");
				} else {
					var sign = parseInt(change) == 0 ? '' : parseInt(change) > 0 ? '+' : '-';
					if(parseInt(change)==0){
						dom.find('.details').text('no change after ' + responses + ' response' + (parseInt(responses)>1 ? 's' : ''));
					} else {
						dom.find('.details').text(sign+Math.abs(change)+'%' + ' after ' + responses + ' response' + (parseInt(responses)>1 ? 's' : ''));
					}
				}
				
				dom.find('div.bulb')
				.removeClass('positive great neutral bad negative')
				.addClass(bdff.mood(parseFloat(
					(parseInt(change)+80)/2
				)));
			};
			
			dom.appendTo(ms.find('.body'));
			
			return aspect;
		};
		
		milestone.remove = function(){
			ms.remove();
		};

		/* Events */
		ms.find('.header').click(function () {
			ms.find('.body, .add').toggle(100);
		});
		
		return milestone;
	}
	
	render(canvas);
	
	face.datahook(0, {
			url : '/api/v1/milestones/list',
			data : { 'store' : bdff.storeID() }
		}, function(data){
		if(data.hasOwnProperty('error') && data.error.length > 0){
			bdff.log('Uh oh...');
		} else if(data.hasOwnProperty('milestones')) {
			
			var processData = function(data){
				if(data.aspects){
					aspects = data.aspects;
				}
				
				var ids = [];
				
				for(var i = 0; i < data.milestones.length; i++){
					var milestone;
					if(milestones.hasOwnProperty(data.milestones[i].id)){
						milestone = milestones[data.milestones[i].id];
					} else {
						milestone = renderMilestone(data.milestones[i].id);
						milestones[data.milestones[i].id] = milestone;
					}
					
					ids.push(data.milestones[i].id);
					
					milestone.setMood(data.milestones[i].mood)
					milestone.setTitle(data.milestones[i].title);
					milestone.setDate(data.milestones[i].date.start, data.milestones[i].date.end);
					milestone.setCompletion(data.milestones[i].date.end);
					
					for(var j = 0; j < data.milestones[i].aspects.length; j++){
						var aspect;
						if(milestone.aspects.hasOwnProperty(data.milestones[i].aspects[j].id)){
							aspect = milestone.aspects[data.milestones[i].aspects[j].id];
						} else {
							aspect = milestone.addAspect(data.milestones[i].aspects[j].id);
							milestone.aspects[data.milestones[i].aspects[j].id] = aspect;
						}
						
						aspect.setTitle(data.milestones[i].aspects[j].title);
						aspect.setDetails(data.milestones[i].aspects[j].change, data.milestones[i].aspects[j].responses);
					}
					
					$('div[data-tooltip]').brevadaTooltip();
				}
				
				/* Check deletes. */
				var keys = Object.keys(milestones);
				for(var i = 0; i < keys.length; i++){
					var key = parseInt(keys[i]);
					if($.inArray(key, ids) < 0){
						milestones[key].remove();
						delete milestones[key];
					}
				}
			};
			
			if(canvas.find('.full-loader').length > 0){
				canvas.find('.full-loader').fadeOut(10, function(){
					processData(data);
					$(this).remove();
				});
			} else {
				processData(data);
			}
			
		} else {
			bdff.log('Uh oh...');
		}
	});	
});