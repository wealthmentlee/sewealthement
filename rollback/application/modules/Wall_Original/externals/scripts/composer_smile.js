/* $Id: composer_smile.js 18.06.12 10:52 michael $ */



Wall.Composer.Plugin.Smile = new Class({

  Extends : Wall.Composer.Plugin.Interface,

  name : 'smile',

  options : {
    smiles: {}
  },
  container: null,

  initialize : function(options) {
    this.elements = new Hash(this.elements);
    this.params = new Hash(this.params);
    this.parent(options);
  },

  attach : function() {
    this.parent();
    this.makeActivator();
    return this;
  },

  detach : function() {
    this.parent();
    return this;
  },

  activate : function()
  {
    if (this.container){
      this.container.destroy();
      this.container = null;
      return ;
    }


    var create_function = function ()
    {
      var self = this;

      var link = this.getComposer().container.getElement('.wall-compose-smile-activator');
      var container = new Element('div', {'class': 'wall-smile-container', 'html': '<div class="wall_data"></div>'});
      this.container = container = Wall.injectAbsolute(link, container, true);

      var arrow = new Element('div', {'class': 'wall_arrow_container', 'html': '<div class="wall_arrow"></div>'});
      arrow.inject(container, 'top');

      var ul = new Element('ul');

      for (var i=0;i<this.options.smiles.length; i++){
        var item = this.options.smiles[i];
        var a = new Element('a', {'title': item.title, 'href': 'javascript:void(0)', 'html': item.html, 'rev': item.index_tag});
        var li = new Element('li', {});
        a.inject(li);
        li.inject(ul);

        a.addEvent('click', function (){
          self.getComposer().editor.setContent(self.getComposer().editor.getContent()+'&nbsp;'+$(this).get('rev')+'&nbsp;');
          self.getComposer().editor.moveCaretToEnd();

        });
      }

      ul.inject(container.getElement('.wall_data'));



    }.bind(this);

    if (this.getComposer().is_opened){
      create_function();
    } else {
      this.getComposer().open(create_function);
    }



  },

  deactivate : function (){
    if (this.container){
      this.container.destroy();
      this.container = null;
    }
    this.parent();
  },

  poll : function() {

  }


});


