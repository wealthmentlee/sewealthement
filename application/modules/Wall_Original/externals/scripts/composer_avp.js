/* $Id: composer_avp.js 18.06.12 10:52 michael $ */



Wall.Composer.Plugin.AVP = new Class({

  Extends : Wall.Composer.Plugin.Interface,

  name : 'avp',

  options :
  {
      title : 'Add Video',
      upload_title: 'Upload',
      import_title: 'Import',
      lang: {}
  },

  initialize : function(options) {
    this.elements = new Hash(this.elements);
    this.params = new Hash(this.params);
    this.parent(options);
  },

  attach : function() {
    this.parent();
    this.makeActivator();
    //this.elements.activator.addClass('smoothbox');
    //this.elements.activator.set('href', en4.core.basePath+'vids/choose');

    return this;
  },

  detach : function() {
    this.parent();
    return this;
  },

  activate : function() {
    if( this.active ) return;
    this.parent();

    this.makeMenu();
    this.makeBody();


    var self = this;

    if (this.options.upload_allowed)
    {
          this.elements.chooseUploadButton = new Element('a', {
            'id' : 'avp-feed-upload',
            'style' : 'display: inline;',
            'html' : this.options.upload_title,
            'href' : en4.core.basePath+'vids/feed-upload/?format=smoothbox',
            'class' : 'smoothbox'
          }).inject(this.elements.body);

          this.elements.chooseUploadButton.addEvent('click', function()
          {
            self.deactivate();
          });

          Smoothbox.bind('#avp-feed-upload');
    }

    if (this.options.import_allowed)
    {
          this.elements.chooseImportButton = new Element('a', {
            'id' : 'avp-feed-import',
            'style' : 'display: inline; margin-left: 10px;',
            'html' : this.options.import_title,
            'href' : en4.core.basePath+'vids/feed-import/?format=smoothbox',
            'class' : 'smoothbox'
          }).inject(this.elements.body);

          this.elements.chooseImportButton.addEvent('click', function()
          {
            self.deactivate();
          });

          Smoothbox.bind('#avp-feed-import');
    }
  },

  deactivate : function() {
    if( !this.active ) return;
    this.parent();

    this.request = false;
  }
});