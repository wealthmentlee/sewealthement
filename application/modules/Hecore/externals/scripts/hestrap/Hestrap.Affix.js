/*
---

name: Hestrap.Affix

description: A MooTools implementation of Affix from Hestrap; allows you to peg an element to a fixed position after scrolling.

authors: [Aaron Newton]

license: MIT-style license.

requires:
 - Core/Element.Dimensions
 - More/Object.Extras
 - More/Element.Event.Pseudos
 - /Hestrap

provides: [Hestrap.Affix]

...
*/

Hestrap.Affix = new Class({

	Implements: [Options, Events],

	options: {
		// onPin: function(){},
		// onUnPin: function(isBottom){},
		// monitor: window,
		top: 0,
		bottom: null,
		classNames: {
			top: "affix-top",
			bottom: "affix-bottom",
			affixed: "affix"
		},
		affixAtElement: {
			top: {
				element: null,
				edge: 'top',
				offset: 0
			},
			bottom: {
				element: null,
				edge: 'bottom',
				offset: 0
			}
		},
		persist: null
	},

	initialize: function(element, options){
		this.element = document.id(element);
		this.setOptions(options);
		this.element.addClass(this.options.classNames.top);
		this.top = this.options.top;
		this.bottom = this.options.bottom;
		if (this.options.affixAtElement.top.element && !this.options.affixAtElement.bottom.element){
			this.options.affixAtElement.bottom.element = this.options.affixAtElement.top.element;
		}
		this.attach();
	},

	refresh: function(){
		['top', 'bottom'].each(function(edge){
			var offset = this._getEdgeOffset(edge);
			if (offset !== null) this[edge] = offset;
		}, this);
		return this;
	},

	_getEdgeOffset: function(edge){
		var options = this.options.affixAtElement[edge];
		if (options && options.element){
			var el = document.id(options.element);
			if (!el) return null;
			var top = el.getPosition(this.options.monitor == window ? document.body : this.options.monitor).y + options.offset;
			if (edge == 'top') top -= this.options.monitor.getSize().y;
			var height = el.getSize().y;
			switch(options.edge){
				case 'bottom':
					top += height;
					break;
				case 'middle':
					top += height/2;
					break;
			}
			return top;
		}
		return null;
	},

	attach: function(){
		this.refresh();
		Hestrap.Affix.register(this, this.options.monitor);
		return this;
	},

	detach: function(){
		Hestrap.Affix.drop(this, this.options.monitor);
		return this;
	},

	pinned: false,

	pin: function(){
		this.pinned = true;
		this._reset();
		this.element.addClass(this.options.classNames.affixed);
		this.fireEvent('pin');
		if (this.options.persist) this.detach();
		return this;
	},

	unpin: function(isBottom){
		if (this.options.persist) return;
		this._reset();
		this.element.addClass(this.options.classNames[isBottom ? 'bottom' : 'top']);
		this.pinned = false;
		this.fireEvent('unPin', [isBottom]);
		return this;
	},

	_reset: function(){
		this.element.removeClass(this.options.classNames.affixed)
								.removeClass(this.options.classNames.top)
								.removeClass(this.options.classNames.bottom);
		return this;
	}

});

Hestrap.Affix.instances = [];

Hestrap.Affix.register = function(instance, monitor){
	monitor = monitor || window;
	monitor.retrieve('Hestrap.Affix.registered', []).push(instance);
	if (!monitor.retrieve('Hestrap.Affix.attached')) Hestrap.Affix.attach(monitor);
	Hestrap.Affix.instances.include(instance);
	Hestrap.Affix.onScroll.apply(monitor);
};

Hestrap.Affix.drop = function(instance, monitor){
	monitor.retrieve('Hestrap.Affix.registered', []).erase(instance);
	if (monitor.retrieve('Hestrap.Affix.registered').length == 0) Hestrap.Affix.detach(monitor);
	Hestrap.Affix.instances.erase(instance);
};

Hestrap.Affix.attach = function(monitor){
	if (!Hestrap.Affix.attachedToWindowResize){
		Hestrap.Affix.attachedToWindowResize = true;
		window.addEvent('resize:throttle(250)', Hestrap.Affix.refresh);
	}
	monitor.addEvent('scroll', Hestrap.Affix.onScroll);
	monitor.store('Hestrap.Affix.attached', true);
};

Hestrap.Affix.detach = function(monitor){
	monitor = monitor || window;
	monitor.removeEvent('scroll', Hestrap.Affix.onScroll);
	monitor.store('Hestrap.Affix.attached', false);
};

Hestrap.Affix.refresh = function(){
	Hestrap.Affix.instances.each(function(instance){
		instance.refresh();
	});
};

Hestrap.Affix.onScroll = function(_y){
	var monitor = this,
			y = _y || monitor.getScroll().y,
			size = monitor.getSize().y;
	var registered = monitor.retrieve('Hestrap.Affix.registered');
	for (var i = registered.length - 1; i >= 0; i--){
		Hestrap.Affix.update(registered[i], y, size);
	}
};

Hestrap.Affix.update = function(instance, y, monitorSize){
	var bottom = instance.bottom,
	    top = instance.top;
	if (bottom && bottom < 0) bottom = monitorSize + bottom;

	// if we've scrolled above the top line, unpin
	if (y < top && instance.pinned) instance.unpin();
	// if we've scrolled past the bottom line, unpin
	else if (bottom && bottom < y && y > top && instance.pinned) instance.unpin(true);
	else if (y > top && (!bottom || (bottom && y < bottom)) && !instance.pinned) instance.pin();
};