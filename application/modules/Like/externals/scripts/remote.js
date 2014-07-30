/* $Id: remote.js 2010-09-07 16:02 idris $ */

var like_api = {
  likes: null,
  viewer: null,
  like_url: '',
  unlike_url: '',
  viewer_url: '',
  urls: {},
  like_img: '',
  unlike_img: '',
  like_count: 0,
  block: false,
  login_url: '',
  unlike_class: 'unlike',
  like_class: 'like',
  likes_container_id: 'like_button_likes',
  subject_guid: '',
  $link: null,
  method: 'post',
  format: 'json',
  like_button_container: 'like_button_wrapper',
  $like_button_container: {},

  like: function(){
    var self = this;
    if (this.block){
      return ;
    }
    if (!this.viewer_url){
      this.login();
      return ;
    }
    this.block = true;
    self.show_loader();
    new Request.JSON({
      method: self.method,
      url: self.like_url,
      data: {
        format: self.format
      },
      onSuccess: function(response){
        self.hide_loader();
        if (response.error == 3){
          self.login();
          return false;
        }else if (response.error > 0){
          return false;
        }

        self.toggle();
        self.viewer_url = response.viewer_url;
        self.viewer = response.viewer;
        self.add_like(response.viewer);
        self.like_count++;
        self.build();
        self.block = false;

        return true;
      }
    }).send();
  },

  unlike: function(){
    var self = this;
    if (this.block){
      return ;
    }
    this.block = true;
    this.show_loader();    
    new Request.JSON({
      method: self.method,
      url: self.unlike_url,
      data: {
        format: self.format
      },
      onSuccess: function(response){
        self.hide_loader();
        if (response.error == 3){
          self.login();
          return false;
        }else if (response.error > 0){
          // alert(response.html);
          return false;
        }

        self.toggle();
        self.remove_like(response.viewer.user_id);
        self.like_count--;
        self.build();
        self.block = false;

        return true;
      }
    }).send();
  },

  show_loader: function(){
    this.$like_button_container.setStyle('display', 'none');
  },

  hide_loader: function(){
    this.$like_button_container.setStyle('display', 'block');
  },

  remove_like: function(user_id){
    var likes = this.likes;
    if (!likes.length || !likes){
      return ;
    }
    for (var i = 0; i < likes.length; i++){
      var like = likes[i];
      if (like.user_id == user_id){
        likes = likes.slice(0,i).concat(likes.slice(i+1));
        break;
      }
    }
    this.urls[user_id] = '';
    this.likes = likes;
  },

  add_like: function(like){
    if (!this.likes){
      this.likes = [];  
    }
    this.likes.push(like);
    this.urls[like.user_id] = this.viewer_url;
  },

  build: function(){
    var likes = this.likes;
    var html = '';
    var urls = this.urls;
    var like = {};
    var out = '';
    var like_count = this.like_count;
    var other_count = like_count - likes.length;
    if (!other_count && !likes.length){
      $(this.likes_container_id).innerHTML = langs.l_noResult;
      return ;
    }

    if (likes[likes.length-1]){
      like = likes[likes.length-1];
      if (like.user_id == this.viewer.user_id){
        html += '<a href="'+urls[like.user_id]+'" style="font-weight: bold; text-decoration: none;" target="_blank">' + langs.l_you + '</a>';
        likes = likes.slice(0,likes.length-1).concat(likes.slice(likes.length));
        if ((other_count > 0 && likes.length > 0) || (!other_count && likes.length > 1) ){
          html += ', ';
        }else if (!other_count && !likes.length){
          html += '';
        }else{
          html += ' '+langs.l_and+' ';
        }
      }
    }
    
    for (var i = 0; i < likes.length; i++){
      like = likes[i];
      out = '<a href="'+urls[like.user_id]+'" style="font-weight: bold; text-decoration: none;" target="_blank">' + like.displayname + '</a>';
      if (i != (likes.length - 1)){
        out += ', '
      }

      html += out;
    }

    if (likes.length > 0){
      html += ' ' + langs.l_and + ' ';
    }

    if (other_count > 1){
      html += other_count + ' ' + langs.l_people + ' ';
    }else if (other_count == 1){
      html += other_count + ' ' + langs.l_person + ' ';
    }
    
    html += ' ' + langs.l_like_it;
    $(this.likes_container_id).innerHTML = html;
  },

  login: function(){
    return window.open(this.login_url,'popup','width=450,height=240,scrollbars=no,resizable=no,toolbar=no,directories=no,location=no,menubar=no,status=yes,left=0,top=0');
  },

  toggle: function(){
    var $link = $(this.$link);
    if ($link.hasClass(this.unlike_class)){
      this.set_like();
    }else{
      this.set_unlike();
    }   
  },

  set_like: function(){
    var $link = $(this.$link);
    var self = this;
    $link.removeEvents('click');
    $link.children[0].className = 'like_button';
    $link.children[0].innerHTML = langs.l_Like;
    $link.children[0].style.backgroundImage = 'url('+this.like_img+')';
    $link.removeClass(this.unlike_class);
    $link.addClass(this.like_class);
    $link.addEvent('click', function(){
      self.like();
    });
    return $link;
  },

  set_unlike: function(){
    var $link = $(this.$link);
    var self = this;
    $link.removeEvents('click');
    $link.children[0].className = 'unlike_button';
    $link.children[0].innerHTML = langs.l_Unlike;
    $link.children[0].style.backgroundImage = 'url('+this.unlike_img+')';
    $link.removeClass(this.like_class);
    $link.addClass(this.unlike_class);
    $link.addEvent('click', function(){
      self.unlike();
    });
    return $link;
  },

  init: function(){
    this.$link = $('_'+this.subject_guid);
    this.$like_button_container = $(this.like_button_container);
    this.init_link();
  },

  init_link: function(){
    var self = this;
    this.$link.removeEvents('click');
    if (this.$link.hasClass(this.unlike_class)){
      this.$link.addEvent('click', function(){
        self.unlike();
      });
    }else{
      this.$link.addEvent('click', function(){
        self.like();
      });
    }
  }
}

function showLikesList($node, type) {
  $node = $($node);
  var $box = $node.getParents('.he_like_cont');

  if (!$box || !$box[0]) {
    return;
  }

  $box = $box[0];
  $box.getElements('ul.like_list_switcher li a').removeClass('active');
  $node.addClass('active');

  $cur_list = $box.getElement('.likes_' + type);
  $old_list = $box.getElement('div.active_list');

  if ($cur_list.hasClass('active_list')) {
    $node.blur();
    return;
  }

  var cur_tween = new Fx.Morph($cur_list, {duration: 300, opacity: 0});
  var old_tween = new Fx.Morph($old_list, {duration: 300, opacity: 1});

  old_tween.start({opacity: 0}).chain(function(){
    $old_list.removeClass('active_list');
    $cur_list.addClass('active_list');

    cur_tween.start({opacity: 1}).chain(function(){
      $node.blur();
    });
  });
}

function print_arr(object, flag){
  var type = typeof(object);
  var output = '';
  var property = null;

  switch (type){
    case 'object':{
      for (property in object){
        output += property + ': ' + print_arr(object[property], true)+'; ';
      }
    }
    break;
    case 'array':{
      for (var i = 0; i < object.length; i++){
        output += i + ': ' + print_arr(object[i], true)+'; ';
      }
    }
    break;
    case 'string': {
      output = '"' + object + '"';
    }
    break;
    case 'number':
    default: {
      output = object;
    }
  }

  if (flag){
    return output;
  }

  if (window.console !== undefined){
    window.console.log(output);
  }else{
    alert(output);
  }
}