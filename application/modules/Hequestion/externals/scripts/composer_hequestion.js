/* $Id: composer_hequestion.js 17.08.12 06:04 michael $ */



Wall.Composer.Plugin.Hequestion = new Class({

  Extends : Wall.Composer.Plugin.Interface,

  name : 'hequestion',

  options : {
    title : 'Add Question',
    lang : {},
    max_option: 10,
    is_timeline: 0
  },

  $container: null,


  initialize : function(options) {
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

  activate : function(no_focus) {
    this.showCreateForm();
  },

  showCreateForm: function ()
  {
    this.getComposer().container.setStyle('display', 'none');


    if (!this.$container){

      this.$container = new Element('div', {'class': 'wallComposer', 'html': this.options.html});
      this.$container.inject(this.getComposer().container, 'after');

      this.$container.getElements('.wall_tips').each(function (item){
        Wall.elementClass(Wall.Tips, item);
      });
      this.$container.getElements('.wall_blurlink').each(function (item){
        Wall.elementClass(Wall.BlurLink, item);
      });

      this.initComposer();
      this.initPrivacy();



      var tray = this.$container.getElement('.wall-compose-tray');
      tray.setStyle('display', 'block');


      var options = new Element('ul', {'class': 'hqOptions'});
      options.inject(tray, 'top');

      this.$options = options;

      this.createDefault();


    }

    this.$container.setStyle('display', 'block');
    this.open();


  },

  createDefault: function ()
  {
    this.$options.getChildren().destroy();

    for (var i=1; i<4; i++){
      this.createOption(i);
    }
  },


  createOption: function (key)
  {
    var self = this;

    var text = en4.core.language.translate('HEQUESTION_ADD_OPTION');

    var option = new Element('li', {'class': 'hqOption', 'html': '<input type="text" class="hqTextDisactive" autocomplete="off" name="options['+key+']" value="'+text+'">'});
    option.inject(this.$options, 'bottom');

    option.getElement('input').addEvent('focus', function (){

      $(this).removeClass('hqTextDisactive');

      if (this.value == text){
        this.value = '';
      } else {

      }

      if (!$(this).getParent().getNext() && self.$options.getChildren().length < self.options.max_option){
        self.createOption($(this).getParent().getAllPrevious().length+2);
      }

    });

    option.getElement('input').addEvent('blur', function (){
      $(this).removeClass('hqTextDisactive');
      if (this.value == ''){
        this.value = text;
        $(this).addClass('hqTextDisactive');
      } else {
      }
    });


  },






  initPrivacy: function ()
  {
    var self = this;

    var container = this.$container;
    var $link = container.getElement('.wall-privacy-link');

    if (!$link){
      return ;
    }

    var $privacy = this.$privacy = Wall.injectAbsolute($link, container.getElement('.wall-privacy'));

    $link.addEvent('click', function (){

      $try(function (){ window.fireEvent('resize'); });

      if ($(this).hasClass('is_active')){
        $(this).removeClass('is_active');
        $privacy.removeClass('is_active');
      } else {
        $(this).addClass('is_active');
        $privacy.addClass('is_active');
      }

    });

    $$('body')[0].addEvent('click', function (e){
      if (!$(e.target).getParent('.wall-privacy-container')){
        $link.removeClass('is_active');
        $privacy.removeClass('is_active');
      }
    });

    $privacy.getElements('a').addEvent('click', function (){
      var value = $(this).get('rev');
      container.getElement('.wall_privacy_input').set('value', value);
      Wall.elements.get('Wall.Tips', (window.$uid || Slick.uidOf)($link)).setTitle( $(this).getElement('.wall_text').get('text') );
      $privacy.getElements('a').removeClass('is_active');
      $(this).addClass('is_active');
    });

  },



  initComposer: function ()
  {
    var self = this;

    var container = this.$container;

    container.getElement('textarea').autogrow();

    container.getElement('.labelBox').addEvent('click', function (){
      this.open();
    }.bind(this));

    container.getElement('.textareaBox .close').addEvent('click', function (){
      this.close();
    }.bind(this));

    container.getElement('.inputBox').addEvent('click', function (){
      $(this).getElement('textarea').focus();
    });


    container.getElement('form').addEvent('submit', function (e){

      e.stop();

      if (!$(this).title.get('value')){
        return ;
      }

      var title = $(this).title.get('value');
      var options = [];

      var text = en4.core.language.translate('HEQUESTION_ADD_OPTION');
      container.getElements('.hqOption input').each(function (i){
        if (i.get('value') != text){
          options[options.length] = i.get('value');
        }
      });




      var button = container.getElement('.submitMenu button');

      button
          .set('html', '&nbsp;&nbsp;&nbsp;' + en4.core.language.translate('WALL_SENDING') + '&nbsp;&nbsp;&nbsp;')
          .addClass('wall_active');


      var data = {
        title: title,
        options: options,
        auth_view: $($(this).auth_view).get('value'),
        can_add: ($(this).can_add.checked) ? 1 : 0,
        subject: en4.core.subject.guid,
        is_timeline: self.options.is_timeline
      };


      Wall.request(en4.core.baseUrl + 'hequestion/index/create', data, function (obj){

        button
            .set('html', '&nbsp;&nbsp;&nbsp;' +en4.core.language.translate('WALL_Share') + '&nbsp;&nbsp;&nbsp;')
            .removeClass('wall_active');


        if (!obj.result){
          Wall.dialog.message(obj.message, 0);
          return ;
        }

        self.loadLastStream(obj.body);

        if (self.options.is_timeline && false){
          var feed = timeline.feed.object.get();
          if (timeline.feed.object.setLasts(obj.last_date, obj.last_id)) {
            feed.checkEmptyFeed();
          }
        } else {
          var wall = Wall.feeds.get(self.getComposer().options.feed_uid);
          wall.checkEmptyFeed();
          wall.setLastId(obj.last_id);
        }


        self.composerDeactivate();
        self.close();

      });
    });




  },

  loadLastStream: function (html, callback)
  {
    var self = this;

    html.stripScripts(true);
    var el = new Element('ul', {html: html});

    if (self.options.is_timeline && false){

      var feed = timeline.feed.object.get();
      feed.initAction(el);

      el.getChildren().each(function (item) {
        timeline.composer.inject(item);
      });

    } else {

      var wall = Wall.feeds.get(self.getComposer().options.feed_uid);

      wall.initAction(el);
      el.getChildren().each(function (item){
        item.inject(wall.getFeed(), 'top');
        Wall.itemEffect(item);
      });

    }

    if ($type(callback) == 'function'){
      callback();
    }


  },

  open: function ()
  {
    this.composerDeactivate();

    if (this.is_composer_opened){
      return ;
    }
    this.is_composer_opened = true;

    this.$container.getElements('.toolsBox a').addClass('is_active');
    this.$container.getElement('.labelBox').removeClass('is_active');

    var textarea_box = this.$container.getElement('.textareaBox');

    var fx = new Fx.Morph(textarea_box, {'duration': 50});
    fx.addEvent('onStart', function (){
      textarea_box.setStyles({
        'height': 30,
        'overflow': 'hidden'
      });
      textarea_box.addClass('is_active');
    }.bind(this));

    fx.addEvent('onComplete', function (){
      textarea_box.setStyles({
        'height': 'auto',
        'overflow': 'visible'
      });
      this.$container.getElement('.submitMenu').addClass('is_active');

    }.bind(this));

    fx.start({'height': [30,64]});

    this.$container.getElement('.textareaBox textarea').focus();


  },

  close: function ()
  {
    var self = this;

    this.composerDeactivate();

    if (!this.is_composer_opened){
      return ;
    }
    this.is_composer_opened = false;

    var textarea_box = this.$container.getElement('.textareaBox');

    textarea_box.getElement('textarea').set('value', '');

    var fx = new Fx.Morph(textarea_box, {'duration': 50});
    fx.addEvent('onStart', function (){
      this.$container.getElements('.toolsBox a').removeClass('is_active');
      this.$container.getElement('.submitMenu').removeClass('is_active');
      textarea_box.setStyles({
        'height': 64,
        'overflow': 'hidden'
      });
    }.bind(this));

    fx.addEvent('onComplete', function (){

      self.hideCreateForm();
      self.getComposer().close();

      textarea_box.setStyles({
        'height': 30,
        'overflow': 'visible'
      });
      textarea_box.removeClass('is_active');


      var label = this.$container.getElement('.labelBox');
      var labelFx = new Fx.Morph(label, {'duration': 1000});

      labelFx
          .addEvent('onStart', function (){
            label
                .addClass('is_active')
                .setStyle('opacity', 0);
          })
          .addEvent('onComplete', function (){

      }.bind(this))
          .start({'opacity': [0, 1]});


    }.bind(this));

    fx.start({'height': [64,30]});




  },

  composerDeactivate: function ()
  {
    this.$container.getElement('.textareaBox textarea').set('value', '');
    this.$container.getElement('.hqAllowAddOptions input[type=checkbox]').set('checked', true);
    this.createDefault();
  },













  hideCreateForm: function ()
  {
    if (this.$container){
      this.$container.setStyle('display', 'none');
    }
    this.getComposer().container.setStyle('display', 'block');
  },


  deactivate : function() {
    if( !this.active ) return;
    this.parent();
    
    this.request = false;
  },

  makeActivator : function() {
    if( !this.elements.activator ) {

      this.elements.activator = new Element('a', {
        'class' : 'wall-compose-activator wall-compose-' + this.getName() + '-activator wall_blurlink',
        'href' : 'javascript:void(0);',
        'html' : '&nbsp;',
        'title': this._lang(this.options.title),
        'events' : {
          'click' : this.activate.bind(this)
        }
      }).inject(this.getComposer().getMenu());

      new Wall.Tips(this.elements.activator);
      new Wall.BlurLink(this.elements.activator);

    }
  }


});