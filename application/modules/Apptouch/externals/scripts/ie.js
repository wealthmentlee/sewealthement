if(!window.JSON){
  window.JSON = {
    stringify: function (obj){
      if (obj && obj.toJSON) obj = obj.toJSON();
      var objType = typeof (obj);
      if(Object.prototype.toString.call(objType) == '[object Array]')
        objType = 'array';
     	switch (objType){
     		case 'string':
     			return '"' + obj.replace(/[\x00-\x1f\\"]/g, escape) + '"';
     		case 'array':
          var string = [];
          for(var i = 0; i < obj.length; i ++){
           var value = obj[key];
            var json = JSON.stringify(value);
            if (json) string.push(json);
          }
     			return '[' + string + ']';
     		case 'object':
     			var string = [];
           for(var key in obj){
            var value = obj[key];
     				var json = JSON.stringify(value);
     				if (json) string.push(JSON.stringify(key) + ':' + json);
     			}
     			return '{' + string + '}';
     		case 'number': case 'boolean': return '' + obj;
     		case 'null': return 'null';
     	}

     	return null;
    }
  };
}
if ( !window.Element )
{
  Element = function(){
  };

  var __IEcreateElement = document.createElement;
  document.createElement = function (tagName) {
    var element =
      __IEcreateElement(tagName);
    var interface = new Element();
    for (var method in interface)
      element[method]
        = interface[method];
    return element;
  }

  var __IEgetElementById = document.getElementById
  document.getElementById = function(id)
  {
    var element =
      __IEgetElementById(id);
    var interface = new Element();
    if(!element)
      return element;
      for (var method in interface){
        element[method]
          = interface[method];
      }
    return element;
  }
}
if(!Element.prototype.addEventListener){
  Element.prototype.addEventListener = function (eventName, eventHandler){
    this.attachEvent('on'+eventName, eventHandler);
  }
}

if(!Element.prototype.querySelector){
  Element.prototype.querySelector = function(selectorString){
//    alert(selectorString);
    return $(selectorString)[0];
  }
}
if(!Element.prototype.querySelectorAll){
  Element.prototype.querySelectorAll = function(selectorString){
//    alert(selectorString);
    return $(selectorString);
  }
}
if(!Element.prototype.removeEventListener){
  Element.prototype.removeEventListener = function(type,listener,useCapture) {
    var iefn = function() { listener.call(this) };
    this.detachEvent('on' + ev, iefn)
  };
}
