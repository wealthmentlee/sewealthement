/*
---

name: Hestrap.Dropdown

description: A simple dropdown menu that works with the Twitter Hestrap css framework.

license: MIT-style license.

authors: [Aaron Newton]

requires:
 - /Hestrap
 - Core/Element.Event
 - More/Element.Shortcuts

provides: Hestrap.Dropdown

...
*/
Hestrap.Dropdown = new Class({

	Implements: [Options, Events],

	options: {
		/*
			onShow: function(element){},
			onHide: function(elements){},
		*/
		ignore: 'input, select, label'
	},

	initialize: function(container, options){
		this.element = document.id(container);
		this.setOptions(options);
		this.boundHandle = this._handle.bind(this);
		document.id(document.body).addEvent('click', this.boundHandle);
	},

	hideAll: function(){
		var els = this.element.removeClass('he-open').getElements('.he-open').removeClass('he-open');
		this.fireEvent('hide', els);
		return this;
	},

	show: function(subMenu){
		this.hideAll();
		this.fireEvent('show', subMenu);
		subMenu.addClass('he-open');
		return this;
	},

	destroy: function(){
		this.hideAll();
		document.body.removeEvent('click', this.boundHandle);
		return this;
	},

	// PRIVATE

	_handle: function(e){
		var el = e.target;
		var open = el.getParent('.he-open');
		if (!el.match(this.options.ignore) || !open) this.hideAll();
		if (this.element.contains(el)) {
			var parent;
			if (el.match('[data-toggle="dropdown"]') || el.getParent('[data-toggle="dropdown"] !')){
				var p = el.getParent();
        if(p.hasClass('he-dropdown') && p.hasClass('he-btn-group'))
  				parent = p;
			}
			// backwards compatibility
			if (!parent) parent = el.match('.he-dropdown-toggle') ? el.getParent() : el.getParent('.he-dropdown-toggle !');
			if (parent){
				e.preventDefault();
				if (!open) this.show(parent);
			}
		}
	}
});