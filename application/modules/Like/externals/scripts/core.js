/* $Id: core.js 2010-09-07 16:02 idris $ */

var LikeButton = new Class({

  Implements : [Options],

  options: {
    object_type: '',
    object_id: 0,
    likeBtn: '',
    loader: '',
    menuHtml: '',
    menuId: '',
    suggestBtn: '',
    likeUrl: en4.core.baseUrl + 'like/like',
    unlikeUrl: en4.core.baseUrl + 'like/unlike',
    switcher: ''
  },

  unlike_class: 'unlike',

  like_class: 'like',

  block: false,

  menuContainer: null,

  initialize: function(options) {
    this.setOptions(options);
    this.options.likeBtn = $(this.options.likeBtn);
    this.options.loader = $(this.options.loader);
    this.options.switcher = $(this.options.switcher);

    if (this.options.likeBtn){
      if (this.options.likeBtn.hasClass(this.unlike_class)){
        this.setUnlike();
      } else {
        this.setLike();
      }
    }

    this.init_menu();
  },

  init_menu: function() {
    var $div = new Element('div', {'html': this.options.menuHtml, 'style':'position:absolute;', 'id':this.options.menuId});
    this.menuContainer = $div = $div.getElements('.like_container_menu_wrapper')[0];
    $$('body')[0].appendChild($div);

    this.initPosition();

    this.options.suggestBtn = $$(this.options.suggestBtn)[0];
    this.init_suggest_link();

    if (this.options.switcher) {
      var self = this;
      this.options.switcher.addEvent('click', function() {
        self.toggle_menu();
      });
    }

    if (Smoothbox && Smoothbox.init){
    Smoothbox.init();
    }
  },

  initPosition: function() {
    var $switcher =  $(this.options.likeBtn).getNext();
    var position = $switcher.getPosition();
    if (this.menuContainer != undefined){
      if(en4.orientation == 'rtl'){
        this.menuContainer.setStyle('left', position.x - this.menuContainer.getSize().x);
      }
      else
      {
        this.menuContainer.setStyle('left', position.x + 20);
      }
      this.menuContainer.setStyle('top', position.y);
    }
  },

  init_suggest_link: function() {
    var self = this;

    if (!this.options.suggestBtn) {
      return ;
    }

    this.options.suggestBtn.addEvent('click', function(e) {
      e.stop();
      like.url.suggest = this.href;
      he_contacts.box('like', 'getFriends', 'like.suggest', en4.core.language.translate('like_Suggest to Friends'), {
        'object': self.options.object_type,
        'object_id': self.options.object_id
      }, 0);
    });
  },

  like: function() {
    var self = this;
    if (this.block) {
      return ;
    }
    this.showLoader();
    this.block = true;
    new Request.JSON({
      method: 'post',
      url: self.options.likeUrl,
      data: {
        format: 'json'
      },
      onSuccess: function(response){
				self.block = false;

				if (response.error) {
					he_show_message(response.html, 'error', 3000);
					return ;
				}
        self.hideLoader();
        self.toggle();
        return true;
      }
    }).send();
  },

  unlike: function(){
    var self = this;
    if (this.block){
      return ;
    }
    self.showLoader();
    this.block = true;
    new Request.JSON({
      method: 'post',
      url: self.options.unlikeUrl,
      data: {
        format: 'json'
      },
      onSuccess: function(response){
				self.block = false;

				if (response.error) {
					he_show_message(response.html, 'error', 3000);
					return ;
				}
        self.hideLoader();
        self.toggle();
        return true;
      }
    }).send();
  },

  toggle: function(){
    var $link = $(this.options.likeBtn);
    if ($link.hasClass(this.unlike_class)){
      this.setLike();
    }else{
      this.setUnlike();
    }
    this.initPosition();
  },

  toggle_menu: function() {
    var menu = this.menuContainer
    var link = this.options.switcher;
    if (menu.hasClass('hidden')){
      menu.removeClass('hidden');
      link.getElements('span')[0].set('class', 'hide_options');
      link.set('title', en4.core.language.translate('like_Hide'));
      this.initPosition();
    } else {
      menu.addClass('hidden');
      link.getElements('span')[0].set('class', 'show_options');
      link.set('title', en4.core.language.translate('like_Show Like'));
    }
  },

  setLike: function() {
    var $link = $(this.options.likeBtn);
    var self = this;
    $link.getElements('span')[0].set('class', 'like_button');
    $link.getElements('span')[0].set('text', en4.core.language.translate('like_Like'));
    $link.set('class', 'like_button_link ' + self.like_class);
    $link.removeEvents('click');
    $link.addEvent('click', function(){
      self.like();
    });

    return this;
  },

  setUnlike: function() {
    var $link = $(this.options.likeBtn);
    var self = this;
    $link.removeEvents('click');
    $link.getElements('span')[0].set('class', 'unlike_button');
    $link.getElements('span')[0].set('text', en4.core.language.translate('like_Unlike'));
    $link.set('class', 'like_button_link ' + self.unlike_class);
    $link.addEvent('click', function(){
      self.unlike();
    });
    return this;
  },

  showLoader: function() {
    this.options.likeBtn.addClass('hidden');
    if (this.options.switcher) {
      this.options.switcher.addClass('hidden');
    }
    this.options.loader.removeClass('hidden');
  },

  hideLoader: function() {
    this.options.likeBtn.removeClass('hidden');
    if (this.options.switcher) {
      this.options.switcher.removeClass('hidden');
    }
    this.options.loader.addClass('hidden');
  }

});

