/* $Id: composer_people.js 18.06.12 10:52 michael $ */



Wall.Composer.Plugin.People = new Class({

  Extends : Wall.Composer.Plugin.Interface,

  name : 'people',

  options : {

    'suggestParam' : [

    ],

    'enabled' : true,
    'suggestProto' : 'request.json',
    requestOptions : {},
    'suggestOptions' : {
      'minLength': 0,
      'maxChoices' : 100,
      'delay' : 250,
      'selectMode': 'pick',
      'multiple': false,
      'filterSubset' : true,
      'tokenFormat' : 'object',
      'tokenValueKey' : 'label',
      'injectChoice': $empty,
      'onPush' : $empty,

      'prefetchOnInit' : true,
      'alwaysOpen' : false,
      'ignoreKeys' : false

    }
  },

  init_active: 0,
  input_c: null,
  peoples_c: null,
  input: null,
  compose_text: null,
  peoples: [],
  peoples_info: {},

  initialize : function(options) {
    this.elements = new Hash(this.elements);
    this.params = new Hash(this.params);
    this.parent(options);
  },

  attach : function()
  {
    this.parent();
    this.makeActivator();

    var self = this;
    this.getComposer().addEvent('editorSubmit', function (){
      self.getComposer().makeFormInputs({peoples: self.peoples});
    });

    this.getComposer().addEvent('editorClose', function (){
      self.resetValues();
    });

    this.getComposer().addEvent('editorClick', function (){
      self.hide();
    });

    $$('body').addEvent('click', function (e){

      if ($(e.target).getParent('.wall-autosuggestion')){

      } else {
        self.endSuggest();
      }

    });

    return this;
  },

  detach : function() {
    return false;
  },

  resetValues: function ()
  {

    this.init_active = 0;

    if (this.container){
      $(this.container).destroy();
      this.container = null;
    }

    if (this.input_c){
      $(this.input_c).destroy();
      this.input_c = null;
    }
    if (this.peoples_c){
      $(this.peoples_c).destroy();
      this.peoples_c = null;
    }
    if (this.input){
      $(this.input).destroy();
      this.input = null;
    }
    if (this.compose_text){
      $(this.compose_text).destroy();
      this.compose_text = null;
    }
    this.peoples = [];
    this.peoples_info = {};
  },

  activate : function()
  {
    this.show();
    return this;
  },

  show: function ()
  {
    var self = this;

    var create_function = function ()
    {

      if (!self.init_active){

        var c = this.getComposer().container.getElement('.wallComposerContainer');

        var people = self.container = new Element('div', {'class': 'wall-compose-people'});
        var input_c = this.input_c = new Element('div', {'class': 'wall-people-edit'});
        var peoples_c = this.peoples_c = new Element('div', {'class': 'wall-peoples_c'});
        var input = this.input = new Element('input', {'type': 'text', 'name': 'people', 'autocomplete': 'off', 'value': en4.core.language.translate('WALL_Who are you with?')});

        input.inject(peoples_c);
        peoples_c.inject(input_c);
        input_c.inject(people);

        people.inject(c, 'after');
        people.setStyle('display', 'block');

        input.addEvent('keyup', function (e){

          var stopped = false;
          if (self.suggest && e){
            self.suggest.onCommand(e);
            if (self.suggest.break_event){
              e.stop();
              stopped = true;
            }

          }
          if (!stopped){



            if ($(this).get('value') == '' && e.key == 'backspace'){
              if (self.peoples[self.peoples.length-1]){
                var key = self.peoples[self.peoples.length-1];
                self.peopleRemove(key);
              }
            }
            self.endSuggest();
            if ($(this).get('value') != ''){
              self.doSuggest($(this).get('value'));
            }


          }


        });

        input_c.addEvent('click', function (){
          input.focus();
        });

        input.addEvent('blur', function (){
          if ($(this).get('value') == ''){
            $(this).set('value', en4.core.language.translate('WALL_Who are you with?'));
          }
        });
        input.addEvent('focus', function (){
          if ($(this).get('value') == en4.core.language.translate('WALL_Who are you with?')){
            $(this).set('value', '');
          }
        });

        self.init_active = 1;

      }

      self.container.setStyle('display', 'block');
      self.input.focus();


    }.bind(this);

    if (this.getComposer().is_opened){
        create_function();
    } else {
      this.getComposer().open(create_function);
    }

  },

  hide: function ()
  {
    if (this.container){
      this.container.setStyle('display', 'none');
    }
  },



  deactivate : function (){
    return false;
  },

  poll : function() {

  },



  doSuggest : function(text) {
    this.currentText = text;
    var suggest = this.getSuggest();
    var input = this.getHiddenInput();
    input.set('value', text);
    input.value = text;
  },

  endSuggest : function() {
    this.currentText = '';
    this.positions = {};
    if( this.suggest ) {
      this.getSuggest().destroy();
      delete this.suggest;
    }
  },

  getHiddenInput : function() {
    if( !this.hiddenInput ) {
      this.hiddenInput = new Element('input', {
        'type' : 'text',
        'styles' : {
          'display' : 'none'
        }
      }).inject(this.getComposer().container.getElement('.wall-compose-people'));
    }
    return this.hiddenInput;
  },

  getSuggest : function() {

    if( !this.suggest ) {

      this.choices = new Element('ul', {
        'class': 'wall-autosuggestion',
        'styles': {
          'position': 'absolute',
          'width': this.getComposer().container.getElement('.wall-compose-people').getCoordinates().width - 2 // 2px borders
        }
      }).inject(this.getComposer().container.getElement('.wall-compose-people'), 'after');

      var self = this;
      var options = $merge(this.options.suggestOptions, {
        'customChoices' : this.choices,
        'injectChoice' : function(token) {

          if (self.peoples.contains(token.guid)){
            return
          }

          if (!token.guid){
            return ;
          }
          var choice = new Element('li', {
            'class': 'autocompleter-choices',
            //'value': token.id,
            'html': token.photo || '',
            'id': token.guid
          });
          new Element('div', {
            'html' : this.markQueryValue(token.label),
            'class' : 'autocompleter-choice'
          }).inject(choice);
          new Element('input', {
            'type' : 'hidden',
            'value' : JSON.encode(token)
          }).inject(choice);
          this.addChoiceEvents(choice).inject(this.choices);
          choice.store('autocompleteChoice', token);
        },
        'onChoiceSelect' : function(choice) {



          var data = JSON.decode(choice.getElement('input').value);



          if (!self.peoples.contains(data.guid)){

            var e = new Element('span', {'class': 'tag', 'html': data.label});
            var x = new Element('a', {'href': 'javascript:void(0);', 'html': 'x', 'rev': data.guid});
            x.inject(e);

            x.addEvent('click', function (){
              self.peopleRemove($(this).get('rev'));
            });


            self.peoples[self.peoples.length] = data.guid;
            self.peoples_info[data.guid] = data;
            self.composeText();


            e.inject(self.input, 'before');

            self.input.set('value', '');
            self.input.focus();


          }

        },
        'emptyChoices' : function() {
          this.fireEvent('onHide', [this.element, this.choices]);
        },
        'onCommand' : function(e) {
          switch (e.key) {
            case 'enter':
              break;
          }
        }
      });

      if( this.options.suggestProto == 'request.json' ) {
        this.suggest = new Wall.Autocompleter.Request.JSON(this.getHiddenInput(), en4.core.baseUrl + 'wall/index/suggest?includeSelf=true ', options);
      }
      if( this.options.suggestProto == 'local' ) {
        this.suggest = new Wall.Autocompleter.Local(this.getHiddenInput(), this.options.suggestParam, options);
      }
    }

    return this.suggest;

  },

  peopleRemove: function (guid)
  {
    this.peoples.erase(guid);
    delete this.peoples_info[guid];
    this.composeText();
    this.peoples_c.getElements('a[rev='+guid+']').getParent().destroy();
  },

  composeText: function ()
  {
    var self = this;

    if (this.compose_text){
      this.compose_text.destroy();
      this.compose_text = null;
    }

    //	&mdash;x

    var el = this.compose_text = new Element('div', {'style': '', 'class': 'wall_index_peoples'});
    el.inject(this.getComposer().container.getElement('.textareaBox'));



    var new_html = '';
    if (this.peoples.length == 0){
      new_html = '';
    } else if (this.peoples.length == 1){
      new_html = ' &mdash; ' + en4.core.language.translate('WALL_with %1$s', '<a href="javascript:void(0);">' + this.peoples_info[this.peoples[0]].label) + '</a>';
    } else if (this.peoples.length == 2){
      new_html = ' &mdash; ' + en4.core.language.translate('WALL_with %1$s and %2$s', '<a href="javascript:void(0);">' + this.peoples_info[this.peoples[0]].label + '</a>', '<a href="javascript:void(0);">' + this.peoples_info[this.peoples[1]].label) + '</a>';
    } else if ((this.peoples.length > 2)){
      new_html = ' &mdash; ' + en4.core.language.translate('WALL_with %1$s and %2$s', '<a href="javascript:void(0);">' + this.peoples_info[this.peoples[0]].label + '</a>', '<a href="javascript:void(0);">' + en4.core.language.translate('WALL_%1$s others', this.peoples.length)) + '</a>';
    }


    el.set('html', new_html);

    el.getElements('a').addEvent('click', function (){
      self.show();
    });

  }



});


