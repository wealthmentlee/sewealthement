/*
---

name: Hestrap.Popover

description: A simple tooltip (yet larger than Hestrap.Tooltip) implementation that works with the Twitter Hestrap css framework.

authors: [Aaron Newton]

license: MIT-style license.

requires:
 - /Hestrap.Tooltip

provides: Hestrap.Popover

...
*/

Hestrap.Popover = new Class({

	Extends: Hestrap.Tooltip,

	options: {
		location: 'right',
		offset: Hestrap.version == 2 ? 10 : 0,
		getTitle: function(el){
			return el.get(this.options.title);
		},
		content: 'data-content',
		getContent: function(el){
			return el.get(this.options.content);
		}
	},

	_makeTip: function(){
		if (!this.tip){
			var title = this.options.getTitle.apply(this, [this.element]) || this.options.fallback;
			var content = this.options.getContent.apply(this, [this.element]);

			var inner = new Element('div.popover-inner');


			if (title) {
				var titleWrapper = new Element('h3.popover-title');
				if (typeOf(title) == "element") titleWrapper.adopt(title);
				else titleWrapper.set('html', title);
				inner.adopt(titleWrapper);
			} else {
				inner.addClass('no-title');
			}

			if (typeOf(content) != "element") content = new Element('p', { html: content});
			inner.adopt(new Element('div.popover-content').adopt(content));
			this.tip = new Element('div.popover').addClass(this.options.location)
				 .adopt(new Element('div.arrow'))
				 .adopt(inner);
			if (this.options.animate) this.tip.addClass('fade');
			if (Browser.Features.cssTransition && this.tip.addEventListener){
				this.tip.addEventListener(Browser.Features.transitionEnd, this.bound.complete);
			}
			this.element.set('alt', '').set('title', '');
		}
		return this.tip;
	}

});