var like = {

	block: false,
	sender: {},
	url: {ajax:'', suggest:'', remove:''},
	clubs: {pages:{count:0, items:[]}, users:{count:0, items:[]}},
  loader_id: 'he_contacts_loading',
  disabled_div: 'he_disabled_div',
  $link: {},
  method: 'post',
  format: 'json',
  unlike_class: 'unlike',
  like_class: 'like',
  like_url: '',
  unlike_url: '',
  menu_html: '',

	init: function() {
		var self = this;

		$$('.disable_like_club, .enable_like_club').addEvent('click', function(e){
			e.stop();
			self.url.ajax = this.href;
			self.sender = this;
			self.send();
		});

		if ($$('a.like_display_none')){
			$$('a.like_display_none').each(function($item){
				$item.getParent().setStyle('display', 'none');
			});
		}

    var guid = en4.core.subject.guid;
    if ($('_'+guid)){
      this.$link = $('_'+guid);
      if (this.$link.hasClass(this.unlike_class)){
        this.set_unlike();
      }else{
        this.set_like();
      }
    }

		this.init_menu();
	},

  init_menu: function() {
    var $div = new Element('div', {'html': this.menu_html, 'style':'position:absolute;'});
    $div = $div.getElements('.like_container_menu_wrapper')[0];
    $$('body')[0].appendChild($div);
    var $switcher =  $(this.$link).getNext();
    var position = $switcher.getPosition();

    $div.setStyle('left', position.x + 20);
    $div.setStyle('top', position.y);

    this.init_suggest_link();
    Smoothbox.init();
  },

  init_suggest_link: function() {
    var self = this;

    if (!$$('.like_suggest')) {
      return ;
    }
    $$('.like_suggest').addEvent('click', function(e) {
      e.stop();
      self.url.suggest = this.href;
      he_contacts.box('like', 'getFriends', 'like.suggest', en4.core.language.translate('like_Suggest to Friends'), {'object':window.en4.core.subject.type, 'object_id':window.en4.core.subject.id}, 0);
    });
  },

  set_menu_pos: function() {
    var $div = $$('.like_container_menu_wrapper')[0];

    var $switcher =  $(this.$link).getNext();
    var position = $switcher.getPosition();

    $div.setStyle('left', position.x + 20);
    $div.setStyle('top', position.y);
  },

  like: function() {
    var self = this;
    if (this.block) {
      return ;
    }
    self.show_like_loader();
    this.block = true;
    new Request.JSON({
      method: self.method,
      url: self.like_url,
      data: {
        format: self.format
      },
      onSuccess: function(response){
				self.block = false;

				if (response.error){
					he_show_message(response.html, 'error', 3000);
					return ;
				}
        self.hide_like_loader();
        self.toggle();
        return true;
      }
    }).send();
  },

  unlike: function(){
    var self = this;
    if (this.block){
      return ;
    }
    self.show_like_loader();
    this.block = true;
    new Request.JSON({
      method: self.method,
      url: self.unlike_url,
      data: {
        format: self.format
      },
      onSuccess: function(response){
				self.block = false;

				if (response.error){
					he_show_message(response.html, 'error', 3000);
					return ;
				}
        self.hide_like_loader();
        self.toggle();
        return true;
      }
    }).send();
  },

  toggle: function(){
    var $link = $(this.$link);
    if ($link.hasClass(this.unlike_class)){
      this.set_like();
    }else{
      this.set_unlike();
    }
  },

  set_like: function() {
    var $link = $(this.$link);
    var self = this;
    $link.getElements('span')[0].set('class', 'like_button');
    $link.getElements('span')[0].set('text', en4.core.language.translate('like_Like'));
    $link.set('class', 'like_button_link ' + self.like_class);
    $link.removeEvents('click');
    $link.addEvent('click', function(){
      self.like();
    });
    this.$link = $($link);
    return this.$link;
  },

  set_unlike: function() {
    var $link = $(this.$link);
    var self = this;
    $link.removeEvents('click');
    $link.getElements('span')[0].set('class', 'unlike_button');
    $link.getElements('span')[0].set('text', en4.core.language.translate('like_Unlike'));
    $link.set('class', 'like_button_link ' + self.unlike_class);
    $link.addEvent('click', function(){
      self.unlike();
    });
    this.$link = $($link);
    return $link;
  },

  show_like_loader: function() {
    $(this.$link).addClass('hidden');
    if ($$('.like_menu_switcher')[0]) {
      $$('.like_menu_switcher')[0].addClass('hidden');
    }
    $$('.like_button_loader')[0].removeClass('hidden');
  },

  hide_like_loader: function() {
    $(this.$link).removeClass('hidden');
    if ($$('.like_menu_switcher')[0]) {
      $$('.like_menu_switcher')[0].removeClass('hidden');
    }
    $$('.like_button_loader')[0].addClass('hidden');
  },

	init_counts: function(){
		var self = this;

		if ($('like_users_count')){
			$('like_users_count').innerHTML = "(" + this.clubs.users.count + ")";
		}

		if ($('like_pages_count')){
			$('like_pages_count').innerHTML = "(" + this.clubs.pages.count + ")";
		}

		if ($$('.like_profile_pages')[0]){
			$$('.like_profile_pages')[0].addEvent('click', function(){
				self.make_active(this);
			});
		}

		if ($$('.like_profile_users')[0]){
			$$('.like_profile_users')[0].addEvent('click', function(){
				self.make_active(this);
			});
			this.make_active($$('.like_profile_users')[0]);
		}

		if ($$('.menu_like_profile')){
			$$('.menu_like_profile').addEvent('focus', function(){
				this.blur();
			});
		}

		if (!this.clubs.users.count){
			if ($$('.no_result_users')) $$('.no_result_users').removeClass('hidden');
		}

		if (!this.clubs.pages.count){
			if ($$('.no_result_pages')) $$('.no_result_pages').removeClass('hidden');
		}
	},

	make_active: function($link) {
		if ($$('ul.like_navigation li.active')) $$('ul.like_navigation li.active').removeClass('active');

		$link.getParent().addClass('active');
	},

	list: function(type, $link) {
		var id = "likes_"+type;

//		$$('.like_navigation_item a').each(function($item){
//			$item.removeClass('active');
//		});
		$$('.like_navigation_item').removeClass('active');
		$($link).getParent().addClass('active');

		if (!$(id).hasClass('hidden')){
			return ;
		}

		$$('.like_club_container').addClass('hidden');
		$(id).removeClass('hidden');
	},

	suggest: function(user_ids) {
		var self = this;

		new Request.JSON({
			'url': self.url.suggest,
			'method': 'post',
			'data': {
				'user_ids': user_ids,
				'format': 'json'
			}
		}).send();
	},

	send: function() {
		if (this.block || !this.url.ajax) {
			return;
		}

		this.block = true;
		var self = this;
		new Request.JSON({
			'url': self.url.ajax,
			'method': 'post',
			'data': {
				'format': 'json'
			},
			onSuccess: function(response) {
				self.sender.getParent().innerHTML = response.html;
				self.init();
				self.block = false;

				switch (response.link){
					case 'disable':
						if ($$('.like_promote')[0]) $$('.like_promote')[0].getParent().setStyle('display', 'block');
						if ($$('.like_send_update')[0]) $$('.like_send_update')[0].getParent().setStyle('display', 'block');
						if ($$('.like_suggest')[0]) $$('.like_suggest')[0].getParent().setStyle('display', 'block');
            if ($$('.like_button_container')[0]) $$('.like_button_container')[0].setStyle('display', 'block');
					break;
					case 'enable':
						if ($$('.like_promote')[0]) $$('.like_promote')[0].getParent().setStyle('display', 'none');
						if ($$('.like_send_update')[0]) $$('.like_send_update')[0].getParent().setStyle('display', 'none');
						if ($$('.like_suggest')[0]) $$('.like_suggest')[0].getParent().setStyle('display', 'none');
            if ($$('.like_button_container')[0]) $$('.like_button_container')[0].setStyle('display', 'none');
					break;
				}
			}
		}).send();
	},

	see_all: function(object, object_id, period_type) {
		he_list.box('like', 'getLikes', 'Likes', {'object':object, 'object_id':object_id, 'period_type':period_type});
	},

  see_all_liked: function(user_id, period_type) {
    var $el = new Element('a', {'href': 'like/see-liked/user_id/'+user_id+'/period_type/'+period_type, 'class': 'smoothbox'});
    Smoothbox.open($el);
  },

  list_like: function(object, object_id, $element, callback){
    var self = this;
    $element = $($element);
    if (this.block){
      return false;
    }
    this.block = true;
    new Request.JSON({
      'url':'like/like/object/'+object+'/object_id/'+object_id,
      'method':'post',
      'data':{
        'format':'json'
      },
      onSuccess: function(response){
        self.block = false;
        if (response.error){
					he_show_message(response.html, 'error', 3000);
          callback(false);
					return ;
				}
        $('unlike_'+object+'_'+object_id).getParent().setStyle('display', 'block');
        $('like_'+object+'_'+object_id).getParent().setStyle('display', 'none');

        if (typeof(callback) == 'function') {
          callback(true);
        }
      }
    }).send();
  },

  list_unlike: function(object, object_id, $element, callback) {
    var self = this;
    $element = $($element);
    if (this.block){
      return false;
    }
    this.block = true;
    new Request.JSON({
      'url':'like/unlike/object/'+object+'/object_id/'+object_id,
      'method':'post',
      'data':{
        'format':'json'
      },
      onSuccess: function(response){
        self.block = false;
        if (response.error){
					he_show_message(response.html, 'error', 3000);
          callback(true);
					return ;
				}
        $('unlike_'+object+'_'+object_id).getParent().setStyle('display', 'none');
        $('like_'+object+'_'+object_id).getParent().setStyle('display', 'block');

        if (typeof(callback) == 'function') {
          callback(false);
        }
      }
    }).send();
  },

  init_buttons: function(){
    var self = this;
    if ($$('div.like_button_container a.unlike')) {
      var $btns = $$('.like_button_container a.unlike');
      $btns.removeEvents('click');
      $btns.each(function($item){
        if ($item.hasClass('unlike')){
          $item.addEvent('click', function(){
            var id = this.id;
            var guid = id.substr(7);
            var info = guid.split('_');
            self.list_unlike(info[0], info[1], $item);
          });
        }
      });
    }

    if ($$('div.like_button_container a.like')){
      var $btns = $$('.like_button_container a.like');
      $btns.removeEvents('click');
      $btns.each(function($item){
        if ($item.hasClass('like')){
          $item.addEvent('click', function(){
            var id = this.id;
            var guid = id.substr(5);
            var info = guid.split('_');
            self.list_like(info[0], info[1], $item);
          });
        }
      });
    }
  },

  init_like_buttons: function(){
    var self = this;
    if ($$('.page_browser_likebox a.unlike')) {
      var $btns = $$('.page_browser_likebox a.unlike');
      $btns.removeEvents('click');
      $btns.each(function($item){
        if ($item.hasClass('unlike')){
          $item.addEvent('click', function(){
            var id = this.id;
            var guid = id.substr(7);
            var info = guid.split('_');
            if (info.length == 3){
              self.show_page_loader(info[2]);
              self.list_unlike(info[0]+'_'+info[1], info[2], $item, function(liked){
                self.hide_page_loader(info[2], liked);
              });
            } else {
              self.show_page_loader(info[1]);
              self.list_unlike(info[0], info[1], $item, function(liked){
                self.hide_page_loader(info[1], liked);
              });
            }
          });
        }
      });
    }

    if ($$('.page_browser_likebox a.like')){
      var $btns = $$('.page_browser_likebox a.like');
      $btns.removeEvents('click');
      $btns.each(function($item){
        if ($item.hasClass('like')){
          $item.addEvent('click', function(){
            var id = this.id;
            var guid = id.substr(5);
            var info = guid.split('_');
            if (info.length == 3) {
              self.show_page_loader(info[2]);
              self.list_like(info[0]+'_'+info[1], info[2], $item, function(liked){
                self.hide_page_loader(info[2], liked);
              });
            } else {
              self.show_page_loader(info[1]);
              self.list_like(info[0], info[1], $item, function(liked){
                self.hide_page_loader(info[1], liked);
              });
            }
          });
        }
      });
    }
  },

  show_page_loader: function(id) {
    $('page_status_' + id).addClass('hidden');
    $('page_loader_like_' + id).removeClass('hidden');
  },

  hide_page_loader: function(id, liked) {
    $('page_status_' + id).removeClass('hidden');
    $('page_loader_like_' + id).addClass('hidden');

    var $item = $('page_status_' + id).getParent('.item');
    if (liked) {
      $item.addClass('liked_item');
    } else {
      $item.removeClass('liked_item');
    }
  },

  select_list: function(list, user_id, $link){
    var self = this;
    self.show_loader();
    new Request.JSON({
      'url': 'like/see-liked/list/'+list+'/user_id/'+user_id,
      'method': 'post',
      'data': {
        'format': 'json'
      },
      onSuccess: function(response){
        $('he_list').innerHTML = response.html;
        if ($$('.select_btns .active')){
          $$('.select_btns .active').each(function($element){
            $($element).removeClass('active');
          });
        }
        $($link).addClass('active');
        self.hide_loader();
        self.init_buttons();
      }
    }).send();
  },

	do_remove: function(object, object_id){
	  var self = this;
    var $item = $('like_'+object+'_'+object_id);
    $item.dispose();

    new Request.JSON({
      'url':self.url.remove,
      'method':'post',
      'data':{
        'object': object,
        'object_id':object_id,
        'format':'json'
      },
      onSuccess: function(response){
				if (response.error){
					he_show_message(response.html, 'error', 3000);
					return ;
				}
        self.counting(object+'s', -1);
      }
    }).send();
	},

	remove: function(object, object_id){
		var self = this;
		var callback = function(){self.do_remove(object, object_id)};
		he_show_confirm(en4.core.language.translate("like_Unlike"), en4.core.language.translate("like_Are you sure you want to unlike this?"), callback);
	},

	counting: function(type, count){
		var prev = $('like_'+type+'_count').innerHTML.substr(1).toInt();
		var next = prev + count;
		$('like_'+type+'_count').innerHTML = "("+next+")";
		if (!next){
			$$('.no_result_'+type).removeClass('hidden');
		}
	},

  show_loader: function() {
    $(this.loader_id).removeClass('hidden');
    $(this.disabled_div).removeClass('hidden');
  },

  hide_loader: function() {
    $(this.loader_id).addClass('hidden');
  },

  toggle_menu: function($link) {
    $link = $($link);
    if ($$('.like_container_menu_wrapper')[0].hasClass('hidden')){
      $$('.like_container_menu_wrapper')[0].removeClass('hidden');
      $link.getElements('span')[0].set('class', 'hide_options');
      $link.set('title', en4.core.language.translate('like_Hide'));
      this.set_menu_pos();
    } else {
      $$('.like_container_menu_wrapper')[0].addClass('hidden');
      $link.getElements('span')[0].set('class', 'show_options');
      $link.set('title', en4.core.language.translate('like_Show Like'));
    }
  },

	get_mutual_friends: function(user_id){
		he_list.box('like', 'getMutualFriends', en4.core.language.translate('Friends'), {'user_id':user_id, 'list_type':'mutual'});
	},

	get_friends: function(user_id){
		he_list.box('like', 'getFriends', en4.core.language.translate('Friends'), {'user_id':user_id, 'list_type':'all'});
	}
}

