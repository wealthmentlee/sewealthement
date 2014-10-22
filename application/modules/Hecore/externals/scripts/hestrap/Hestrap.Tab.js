/*
---

name: Hestrap.Tab

description: A simple dropdown menu that works with the Twitter Hestrap css framework.

license: MIT-style license.

authors: [Aaron Newton]

requires:
 - /Hestrap
 - Core/Element.Event
 - More/Element.Shortcuts

provides: Hestrap.Tab

...
*/
Hestrap.Tab = new Class({

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

	show: function(tab){
    var content = $(tab.getAttribute('href'));
    if(content) {
      content.getParent().getChildren().hide();
      tab.getParent('ul.he-nav-tabs').getElements('.he-active').removeClass('he-active');
      tab.getParent('li').addClass('he-active');
      var dropdown = tab.getParent('li.he-dropdown');
      if(dropdown) dropdown.addClass('he-active');
      tab.addClass('he-active');
      content.show();
    }
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
      if(el.tagName != 'A') {

        el = el.getParent('a');
      }
      if(el && el.getParent('.he-nav-tabs') && !el.hasClass('he-dropdown-toggle')) {
        e.preventDefault();
        this.show(el);
      }
	}
});