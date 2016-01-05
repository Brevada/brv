/*
	Brevada Dashboard Frontend Framework (BDFF)
*/

(function(bdff, $, undefined){
	
	var version = '0.0.1';
	var debug = true;
	var settings = {
		face : undefined,
		canvas : undefined,
		apiUrl : '/api/v1/bdff',
		fetchTimeout : 60000
	};
	
	bdff.faces = {};
	
	bdff.callbacks = {
		'rendered' : undefined
	};
	
	bdff.log = function(msg){
		if(!debug){ return; }
		console.log('BDFF, ' + version + ' @ ' + new Date().toLocaleString() + ': ' + msg);
	};
	
	bdff.canvas = function(canvas){
		if(canvas !== undefined){
			if(typeof canvas === 'string'){
				settings.canvas = $(canvas);
			} else {
				settings.canvas = canvas;
			}
		}
		return settings.canvas;
	};
	
	bdff.face = function(face){
		if(typeof face === 'string'){
			face = bdff.faces[face];
		}
		
		if(face !== undefined && settings.face !== undefined){
			if(settings.face === face){
				return;
			}
			settings.face.cleanUp();
		}
		
		if (face !== undefined){
			settings.face = face;
		}
		
		if(!settings.face.hasOwnProperty('render')){
			bdff.log('Invalid face: ' + face);
		} else {
			settings.face.render(settings.canvas);
			settings.face.startHooks();
		}
	};
	
	bdff.persistent = function(face){
		if(typeof face === 'string'){
			face = bdff.faces[face];
		}
		
		if(!face.hasOwnProperty('render')){
			bdff.log('Invalid face.');
		} else {
			face.render(settings.canvas);
			face.startHooks();
		}
	};

	var startDatahook = function(datahook){
		if(datahook.active){
			datahook.timer = setTimeout(function(){
				bdff.fetch(datahook);
			}, datahook.interval);
		}
	};
	
	bdff.create = function(label, render){
		var face = {
			'label' : label,
			'canvas' : undefined,
			'datahooks' : []
		};
		
		face.attach = function(canvas){
			canvas = canvas || bdff.canvas();
			if(typeof canvas === 'string'){
				canvas = $(canvas);
			}
			return face.canvas = canvas;
		};
		
		face.render = function(){
			render(face.canvas || bdff.canvas(), face);
			if(bdff.callbacks.rendered !== undefined){
				bdff.callbacks.rendered(face);
			}
		};
		
		face.datahook = function(interval, request, available){
			var hook = {
				'interval' : interval,
				'timer' : undefined,
				'active' : false,
				'request' : request,
				'available' : available
			};
			face.datahooks.push(hook);
		};
		
		face.startHooks = function(){
			for(var i = 0; i < face.datahooks.length; i++){
				var dh = face.datahooks[i];
				clearTimeout(dh.timer);
				dh.active = true;
				
				dh.timer = setTimeout(function(){
					bdff.fetch(dh);
				}, 1);
			}
		};
		
		face.stopHooks = function(){
			for(var i = 0; i < face.datahooks.length; i++){
				var dh = face.datahooks[i];
				dh.active = false;
				clearTimeout(dh.timer);
			}
		};
		
		face.cleanUp = function(){
			face.stopHooks();
			face.datahooks = [];
		};
		
		return bdff.faces[face.label] = face;
	};
	
	bdff.fetch = function(datahook){
		if(datahook.active && datahook.hasOwnProperty('request')){
			$.ajax({
				url: datahook.request.url || settings.apiUrl,
				dataType: 'json',
				method: 'GET',
				timeout: datahook.request.timeout || settings.fetchTimeout,
				data: $.extend({ 'localtime' : Math.floor((new Date()).getTime()/1000) }, datahook.request.data)
			}).done(function(resp){
				if(datahook.hasOwnProperty('available')){
					datahook.available(resp);
				}
				if(datahook.interval > 0){
					startDatahook(datahook);
				}
			}).fail(function(jqXHR, textStatus){
				bdff.log(textStatus);
				if(datahook.interval > 0){
					startDatahook(datahook);
				}
			});
		}
	};
	
	bdff.notify = function(message, type){
		$("<div>").addClass('alert').addClass(type).prependTo($('#alert-holder'));
		setTimeout(function(){
			$('#alert-holder > div.alert:not(.fading)').first().addClass('fading').fadeOut(function(){
				$(this).remove();
			}, 500);
		}, 3000);
	};
	
	bdff.mood = function mood(val){
		var moods = ['positive', 'great', 'neutral', 'bad', 'negative'];
		var i = Math.ceil((100-val)/20);
		return i == 0 ? moods[0] : moods[i -  1];
	};
	
	bdff.tickerIcon = function(val){
		if(val == null){
			return 'fa-times-circle';
		}
		return val == 0 ? 'fa-minus-circle' : (val >= 50 ? 'fa-arrow-circle-up' : 'fa-arrow-circle-down');
	};
	
	bdff.storeID = function(id){
		if(id){
			settings.storeID = id;
		}
		
		if(settings.hasOwnProperty('storeID')){
			return settings.storeID;
		} else {
			return undefined;
		}
	};
	
})( window.bdff = window.bdff || {}, jQuery );