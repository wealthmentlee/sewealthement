/* $Id: tabs.js 2010-08-02 16:02 idris $ */

var HE_Tabs = new Class({
	Implements: [Options],
	
	options: {
		width: 0, 
		height: 0,
		container: '',
		tabs: '',
		content: '',
		element: '',
		tab_fx: {
			_selected: 'selected',
			_default: 'default' 
		},
		content_trans_duration: 500,
		tab_trans_duration: 400,
		interval: 3000,
		enable_auto: true
	},
	
	currentTab: 0,
	auto: true,
	timer: null,
  
	initialize: function(options){
		var self = this;
		
		this.setOptions(options);
		this.container = $(this.options.container);
		this.currentTab = 0;
		this.tabs = $(document.body).getElements(this.options.tabs);
		this.contents = $(this.options.content).getElements(this.options.element);
		this.hide = {
			'opacity': 0,
			'display': 'none'
		};
		this.show = {
			'display': 'block'
		};
		
		this.content = $(this.options.content);
		this.content.set('tween', {duration: self.options.content_trans_duration, transition: 'back:out', wait: false});
		
		var tab_width = ( this.options.width / this.tabs.length );
		var tab_height = 40;
		
		this.container.setStyle('width', this.options.width);
		this.container.setStyle('height', this.options.height + 62);
    
		this.content.setStyle('width', this.options.width);
		this.content.setStyle('height', this.options.height);

		this.tabs.setStyle('width', tab_width - 10);
		$$(this.options.tabs + ' img').setStyle('width', tab_width - 10);
//		$$(this.options.tabs + ' img').setStyle('height', 50);
    
		this.contents.setStyle('width', this.options.width);
		this.contents.setStyle('height', this.options.height);
		
		if (this.options.enable_auto){
			setTimeout(function(){self.auto_change()}, this.options.interval);
		}
    
		this.tabs.each(function(el, i){
			el.set('tween', {duration: self.options.tab_trans_duration, transition: 'sine:out', wait: false});
			
			if (el.hasClass(self.options.tab_fx._selected)){
				this.currentTab = i;
				self.show_tab(el);
			}
			
			el.addEvents({
				'mouseover': function(){
					if (!el.hasClass(self.options.tab_fx._selected))
					{
						self.show_tab(el);
						if (self.options.enable_auto){
							self.auto = false;
						}
					}
				},
				'mouseout': function(){
					if (!el.hasClass(self.options.tab_fx._selected))
					{
						self.shade_tab(el);
					}
					if (self.options.enable_auto){
						self.auto = true;
					}
				},
				'click': function(){
					if (!el.hasClass(self.options.tab_fx._selected))
					{
						self.select_tab(el, i);
						if (self.options.enable_auto){
							self.auto = false;
						}
					}
				}
			});
		});
	},

  auto_change: function()
  {
    var self = this;
		if (this.auto && this.options.enable_auto) this.timer = setInterval(function(){self._auto_change();}, self.options.interval);
  },

  _auto_change: function()
  {
    if (!this.auto){
      return false;
    }
    var i = this.currentTab;
    this.shade_tab(this.tabs[i]);
    if (i == (this.tabs.length - 1)){
      i = -1;
    }
    this.show_tab(this.tabs[i+1]);
    this.select_tab(this.tabs[i+1], i+1);
  },
	
	set_tab_fx: function(tab)
	{
		var self = this;
		tab.set('tween', {duration: self.options.tab_trans_duration, transition: 'sine:out', wait: false});
		return tab;
	},
	
	show_tab: function(tab)
	{
		tab.tween('opacity', [0.4, 1]);
	},
	
	shade_tab: function(tab)
	{
		var self = this;
		tab.tween('opacity', [1, 0.4]);
	},
	
	select_tab: function(tab, i)
	{
		var self = this;
		var selector = "."+self.options.tab_fx._selected;
		
		$$(selector).each(function(node, i){
			self.shade_tab(node);
			node.removeClass(self.options.tab_fx._selected);
		});
		tab.addClass(self.options.tab_fx._selected);
		
		this.contents.each(function(node, i){
			self.shade_tab(node);
			node.setStyle('display', 'none');
		});
		self.show_tab(this.contents[i]);
		this.contents[i].setStyle('display', 'block');
    self.currentTab = i;
	}
});