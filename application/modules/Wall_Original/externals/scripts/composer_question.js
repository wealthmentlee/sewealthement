/* $Id: composer_question.js 18.06.12 10:52 michael $ */

Wall.Composer.Plugin.Question = new Class({

  Extends : Wall.Composer.Plugin.Interface,

  name : 'question',

  options : {
    title : 'Ask',
    lang : {},
    requestOptions : {getFormURL : en4.core.baseUrl + 'question/wall/getform'},
    fancyUploadEnabled : false,
    fancyUploadOptions : {}
  },

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

  activate : function() {
    if( this.active ) return;
    this.parent();

    this.makeMenu();
    this.makeBody();

    this.getComposer().container.hide();
    this.makeLoading();
    var bind = this;

    new Request.HTML({
        method: 'get',
        url: this.options.requestOptions.getFormURL,
        update:this.elements.cnew,
        onComplete:function(){
                bind.initform();
            }
        }
    ).send();
  },
  initform : function () {
    var form = this.elements.cnew.getFirst('form');
    form.addEvent('submit', function (){return false;})
    var form_request = new Form.Request(form, this.elements.cnew);
    form_request.addEvent('success', function() {
                                                this.initform();
                                              }.bind(this));
    form_request.addEvent('send', function() {
                                        this.elements.loading = null;
                                        this.makeLoading();
                                        this.elements.loading.replaces(form.getElement('button[id=submit]'));
                                       }.bind(this));
  },
  deactivate : function() {
    if( !this.active ) return;
    this.getComposer().container.show();

    this.parent();
  },

  makeNew: function ()
  {
    if (!this.elements.cnew){
      this.elements.cnew = new Element('div', {'class': 'wall-compose-new'});
      this.elements.cnew.inject(this.getComposer().container, 'after');
    }
  },

  getNew: function ()
  {
    return this.elements.cnew;
  },



  makeBody : function() {
    if( !this.elements.body ) {

      this.makeNew();

      this.elements.body = new Element('div', {
        'class' : 'wall-compose-body wall-compose-' + this.getName() + '-body'
      }).inject(this.getNew());


    }
  },

  makeMenu : function() {
    if( !this.elements.menu ) {

      this.makeNew();
      var tray = this.getNew();

      this.elements.menu = new Element('div', {
        'class' : 'wall-compose-container wall-compose-tray-headline  wall-compose-' + this.getName() + '-menu'
      }).inject(tray);

      this.elements.menuTitle = new Element('span', {
        'html' : this._lang(this.options.title) + ' ('
      }).inject(this.elements.menu);

      this.elements.menuClose = new Element('a', {
        'href' : 'javascript:void(0);',
        'html' : this._lang('cancel'),
        'events' : {
          'click' : function(e) {
            e.stop();
            this.getComposer().deactivate();
          }.bind(this)
        }
      }).inject(this.elements.menuTitle);

      this.elements.menuTitle.appendText(')');
    }
  }


});