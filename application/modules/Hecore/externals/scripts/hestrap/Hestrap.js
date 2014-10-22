/*
---

name: Hestrap

description: The BootStrap namespace.

authors: [Aaron Newton]

license: MIT-style license.

provides: [Hestrap]

...
*/
var Hestrap = {
	version: 3,
  components: {
    dropdown: null,
    tab: null
  },
  init: function(){
    this.components.dropdown = new Hestrap.Dropdown(document.body);
    this.components.tab = new Hestrap.Tab(document.body);
  }
};
document.addEvent('domready', function(){
  Hestrap.init();
});