/* $Id: OfferReview.js 2012-09-28 01:44 taalay $ */

var OfferReview = {

  offerId:0,
  url:{},
  allowedComment:false,
  timeOut:100,

  init:function () {

    var self = this;
    self.elm = {};
    self.elm.list = '.offerreview_container_list';
    self.elm.create = '.offerreview_container_create';
    self.elm.edit = '.offerreview_container_edit';
    self.elm.view = '.offerreview_container_view';
    self.elm.message = '.offerreview_container_message';

    self.elm.error = '.offerreview_container_error';
    self.elm.loader = '#offerrate_loader';
    self.elm.count = '.tab_layout_rate_offer_review a span';

    self.elm.commentForm = 'offerreview-comment-form';
    self.elm.mainTab = '.tab_layout_rate_offer_review a';
    self.elm.mainTabli = '.more_tab li.tab_layout_rate_offer_review';
    self.elm.mainContainer = 'generic_layout_container layout_rate_offer_review';

    var $form = $$(self.elm.create)[0].getElement('form');
    var $create = $$(self.elm.create)[0];
    $form.addEvent('submit', function () {
      if ($create) {
        $create.addClass('hidden');
      }
      self.request(self.url.create, $(this).toQueryString(),
        function (obj) {
          self.showMessage(obj.msg, obj.result);
          if (obj.result) {
            $form.getElement('input[name=title]').setProperty('value', '');
            $form.getElement('textarea[name=body]').setProperty('value', '');
            $form.getElements('.review_stars input').setProperty('value', 0);
            $form.getElements('.review_stars .rate_star').removeClass('rated');
            self.setCount(1);
            setTimeout(function () {
              self.view(obj.id);
            }, self.timeOut);
          } else {
          }
        }).send();
    });
  },

  initView:function (id) {
    if ($$(this.elm.mainTab)[0]) {
      tabContainerSwitch($$(this.elm.mainTab)[0], this.elm.mainContainer);
    } else if ($$(this.elm.mainTabli)[0]) {
      tabContainerSwitch($$(this.elm.mainTabli)[0], this.elm.mainContainer);
    }
    this.view(id);
  },

  add_class:function ($element, css_class) {
    if ($element.hasClass(css_class)) {
      return;
    }
    $element.addClass(css_class);
    return;
  },
  toggleTab:function (tab) {
    var self = this;
    if (tab != 'list' && tab != 'create' && tab != 'view' && tab != 'edit' && tab != 'message') {
      return;
    }
    $$(self.elm.list).addClass('hidden');
    $$(self.elm.create).addClass('hidden');
    $$(self.elm.view).addClass('hidden');
    $$(self.elm.edit).addClass('hidden');
    $$(self.elm.message).addClass('hidden');

    var $container = $$(self.elm[tab]);
    if ($container) {
      $container.removeClass('hidden');
    }
  },
  showMessage:function (message, result) {
    var self = this;
    self.toggleTab('message');
    var $container = $$(self.elm.message)[0];
    var ul = (result) ? '.success' : '.error';
    $container.getElements('.success, .error').addClass('hidden');
    $container
      .getElement(ul)
      .removeClass('hidden')
      .getElement('li')
      .setProperty('html', message);
  },
  view:function (id) {
    var self = this;
    var $view = $$(self.elm.view)[0];
    self.request(self.url.view, {'review_id':id},
      function (obj) {
        if (obj.result) {
          new LikeTips('offerreview', id, {
            'container':'offerreview_comments',
            'html':obj.likeHtml,
            'url':{
              'like':obj.likeUrl,
              'unlike':obj.unlikeUrl,
              'hint':obj.hintUrl,
              'showLikes':obj.showLikesUrl,
              'postComment':obj.postCommentUrl
            }
          });
        }
      }, $view).send();
    self.toggleTab('view');
  },
  create:function () {
    this.toggleTab('create');
  },
  edit:function (id) {
    var self = this;
    var $edit = $$(self.elm.edit)[0];
    self.toggleTab('edit');
    self.request(self.url.edit, {'offerreview_id':id},
      function () {
      }, $edit).send();
  },
  showLoader:function () {
    var $loader = $$(this.elm.loader)[0];
    if ($loader) {
      $loader.removeClass('hidden');
    }
  },
  hideLoader:function () {
    var $loader = $$(this.elm.loader)[0];
    if ($loader) {
      $loader.addClass('hidden');
    }
  },
  goEdit:function () {
    this.toggleTab('edit');
  },
  goCreate:function () {
    this.toggleTab('create');
  },
  editSubmit:function (form) {
    var self = this;
    var values = $(form).toQueryString();
    values += "&task=dosave";
    var $edit = $$(self.elm.edit)[0];
    if ($edit) {
      $edit.addClass('hidden');
    }
    self.request(self.url.edit, values,
      function (obj) {
        self.showMessage(obj.msg, obj.result);
        if (obj.result) {
          setTimeout(function () {
            self.view(obj.id);
          }, self.timeOut);
        }
      }).send();
    return false;
  },
  setCount:function (direction) {
    var self = this;
    var $counter = $$(self.elm.count)[0];
    if ($counter) {
      var str = $$(self.elm.count)[0].getProperty('html');
      var count = str.substr(1, (str.length - 2)).toInt() + direction;
      $counter.setProperty('html', '(' + count + ')');
    }
  },
  list:function () {
    var self = this;
    self.toggleTab('list');
    self.refresh();
  },
  remove:function (id) {
    var self = this;
    var title = en4.core.language.translate('RATE_REVIEW_DELETE');
    var description = en4.core.language.translate('RATE_REVIEW_DELETEDESC');
    var $list = $$(self.elm.list)[0];
    he_show_confirm(title, description, function () {
      if ($list) {
        $list.setProperty('html', '');
      }
      self.request(self.url.remove, {'review_id':id},
        function (obj) {
          self.showMessage(obj.msg, obj.result);
          if (obj.result) {
            self.setCount(-1);
          }
          setTimeout(function () {
            self.list();
          }, self.timeOut);
        }).send();
    });
  },
  refresh:function (page) {
    var self = this;
    if (!page) {
      page = 1;
    }
    var $view = $$(self.elm.list)[0];
    var $counter = $$(self.elm.count)[0];
    self.request(self.url.list, {'page':page},
      function (obj) {
        if ($counter) {
          $counter.setProperty('html', '(' + obj.count + ')');
        }
      }, $view).send();
  },
  request:function (url, data, callback, $container) {
    var self = this;
    if (typeof(data) == 'string') {
      data += '&no_cache=' + Math.random();
      data += '&offer_id=' + self.offerId;
      data += '&format=json';
    } else {
      data.no_cache = Math.random();
      data.offer_id = self.offerId;
      data.format = 'json';
    }
    if ($container) {
      $container.setProperty('html', '');
    }
    self.showLoader();
    return new Request.JSON({
      'url':url,
      'method':'post',
      'data':data,
      onSuccess:function (obj) {
        self.hideLoader();
        if ($container && obj.html) {
          $container.setProperty('html', obj.html);
          obj.html.stripScripts(true);
          en4.core.runonce.trigger();
        }
        if (callback) {
          callback(obj);
        }
        if (obj.js) {
          eval(obj.js);
        }
      }
    });
  }

};

function ReviewRate(uid) {
  this.init(uid);
}

ReviewRate.prototype = {

  init:function (uid) {

    var self = this;
    self.$container = $(uid);
    if (self.$container) {
      self.$stars = self.$container.getElements('.rate_star');
      self.$stars.addEvent('mouseover', function () {
        self.$stars.removeClass('rate');
        var $star = $(this);
        if ($star) {
          var $prev = $star.getAllPrevious();
          $prev.addClass('rate');
          $star.addClass('rate');
        }
      });
      self.$stars.addEvent('mouseout', function () {
        self.$stars.removeClass('rate');
      });
      self.$stars.addEvent('click', function () {
        var $star = $(this);
        var score = $star.getProperty('id').substr(10);
        if ($star) {
          var $hidden = self.$container.getElement('input');
          if ($hidden) {
            $hidden.setProperty('value', score);
          }

          self.$stars.removeClass('rated');
          var $prev = $star.getAllPrevious();
          $prev.addClass('rated');
          $star.addClass('rated');
        }
      });
    }

  }

};