var MyTools = {
	start_safariWebAppClicker : function () {// Код предназначен для правильного открывания ссылок через Safari Web App
		(function(a,b) {
			var d,e=a.location,f=/^(a|html)$/i;
			a.addEventListener("click",function(a){
				d=a.target;
				while(!f.test(d.nodeName))d=d.parentNode;
				if(d.href !== undefined && d.href.trim() !== "") {
				("href" in d) && (d.href.toLowerCase().indexOf("http") || ~d.href.toLowerCase().indexOf(e.host.toLowerCase())) && (a.preventDefault(),e.href=d.href);
				}
			},!1);
		})(document,window.navigator);
	},
	rollMoreListeners : function () { // Сворачивать блоки
		$('.roll-more').not('.opened').each(function(key,val){ $(val).closest( "div.information" ).find('.more').first().hide() });
		$('.roll-more.opened').each(function(key,val){ $(val).closest( "div.information" ).find('.more').first().show() });
		$('.roll-more').not('.found').click(function() {
			var form = $( this ).closest( "div.information" );
			if($(this).hasClass('opened')) {
				form.find('.more').first().slideUp(500);
				$(this).removeClass('opened');
			} else {
				form.find('.more').first().slideDown(500);
				$(this).addClass('opened');
			}
		}).addClass("found");
	},
	fillBlock : function (block,area) { // Вписать блок в рамки окна
		block.style.height = area.innerHeight - parseInt(block.getBoundingClientRect().top) -20 +"px";
		block.style.width = area.innerWidth - parseInt(block.getBoundingClientRect().left) -20 +"px";
	},
	isFunction : function (functionToCheck) {
		var getType = {};
		return functionToCheck && getType.toString.call(functionToCheck) === '[object Function]';
	},
	encode_utf8 : function ( s ) { // из windows-1251 (cp1251) в utf-8
		return unescape( encodeURIComponent( s ) );
	},
	decode_utf8 : function ( s ) { // из utf-8 в windows-1251 (cp1251)
		return decodeURIComponent( escape( s ) );
	},
	ajax : function (obj) {
		var xhr = new XMLHttpRequest();
		var method = ("method" in obj) ? obj.method :"GET";
		var dataString = "";
		for(var name in obj.data) {
			if(dataString != "") dataString += "&";
			dataString += ( encodeURIComponent(name)+"="+encodeURIComponent(obj.data[name]) );
		}
		var body = "";
		var params = "";
		if(method.toLowerCase() == "get" && dataString != "") {
			if(obj.url.indexOf('?') < 0) params = "?"+dataString;
			else params = "&"+dataString;
		} else {
			body = dataString;
		}
		xhr.open(method, (obj.url+params), false);
		//xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
		if(method.toLowerCase() == "post") {
			xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xhr.setRequestHeader("Accept", "*/*");
		}
		xhr.onreadystatechange = function ()
		{
			switch(xhr.readyState) {
				case 4: // DONE
					try{
						if(xhr.status === 200 || xhr.status == 0)
						{
							if(typeof obj.success == 'function') {
								obj.success({
									data: (obj.dataType=="json" ? JSON.parse( xhr.responseText ) : xhr.responseText),
									status: xhr.status,
								});
							}
						} else {
							if(typeof obj.error == 'function') {
								obj.error({
									data: xhr.responseText,
									status: xhr.status,
								});
							}
						}
					} catch(e) {}
					if(typeof obj.complete == 'function') {
						obj.complete();
					}
					break;
			}
		}
		xhr.send(body);
		/*
		ajax({
			url:"http://localhost",
			data: {period:'week'},
			method:"GET",
			success:function(response){ console.log(response.data); }
		})
		*/
	},
	trim: function (s, c) {
	if (c === "]") c = "\\]";
	if (c === "\\") c = "\\\\";
	return s.replace(new RegExp(
		"^[" + c + "]+|[" + c + "]+$", "g"
	), "");
	},
	serialize: function (form) {
		var obj = {};
		var elements = form.querySelectorAll( "input, select, textarea" );
		for( var i = 0; i < elements.length; ++i ) {
			var element = elements[i];
			var name = element.name;
			var value = element.value;
			if( name ) {
				if(element.getAttribute("type") == "radio" || element.getAttribute("type") == "checkbox") {
					if(element.checked) {
						obj[ name ] = value;
					}
				}
				else {
					obj[ name ] = value;
				}
			}
		}
		return obj;
	},
	xmlNodeToText : function (xmlNode) {
		var xmlText = (new XMLSerializer()).serializeToString(xmlNode);
		return xmlText;
	},
	newBlob : function (data, mimeString) {
		try {
			return new Blob([data], {type: mimeString});
		} catch (e) {
			// The BlobBuilder API has been deprecated in favour of Blob, but older
			// browsers don't know about the Blob constructor
			// IE10 also supports BlobBuilder, but since the `Blob` constructor
			//  also works, there's no need to add `MSBlobBuilder`.
			var BlobBuilder = window.WebKitBlobBuilder || window.MozBlobBuilder || window.BlobBuilder;
			var bb = new BlobBuilder();
			bb.append(data);
			return bb.getBlob(mimeString);
		}
	},
	writeBlobToFile : function (blob, file) {
		file.entry.createWriter(function(fileWriter) {
			fileWriter.onwriteend = function(e) {
				console.log('Write completed.');
			};
			fileWriter.onerror = function(e) {
				console.log('Write failed: ' + e.toString());
			};
			fileWriter.write(blob);
		}, function(e){console.error(e);} );
	},
	nextTabindex : function (el) {
		var frm = el.form;
		for (var i = 0; i < frm.elements.length; i++) {
			if (frm.elements[i].tabIndex == el.tabIndex + 1) {
				frm.elements[i].focus();
			}
		}
	},
	addResizeendEventListener : function (element, action) {
		var event; // The custom event that will be created
		if (document.createEvent) {
			event = document.createEvent("HTMLEvents");
			event.initEvent("resizeend", true, true);
		} else {
			event = document.createEventObject();
			event.eventType = "resizeend";
		}
		event.eventName = "resizeend";
		var rtime;
		var timeout = false;
		var delta = 50;
		function resizeend() {
			if (new Date() - rtime < delta) {
				setTimeout(resizeend, delta);
			} else {
				timeout = false;
				if(typeof action == "function") {
					action(event);
				} else {
					if (document.createEvent) {
						element.dispatchEvent(event);
					} else {
					  element.fireEvent("on" + event.eventType, event);
					}
				}
			}
		}
		element.addEventListener("resize",function() {
			rtime = new Date();
			if (timeout === false) {
				timeout = true;
				setTimeout(resizeend, delta);
			}
		});
	},
	event_personal : new (function() {
		var self = this;
		this.add = function (element, event, listener){
			if (element.addEventListener) {
				element.addEventListener(event, listener, false);
			} else if (element.attachEvent)  {
				element.attachEvent("on"+event, listener); }
		};
		this.remove = function (element, event, listener){
			if (element.removeEventListener) {
				element.removeEventListener( event, listener );
			} else if (element.detachEvent)  {
				element.detachEvent("on"+event, listener); }
		};
		this.fireEvent = function(element,eventName,params) {
			if (document.createEventObject)
			{
				// Создаем объект событие (для IE не обязательно, но полезно знать, чтоб
				// передавать "синтетические" свойства события обработчику(ам)):
				var evt = document.createEventObject();
				for(var key in params) { evt[key] = params[key]; }
				// Запускаем событие на элементе:
				element.fireEvent(eventName, evt);
			} else if (document.createEvent) {
				// Создаем объект событие:
				var evt = document.createEvent("HTMLEvents");
				for(var key in params) { evt[key] = params[key]; }
				// Инициализируем:
				evt.initEvent(eventName, false, false);
				// Запускаем на элементе:
				element.dispatchEvent(evt);
			} else {
				return false;
			}
		}
		this.AET = function (element, listener){ // Устанавливает обработчик для CSS события TransitionEnd
			self.add( element, "webkitTransitionEnd", listener );
			self.add( element, "transitionend", listener );
			self.add( element, "msTransitionEnd", listener );
			self.add( element, "oTransitionEnd", listener );
		};
		this.RET = function (element, listener){ // Удаляет обработчик для CSS события TransitionEnd
			self.remove( element, "webkitTransitionEnd", listener );
			self.remove( element, "transitionend", listener );
			self.remove( element, "msTransitionEnd", listener );
			self.remove( element, "oTransitionEnd", listener );
		};
	})(),
	getObjectClass : function (obj) {
		if (typeof obj === "undefined")
			return "undefined";
		if (obj === null)
			return "null";
		if (obj.constructor) {
			if(obj.constructor.name) {
				return obj.constructor.name;
			}
			if(obj.constructor.toString) {
				var arr = obj.constructor.toString().match(/function\s*(\w+)/);
				if (arr && arr.length == 2) {
					return arr[1];
				}
			}
		}
		if (Object.prototype && Object.prototype.toString) {
			return Object.prototype.toString.call(obj)
				match(/^\[object\s(.*)\]$/)[1];
		}
		return undefined;
	},
	randomString : function (number) {
		if(number == undefined) number = 6;
		var text = "";
		var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
		for( var i=0; i < number; i++ )
			text += possible.charAt(Math.floor(Math.random() * possible.length));
		return text;
	},
	cookie: {
		create : function (name,value,days) {
			if (days) {
				var date = new Date();
				date.setTime(date.getTime()+(days*24*60*60*1000));
				var expires = "; expires="+date.toGMTString();
			}
			else var expires = "";
			document.cookie = name+"="+value+expires+"; path=/";
		},
		read : function (name) {
			var nameEQ = name + "=";
			var ca = document.cookie.split(';');
			for(var i=0;i < ca.length;i++) {
				var c = ca[i];
				while (c.charAt(0)==' ') c = c.substring(1,c.length);
				if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
			}
			return null;
		},
		erase : function (name) {
			this.create(name,"",-1);
		}
	},
	getRandomColor:	function () {
			if(this.number == undefined) this.number = 0.23387129164964926;
		    var letters = '0123456789ABCDEF';
		    var color = "";
		    for (var i = 0; i < 6; i++ ) {
					this.number = (this.number+0.9096207405260299);
					this.number = this.number - parseInt(this.number);
	        color += letters[parseInt(this.number*1000)%16];
		    }
		    return '#'+color;
	},
	formatMoney: function(n, c, d, t) {
	  var c = isNaN(c = Math.abs(c)) ? 2 : c,
	    d = d == undefined ? "." : d,
	    t = t == undefined ? "," : t,
	    s = n < 0 ? "-" : "",
	    i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))),
	    j = (j = i.length) > 3 ? j % 3 : 0;

	  return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
	},
	updateURLParameter: function (url, param, paramVal){
		var updatedUrl= url.substring(0, url.lastIndexOf("#"));
		var newAdditionalURL = "";
		var tempArray = updatedUrl.split("?");
		var baseURL = tempArray[0];
		var additionalURL = tempArray[1];
		var temp = "";
		if (additionalURL) {
			tempArray = additionalURL.split("&");
			for (var i=0; i<tempArray.length; i++){
				if(tempArray[i].split('=')[0] != param){
					newAdditionalURL += temp + tempArray[i];
					temp = "&";
				}
			}
		}

		var rows_txt = temp + "" + param + "=" + paramVal;
		return baseURL + "?" + newAdditionalURL + rows_txt;
	}
};
try {
	module.exports = MyTools;
} catch(e) {}
