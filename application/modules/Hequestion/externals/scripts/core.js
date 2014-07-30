/* $Id: core.js 17.08.12 06:04 michael $ */

var Hequestion = {};

Hequestion.is_smoothbox = 0;

Hequestion.hequestion_askfriend_id = 0;
Hequestion.hequestion_askfriend = function (ids){


  (new Request.JSON({
    url: en4.core.baseUrl + 'hequestion/index/ask-friends',
    method: 'post',
    data: {
      'format': 'json',
      'question_id': Hequestion.hequestion_askfriend_id,
      'user_ids': ids
    },
    onComplete: function (obj)
    {
      if (obj.result){
        he_show_message(obj.message);
      } else {
        he_show_message(obj.message, 'error');
      }
    }
  })).send();

};


// Initialize questions choosing and option links

Hequestion.bindQuestion = function (c)
{
  c = $(c);

  if ($type(c) != 'element'){
    return ;
  }
  if (c.hasClass('hqBound')){
    return ;
  }
  c.addClass('hqBound');

  c.getElements('.hqselected, .hqchecked').addEvent('change', function (e){

    var self = $(this);
    var question = self.getParent('.hqQuestion');

    c.getElements('.hqselected, .hqchecked').set('disabled', true);

    var data = {};
    if (self.get('type') == 'checkbox'){
      data = {
        question_id: self.getParent('.hqQuestion').getElement('.hq_question_id').get('value'),
        vote: (self.checked) ? 1 : 0,
        option_id: self.get('rev'),
        format: 'json',
        show_all: (self.getParent('.hqQuestion').hasClass('hqShowAllOptions')) ? 1 : 0
      }
    } else {
      data = {
        question_id: self.getParent('.hqQuestion').getElement('.hq_question_id').get('value'),
        option_id: self.get('value'),
        format: 'json',
        show_all: (self.getParent('.hqQuestion').hasClass('hqShowAllOptions')) ? 1 : 0
      }
    }

    (new Request.JSON({
      url: en4.core.baseUrl + 'hequestion/index/vote',
      method: 'post',
      data: data,
      onComplete: function (obj){

        c.getElements('.hqselected, .hqchecked').set('disabled', false);

        if (!obj.result){
          he_show_message(obj.message, 'error');
          return ;
        }


        question.removeClass('hqBound').set('html', obj.body);
        Hequestion.bindQuestion(question);

        var asked_block = $$('.layout_hequestion_asked')[0];
        if (asked_block){
          asked_block.removeClass('hqBound').set('html', obj.widget);
          Hequestion.bindAskedBlock(asked_block, question);
        }


      }
    })).send();

  });

  var option = c.getElement('.hqAddAnswer');

  if (option){

    //var text = en4.core.language.translate('HEQUESTION_ADD_ANSWER');
    var text = option.getElement('input').get('rev');

    option.getElement('input').addEvent('focus', function (){
      $(this).removeClass('hqTextDisactive');
      if (this.value == text){
        this.value = '';
      } else {
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

    option.getElement('.hqAddAnswerForm').addEvent('submit', function (e){

      var self = $(this);
      if (e){
        e.stop();
      }

      if (self.hasClass('hqLoading')){
        return ;
      }
      self.addClass('hqLoading');

      var title = self.title.value;
      if (title == $(self.title).get('rev')){
        title = '';
      }

      (new Request.JSON({
        url: en4.core.baseUrl + 'hequestion/index/add-answer',
        method: 'post',
        data: {
          question_id: self.getParent('.hqQuestion').getElement('.hq_question_id').get('value'),
          title: title,
          format: 'json',
          show_all: (self.getParent('.hqQuestion').hasClass('hqShowAllOptions')) ? 1 : 0
        },
        onComplete: function (obj){

          self.removeClass('hqLoading');

          if (!obj.result){
            he_show_message(obj.message, 'error');
            return ;
          }

          var question = self.getParent('.hqQuestion');
          question.removeClass('hqBound').set('html', obj.body);
          Hequestion.bindQuestion(question);

          var asked_block = $$('.layout_hequestion_asked')[0];
          if (asked_block){
            asked_block.removeClass('hqBound').set('html', obj.widget);
            Hequestion.bindAskedBlock(asked_block, question);
          }

        }
      })).send();

    });


    option.getElement('.hqAddAsnwerSubmit').addEvent('click', function (){
      option.getElement('.hqAddAnswerForm').fireEvent('submit');
    });

  }

  c.getElements('.hqQuestionAddFollow').addEvent('click', function (){

    var self = $(this);

    (new Request.JSON({
      url: en4.core.baseUrl + 'hequestion/index/follow',
      method: 'post',
      data: {
        question_id: self.getParent('.hqQuestion').getElement('.hq_question_id').get('value'),
        format: 'json'
      },
      onComplete: function (obj){
      }
    })).send();

    $(this).getParent('.hqQuestionFollow').addClass('hqQuestionIsFollower');

  });

  c.getElements('.hqQuestionRemoveFollow').addEvent('click', function (){

    var self = $(this);

    (new Request.JSON({
      url: en4.core.baseUrl + 'hequestion/index/unfollow',
      method: 'post',
      data: {
        question_id: self.getParent('.hqQuestion').getElement('.hq_question_id').get('value'),
        format: 'json'
      },
      onComplete: function (obj){
      }
    })).send();

    $(this).getParent('.hqQuestionFollow').removeClass('hqQuestionIsFollower');

  });

  c.getElements('.hqQuestionAskFriends').addEvent('click', function (){

    var self = $(this);

    // :)

    var handle = window;
    if (Hequestion.is_smoothbox){
      handle = window.parent;
    }

    handle.Hequestion.hequestion_askfriend_id = self.getParent('.hqQuestion').getElement('.hq_question_id').get('value');

    handle.Smoothbox.close();

    (new handle.HEContacts({
      l: 'getFriends',
      c: 'Hequestion.hequestion_askfriend',
      params: {}
    })).box();

  });

  c.getElements('.hqQuestionUnvote').addEvent('click', function (){

    var self = $(this);

    (new Request.JSON({
      url: en4.core.baseUrl + 'hequestion/index/unvote',
      method: 'post',
      data: {
        question_id: self.getParent('.hqQuestion').getElement('.hq_question_id').get('value'),
        format: 'json',
        show_all: (self.getParent('.hqQuestion').hasClass('hqShowAllOptions')) ? 1 : 0
      },
      onComplete: function (obj){

        if (!obj.result){
          he_show_message(obj.message, 'error');
          return ;
        }

        var question = self.getParent('.hqQuestion');
        question.removeClass('hqBound').set('html', obj.body);
        Hequestion.bindQuestion(question);

        var asked_block = $$('.layout_hequestion_asked')[0];
        if (asked_block){
          asked_block.removeClass('hqBound').set('html', obj.widget);
          Hequestion.bindAskedBlock(asked_block, question);
        }

      }
    })).send();

  });

  c.getElements('.hqQuestionEditOptions').addEvent('click', function (){

    var self = $(this);


    if (self.getParent('.hqQuestion').getElement('.hqEditOptionsBox')){
      return ;
    }

    (new Request.JSON({
      url: en4.core.baseUrl + 'hequestion/index/edit-options',
      method: 'post',
      data: {
        format: 'json',
        question_id: self.getParent('.hqQuestion').getElement('.hq_question_id').get('value')
      },
      onComplete: function (obj)
      {

        if (!obj.result){
          he_show_message(obj.message, 'error');
          return ;
        }


        var questions = self.getParent('.hqQuestion .hqQuestions');
        var editOptions = new Element('div', {'class': 'hqEditOptionsBox', html: obj.body});


        questions.setStyle('display', 'none');
        editOptions.inject(questions, 'after');

        editOptions.getElements('.hqEditDone').addEvent('click', function (){
          editOptions.destroy();
          questions.setStyle('display', 'block');
        });

        editOptions.getElements('.hqEditQuestionDelete').addEvent('click', function (){

          if (confirm(en4.core.language.translate('HEQUESTION_OPTION_DELETE'))){

            (new Request.JSON({
              url: en4.core.baseUrl + 'hequestion/index/delete-option',
              data: {
                format: 'json',
                question_id: self.getParent('.hqQuestion').getElement('.hq_question_id').get('value'),
                option_id: $(this).get('rev'),
                show_all: (self.getParent('.hqQuestion').hasClass('hqShowAllOptions')) ? 1 : 0
              },
              onComplete: function (obj)
              {
                if (!obj.result){
                  he_show_message(obj.message, 'error');
                  return ;
                }

                var question = self.getParent('.hqQuestion');
                question.removeClass('hqBound').set('html', obj.body);
                Hequestion.bindQuestion(question);

                var asked_block = $$('.layout_hequestion_asked')[0];
                if (asked_block){
                  asked_block.removeClass('hqBound').set('html', obj.widget);
                  Hequestion.bindAskedBlock(asked_block, question);
                }

              }

            })).send();

          }

        });

      }
    })).send();


  });

  c.getElements('.hqLinkVoters').addEvent('click', function (){

    var self = $(this);

    he_list.box('hequestion', 'getQuestionVoters', en4.core.language.translate("HEQUESTION_LIST_TITLE_VOTERS"), {
      question_id: self.getParent('.hqQuestion').getElement('.hq_question_id').get('value'),
      option_id: $(this).get('rev'),
      list_title2: en4.core.language.translate("HEQUESTION_FRIENDS")
    });

  });

  Hequestion.initPrivacy(c);

  // inject below options

/*  if (c.getParent('.feed_item_body')){
    c.getParent('.feed_item_body').getElement('.hqQuestionOptions ul').getChialdren().each(function (i){
      i.inject(c.getParent('.feed_item_body').getElement('.feed_item_date ul'), 'top');
    });
  }*/





  en4.core.runonce.trigger();
  Smoothbox.bind($$('body')[0]);


};

// Initialize privacy block

Hequestion.initPrivacy = function (container)
{

  container.getElements('.wall_tips').each(function (item){
    Wall.elementClass(Wall.Tips, item);
  });
  container.getElements('.wall_blurlink').each(function (item){
    Wall.elementClass(Wall.BlurLink, item);
  });

  var self = this;
  var $link = container.getElement('.wall-privacy-action-link');

  if (!$link){
    return ;
  }

  var $privacy = Wall.injectAbsolute($link, container.getElement('.wall-privacy'), true);

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

    if (value == 'everyone'){
      $link.addClass('wall_is_public');
    } else {
      $link.removeClass('wall_is_public');
    }

    (new Request({
      url: en4.core.baseUrl + 'hequestion/index/privacy',
      method: 'post',
      data: {
        format: 'json',
        privacy: value,
        question_id: ( container.getParent('.hqQuestion') || container ) .getElement('.hq_question_id').get('value')
      }
    })).send();

  });

};


Hequestion.requestHTML = function (url, callback, $container, data)
{
  if ($type(data) == 'object'){
    data = $merge({'format': 'html'}, data);
  } else if ($type(data) == 'string'){
    data += '&format=html';
  }

  Hequestion.is_request = true;

  var request = new Request.HTML({
    'url': url,
    'method': 'get',
    'data': data,
    'evalScripts' : false,
    'onComplete': function (responseTree, responseElements, responseHTML, responseJavaScript){

      Hequestion.is_request = false;

      if ($container && $type($container) == 'element'){
        $container.set('html', responseHTML);
      }
      if ($type(callback) == 'function'){
        callback(responseHTML);
      }
      eval(responseJavaScript);
      en4.core.runonce.trigger();
    }
  });
  request.send();
};


// Initialize option links for question block

Hequestion.bindAskedBlock = function (asked_block, c)
{
  c = $(c);
  asked_block = $(asked_block);


  if ($type(c) != 'element'){
    return ;
  }
  if ($type(asked_block) != 'element'){
    return ;
  }

  if (asked_block.hasClass('hqBound')){
    return ;
  }
  asked_block.addClass('hqBound');


  asked_block.getElements('.hqQuestionAddFollow').addEvent('click', function (){

    var self = $(this);

    (new Request.JSON({
      url: en4.core.baseUrl + 'hequestion/index/follow',
      method: 'post',
      data: {
        question_id: asked_block.getElement('.hq_question_id').get('value'),
        format: 'json'
      },
      onComplete: function (obj){
      }
    })).send();

    $(this).getParent('.hqQuestionFollow').addClass('hqQuestionIsFollower');

  });

  asked_block.getElements('.hqQuestionRemoveFollow').addEvent('click', function (){

    var self = $(this);

    (new Request.JSON({
      url: en4.core.baseUrl + 'hequestion/index/unfollow',
      method: 'post',
      data: {
        question_id: asked_block.getElement('.hq_question_id').get('value'),
        format: 'json'
      },
      onComplete: function (obj){
      }
    })).send();

    $(this).getParent('.hqQuestionFollow').removeClass('hqQuestionIsFollower');


  });

  asked_block.getElements('.hqQuestionAskFriends').addEvent('click', function (){

    var self = $(this);

    // :)

    var handle = window;
    if (Hequestion.is_smoothbox){
      handle = window.parent;
    }

    handle.Hequestion.hequestion_askfriend_id = self.getParent('.hqAsked').getElement('.hq_question_id').get('value');

    handle.Smoothbox.close();

    (new handle.HEContacts({
      l: 'getFriends',
      c: 'Hequestion.hequestion_askfriend',
      params: {}
    })).box();

  });

  asked_block.getElements('.hqQuestionUnvote').addEvent('click', function (){

    var self = $(this);

    (new Request.JSON({
      url: en4.core.baseUrl + 'hequestion/index/unvote',
      method: 'post',
      data: {
        question_id: asked_block.getElement('.hq_question_id').get('value'),
        format: 'json',
        show_all: (c.hasClass('hqShowAllOptions')) ? 1 : 0
      },
      onComplete: function (obj){

        if (!obj.result){
          he_show_message(obj.message, 'error');
          return ;
        }

        c.removeClass('hqBound').set('html', obj.body);
        Hequestion.bindQuestion(c);

        asked_block.removeClass('hqBound').set('html', obj.widget);
        Hequestion.bindAskedBlock(asked_block, c);

      }
    })).send();

  });

  asked_block.getElements('.hqQuestionEditOptions').addEvent('click', function (){

    var self = $(this);


    if (c.getElement('.hqEditOptionsBox')){
      return ;
    }

    (new Request.JSON({
      url: en4.core.baseUrl + 'hequestion/index/edit-options',
      method: 'post',
      data: {
        format: 'json',
        question_id: asked_block.getElement('.hq_question_id').get('value')
      },
      onComplete: function (obj)
      {

        if (!obj.result){
          he_show_message(obj.message, 'error');
          return ;
        }


        var questions = c.getElement('.hqQuestions');
        var editOptions = new Element('div', {'class': 'hqEditOptionsBox', html: obj.body});


        questions.setStyle('display', 'none');
        editOptions.inject(questions, 'after');

        editOptions.getElements('.hqEditDone').addEvent('click', function (){
          editOptions.destroy();
          questions.setStyle('display', 'block');
        });

        editOptions.getElements('.hqEditQuestionDelete').addEvent('click', function (){

          if (confirm(en4.core.language.translate('HEQUESTION_OPTION_DELETE'))){

            (new Request.JSON({
              url: en4.core.baseUrl + 'hequestion/index/delete-option',
              data: {
                format: 'json',
                question_id: c.getElement('.hq_question_id').get('value'),
                option_id: $(this).get('rev'),
                show_all: (c.hasClass('hqShowAllOptions')) ? 1 : 0
              },
              onComplete: function (obj)
              {
                if (!obj.result){
                  he_show_message(obj.message, 'error');
                  return ;
                }

                c.removeClass('hqBound').set('html', obj.body);
                Hequestion.bindQuestion(c);

                asked_block.removeClass('hqBound').set('html', obj.widget);
                Hequestion.bindAskedBlock(asked_block, c);

              }

            })).send();

          }

        });

      }
    })).send();



  });




  en4.core.runonce.trigger();
  Smoothbox.bind($$('body')[0]);


  Hequestion.initPrivacy(asked_block);

};
