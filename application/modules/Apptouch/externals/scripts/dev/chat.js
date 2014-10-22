var Chat = {

  join_url:'',
  ping_url:'',
  leave_url:'',
  list_url:'',
  status_url:'',
  send_url:'',
  whisper_url:'',
  whisper_close_url: '',
  settings_url:'',

  params: '',
  initialized: false,

  chatRoom: '',
  rooms:'',
  current_room:'0',
  roomHeader:'',
  room_count:[],
  roomViewer:'',

  users_container:'',
  message_container:'',
  main_container:'',
  user0:'',
  lastEventTime:false,


  viewer_id:0,
  im_status:true,
  interval:'',
  delay:10000,
  pingOk: true,
  enableIM:false,
  chat_status:0,

  IMContainer:'',
  groupContainer:'',
  statusBtn:'',
  friendsBtn:'',

  imUserCount:-1,
  imUserIds:[],

  canClick:true,

  imConversationContainer:'',
  imConversationUser0:'',
  imConversationItems:'',
  imConversationItem0:'',

  imUserContainer: $('<div id="im-users-container"><div class="users-options ui-body-b"><div class="head-title"></div><span class="ui-icon ui-icon-minus"></span></div></div>'),
  imUsers:$('<ul id="im-users" data-role="listview" class="ui-listview">'),
  imUser0: $('<li id="im-user-0" class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ui-li-has-thumb ui-btn-up-c" data-corners="false" data-shadow="false" data-iconshadow="true" data-wrapperels="div" data-icon="arrow-r" data-iconpos="right" data-theme="c">' +
    '<div class="ui-btn-inner ui-li"><div class="ui-btn-text"><a class="ui-link-inherit" href=""><img class="ui-li-thumb" src=""><h3 class="ui-li-heading"></h3></a></div><span class="ui-icon ui-icon-chat"></span></div></li>'),

  activePage:null,

  setParams: function(params){
    this.params = params;

    this.join_url = params.join_url;
    this.ping_url = params.ping_url;
    this.leave_url = params.leave_url;
    this.list_url = params.list_url;
    this.status_url = params.status_url;
    this.send_url = params.send_url;
    this.whisper_url = params.whisper_url;
    this.whisper_close_url = params.whisper_close_url;
    this.settings_url = params.settings_url;

    if(parseInt(params.identity)) {
      this.init();
      this.initialized = true;
    }
  },

  init:function() {
    console.log('chat is inited');
    var params = this.params;
    if (!params){
      return ;
    }
    var self = this;

    self.viewer_id = params.identity;
    self.enableIM = params.enableIM;
    self.delay = parseInt(params.delay);

    self.groupContainer = $('<div data-role="controlgroup" data-type="horizontal" data-mini="true" data-corners="false">');
    self.statusBtn = $('<a data-role="button" data-theme="b" data-corners="false" data-icon="chat" data-iconpos="notext">');
    self.friendsBtn = $('<a data-role="button" data-theme="b" data-corners="false" data-icon="person">').html('(0)');
    self.groupContainer.append(self.friendsBtn).append(self.statusBtn);
    self.IMContainer = $('<div id="im-container">').append(self.groupContainer);

    self.groupContainer.controlgroup();
    self.statusBtn.button();
    self.friendsBtn.button();

    self.imUserContainer.find('span.ui-icon-minus').bind('vclick', function() {
      if( self.activePage ) {
        self.activePage.addClass('ui-page-active');
        self.activePage = null;
        self.imUserContainer.hide();
      }
    });
    self.imUserContainer.hide();
    $('body').append(self.imUserContainer);

    $(document).bind('pageshow', function(e, data){
      if($(e.target).attr('id') != 'core_board_index_rewrite')
        $(e.target).find('.ui-footer').append(self.IMContainer);
    });

    var activepage = $(document).find('.ui-page-active');
    if( activepage.attr('id') != 'core_board_index_rewrite') {
      var footer = activepage.find('.ui-footer');
      footer.css('display', '');
      footer.empty();
      footer.append(self.IMContainer);
    }

    self.statusBtn.bind('vclick', function() {
      if( !self.canClick )
        return;
      self.canClick = false;
      if(self.im_status) {
        self.statusBtn.find('span.ui-icon-chat').addClass('ui-icon-minus').removeClass('ui-icon-chat');
        self.friendsBtn.hide();
        self.status({'format':'json', 'status':0, 'type':'im'});
        self.im_status = 0;

        if( self.activePage ) {
          self.activePage.addClass('ui-page-active');
          self.imConversationContainer.hide();
          self.imConversationContainer.find('.conversation-item').hide();
          self.imUserContainer.hide();
          self.activePage = null;
        }

        self.stop();
        $.cookie('en4_chat_imstate', 0);
      } else {
        self.status({'format':'json', 'status':1, 'type':'im'});
        self.friendsBtn.show();
        self.statusBtn.find('span.ui-icon-minus').addClass('ui-icon-chat').removeClass('ui-icon-minus');
        self.im_status = 1;

        if( self.chat_status == 0 ) {
          self.chat_status = 1;
          self.ping({'format':'json', 'fresh':true, 'im':true});
        }
        $.removeCookie('en4_chat_imstate');
        self.start();
      }
    });

   $(document).bind("pagebeforechange", function (event, jQPageData) {
     self.imConversationContainer.hide();
     self.imConversationContainer.find('.conversation-item').hide();
     self.imUserContainer.hide();
     if(self.activePage)
       self.activePage.addClass('ui-page-active');

     self.activePage = null;
   });

    self.friendsBtn.bind('vclick', function() {
      self.imConversationContainer.hide();
      self.imConversationContainer.find('.conversation-item').hide();
      if( self.activePage == null ) {
        self.activePage = $('body > .ui-page-active');
        self.activePage.removeClass('ui-page-active');
        self.imUserContainer.show();

      } else {
        self.activePage.addClass('ui-page-active');
        self.activePage = null;
        self.imUserContainer.hide();
      }

      $(this).removeClass('chat-unread-message');
    });


    //Conversation
    self.imConversationContainer = $('<div id="conversation-container">');
    self.imConversationUser0 = $('<div id="conversation-user-0" class="conversation-item"><div class="conversation-options ui-body-b"><div class="user-name"></div><span class="ui-icon ui-icon-delete"></span><span class="ui-icon ui-icon-minus"></span></div>'
      + '<ul class="conversations"></ul><div class="conversation-input ui-body-b"><input type="text"></div></div>');
    self.imConversationUser0.find('input').textinput();
    self.imConversationUser0.hide();
    self.imConversationItems = $('<ul></ul>');
    self.imConversationItem0 = $('<li id="conversatin-item-0"><span class="conversation-author"><img src=""></span><span class="message-arrow"></span><span class="message-body"></span></li>');
    $('body').append(self.imConversationContainer);
    self.imConversationContainer.hide();

    var imState = parseInt($.cookie('en4_chat_imstate'));

    if(imState == 0) {
      self.status({'format':'json', 'status':1, 'type':'im'});
      self.chat_status = 0;
      self.im_status = 0;
      self.statusBtn.find('span.ui-icon-chat').removeClass('ui-icon-chat').addClass('ui-icon-minus');
      self.friendsBtn.hide();
    } else {
      self.ping({'format':'json', 'fresh':true, 'im':true});
      self.start();
      $.removeCookie('en4_chat_imstate');
      self.chat_status = 1;
      self.im_status = 1;
    }

    $(document).bind('stateactive', function(event, data){
      if(self.im_status && self.initialized) {
        self.status({'format':'json', 'status':1, 'type':'im'});
      }
    });

    $(document).bind('stateidle', function(event, data){
      if( self.im_status && self.initialized) {
        self.status({'format':'json', 'status':2, 'type':'im'});
      }
    });

  },

  destroy: function()
  { console.log('loging out');
    this.stop();
    this.initialized = false;
    this.viewer_id = 0;
    this.imUserIds = [];

    if(this.groupContainer)
      this.groupContainer.remove();

    if(this.statusBtn)
      this.statusBtn.remove();

    if(this.friendsBtn)
      this.friendsBtn.remove();

    if(this.IMContainer)
      this.IMContainer.remove();

    if(this.imConversationContainer)
      this.imConversationContainer.remove();

    if(this.imConversationUser0)
      this.imConversationUser0.remove();

    if(this.imConversationItems)
      this.imConversationItems.remove();

    if(this.imConversationItem0)
      this.imConversationItem0.remove();

    if(this.imUsers)
      this.imUsers.remove();
    this.imUsers = $('<ul id="im-users" data-role="listview" class="ui-listview">');

    if(this.imConversationContainer)
      this.imConversationContainer.remove();

    if(this.imConversationUser0)
      this.imConversationUser0.remove();
    this.imConversationUser0 = '';

    if(this.imConversationItems)
      this.imConversationItems.remove();
    this.imConversationItems = '';

    if(this.imConversationItem0)
      this.imConversationItem0.remove();
    this.imConversationItem0 = '';
  },

  getSettings: function() {
    console.log('getting chat settings');
    if(!this.settings_url)
      return;
    var self = this ;
    $.post(self.settings_url, {'format':'json'}, function(response){
      if( response && response.chatSettings ) {
        var chatSettings = response.chatSettings;
        self.params.viewer_id = parseInt(chatSettings.identity);
        self.params.enableIM = chatSettings.enableIm;

        if( parseInt(chatSettings.identity) ) {
          self.init();
          self.initialized = true;
        }
      }
    }, 'json');
  },

  initChatRoom:function($template, viewer) {
    var self = this;
    self.chatRoom = $template;
    self.users_container = self.chatRoom.find('#chat-users');
    self.message_container = self.chatRoom.find('#chat-main-messages');
    self.main_container = self.chatRoom.find('#chat-main');
    self.user0 = self.users_container.find('#user-0');
    self.user0.remove();

    if( !viewer.photo ) {
      viewer.photo = '/applicatoin/modules/Apptouch/externals/images/nophoto_user_thumb_profile.png';
    }
    var user = self.user0.clone();
    user.attr('id', 'user-' + viewer.id);
    user.find('img').attr('src', viewer.photo);
    user.find('a.user-title').attr('href', viewer.href);
    user.find('a.user-title').html(viewer.name);
    user.find('span.user-status').attr('class', 'user-status-1');
    self.users_container.append(user);

    self.viewer_id = viewer.id;

    self.chatRoom.find('#chat-input').unbind('keypress').bind('keypress', function(event) {
      if( event.which == 13 && $(this).val()) {
        var message = self.stripTags($(this).val());
        self.send({'format':'json', 'room_id':self.current_room, 'message':message});
        $(this).val('');
      }
    });

  },

  initRoomHeader:function(room_header) {
    var self = this;
    self.roomHeader = room_header;
    self.rooms = self.roomHeader.find('#chat-rooms');

    self.rooms.bind('change', function() {
      var room_id = $(this).val();

      if( room_id != self.current_room ) {
        var leave_room = self.current_room;
        self.current_room = room_id;
        self.switchRoom({'format':'json', 'room_id':leave_room}, {'format':'json', 'room_id':room_id});
        self.message_container.html('');
      }
    });

    if( $.cookie('en4_chat_room_last') ) {
      self.rooms.val($.cookie('en4_chat_room_last'));
      self.rooms.selectmenu('refresh');
    }

    self.current_room = self.rooms.val();

    self.join({'format':'json', 'room_id':self.current_room});

    self.roomHeader.find('#show-room-users').bind('vclick', function() {
      self.roomHeader.find('#show-room-messages').show();
      self.users_container.show();
      self.main_container.hide();
      self.chatRoom.find('#chat-main-input').hide();
      $(this).hide();
    });

    self.roomHeader.find('#show-room-messages').bind('vclick', function() {
      self.roomHeader.find('#show-room-users').show();
      self.users_container.hide();
      self.main_container.show();
      self.chatRoom.find('#chat-main-input').show();
      $(this).hide();
    });

  },

  join:function(data) {
    var self = this;
    $.post(self.join_url, data, function (response) {

      self.list({'format':'json'});

      if($.type(response.users) == 'object' ) {
        self.setRoomUsers(response.users);
      }

      if( response.room ) {
        self.current_room = response.room['room_id'];
        $.cookie('en4_chat_room_last', self.current_room);
      }

    }, 'json');
  },

  ping:function(data) {
    var self = this;
    if(!this.pingOk)
      return;
    this.pingOk = false;
    $.ajax({
      url: self.ping_url,
      data: data,
      type: 'post',
      success: function(response){
        self.pingOk = true;
        if($.type(response) == 'object' ) {

          if($.type(response.lastEventTime) && response.lastEventTime ) {
            self.lastEventTime = response.lastEventTime;
          }

          if( $.type(response.users) == 'object' ) {
            self.imUserCount = -1;
            self.imUserIds = [];

            for( var key in response.users ) {
              self.setUsers(response.users[key]);
              self.imUserIds[response.users[key].identity] = 1;

              if(response.users[key].state == 0)
                continue;

              self.imUserCount++;
            }

            self.imUserContainer.append(self.imUsers);

            self.friendsBtn.find('.ui-btn-text').html('(' + self.imUserCount + ')');
            self.imUserContainer.find('.users-options > .head-title').html(core.lang.get('Online ') + '(' + self.imUserCount + ')');
          }

          if($.type(response.whispers) == 'object') {
            for( var key in response.whispers ) {
              self.setWhispers(response.whispers[key], 1);
            }
          }

          if( $.type(response.events) == 'object') {
            for( var key in response.events ) {
              var event = response.events[key];

              if('function' == typeof self['event_' + event.type]) {
                self['event_' + event.type](event);
              }
            }
          }
        }
      },
      error: function(response){
        self.pingOk = true;
      }
    });
  },

  switchRoom:function(data, joinData) {
    var self = this;
    $.post(self.leave_url, data, function (response) {
      if( response.status ) {
        self.join(joinData);
      }
    }, 'json');
  },

  list:function(data) {
    var self = this;
    $.post(self.list_url, data, function (response) {
      self.rooms.html('');
      for( var key in response.rooms ) {
        var room = $('<option></option>');
        room.attr('id', 'room-' + response.rooms[key].identity);
        room.attr('value', response.rooms[key].identity);
        room.append(response.rooms[key].title + '<span> (' + core.lang.get('%1$s person', parseInt(response.rooms[key].people)) + ')</span>');

        self.rooms.append(room);

        self.room_count[parseInt(response.rooms[key].identity)] = parseInt(response.rooms[key].people);
      }
      self.rooms.val(self.current_room);
      self.rooms.selectmenu('refresh');
    }, 'json');
  },

  status:function(data) {
    var self = this;
    $.post(self.status_url, data, function(response) {
      self.canClick = true;
    });
  },

  whisper:function(data) {
    var self = this;

    var conversation = self.imConversationContainer.find('#conversation-user-' + data.user_id);
    var li = self.imConversationItem0.clone();
    li.attr('class', 'sender');
    li.find('img').attr('src', self.imUsers.find('#im-user-' + self.viewer_id + ' img').attr('src'));
    li.find('span.message-body').html(data.message);
    conversation.find('ul.conversations').append(li);

    $(window).scrollTop(li.position().top);
    $(window).trigger('resize');

    $.post(self.whisper_url, data, function(response) {
      li.attr('id', 'conversation-item-' + response.whisper_id);
    });
  },

  whisper_close:function(data) {
    var self = this;
    $.post(self.whisper_close_url, data, function(response) {

    });
  },

  send:function(data) {
    var self = this;
    var item = self.users_container.find('#user-' + self.viewer_id).clone();
    item.attr('id', '');
    item.attr('class', 'message-box');
    item.find('.user-info').append('<div class="message-body">' + data.message + '</div>');
    self.message_container.append(item);

    $.post(self.send_url, data, function(response) {
      item.attr('id', 'message-' + response.message_id);
    });

    $(window).scrollTop(item.position().top);
    $(window).trigger('resize');
  },

  setRoomUsers:function(users) {
    var self = this;
    self.users_container.html('');
    for( var key in users ) {
      if( users[key].type == 'grouppresence' || users[key].self) {
        var user = self.user0.clone();
        user.attr('id', 'user-' + users[key].identity);

        user.find('img').attr('src', users[key].photo);
        user.find('a.user-title').attr('href', users[key].href);
        user.find('a.user-title').html(users[key].title);
        user.find('span.user-status').attr('class', 'user-status-' + users[key].state);

        self.users_container.append(user);
      }
    }
    $(window).trigger('resize');
  },

  setUsers:function(user) {

    var self = this;
//      if( users[key].type == 'presence') {
    var item = self.imUser0.clone();
    item.attr('id', 'im-user-' + user.identity);
    item.find('a').attr('href', '');
    item.find('img').attr('src', user.photo);
    item.find('h3').html(user.title);
    item.find('.ui-icon-chat').addClass('state-' + user.state);

    self.imUsers.append(item);
    if( user.self ) {
      item.hide();
    } else {
      item.bind('vclick', function() {
        self.initIMChat(user.identity);
        self.imUserContainer.hide();
        self.imConversationContainer.show();
        self.imConversationContainer.find('#conversation-user-' + user.identity).show();
        $(this).removeClass('chat-unread-message');
        $.cookie('en4_whispers_unread_convo' + user.identity, 0);
      });
    }
//      }
  },

  start:function() {
    var self = this;
    self.interval = setInterval(function(){
      var data = {
        'format' : 'json',
        'lastEventTime':self.lastEventTime,
        'fresh' : false,
        'im': self.im
      };
      if( self.current_room != '0' ) {
        data.rooms = [self.current_room];
      }
      self.ping(data);
    }, self.delay);
  },

  stop:function() {
    var self = this;
    clearInterval(self.interval);
  },


  event_grouppresence:function(event) {
    var self = this;
    if( event.room_id != this.current_room ) {
      return;
    }

    if( parseInt(event.state) == 1 ) {
      if(self.users_container.find('#user-' + event.identity).length)
        self.users_container.find('#user-' + event.identity + ' > .user-info > span').attr('class', 'user-status-1');
      else{
        var item = $('<div class="system-message">' + core.lang.get('%1$s has joined the room.', '<a href="' + event.href + '" target="_blank">' + event.title + '</a>') + '</div>');
        self.message_container.append(item);
        var user = self.user0.clone();
        user.attr('id', 'user-' + event.identity);

        user.find('img').attr('src', event.photo);
        user.find('a.user-title').attr('href', event.href);
        user.find('a.user-title').html(event.title);
        user.find('span.user-status').attr('class', 'user-status-1');

        self.users_container.append(user);

        $(window).scrollTop(item.position().top);
        $(window).trigger('resize');

        self.room_count[event.room_id]++;
        self.rooms.find('#room-' + event.room_id + ' > span').html(' (' + core.lang.get('%1$s person', parseInt(self.room_count[event.room_id])) + ')');
        self.rooms.selectmenu('refresh');
      }
    } else if(parseInt(event.state) == 2) {
      self.users_container.find('#user-' + event.identity + ' > .user-info > span').attr('class', 'user-status-2');
    }else if( parseInt(event.state) < 1 ) {
      var item = $('<div class="system-message">' + core.lang.get('%1$s has left the room.', '<a href="' + event.href + '" target="_blank">' + event.title + '</a>') + '</div>');
      self.message_container.append(item);
      self.users_container.find('#user-' + event.identity).remove();

      $(window).scrollTop(item.position().top);
      $(window).trigger('resize');

      self.room_count[event.room_id]--;
      self.rooms.find('#room-' + event.room_id + ' > span').html(' (' + core.lang.get('%1$s person', parseInt(self.room_count[event.room_id])) + ')');
      self.rooms.selectmenu('refresh');
    }
  },

  event_groupchat:function(event) {
    var self = this;

    if( event.room_id != self.current_room || $('#message-' + event.message_id).length) {
      return;
    }

    var item = self.users_container.find('#user-' + event.user_id).clone();
    item.attr('id', 'message-' + event.message_id);
    item.attr('class', 'message-box');
    item.find('.user-info').append('<div class="message-body">' + event.body + '</div>');

    self.message_container.append(item);
    $(window).scrollTop(item.position().top);
  },

  event_presence:function(event) {
    var state = parseInt(event.state);
    if(state == 1) {
      if( !this.imUserIds[event.identity] ) {
        this.imUserCount++;
        this.friendsBtn.find('.ui-btn-text').html('(' + this.imUserCount + ')');

        this.imUserIds[event.identity] = 1;
        this.setUsers(event);
      } else if(this.imUserContainer.find('#im-user-' + event.identity).hasClass('state-0')){
        this.imUserCount++;
        this.friendsBtn.find('.ui-btn-text').html('(' + this.imUserCount + ')');
      }
      this.imUserContainer.find('#im-user-' + event.identity + ' span.ui-icon-chat').removeClass('state-2').removeClass('state-0').addClass('state-1');
    } else if(state == 0) {
      if( this.imUserIds[event.identity] ) {
        this.imUserCount--;
        this.imUserIds[event.identity] = 0;
        this.friendsBtn.find('.ui-btn-text').html('(' + this.imUserCount + ')');
      }

      this.imUserContainer.find('#im-user-' + event.identity).remove();
    } else if(state == 2) {
      this.imUserContainer.find('#im-user-' + event.identity + ' span.ui-icon-chat').removeClass('state-1').removeClass('state-0').addClass('state-2');
    }

  },

  event_chat:function(event) {
    this.setWhispers(event, 0);
  },

  event_reconfigure:function(event) {

  },

  setWhispers:function(whisper, check) {

    var user_id = whisper.sender_id;
    var IMclass = 'receiver';
    if( user_id == this.viewer_id ) {
      user_id = whisper.recipient_id;
      IMclass = 'sender';
    }

    var newMessage = parseInt($.cookie('en4_whispers_unread_convo' + user_id));

    var user = this.imUsers.find('#im-user-' + user_id);

    if(!check) {
      newMessage = 1;
    }

    if( (this.activePage && this.imUserContainer.css('display') != 'none') && newMessage ) {
      user.addClass('chat-unread-message');
      if(IMclass == 'receiver')
        $.cookie('en4_whispers_unread_convo' + user_id, 1);
    } else if( (!this.activePage || (this.imConversationContainer.css('display') != 'none' && user.css('display') == 'none')) && newMessage ) {
      this.friendsBtn.addClass('chat-unread-message');
      user.addClass('chat-unread-message');
      if(IMclass == 'receiver')
        $.cookie('en4_whispers_unread_convo' + user_id, 1);
    } else if(IMclass == 'receiver') {
      $.cookie('en4_whispers_unread_convo' + user_id, 0);
    }

    if( !this.imConversationContainer.find('#conversation-user-' + user_id).length) {
      this.initIMChat(user_id);
    }

    var conversation = this.imConversationContainer.find('#conversation-user-' + user_id);
    if( !conversation.find('ul > #conversation-item-' + whisper.whisper_id).length ) {
      var li = this.imConversationItem0.clone();
      li.attr('id', 'conversation-item-' + whisper.whisper_id);
      li.attr('class', IMclass);
      li.find('img').attr('src', this.imUsers.find('#im-user-' + whisper.sender_id + ' img').attr('src'));
      li.find('span.message-body').html(whisper.body);

      conversation.find('ul.conversations').append(li);
      $(window).scrollTop(li.position().top);
    }
  },

  initIMChat: function(user_id) {
    var self = this;
    if( self.imConversationContainer.find('#conversation-user-' + user_id).length ) {
      return;
    }

    var conversation = self.imConversationUser0.clone();
    conversation.attr('id', 'conversation-user-' + user_id);
    conversation.find('.conversation-options > .user-name').html(self.imUsers.find('#im-user-' + user_id + ' h3').html());
    conversation.find('input').bind('keypress', function(event) {
      if( event.which == 13 && $(this).val()) {
        var message = $(this).val();
        message = self.stripTags(message);
        self.whisper({'format':'json', 'user_id':user_id, 'message':message});
        $(this).val('');
      }
    });
    conversation.find('.conversation-options > .ui-icon-delete').bind('vclick', function() {
      self.whisper_close({'format':'json', 'user_id':user_id});
      self.imConversationContainer.hide();
      self.imUserContainer.hide();
      self.activePage.addClass('ui-page-active');
      self.activePage = null;
      conversation.remove();
      conversation = null;
    });
    conversation.find('.conversation-options > .ui-icon-minus').bind('vclick', function() {
      self.imConversationContainer.hide();
      self.imUserContainer.show();
    });

    self.imConversationContainer.append(conversation);
  },

  stripTags: function($html) {
    var html = $('<div>' + $html + '</div>');
    return html.text();
  }
}