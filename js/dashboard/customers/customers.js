/* Overall Customers Dashboard App */

var customers = {}; 

bdff.create('customers', function(canvas, face){	
	canvas.children().not('div.message-container').remove();
	
	canvas.append(
		$('<div>').addClass('full-loader').append(
			$('<div>').addClass('fa fa-spin fa-gear')
		)
	);
	
	customers = {};

	var outstandingDom = $('<div>').addClass('customers customers-outstanding col-xs-12').appendTo(canvas);
	outstandingDom
		.append($('<span>').addClass('header').html('Outstanding Responses <span class="badge"></span>'))
		.append($('<span>').addClass('subtitle').text("New customer responses are listed here until acknowledged, at which point they are moved to the 'acknowledged responses' section."));
		
	
	var acknowledgedDom = $('<div>').addClass('customers customers-acknowledged col-xs-12').appendTo(canvas);
	acknowledgedDom
		.append($('<span>').addClass('header').html('Acknowledged Responses <span class="badge"></span>'))
		.append($('<span>').addClass('subtitle').text("New customer responses are listed here until acknowledged, at which point they are moved to the 'acknowledged responses' section."));
		
	var renderCustomer = function(id, is_acknowledged){
		var customer = { 'id' : id };
		
		var destination = is_acknowledged ? acknowledgedDom : outstandingDom;
		
		var dom = $('<div>').addClass('customer').append(
			$('<div>').addClass('customer-top').append(
				$('<div>').addClass('device').append($('<i>').addClass('fa'))
			).append(
				$('<div>').addClass('details').append(
					$('<span>').addClass('customer-date')
				).append(
					$('<span>').addClass('customer-email')
				)
			).append(
				$('<div>').addClass('btn-aspect-view').append(
					$('<i>').addClass('fa fa-list')
				)
			)
		).append(
			$('<div>').addClass('customer-middle').append(
				$('<div>').addClass('aspects-overview').append(
					$('<div>').addClass('average-score').append(
						$('<div>').addClass('bulb')
					).append(
						$('<span>').addClass('label').text('Average Score')
					)
				).append(
					$('<div>').addClass('relative').append(
						$('<div>').addClass('bulb')
					).append(
						$('<span>').addClass('label').text('Benchmark')
					)
				).append(
					$('<div>').addClass('lowest-score').append(
						$('<div>').addClass('bulb')
					).append(
						$('<span>').addClass('label').text('Lowest Score')
					)
				)
			).append(
				$('<div>').addClass('aspect-list')
			)
		).append(
			$('<div>').addClass('customer-bottom').append(
				$('<div>').attr('data-customer-id', id).addClass('btn-acknowledge').text('Acknowledge')
			)
		);
		
		if (is_acknowledged){
			dom.addClass('acknowledged');
		}
		
		dom.appendTo(destination);
		
		dom.find('.btn-aspect-view').click(function(){
			if (dom.find('.aspects-overview').is(':visible')){
				$(this).children('i').removeClass('fa-list').addClass('fa-arrow-left');
				dom.find('.aspects-overview').slideUp();
				dom.find('.aspect-list').slideDown();
			} else {
				$(this).children('i').removeClass('fa-arrow-left').addClass('fa-list');
				dom.find('.aspects-overview').slideDown();
				dom.find('.aspect-list').slideUp();
			}
		});
		
		dom.find('.btn-acknowledge').click(function(){
			$.post('/api/v1/customers/acknowledge', { 'store' : bdff.storeID(), 'id' : $(this).attr('data-customer-id'), 'localtime' : Math.ceil(((new Date()).getTime()/1000)) }, function(data){
				if(data.error && data.error.length > 0){
					bdff.notify(data.error[0], '', 'error');
				}
			});
			customer.setAcknowledged(true);
		});
		
		customer.setDevice = function(device){
			dom.find('div.customer-top > div.device > i')
				.removeClass('fa-tablet fa-desktop fa-mobile')
				.addClass('fa-'+device);
		};
		
		customer.setDate = function(cdate){
			dom.find('div.customer-top > div > span.customer-date').text(moment(cdate*1000).format('dddd MMMM Do, YYYY'));
		};
		
		customer.setEmail = function(email){
			dom.find('div.customer-top > div > span.customer-email').text(email);
		};
		
		customer.setAcknowledged = function(ack){
			var currentlyAck = !dom.parent().is(outstandingDom);
			if (currentlyAck != ack){
				dom.detach().insertAfter((ack ? acknowledgedDom : outstandingDom).children('span.subtitle')).addClass('acknowledged');
			}
		};
		
		customer.setAverageScore = function(val){		
			dom.find('div.customer-middle > div.aspects-overview > div.average-score > div.bulb').removeClass('good positive neutral bad negative').addClass(val !== false ? bdff.mood(val) : bdff.mood(20)).text(val !== false ? Math.round(val) + '%' : 'N/A');
		};
		
		customer.setRelative = function(val){	
			var sign = val == 0 ? '' : val > 0 ? '+' : '-';
			dom.find('div.customer-middle > div.aspects-overview > div.relative > div.bulb').removeClass('good positive neutral bad negative').addClass(val !== false ? bdff.mood((parseFloat(val)+100.0)/2) : bdff.mood(20)).text(val !== false ? sign + Math.round(Math.abs(val))+'%' : 'N/A');
		};
		
		customer.setLowestScore = function(val){		
			dom.find('div.customer-middle > div.aspects-overview > div.lowest-score > div.bulb').removeClass('good positive neutral bad negative').addClass(val !== false ? bdff.mood(val) : bdff.mood(20)).text(val !== false ? Math.round(val) + '%' : 'N/A');
		};
		
		customer.addAspect = function(percent, title){
			// Check if exists.
			var list = dom.find('div.customer-middle > div.aspect-list');
			var found = false;
			list.children('div.customer-aspect').each(function(){
				if ($(this).children('span.customer-aspect-title').text() == title ||
					$(this).children('span.customer-aspect-title').attr('title') == title){
					found = true;
					return false;
				}
			});
			
			if (!found){
				var aspect = $('<div>').addClass('customer-aspect');
				aspect.append($('<div>').addClass('bulb ' + bdff.mood(percent)).text(Math.round(percent) + '%'));
				aspect.append($('<span>').addClass('customer-aspect-title').text(title.length > 28 ? title.substring(0, 26) + '...' : title).attr('title', title));
				aspect.hide().prependTo(list).slideDown();
			}
		};
		
		return customer;
	};
	
	face.datahook(15000, {
			url : '/api/v1/customers/list',
			data : { 'store' : bdff.storeID() }
		}, function(data){
		if(data.hasOwnProperty('error') && data.error.length > 0){
			bdff.log('Uh oh...');
		} else if(data.hasOwnProperty('customers')) {
			var processData = function(data){
				var ids = [];
				for(var i = 0; i < data.customers.length; i++){
					var customer;
					if(customers.hasOwnProperty(data.customers[i].id)){
						customer = customers[data.customers[i].id];
					} else {
						customer = renderCustomer(data.customers[i].id, data.customers[i].acknowledged);
						customers[data.customers[i].id] = customer;
					}
					
					ids.push(data.customers[i].id);
					
					customer.setDevice(data.customers[i].device);
					customer.setDate(data.customers[i].date);
					customer.setEmail(data.customers[i].email);
					
					customer.setAverageScore(data.customers[i].average);
					customer.setRelative(data.customers[i].relative);
					customer.setLowestScore(data.customers[i].lowest);
					
					for (var j = 0; j < data.customers[i].aspects.length; j++){
						customer.addAspect(data.customers[i].aspects[j].percent, data.customers[i].aspects[j].title);
					}
					
					customer.setAcknowledged(data.customers[i].acknowledged);
					
					if(!customer.data){ customer.data = {}; }
					
					
					$('[data-tooltip]').each(function(){
						$(this).brevadaTooltip();
					});
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