var LikeTips = new Class({

	Implements : [Options],

	type : '',

	id : 0,

	guid : '',

	options : {
		container : 'comments',
		html : '',
		url : {
			like : '',
			unlike : '',
			hint : '',
			showLikes : '',
			postComment : ''
		}
	},

	likeBtn : null,

	unlikeBtn : null,

	commentBtn : null,

	commentForm : null,

	commentViewAllBtns : [],

	container : null,

	likeContainer : null,

	likeCountBtn : null,

	viewAllLikes : false,

	cache : {},

	tipBlock : {},

	blockHints : false,

	timeout : null,

  tips : null,

	initialize : function(type, id, options) {

		// Init Options
		this.type = type;
		this.id = id;
		this.guid = type + '_' + id;
		this.setOptions(options);

		// Init tip
		this.tipBlock = this.createTipBlock();

		// Init main container
		this.container = $(this.options.container);

		// Replace with our html
		this.replace();
		this.init();
		this.bind();

		return this;
	},

	createTipBlock : function() {

		if ($('like_tip_'+this.guid)) {
			return $('like_tip_'+this.guid);
		}

		var $container = new Element('div', {'class':'like_tool_tip hidden', 'id':'like_tip_'+this.guid, 'style':'position:absolute;'});
		var $header = new Element('div', {'class':'like-tip-title'});
		var $content = new Element('div', {'class':'like-tip'});
		var $footer = new Element('div', {'class':'like-tip-footer'});
		var $clr = new Element('div', {'class':'clr'});

		$container.appendChild($header);
		$container.appendChild($content);
		$container.appendChild($footer);
		$container.appendChild($clr);

		$$('body')[0].appendChild($container);

		return $container;
	},

	init: function() {

		// Init containers
		this.likeContainer = $('comments_likes_list_'+this.guid);

		// Init buttons
		this.likeCountBtn = $('show_likes_'+this.guid);
		this.likeBtn = $('comments_like_'+this.guid);
		this.unlikeBtn = $('comments_unlike_'+this.guid);
		this.commentBtn = $('post_comment_'+this.guid);
		this.commentViewAllBtns = $$('.comments_view_all_'+this.guid);

		// Init comment form
		this.commentForm = $('comment-form_'+this.guid);

		// Binding buttons
		if (this.likeCountBtn) {
			this.bindLikeCountBtn();
		}
		if (this.likeBtn){
			this.bindLikeBtn();
		}
		if (this.unlikeBtn) {
			this.bindUnlikeBtn();
		}
		if (this.commentBtn){
			this.bindCommentBtn();
		}
		if (this.commentViewAllBtns) {
			this.bindCommentViewAllBtns();
		}

		// Binding comment form
		if (this.commentForm){
			this.bindCommentForm();
		}

		return this;
	},

	replace : function(html) {
		if (!html) {
			html = this.options.html;
		}

    if (this.container){
		this.container.set('html', html);
    }
		// this.runScripts();

		return this;
	},

	runScripts : function() {
		$$(this.container.getElements('script')).each(function($script) {
			var script = $script.innerHTML;
			eval(script);
		});

		return this;
	},

	bind : function() {
		var self = this;

    if (!$(this.likeContainer)) {
      return ;
    }

		var $likes = $$($(this.likeContainer).getElements('a'));
    if ($likes.length) {
      $likes.each(function($item) {
        self.bindItem($item);
      });
    }
	},

	bindItem : function($item) {
		var self = this;
    if (!$item) {
      return ;
    }
		if ($item.href.indexOf('javascript') >= 0){
			return ;
		}
		$($item).addEvents({
			'mouseover' : function() {
				var username = this.href.split('/').pop();
				var x = $(this).getPosition().x;
				var y = $(this).getPosition().y;
				self.timeout = window.setTimeout(function() {
					self.hint(username, x, y);
				}, 500);
			},
			'mouseout' : function() {
				window.clearTimeout(self.timeout);
				self.timeout = window.setTimeout(function() {
					self.hideTip();
				}, 500);
			}
		});
	},

	hint : function(username, x, y) {
		var self = this;
		if (this.blockHints) {
			return ;
		}
		if (this.cache[username]) {
			this.showTip(this.cache[username], x, y);
			this.blockHints = false;
			return ;
		}
		this.blockHints = true;
		new Request.JSON({
			'method' : 'post',
			'url' : self.options.url.hint,
			'data' : {
				'username' : username,
				'format' : 'json'
			},
			onSuccess : function(response) {
				self.cache[username] = response.html;
				self.showTip(response.html, x, y);
				self.blockHints = false;
			}
		}).send();
	},

	showTip : function(html, x, y) {
		var self = this;

		$(this.tipBlock).getElements('.like-tip')[0].set('html', html);
		$(this.tipBlock).setStyle('left', x);
		$(this.tipBlock).setStyle('top', (y-175));
		$(this.tipBlock).removeClass('hidden');

		Smoothbox.bind();

		var miniTipsOptions = {
			'htmlElement': '.like_hint_text',
			'delay': 100,
			'className': 'he-tip-mini',
			'id': 'he-mini-tool-tip-id',
			'ajax': false,
			'visibleOnHover': false
		};

		var internalTips = new HETips($$('.like_hint_tip_links'), miniTipsOptions);

		this.tipBlock
			.removeEvents('mouseover')
			.removeEvents('mouseout')
			.addEvents({
				'mouseover' : function() {
          if(self.options.visibleOnHover){
					  window.clearTimeout(self.timeout);
          }
				},
				'mouseout' : function() {
					self.timeout = window.setTimeout(function() {
						self.hideTip();
					},500);
				}
			});
	},

	hideTip : function() {
		this.tipBlock.addClass('hidden');
	},

	bindLikeCountBtn : function() {
		var self = this;
		this.resetLikeCountBtn();
		var $btn = this.likeCountBtn;
		$btn
			.removeEvents('click')
			.addEvent('click', function(){
				self.viewAllLikes = true;
				new Request.JSON({
					'method' : 'post',
					'url' : self.options.url.showLikes,
					'data' : {
						'format' : 'json',
						'type' : self.type,
						'id' : self.id,
						'viewAllLikes' : self.viewAllLikes
					},
					onSuccess : function(response) {
						self.replace(response.body);
						self.init();
						self.bind();
					}
				}).send();
			});

		return this;
	},

	bindLikeBtn : function() {
		var self = this;
		this.resetLikeBtn();
		var $btn = this.likeBtn;
		$btn
			.removeEvents('click')
			.addEvent('click', function() {
				new Request.JSON({
					'method' : 'post',
					'url' : self.options.url.like,
					'data' : {
						'format' : 'json',
						'type' : self.type,
						'id' : self.id,
						'viewAllLikes' : self.viewAllLikes
					},
					onSuccess : function(response) {
						self.replace(response.body);
						self.init();
						self.bind();
					}
				}).send();
			});

		return this;
	},

	bindUnlikeBtn : function() {
		var self = this;
		this.resetUnlikeBtn();
		var $btn = this.unlikeBtn;
		$btn
			.removeEvents('click')
			.addEvent('click', function() {
				new Request.JSON({
					'method' : 'post',
					'url' : self.options.url.unlike,
					'data' : {
						'format' : 'json',
						'type' : self.type,
						'id' : self.id,
						'viewAllLikes' : self.viewAllLikes
					},
					onSuccess : function(response) {
						self.replace(response.body);
						self.init();
						self.bind();
					}
				}).send();
			});

		return this;
	},

	bindCommentBtn : function() {
		var self = this;
		this.resetCommentBtn();
		var $btn = this.commentBtn;
		$btn
			.removeEvents('click')
			.addEvent('click', function() {
				if (self.commentForm){
					self.commentForm.style.display = '';
					self.commentForm.body.focus();
				}
			});

		return this;
	},

	bindCommentForm : function() {
		var self = this;
		this.resetCommentForm();
		var $form = $(this.commentForm);
		$($form.body).autogrow();
		$form
			.removeEvents('submit')
			.addEvent('submit', function(e) {
				e.stop();
				var body = this.body.value;
				new Request.JSON({
					'method' : 'post',
					'url' : self.options.url.postComment,
					'data' : {
						'format' : 'json',
						'type' : self.type,
						'id' : self.id,
						'body' : body,
						'viewAllLikes' : self.viewAllLikes
					},
					onSuccess : function(response) {
						self.replace(response.body);
						self.init();
						self.bind();
						$form.style.display = 'none';
					}
				}).send();
			});

		return this;
	},

	bindCommentViewAllBtns : function() {
		var self = this;
		this.resetCommentViewAllBtns();
		var $btns = this.commentViewAllBtns;
		$btns
			.removeEvents('click')
			.addEvent('click', function() {
				var page = this.id.split('_').pop();
				new Request.JSON({
					'method' : 'post',
					'url' : self.options.url.showLikes,
					'data' : {
						'format' : 'json',
						'type' : self.type,
						'id' : self.id,
						'page' : page
					},
					onSuccess : function(response) {
						self.replace(response.body);
						self.init();
						self.bind();
					}
				}).send();
			});

		return this;
	},

	resetLikeCountBtn : function() {
		this.likeCountBtn.set('onclick', '');
		return this;
	},

	resetLikeBtn : function() {
		this.likeBtn.set('onclick', '');
		return this;
	},

	resetUnlikeBtn : function() {
		this.unlikeBtn.set('onclick', '');
		return this;
	},

	resetCommentBtn : function() {
		this.commentBtn.set('onclick', '');
		return this;
	},

	resetCommentForm : function() {
		$(this.commentForm).set('onsubmit', '');
		return this;
	},

	resetCommentViewAllBtns : function() {
		this.commentViewAllBtns.set('onclick', '');
	}
});


var likeInterest = {

	data : {},

	interests : {},

	html : null,

	id : 0,

	button : "save_changes",

	$button : null,

	url : {
		add : '',
		remove : '',
		suggest : ''
	},

	defaultText : {},

	defaultTextClass : 'default_text',

	autoCompleters : {},

	interestItemClass : '.select',

	interestSelectedClass : 'selected',

	linkIdPrefix : 'select_',

	inputIdPrefix : 'interest_',

	selectedInterestWrapperIdPrefix : 'selected_interest_',

	linkListIdPrefix : 'interests_list_',

	addedInterests : null,

  addedInterestsFake : null,

	isChanged : null,

	addedFiedlId : 'added',

	privacyOptions : '.like_interests_privacy .options .option',

	privacyValue : '',


	init : function(id) {
    this.id = id;
		this.initButton();
    this.initInputs();
    this.initInterests();
		this.initPrivacy();
	},

	input : function(type) {
		return $(this.data[type].input);
	},

	initButton : function() {
		var self = this;
		this.$button = $(this.button);
		this.$button.addEvent('click', function(){
			var $form = self.createForm();
			$$('body')[0].appendChild($form);
			$form.submit();
		});
	},

	initPrivacy : function() {
		var self = this;
		var $buttons = $$(this.privacyOptions);
		$buttons.addEvent('click', function() {
			$buttons.removeClass('active');
			this.addClass('active');
			self.privacyValue = this.id;
		});
	},

	createForm : function() {
		var $form = new Element('form', {'action':this.url.submit, 'method':'post', 'class':'hidden'});
		for(var type in this.addedInterests){
			var ids = this.addedInterests[type];
			var $input = new Element('input', {'type':'hidden', 'name':'data['+type+']', 'value':ids});
			$form.appendChild($input);
		}

    for(var type in this.addedInterestsFake){
      var ids = this.addedInterestsFake[type];
      var $input = new Element('input', {'type':'hidden', 'name': 'fake_data['+type+']', 'value':ids});
      $form.appendChild($input);
    }

		var $input = new Element('input', {'type':'hidden', 'name':'view_interest', 'value':this.privacyValue});
		$form.appendChild($input);

		return $form;
	},

	initInterests : function(){
		var self = this;
		this.interests = $$(this.interestItemClass);
		this.interests.removeEvents('click').addEvents({
			'click': function(){
				self.initLink(this);
			},
			'focus': function(){
				this.blur();
			}
		});
	},

	initLink : function($link){
		this.interests.removeClass(this.interestSelectedClass);
		$link.addClass(this.interestSelectedClass);
		var guid = $link.id.substr(this.linkIdPrefix.length);
		var data = guid.split('_');
		var type = data[0];
		if (data.length > 2){
			for (var i=1; i<(data.length-1); i++) {
				type += '_'+data[i];
			}
		}
		var id = data[data.length-1];
		this.showInterest(type, id);
	},

	showInterest : function(type, id){
    var previewType = this.convertType(type);
		$(this.selectedInterestWrapperIdPrefix + previewType).set('html', this.html[type+'_'+id]);
	},

	showInterestGuid : function(guid){
		var data = guid.split('_');
		var type = data[0];
		if (data.length > 2){
			for (var i=1; i<(data.length-1); i++) {
				type += '_'+data[i];
			}
		}
		var id = data[data.length-1];
		this.showInterest(type, id);
	},

	initInputs : function() {
		var self = this;
		var data = {id : self.id};
    if (this.data && this.data.length < 1) {
      return;
    }

		for(var type in this.data) {
			data.type = type;
			data.except = self.addedInterests[type];
      data.fake_except = self.addedInterestsFake[type];
      switch(type)
      {
        case 'event':
          data.page_except=self.addedInterests['pageevent'];
          break;
        case 'blog':
          data.page_except=self.addedInterests['pageblog'];
          break;
        case 'music_playlist':
          data.page_except=self.addedInterests['playlist'];
          break;
        case 'album':
          data.page_except=self.addedInterests['pagevideo'];
        default:
          break;
      }
			this.autoCompleters[type] = new Autocompleter.Request.JSON(this.data[type].input, this.url.suggest, {
				'postVar' : 'text',
				'postData' : data,
				'customChoices' : true,
				'minLength': 1,
				'selectMode': 'selection',
				'autocompleteType': 'interest',
				'className': 'interest-autosuggest',
				'filterSubset' : true,
				'multiple' : false,
				'maxChoices' : 5,
				'cache' : false,
				'injectChoice': function(token) {
					var choice = new Element('li', {'class':'interest-choice','value':token.label, 'id':token.id});
					var photo = new Element('div', {'html':'', 'class':'interest-choice-photo'});
					var a = new Element('a', {'html':'', 'class':'hidden href_holder', 'href':token.href});
					var img = new Element('img', {'src':token.img, 'class':'thumb_icon item_photo_interest_suggest'});
					var title = new Element('div', {'html':this.markQueryValue(token.label), 'class':'interest-choice-title'});
					var clr = new Element('div', {'html':'', 'class':'clr'});

					photo.appendChild(img);
					choice.appendChild(photo);
					choice.appendChild(title);
					choice.appendChild(a);
					choice.appendChild(clr);
					choice.inputValue = token;

					this.addChoiceEvents(choice).inject(this.choices);
					choice.store('autocompleteChoice', token);
				},
				onChoiceSelect: function(selected) {
					var guid = selected.id;
					var data = guid.split('_');
					var type = data[0];
					if (data.length > 2){
						for (var i=1; i<(data.length-1); i++) {
							type += '_'+data[i];
						}
					}
					var id = data[data.length-1];

					var interest = {};
					var label = $(selected).getElement('.interest-choice-title').get('text');

					interest.title = label;
					interest.img = $(selected).getElement('img').src;
					interest.href = $(selected).getElement('a.href_holder').href;

					self.newInterest(type, id, interest);
					self.newInterestLink(type, id, label);

          switch (type) {
            case 'pageevent':
              var previewType = 'event';
              break;
            case 'pageblog':
              var previewType = 'blog';
              break;
            case 'pagevideo':
              var previewType = 'video';
              break;
            case 'playlist':
              var previewType = 'music_playlist';
              break;
            default:
              var previewType = type;
              break;
          }
          $(self.data[previewType].input).value = '';
				},
				onBlur: function(input) {
					var type = input.id.substr(self.inputIdPrefix.length);
					if (input.value.trim() == ''){
						input.value = self.defaultText[type];
						$(input).addClass(self.defaultTextClass);
					}
				},
				onFocus: function(input) {
					var type = input.id.substr(self.inputIdPrefix.length);
					if (input.value == self.defaultText[type]){
						input.value = '';
						$(input).removeClass(self.defaultTextClass);
					}
				}
			});
		}
	},

	newInterestLink : function(type, id, title) {
    var previewType = this.convertType(type);
		var self = this;
		var $item = new Element('div', {'class':'item'});
		var $a = new Element('a', {'href':'javascript:void(0)', 'html':title, 'class':self.interestItemClass.substr(1), 'id':self.linkIdPrefix+type+'_'+id});

		$item.appendChild($a);
		$item.setStyle('opacity', 0);
		$item.set('tween', {duration: 300});

		var $list = $(this.linkListIdPrefix+previewType).grab($item, 'top');

		window.setTimeout(function(){
			$item.tween('opacity', 1);
		}, 300);

		this.initInterests();
		$($a).fireEvent('click');
	},

	newInterest : function(type, id, interest) {
		var $container = new Element('div');

		var $photo = new Element('div', {'class':'pic'});
		var $a = new Element('a', {'href':interest.href});
		var $img = new Element('img', {'src':interest.img, 'class':'thumb_icon item_photo_'+type});

    if(parseInt(id))
    {
      $a.appendChild($img);
      $photo.appendChild($a);
    }
    else
    {
      $photo.appendChild($img);
    }
		$container.appendChild($photo);

		var $wrapper = new Element('div', {'class':'wrapper'});
		var $link = new Element('div', {'class':'link'});
    if(parseInt(id))
    {
      var $a = new Element('a', {'href':interest.href, 'html':interest.title});
    }
    else
    {
      var $a = new Element('label', {'text':interest.title});
    }

		$link.appendChild($a);

		var $delete = new Element('div', {'class':'delete'});
    if(parseInt(id))
    {
      var $a = new Element('a', {'href':'javascript:likeInterest.doRemove("'+type+'", '+id+')', 'html':'Remove'});
    }
    else
    {
      var $a = new Element('a', {'href':'javascript:likeInterest.doRemove("'+type+'", "'+id+'")', 'html':'Remove'});
    }

		$delete.appendChild($a);

		$wrapper.appendChild($link);
		$wrapper.appendChild($delete);

		$container.appendChild($wrapper);
		var html = $container.innerHTML;
    if(this.addedInterests[type] === undefined)
    {
      this.addedInterests[type]=[];
    }
    if(parseInt(id))
    {
		  this.addedInterests[type].push(parseInt(id));
      this.refreshPostData(type);
    }
    else
    {
      this.addedInterestsFake[type].push(id);
      this.refreshPostData(type,true);
    }

		this.newInterestHTML(type, id, html);
		this.showInterest(type, id);
	},

	newInterestHTML : function(type, id, html) {
		this.html[type+'_'+id] = html;
	},

	remove : function(type, id) {
		var self = this;
		he_show_confirm(en4.core.language.translate('like_Delete Interest'), en4.core.language.translate('like_If you delete it, you will unlike this item. Are you sure you want to delete it?'), function(){
			self.doRemove(type, id);
		});
	},

	doRemove : function(type, id) {
		this.removeInterest(type, id);
		this.removeInterestLink(type, id);
	},

	removeInterest : function(type, id) {
    var previewType = this.convertType(type);
		var $wrapper = $(this.selectedInterestWrapperIdPrefix+previewType);

		var guid = type+'_'+id;

		delete this.html[guid];

    var last = 0;

    if(parseInt(id))
    {
		  this.addedInterests[type].erase(id);
      this.refreshPostData(type);

    }
    else
    {
      //If deleting fake like
      this.addedInterestsFake[type].erase(id);
      this.refreshPostData(type,true);

      if(this.addedInterestsFake[type].length)
      {
        last = this.addedInterestsFake[type][this.addedInterestsFake[type].length-1];
      }
      if(last)
      {
        $(this.linkIdPrefix+type+'_'+last).fireEvent('click');
        return;
      }
    }

		if (this.addedInterests[type].length){
			last = this.addedInterests[type][this.addedInterests[type].length-1];
		}

		if (last){
			$(this.linkIdPrefix+type+'_'+last).fireEvent('click');
		}
    else{
      // Если это page_type
      if(this.isPageType(type))
      {
        var new_type = this.convertType(type);
      }
      else
      {
         var new_type = this.convertToPageType(type);
      }
      if(this.addedInterests[new_type] === undefined)
      {
        this.addedInterests[new_type]=[];
      }
      if(this.addedInterests[new_type].length)
      {
        last = this.addedInterests[new_type][this.addedInterests[new_type].length-1];
      }
      if(last)
      {
        $(this.linkIdPrefix+new_type+'_'+last).fireEvent('click');
      }
      else
      {
        new_type = this.convertType(type);
        if(this.addedInterestsFake[new_type].length)
        {
           last = this.addedInterestsFake[new_type][this.addedInterestsFake[new_type].length-1];
        }
        if(last)
        {
          $(this.linkIdPrefix+new_type+'_'+last).fireEvent('click');
        }
        else
        {
          $wrapper.set('html', ' &nbsp; ');
        }
      }
		}
	},

	removeInterestLink : function(type, id) {
		var $link = $(this.linkIdPrefix+type+'_'+id);
		$link.dispose();
	},

	refreshPostData : function(type, fake){
    if(fake)
    {
      this.autoCompleters[type].options.postData.fake_except = this.addedInterestsFake[type];
      return;
    }
    switch(type)
    {
      case 'pageevent':
        this.autoCompleters['event'].options.postData.page_except = this.addedInterests[type];
        break;
      case 'pageblog':
        this.autoCompleters['blog'].options.postData.page_except = this.addedInterests[type];
        break;
      case 'pagevideo':
        this.autoCompleters['video'].options.postData.page_except = this.addedInterests[type];
        break;
      case 'playlist':
        this.autoCompleters['music_playlist'].options.postData.page_except = this.addedInterests[type];
        break;
      default:
        this.autoCompleters[type].options.postData.except = this.addedInterests[type];
        break;
    }
	},

  convertType: function(type){
    switch (type) {
      case 'pageevent':
        var previewType = 'event';
        break;
      case 'pageblog':
        var previewType = 'blog';
        break;
      case 'pagevideo':
        var previewType = 'video';
        break;
      case 'playlist':
        var previewType = 'music_playlist';
        break;
      default:
        var previewType = type;
        break;
    }
    return previewType;
  },

  convertToPageType: function(type)
  {
    switch(type)
    {
      case 'event':
        var pageType = 'pageevent';
        break;
      case 'blog':
        var pageType = 'pageblog';
        break;
      case 'video':
        var pageType = 'pagevideo';
        break;
      case 'music_playlist':
        var pageType = 'playlist';
        break;
      default:
        var pageType = type;
        break;
    }
    return pageType;
  },

  isPageType: function(type)
  {
    if(type=='playlist')
      return 1;
    else
      return type.indexOf('page')+1;
  }
};

var LikeAction = new Class({

	Implements : [Options],

	options : {
		userIds : [],
		likecount : 0
	},

	nodeId : '',

	$node : null,

	viewerId : 0,

	viewerLiked : false,

	initialize : function(nodeId, options) {
		this.nodeId = nodeId;
		this.$node = $(nodeId);
		this.viewerId = en4.user.viewer.id;
		this.setOptions(options);
		this.viewerLiked = this.options.userIds.indexOf(this.viewerId) < 0 ? false : true;

		this.render();
	},

	render : function() {
		var out = 'like_';
		var likeCount = this.options.likeCount;

		if (this.viewerLiked) {
			out += 'You ';
			likeCount--;
			if (likeCount) {
				out += 'and ';
			}
		}

		if (likeCount > 1) {
			out += '%s other people ';
		} else if (likeCount == 1) {
			out += '%s other person ';
		} else if (!this.viewerLiked) {
			out += 'No one ';
		}

		out += 'like it.'

		this.$node.set('html', en4.core.language.translate(out, likeCount));
		return out;
	}

});