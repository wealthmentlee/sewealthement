

function ActivityFeed($feed, params) {
  var object = {

    timeline_view:1,
    dates:{},
    subject_guid: '',

    params:{
      composer:{}
    },

    $feed:null,
    $wall:null,
    $viewMore:null,
    $new_updates:null,
    initialize:function ($feed, params) {
      var self = this;
      if (params.dates) {
        this.timeline_view = 1;
        this.dates = params.dates;
      }
      this.$feed = $feed;
      this.params = params;
      this.preInit();
      this.watcher.initialize(this);
      this.feedInit();
      if (this.timeline_view) {
        this.separator.scan();
      }
      var subject_guid = '';
      if (UIComponent.pageResponseData.info.subject) {
        subject_guid = UIComponent.pageResponseData.info.subject.type + '_' + UIComponent.pageResponseData.info.subject.id
      }
      this.subject_guid = subject_guid;
      // if enabled composer
      if (this.params.composer){
        $feed.closest('.component-feed').find('.composeLink').data('subject_guid', subject_guid).show();
      }
      // Loading on scroll down
      $feed.bind('vmousemove', function(e){
        var p = document.querySelector('.smooth-scroll');
        if (p && !window.wall_scrollajax && self.params.scrollajax) {
          window.wall_scrollajax = true;
          window.addEventListener('scroll', function (e) {
              var c = p.scrollHeight - (window.scrollY + $(window).height());
              if (c > 100) {
                return;
              }
              $(document.querySelector('.ui-page-active .component-feed .viewMore a')).trigger('vclick');
            });
          $(this).unbind('vmousemove');
        }
      });
    },


    preInit:function () {

      this.$wall = this.$feed.find('.wall');
      this.$viewMore = this.$feed.find('.viewMore');
      this.$new_updates = this.$feed.find('.feed-new-updates');

    },
    inlineVideo:function(actionEl){
      var videoRC = actionEl.querySelector('.richContent .video');
      var iFrame  = null;
      if(videoRC && (iFrame = videoRC.querySelector('iframe'))){ // inline video
        var links = videoRC.querySelectorAll('a');
        var thumb = videoRC.querySelector('img');
//        var thumbLoadCB = function(){
//          this.iFrame.height = this.height;
//        }
//        thumb.onload = thumbLoadCB;
        thumb.iFrame = iFrame;

        links[0].iFrame = iFrame;
        links[0].thumb = $(thumb);
        links[0].playBtn = $(links[1]);

        links[1].iFrame = iFrame;
        links[1].thumb = links[0].thumb;
        links[1].playBtn = links[0].playBtn;
        $(links).bind('vclick', function(){
          this.iFrame.height = this.thumb[0].height;
          $(this.iFrame).show();
          this.thumb.hide();
          this.playBtn.hide();
        });
    //            iFrame
      }
    },
    prepareHashTags: function(actionEl, hashtags){
      var l = hashtags.length;
      var htt = ['<div class="hashtags">'];
      for(var i = 0; i < l; i++){
        var ht = hashtags[i];

        htt.push('<a class="wall-event we-hashtag">' + ht + '</a>');
      }
      htt.push('</div>');
      actionEl.querySelector('.body').appendChild($(htt.join(''))[0]);
    },
    renderActions:function (actions, end) {
      var self = this;
      if (actions.length) {
        var actionsEl = [];
        var activehtag = $(document.querySelector('.ui-page-active .active_hashtags')).attr('id');
        for (var i = 0; i < actions.length; i++) {
          var action = actions[i];
          if(!action.html)
            continue;
          var actionEl = $(action.html)[0];
          this.inlineVideo(actionEl);
          if(action.hashtags){
            if(action.hashtags.length > 1 && action.hashtags[0] == activehtag){
              action.hashtags.push(activehtag);
              action.hashtags.splice(0, 1);
            }

              this.prepareHashTags(actionEl, action.hashtags);
          }
          actionsEl.push(actionEl);
        }
        self.$wall.append(actionsEl);
        if (end) {
          self.$viewMore.removeClass('viewMore').hide();
        } else {
          self.$viewMore.addClass('viewMore').show();
        }
      } else {
        self.$viewMore.removeClass('viewMore').hide();
        setTimeout(function () {
          UIComponent.helper.showMessage(self.params.noItemsMessage);
        }, 100);
      }
      if (this.timeline_view) {
        this.separator.scan();
      }
      window.addEventListener('orientationchange', function(){
        if(!self.$wall._parentPage)
          self.$wall._parentPage = self.$wall.closest('.template-page');
        if(self.$wall._parentPage.hasClass('ui-page-active'))
          core.helper.preventZoom();
          $(self.$wall[0].querySelectorAll('.attachment_thumbs')).each(function (i, e){
            var imgs = e.querySelectorAll('img');
            self.alignPhotos(imgs);
          });
      });

      $(self.$wall[0].querySelectorAll('.attachment_thumbs.not-inited, .attachment_item.big')).removeClass('not-inited').each(function (i, e){
        var imgs = e.querySelectorAll('img');
        self.alignPhotos(imgs);
        var $photoAttachments = $(e.querySelectorAll('.photoviewer'));
        if($photoAttachments.length)
          $photoAttachments.removeClass('photoviewer').photoSwipe({ enableMouseWheel:false, enableKeyboard:false, enableComment: true, enableTags: self.params.canViewTags});
      });
    },

    alignPhotos:function (imgs) {
      var imgCount = imgs.length;
      if(imgCount > 1){
        var i = 0;
        var imgSizes = [];
        var container = null;
        var sliderContainer = null;
        var containerWidth = 0;
        var heightAverage = 0;
        var widthAverage = 0;
        var photosInRow = 0;
        var margins = 4;
        var subPadding = 0;
        if(core.helper.isTablet()){
          margins = 12;
          subPadding = 10;
        }
        var cb = function(size){
//          size.width + margins;
          if(containerWidth < 1){
            container = $(imgs[i]).closest('ul');
            container.removeAttr('style');
            sliderContainer = $(imgs[i]).closest('div');
            sliderContainer[0].container = container;
            containerWidth = container[0].offsetWidth - subPadding;
          }
          heightAverage += size.height;
          widthAverage += size.width;
          imgSizes.push(size);
          i++;
          if(imgCount - i == 1 && containerWidth < 1 ){
            setTimeout(function(){core.helper.getImageDimensions(imgs[i].getAttribute('normal-src'), cb);}, 500);
          } else if(i < imgCount)
            core.helper.getImageDimensions(imgs[i].getAttribute('normal-src'), cb);
          else{
            heightAverage /= imgCount;
            var allTotalWidth = widthAverage + (margins + 25) * imgCount;
            widthAverage /= imgCount;
            photosInRow = Math.round(containerWidth/(widthAverage + margins));
            var rows = Math.floor(imgCount/photosInRow);
            if(imgCount < 6 && core.device.platform.isIOS() && allTotalWidth > containerWidth){
              var wol = widthAverage > containerWidth ? containerWidth / widthAverage : 1;
              if(wol < 1){
                allTotalWidth *= wol;
                heightAverage *= wol;
              }
              $(imgs).css('height', heightAverage);
              container.css('width', allTotalWidth + 'px');
              sliderContainer[0].deltaX = 0;
              sliderContainer[0].imgs = sliderContainer[0].querySelectorAll('img');
              sliderContainer[0].deltaMax = allTotalWidth - containerWidth;
              sliderContainer.unbind('touchstart').bind('touchstart', function(e){
                this.container.css('-webkit-transition', '');
                this.startX = e.originalEvent.touches[0].pageX;
              });
              sliderContainer.unbind('touchend').bind('touchend', function(e){
                this.container.css('-webkit-transition', '-webkit-transform 0.4s ease 0s');
                var localDelta = e.originalEvent.changedTouches[0].pageX - this.startX;
                var oldDelta = this.deltaX;
                var delta = this.deltaX += localDelta;
                var width = 0, i = 0, lw = 0;
                var imgs = this.imgs;
                var len = imgs.length;
                var d = -delta;
                while(width < d && i < len){
                  var img =  imgs[i];
                  lw = img.width  + margins;
                  i ++;
                  width += lw;
                }
                if(Math.abs(localDelta) < 100)
                  delta = oldDelta;
                else if(localDelta > 0){
                  delta = -(width - lw);
                } else {
                  delta = -width;
                }
                if(delta > 0){
                  this.container.css('-webkit-transform', 'translate3d(0, 0, 0)');
                  this.deltaX = 0;
                } else if(this.deltaMax + delta < 0){
                  this.container.css('-webkit-transform', 'translate3d(' + (-this.deltaMax) + 'px, 0, 0)');
                  this.deltaX = -this.deltaMax;
                } else {
                  this.deltaX = delta;
                  this.container.css('-webkit-transform', 'translate3d(' + delta + 'px, 0, 0)');
                }

              });
              sliderContainer.unbind('touchmove').bind('touchmove', function(e){
                var delta = e.originalEvent.touches[0].pageX - this.startX;
                this.container.css('-webkit-transform', 'translate3d(' + (this.deltaX + delta) + 'px, 0, 0)');
              });

            } else {
              var rowArr = [];
              var ratioAverageSum = 0;
              var totalWidth = 0;
              for(var index = 0; index < imgCount; index ++){
                var current = imgSizes[index];
                var tmpTotalW = totalWidth + current.width + margins;
                if((tmpTotalW > containerWidth && (current.width + margins) / (tmpTotalW - containerWidth) < 2) || imgCount - index == 1){
                  if(imgCount - index == 1){
                    ratioAverageSum += current.width / current.height;
                    rowArr.push(imgs[index]);
                    totalWidth += current.width + margins;
                  }
                  var avgHeight = Math.floor((containerWidth - rowArr.length * margins)/ratioAverageSum);
                  var $imgs = $(rowArr);
                  if((avgHeight / heightAverage) > 1.41){
                    for(var k = 0; k < rowArr.length; k ++){
                      var c = rowArr[k];
                      c.src = c.getAttribute('profile-src');
                    }
                  }
                  $imgs.css('height', avgHeight);
                  rowArr = [];
                  ratioAverageSum = 0;
                  totalWidth = 0;
                }
                ratioAverageSum += current.width / current.height;
                rowArr.push(imgs[index]);
                totalWidth += current.width + margins;
              }
            }
          }
        }
        core.helper.getImageDimensions(imgs[i].getAttribute('normal-src'), cb);
      }

    },

    get_items:function () {
      return this.$wall.find('.feedItem');
    },


    feedInit:function () {

      var self = this;
      var $viewMore = self.$viewMore;

      this.renderActions(self.params.actions, self.params.settings.endOfFeed);

      $viewMore.find('a').unbind().bind('vclick', function (event) {

        event.preventDefault();
        var $button = $(this);
        if ($button.data('completed') === false)
          return false;

        $.mobile.showPageLoadingMsg();

        $button.data('completed', false);

        var requestParams = {
          'format':'json',
          'feedOnly':true,
          'maxid':self.params.settings.nextid,
          'maxdate':self.params.settings.max_date
        };
        var hashtag;
        if(hashtag = $('.active_hashtags').attr('id')){
          requestParams.ht_name = hashtag;
        }

        if (self.subject_guid) {
          requestParams.subject = self.subject_guid;
        }

        var $option = self.$feed.find('select.feed-filter-select').find('option:selected');
        if ($option.length){
          requestParams.mode = $option.data('mode');
          requestParams.type = $option.data('type');
          requestParams.list_id = $option.data('id');
        }

        $.post(self.params.feedUrl,
          requestParams,
          function (response) {
            if (response.status) {
              self.renderActions(response.component.params.actions, response.component.params.settings.endOfFeed);
              self.params.settings = response.component.params.settings;
            }
            $button.data('completed', true);
            $.mobile.hidePageLoadingMsg();

          },
          'json'
        );
        return false;
      });


      /**
       * Filter
       */
      if (self.params.list && self.params.list.length) {

        self.$feed.find('.feed-filter').show();
        var $select = self.$feed.find('.feed-filter-select');

        var $optionTpl = self.$feed.find('.feed-filter-select').find('option').clone();
        var l = self.params.list.length;
        var selected;
        for(var index = 0;index < l; index ++) {
          var item = self.params.list[index];
          var $option = $optionTpl.clone();
          var v = item.mode + item.type + item.list_id;
          $option.attr('value', v);
          $option.html(item.title);

          $option.data('mode', item.mode);
          $option.data('type', item.type);
          $option.data('id', item.list_id);
          if(item.active){
            selected = v;
          }
          $option.appendTo(self.$feed.find('.feed-filter-select'));

        }
        if(selected)
          $select.val(selected);
        document.title = $select.find('option[value=' + $select.val() + ']').text();
        $select.change(function (event) {
          Wall.events.hashtagDectivate(document.querySelector('.ui-page-active .active_hashtags'), true);
          event.preventDefault();
          var $option = $select.find('option[value=' + $select.val() + ']');
          var data = {
            typeName: $option.text(),
            mode: $option.data('mode'),
            type: $option.data('type'),
            list_id: $option.data('id')
          };
          core.helper.preventZoom();
          self.feedTypeChange(data);
        });
      }


      if (this.params.isViewPage){
        this.$wall.find('.options').hide();
      }


    },

    feedTypeChange: function(data){
      var self = this;
      if(data.typeName)
        document.title = data.typeName;

//      if (data.completed === false) // todo
//        return false;

      $.mobile.showPageLoadingMsg();

      //$select.data('completed', false); // todo

      var requestParams = {
        'format':'json',
        'feedOnly':true,
        'mode':data.mode,
        'type':data.type,
        'list_id':data.list_id
      };

      if (this.subject_guid) {
        requestParams.subject = this.subject_guid;
      }
      $.post(this.params.feedUrl,
        requestParams,
        function (response) {

          if (response.status) {

            self.$wall.find('.feedItem').remove();

            self.renderActions(response.component.params.actions, response.component.params.settings.endOfFeed);

            self.params.settings = response.component.params.settings;
          }

//          $select.data('completed', true); //todo
          $.mobile.hidePageLoadingMsg();

        },
        'json'
      );
    },

    getHashtagRelatedFeed:function (hashtag) {

      var self = this;
      var option = document.querySelectorAll('.ui-page-active select.feed-filter-select option')[0];
      if(option && !option.selected){
        option.selected = true;
        $(document.querySelector('.ui-page-active span.feed-filter-select')).text(option.innerHTML);
      }

      $.mobile.showPageLoadingMsg();
        var temp = hashtag;
        temp = temp.replace(/&/g, "%26");
      var requestParams = {
        feedOnly:true,
        format:'json',
        ht_name:temp
      };

      $.post(this.params.feedUrl,
        requestParams,
        function (response) {
          if (response.status) {
            self.$wall.find('.feedItem').remove();
            self.renderActions(response.component.params.actions, response.component.params.settings.endOfFeed);
            self.params.settings = response.component.params.settings;
          }
          //          $select.data('completed', true); //todo
          $.mobile.hidePageLoadingMsg();

        },
        'json'
      );
    },

    watcher:{
      activity:null,
      params:null,
      $composer:null,

      initialize:function (activity)
      {
        var self = this;

        this.activity = activity;
        this.params = activity.params;

        self.activity.$new_updates.data('nextid', self.params.settings.firstid);

        if (self.params.settings.updateSettings) {

          if (window.updateHandler) {
            clearInterval(window.updateHandler);
          }
          window.updateHandler = setInterval(function (){
            self.updateHandler();
          }, self.params.settings.updateSettings);
        }


      },

      updateHandler:function () {

        var self = this;
        if (!self.activity) {
          return;
        }
        var $new_updates = self.activity.$new_updates;
        if (!$new_updates) {
          return;
        }
        // Is is not the active page
        if (!$new_updates.closest('.template-page.ui-page-active').length) {
          return;
        }

        var url = $new_updates.data('url');
        var data = {
          checkUpdate:true,
          minid:$new_updates.data('nextid') + 1,
          subject:self.activity.subject_guid,
          format:'json'
        };



        var $option = self.activity.$feed.find('select.feed-filter-select').find('option:selected');
        if ($option.length){
          data.mode = $option.data('mode');
          data.type = $option.data('type');
          data.list_id = $option.data('id');
        }

        $.post(url, data, function (obj) {

            if (!obj.component.params.updateTitle) {
              $new_updates.hide();
              return;
            }

            $new_updates.show();
            $new_updates.find('a').html(obj.component.params.updateTitle);

            $new_updates.show().find('a').unbind().bind('vclick', function () {

              $new_updates.hide();


              var data = {
                getUpdate:true,
                minid:$new_updates.data('nextid') + 1,
                subject:self.activity.subject_guid,
                format:'json'
              };

              var $option = self.activity.$feed.find('select.feed-filter-select').find('option:selected');
              if ($option.length){
                data.mode = $option.data('mode');
                data.type = $option.data('type');
                data.list_id = $option.data('id');
              }

              $.post($new_updates.data('url'),
                data,
                function (obj) {

                  $new_updates.data('nextid', obj.component.params.settings.firstid);

                  var actions = obj.component.params.actions;

                  for (var i = actions.length - 1; i > -1; i--) {
                    var action = actions[i];

                    var $li = $(action.html);

                    self.activity.$wall.prepend($li);
                    Wall.customAnimate($li);

                  }

                },
                'json'
              );

            });

          },
          'json'
        );

      }

    },


    separator:{

      prefix:'sep',

      scan:function () {
        var self = this;
        var items = [];
        var aligns = {left:0, right:0};

        if (items.length == 0) {
          items = object.get_items();
        }
        $.map(items, function (el) {
          if ($(el).hasClass('utility-viewall')) {
            self.pagination.toSeparator(el);
          } else {
            if (!$(el).hasClass('d')) {
              self.setDate(el);
              if (null != self.lookup(el)) {
                aligns = {left:0, right:0}
              }
            }
          }
        });

        return self;
      },

      getDecade:function (d) {
        return (d < 10) ? '0' + d : d;
      },

      itemDate:function (item) {

        if (item == null || item.find('.timestamp') == null) return null;

        var date_str = item.find('.timestamp').attr('title');
        var date = new Date(date_str);

        var d = {
          'ye':date.getUTCFullYear(),
          'mo':this.getDecade(parseInt(date.getUTCMonth() + 1)),
          'da':this.getDecade(date.getUTCDate()),
          'ho':this.getDecade(date.getUTCHours()),
          'mi':this.getDecade(date.getUTCMinutes()),
          'se':this.getDecade(date.getUTCSeconds())
        }

        var new_date = d.ye + '-' + d.mo + '-' + d.da + ' ' + d.ho + ':' + d.mi + ':' + d.se;

        return new_date;
      },

      setDate:function (el) {
        if ($(el).hasClass('d')) return;
        var self = this;
        var date = null;

        if (null == (date = self.itemDate($(el)))) return;

        var date_arr = self.dateToArray(date);

        $(el).addClass('d');
        $(el).data('date', date);
      },

      dateToArray:function (date, allowNull) {
        if (date == null) return null;

        if ($.type(date) == 'object' && 'year' in date) {
          return date
        }

        if ($.type(date) != 'string') {
          date = date.toString()
        }

        var arr = date.split(' ');
        var d = arr[0].split('-');
        var result = {};

        if (allowNull) {
          result = {'year':d[0], month:null, day:null, hour:null, minute:null, second:null}
        } else {
          result = {'year':d[0], month:12, day:31, hour:23, minute:59, second:59}
        }


        if (1 in d) {
          result.month = d[1]
        }

        if (2 in d) {
          result.day = d[2]
        }

        if (1 in arr) {
          var h = arr[1].split(':');
          result.hour = h[0];

          if (1 in h) {
            result.minute = h[1]
          }

          if (2 in h) {
            result.second = h[2]
          }
        }

        return result
      },

      lookup:function (el) {
        var previous = $(el).prev('li');

        var self = this;
        var date = null;

        if (null == (date = self.dateToArray($(el).data('date'), true))) {
          return null;
        }
        var key = date.year + '-' + date.month;

        if (this.exists(key)) {
          return null;
        }
        var y = 'y' + date.year;
        var m = 'm' + date.month;

        var text = '';
        if (!((object.dates.years != null && y in object.dates.years && m in object.dates.years[y]) || (object.dates.last_month != null && key == object.dates.last_month['key']))) {
          return null;
        }

        var li = null;
        if (!this.exists(key) && key == object.dates.last_month['key']) {
          li = this.add({
            'date':key,
            'text':object.dates.last_month['name'],
            'class':'m ' + object.dates.last_month.year + ' ' + object.dates.last_month.month,
            'max_id':object.dates.last_month['max_id']
          }, previous);
        } else {

          if (!this.exists(y) && $.inArray(y, object.dates.years)) {
            li = this.add({
              'date':date.year,
              'text':date.year,
              'class':'y ' + date.year,
//                'max_id':self.scroller.getMaxId(y)
              'max_id':object.dates.last_month['max_id']
            }, previous);

            if (li != null) {
              previous = li;
            }
          }

          if (object.dates.years) {
            if ($.inArray(m, object.dates.years[y])) {
              li = this.add({
                'date':key,
                'text':object.dates.years[y][m]['name'] + ', ' + date.year,
                'class':'m ' + object.dates.years[y][m]['month'] + ' ' + object.dates.years[y][m]['year'],
                'max_id':object.dates.years[y][m]['max_id']
              }, previous);
            }
          }
        }

        return li;
      },

      add:function (params, el) {
        if (this.exists(params['date'])) {
          return null;
        }
        var self = this;

        var li = $('<li>', {'id':this.prefix + params['date'], 'class':'sep ' + params['class']});
// working copy
//          var div = $('<div>');
//          var a = $('<a>', {'text':params['text'], 'href':'javascript:void(0);'});
//          li.data('date', params['date']);
//          li.data('maxid', params['max_id']);

        var li = $('<li>', {'id':this.prefix + params['date'], 'class':'sep ' + params['class']});
        var div = $('<div>', {'class':'ui-header ui-bar-c', 'data-theme':'c'});
//            var div = $('<div>', {'data-role':'header', 'data-theme':'c'});
        var a = $('<h1>', {'text':params['text'], 'class':'ui-title ui-li-heading'});
        li.data('date', params['date']);
        li.data('maxid', params['max_id']);

        a.appendTo(div);
        div.appendTo(li);

        if (el != null) {
          el.after(li);
        } else {
          el.before(li);
        }

        return li;
      },

      get:function (date) {
        return document.getElementById(this.prefix + date);
      },

      getNext:function (item, cl) {
        var previous = item, next;

        if ($type(item) == 'string') {
          previous = this.get(item);
        }

        if ($type(previous) != 'element') {
          return null
        }

        if ($type(cl) != 'string') {
          return previous.getNext('.sep');
        }

        return previous.getNext('.sep.' + cl);
      },

      load:function (item) {
        var self = timeline;

        var date = self.tools.dateToArray(item, true);
        var key = date.year + '-' + date.month;
        var last = null;
        var el = null;

        if (key == self.last_month['key']) {
          if ((el = this.exists(date.year)) && el.hasClass('active')) {
            last = el;
          } else {
            last = this.loadLastMonth();
          }

          self.feed.load(last);
          self.scroller.scroll(last);
        } else if (!this.exists(date.year)) {
          last = this.loadYears(date);
          self.feed.load(last);
          self.scroller.scroll(last);
        } else if (!(this.exists(date['year'] + '-' + date['month']))) {
          last = this.loadMonths(date);
          self.feed.load(last);
          self.scroller.scroll(last);
        }

        return self.feed.get();
      },

      loadLastMonth:function () {
        var self = timeline, last = null;

        if (this.exists(self.last_month.key)) {
          last = self.feed.getLast(self.last_month.key);

          if (last.hasClass('y')) {
            last.getElement('a').set('text', en4.core.language.translate('Earlier in %1s', ' ' + self.last_month['name']));
            last.removeClass('y');
            last.addClass('m');
          }

          return last;
        }

        last = self.feed.getLast(self.now['key']);

        if (last.hasClass('sep')) {
          last.getElement('a').set('text', en4.core.language.translate('Earlier in %1s', ' ' + self.now['name']));
          last.removeClass('y');
          last.addClass('m');
        }

        last = this.add({
          'date':self.last_month.key,
          'text':en4.core.language.translate('Show %1s', ' ' + self.last_month['name']),
          'class':'active m ' + self.last_month.year + ' ' + self.last_month.month,
          'max_id':self.last_month['max_id']
        }, last);

        return last;
      },

      loadMonths:function (date) {
        var self = timeline;
        var year = 'y' + date.year, month = 'm' + date.month;
        var months = [], i = 0, previous = null, last = null;
        for (m in self.years[year]) {
          var key = date.year + '-' + self.years[year][m]['month'];

          if (self.years[year][m]['month'].toInt() > date.month.toInt() || (self.years[year][m]['month'].toInt() == date.month.toInt() && !this.exists(key))) {
            if (this.exists(key)) {
              previous = m;
              months = [];
              i = 0;
            } else {
              months[i] = m;
              i++;
            }
          }
        }

        if (months.length == 0) {
          return null;
        }

        if (previous == null) {
          last = this.get(date.year);
        } else {
          last = self.feed.getLast(date.year + '-' + self.years[year][previous]['month']);

          if (last.hasClass('y')) {
            var a = last.getElement('a');
            a.set('text', en4.core.language.translate('Earlier in %1s', ' ' + self.years[year][previous]['name'] + ', ' + date.year));
            last.removeClass('y');
            last.addClass('m');
          }
        }

        for (i = 0; i < months.length; i++) {
          last = this.add({
            'date':date.year + '-' + self.years[year][months[i]]['month'],
            'text':en4.core.language.translate('Show %1s', ' ' + self.years[year][months[i]]['name'] + ', ' + date.year),
            'class':'active m ' + date.year + ' ' + self.years[year][months[i]]['month'],
            'max_id':self.years[year][months[i]]['max_id']
          }, last);
        }

        return last;
      },

      loadYears:function (date) {
        var self = timeline;
        var year = 'y' + date.year;

        /*if (!( year in self.years)) {
         return null;
         }*/

        var month = 'm' + date.month, years = [], i = 0, previous = null, last = null;

        if (date.year == self.last_month.year) {
          last = this.loadLastMonth();

          for (m in self.years[year]) {
            month = m;
            break;
          }
          last = this.add({
            'date':date.year + '-' + self.years[year][month]['month'],
            'text':self.years[year][month]['name'] + ', ' + date.year,
            'class':'active m ' + date.year + ' ' + self.years[year][month]['month'],
            'max_id':self.years[year][month]['max_id']
          }, last);
          return last;
        }


        for (var y in self.years) {
          y = y.substr(1).toInt();
          if (y > date.year || (y == date.year && !this.exists(y))) {
            if (this.exists(y)) {
              previous = y;
              years = [];
              i = 0;
            } else {
              years[i] = y;
              i++;
            }
          }
        }

        if (years.length == 0) {
          return null;
        }
        last = self.feed.getLast(previous);

        var ldate = self.tools.dateToArray(last.retrieve('date'));

        var lkey = ldate.year + '-' + ldate.month;

        var temp_years = [], j = 0;
        for (i = 0; i < years.length; i++) {
          if (ldate.year == years[i]) {
            temp_years = [];
            j = 0;
          } else {
            temp_years[j] = years[i];
            j++;
          }
        }

        years = temp_years;

        if (years.length == 0) {
          return last;
        }

        for (i = 0; i < years.length; i++) {
          last = this.add({
            'date':years[i],
            'text':en4.core.language.translate('Show %1s', ' ' + years[i]),
            'class':'active y ' + years[i],
            'max_id':self.scroller.getMaxId('y' + years[i])
          }, last);
        }

        return last;
      },

      loadLifeEvent:function (date, params) {
        var self = timeline;

        var el = null;
        if (null != (el = this.exists(params.type))) {
          return el;
        }

        var date_arr = self.tools.dateToArray(date);

        if (self.years != null) {
          this.loadYears(date_arr)
        }

        var last = self.feed.getLast(date);

        if (last == null) {
          last = self.feed.getLast();
        }

        last = this.add({
          'date':params.type,
          'text':params.text,
          'class':'active le',
          'max_id':'0'
        }, last);

        return last;
      },

      exists:function (date) {
        var self = this, el = null;
        el = object.$wall.find('#' + this.prefix + date);
        if (el.length) {
          return el;
        }
        return false;
      }
    }

  };

  object.initialize($feed, params);
  return object;

}




var ShareForm = {

  /**
   * Require Params
   */
  services:{},
  serviceRequestUrl:'',
  $form:null,

  init:function () {


    var self = this;

    // all services
    var services = ['facebook', 'twitter', 'linkedin'];

    var $div = this.$form;
    $div.show();


    var data = {};
    $.each(services, function (index, item) {
      data[item] = true;


      if (!self.services || !self.services[item]) {
        return;
      }


      var $div = self.$form;

      $div.show();

      $div.find('.wall-share-' + item).attr('href', self.services[item].url);
      $div.find('.wall-share-' + item).parent().show();


    });

    $.post(this.serviceRequestUrl, data, function (obj) {
      if (!obj) {
        return;
      }
      $.each(obj, function (index, item) {
        if (!item || !$.inArray(services, index)) {
          return;
        }
        if (!self.services || !self.services[index] || !self.services[index].enabled) {
          return;
        }
        if (!obj[index] || !obj[index].enabled) {
          return;
        }
        self.enableShareButton(index, (self.services[index].enabled));
      });
    });

  },


  enableShareButton:function (index, is_active) {

    var self = this;

    var $div = this.$form;

    if (!$div) {
      return;
    }

    if (is_active) {
      $div.find('.wall-share-' + index).removeClass('disabled');
      $div.find('.wall-share-' + index).parent().find('input').val(1);
    }

    // !!!!
    $div.find('.wall-share-' + index).unbind().bind('vclick', function (event) {

      event.preventDefault();
      event.stopPropagation();

      if ($(this).hasClass('disabled')) {
        $div.find('.wall-share-' + index).removeClass('disabled');
        $div.find('.wall-share-' + index).parent().find('input').val(1);
      } else {
        $div.find('.wall-share-' + index).addClass('disabled');
        $div.find('.wall-share-' + index).parent().find('input').val(0);
      }

      // Save via Ajax

      $.post(self.services[index].serviceShareUrl, {
        provider:index,
        status:$div.find('.wall-share-' + index).parent().find('input').val()
      });

    });
  },

  callbackFromWindow:function (name) {
    this.enableShareButton(name, true);
  }
};


function print_r(arr, level) {
  var print_red_text = "";
  if (!level) level = 0;
  var level_padding = "";
  for (var j = 0; j < level + 1; j++) level_padding += "    ";
  if (typeof(arr) == 'object') {
    for (var item in arr) {
      var value = arr[item];
      if (typeof(value) == 'object') {
        print_red_text += level_padding + "'" + item + "' :\n";
        print_red_text += print_r(value, level + 1);
      }
      else
        print_red_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
    }
  }

  else  print_red_text = "===>" + arr + "<===(" + typeof(arr) + ")";
  return print_red_text;
}


function arrayToUrl(array) {
  var pairs = [];
  for (var key in array)
    if (array.hasOwnProperty(key))
      pairs.push(encodeURIComponent(key) + '=' + encodeURIComponent(array[key]));
  return pairs.join('&');
}


var checkin_map =
{
  checkin_array:{},
  markers_array:{},
  map_bounds:{},
  current_data:{},
  zoom:4,
  constructed:false,
  get_event_loc_url:'',
  map_canvas:null,
  $template:null,

  construct:function (checkin_array, markers_array, zoom, map_bounds, edit_mode) {
    this.checkin_array = checkin_array;
    this.markers_array = markers_array;
    this.map_bounds = map_bounds;
    this.zoom = zoom;
    this.init();

    if (edit_mode == undefined && !edit_mode) {
      this.show_map();
    } else {
      this.show_edit_map();
    }

    this.constructed = true;
  },

  init:function () {
    this.map = new google.maps.Map(this.map_canvas, {mapTypeId:google.maps.MapTypeId.ROADMAP, center:new google.maps.LatLng(0, 0), zoom:15});
  },

  show_map:function () {
    var self = this;
    if (this.markers_array.length == 0) {
      return false;
    }

    var infowindow = new google.maps.InfoWindow();

    for (var i = 0; i < this.markers_array.length; i++) {
      var marker = this.markers_array[i];

      var marker_obj = new google.maps.Marker({
        map:this.map,
        position:new google.maps.LatLng(marker.lat, marker.lng)
      });

      this.setMarkerInfo(marker, infowindow, marker_obj);
      this.map.setCenter(new google.maps.LatLng(marker.lat, marker.lng), 4);
    }

    this.setMapCenterZoom();
  },

  setMarkerInfo:function (marker, infowindow, marker_obj) {
    var self = this;
    google.maps.event.addListener(marker_obj, 'vclick', function () {
      if (marker.url) {
        var marker_content = '<table width="500"><tr valign="top"><td width="110"><img src="' + marker.pages_photo + '" width="100"></td><td width="400"><h3 style="margin-top:0; margin-bottom:6px;"><a href="' + marker.url + '">' + marker.title + '</a></h3>' + marker.desc + '</td></tr></table>';
      } else {
        var marker_content = '<table width="500"><tr valign="top"><td width="110"><img src="' + marker.checkin_icon + '" width="100"></td><td width="400"><h3 style="margin-top:0; margin-bottom:6px;">' + marker.title + '</h3></td></tr></table>';
      }

      infowindow.setContent(marker_content);
      infowindow.open(self.map, this);
    });
  },

  setMapCenterZoom:function () {
    if (this.map_bounds && this.map_bounds.min_lat && this.map_bounds.max_lng && this.map_bounds.min_lat && this.map_bounds.max_lng) {
      var bds = new google.maps.LatLngBounds(new google.maps.LatLng(this.map_bounds.min_lat, this.map_bounds.min_lng), new google.maps.LatLng(this.map_bounds.max_lat, this.map_bounds.max_lng));
    }
    if (this.map_bounds && this.map_bounds.map_center_lat && this.map_bounds.map_center_lng) {
      this.map.setCenter(new google.maps.LatLng(this.map_bounds.map_center_lat, this.map_bounds.map_center_lng), 4);
    } else {
      //this.map.setCenter(new google.maps.LatLng(marker.lat, marker.lng), this.zoom);
    }
    if (bds) {
      this.map.setCenter(bds.getCenter());
      this.map.fitBounds(bds);
    }
  },

  setView:function (view, el) {
    this.$template.find('.checkin-view-types').removeClass('active');
    if (el == 'element') {
      el.addClass('active');
    }

    if (view == 'map') {
      this.$template.find('.checkin_list_cont').css('display', 'none');
      this.$template.find('.map_canvas').css('position', 'relative');
      this.$template.find('.map_canvas').css('top', '0px');
      google.maps.event.trigger(this.map, 'resize');
      this.setMapCenterZoom();
    } else if (view == 'list') {
      this.$template.find('.checkin_list_cont').css('display', 'block');
      this.$template.find('.map_canvas').css('position', 'absolute');
      this.$template.find('.map_canvas').css('top', '10000px');
    }
  },


  // Event Map Widget
  initEventMap:function (widgetID, marker) {

    var self = this;
    this.$event_cont = $(widgetID);
    this.$event_info = this.$event_cont.find('.checkin_event_info');
    this.$event_get_location = this.$event_cont.find('.get_location');
    this.$event_edit_location = this.$event_cont.find('.edit_location');
    this.$event_location = this.$event_cont.find('.checkin_event_location');
    this.$event_locations = this.$event_cont.find('.event_locations');
    this.$event_map = this.$event_cont.find('.checkin_event_map');
    this.event_marker = false;

    try {
      if (marker) {
        this.event_marker = marker;
      }
    } catch (e) {
    }

    if (this.event_owner_mode) {
      this.initOwnerMode();
    } else if (this.event_marker && this.event_marker.place_id) {
      this.showEventMap();
    }
  },

  initOwnerMode:function () {
    var self = this;
    if (this.event_marker && this.event_marker.place_id) {
      this.showEventMap();
    } else {
      this.getEventLocation();
    }

    this.$event_edit_location.keydown(function (event) {
      if (event.key == 'enter') {
        self.getEventLocation();
      }
    });

    this.$event_edit_location.blur(function () {
      if (this.value == self.event_marker.name) {
        self.edit_event_interval = window.setTimeout(function () {
          self.$event_info.removeClass('display_none');
          self.$event_location.addClass('display_none');
        }, 3000);
      }
    });

    this.$event_info.bind('vclick', function () {
      self.editEventLocation();
    });

    this.$event_get_location.bind('vclick', function () {
      self.getEventLocation();
      if (self.edit_event_interval) {
        window.clearInterval(self.edit_event_interval);
      }
    });
  },

  getEventLocation:function () {
    var self = this;
    this.showEventLoader();

    $.post(self.get_event_loc_url + '?nocache=' + Math.random(), {
      'format':'json',
      'keyword':self.$event_edit_location.value
    }, function (response) {
      self.hideEventLoader();
      if (response && response.places) {
        self.showEventLocations(response.places);
      }
    });

  },

  showEventLoader:function () {
    this.$event_get_location.addClass('loading');
    this.$event_edit_location.attr('disabled', true);
  },

  hideEventLoader:function () {
    this.$event_get_location.removeClass('loading');
    this.$event_edit_location.attr('disabled', false);
  },

  showEventLocations:function (locations) {
    var self = this;

    this.$event_locations.empty();
    this.$event_map.addClass('display_none');

    for (var i = 0; i < locations.length; i++) {
      var location = locations[i];
      var $suggest = $('<div></div>');
      $suggest.attr({'class':'choice_label'});
      $suggest.html(location.name);

      this.$event_locations.grab($suggest);
      $suggest.data('location', location);
      $suggest.bind('vclick', function () {
        var suggest = $(this).data('location');
        self.selectEventLocation(suggest);
        self.hideEventSuggests();
      });
    }

    if (locations.length == 0) {
      var $suggest = new Element('div');
      $suggest.html(core.lang.get('CHECKIN_There are no locations'));
      this.$event_locations.grab($suggest);
    }

    this.$event_locations.removeClass('display_none');
  },

  selectEventLocation:function (location) {
    var self = this;
    this.$event_info.getElement('.checkin_label').set('html', location.name);
    this.$event_info.removeClass('display_none');
    this.$event_location.addClass('display_none');

    this.$event_map.removeClass('display_none');
    this.$event_map.addClass('checkin_event_map_loading');

    $.post(self.set_event_loc_url + '?nocache=' + Math.random(), {
        'format':'json',
        'reference':location.reference}, function (response) {
        if (response && response.place) {
          self.$event_map.removeClass('checkin_event_map_loading');
          self.event_marker = response.place;
          var LatLng = new google.maps.LatLng(self.event_marker.latitude, self.event_marker.longitude);
          self.map = new google.maps.Map(self.$event_map, {mapTypeId:google.maps.MapTypeId.ROADMAP, center:LatLng, zoom:15});
          var marker_obj = new google.maps.Marker({
            map:self.map,
            position:LatLng
          });
        }
      }
    );

  },

  hideEventSuggests:function () {
    var self = this;
    this.$event_locations.addClass('display_none');
  },

  editEventLocation:function () {
    if (this.event_marker && this.event_marker.name)
      this.$event_edit_location.attr('value', this.event_marker.name);
    this.$event_info.addClass('display_none');
    this.$event_location.removeClass('display_none');
  },

  showEventMap:function () {
    this.$event_info.find('.checkin_label').html(this.event_marker.name);
    this.$event_info.removeClass('display_none');
    this.$event_location.addClass('display_none');
    this.$event_map.removeClass('display_none');
    var LatLng = new google.maps.LatLng(this.event_marker.latitude, this.event_marker.longitude);
    this.map = new google.maps.Map(this.$event_map, {mapTypeId:google.maps.MapTypeId.ROADMAP, center:LatLng, zoom:15});
    var marker_obj = new google.maps.Marker({
      map:this.map,
      position:LatLng
    });
  }
};







var Wall = {};

Wall.events = {

  $wallComposer: [],
  $mc: null,
  like:function (e) {
    $(e).hide();
    $(e).closest('.options').find('.unlike').show();

    $.post($(e).data('url'));
  },

  _showModalContainer: function(){
    core.helper.preventZoom();
    var jq = $;
    var isIOS;
    if(!this.$mc){
      isIOS = core.device.platform.isIOS();
      var mc = document.querySelector('#feed-tools'); // Modal Container
      window.scrollTo(0, 100);
      mc._isCont = true;
      mc._page = document.querySelector('.ui-page-active');
      var $mc = jq(mc);
      if(isIOS)
        $mc.addClass('ios');
      $mc.bind('vclick', function(e){
        if(e.target._isCont){
          Wall.events._hideModalContainer();
        }
      });
      var cancelBtn = mc.querySelectorAll('.cancel');
      var postBtn = mc.querySelectorAll('.post');
      var bodyEl = mc.querySelectorAll('.body');
      jq(cancelBtn).bind('vclick', function(e){
        Wall.events._hideModalContainer();
        e.preventDefault();
        return false;
      });

      var i = 0;
      while(postBtn[i]){
        postBtn[i].bodyEl = bodyEl[i];
        i++;
      }
      jq(postBtn).bind('click', function(e){
        var bodyEl = this.bodyEl;
        var postData = new Object({format:'json'});
        var url;
        if(core.baseUrl == '/')
          url = '/comment/create';
        else
          url = core.baseUrl + '/comment/create';

        if(this.post_url)
          url = this.post_url;
        if(this.item_type)
          postData.type = this.item_type;
        if(this.item_id)
          postData.id = this.item_id;

        delete this.post_url;
        delete this.item_type;
        delete this.item_id;

        postData.body = bodyEl.value.replace(/^\s+|\s+$/g, ''); // Trimming
        if(postData.body.length){
          Wall.events._hideModalContainer();
          $.ajax({
            type:'post',
            url: url,
            data: postData,
            success: function(response){
//              TODO some action
            },
            error: function(err){

            }
          });
//        } else {
//          alert("Type some text");
//          return false;
        }
        e.preventDefault();
        return false;
      });
      jq(mc.querySelector('.compose')).bind('vclick', function(e){
        var we = Wall.events;
        we.form.submit();
        we._hideModalContainer();
        e.preventDefault();
        return false;
      });
      this.$mc = $mc;
    }
    if(isIOS) // todo opt
      $(this.$mc[0]._page).css('height', window.innerHeight + 'px');
//    page.scrollTop = 100;
    this.$mc.addClass('show')._isVisible = true;
  },
  _hideModalContainer: function(){
    if(this.$mc && this.$mc._isVisible){
      $('.ui-page-active').removeClass('ui-page-hide');
      var $mc = this.$mc;

      $mc.children().removeClass('show');
      $($mc[0].querySelectorAll('textarea')).val('');
      $mc._isVisible = false;
      setTimeout(function(){
        $mc.removeClass('show');
      }, 400);
    }
  },

  showCommentModal:function (el) {
    this._showModalContainer();
    var $fcm = $('#feed-comment-modal');
    var postBtn = $fcm[0].querySelector('.post'); // TODO NEED SOME OPTIMIZATION
    postBtn.item_id = el.getAttribute('item-id');
    postBtn.item_type = el.getAttribute('item-type');
    $fcm.addClass('show');
//    if(core.helper.isTablet())
//      $($fcm[0].querySelector('.body')).focus();
  },

  showShareModal:function (el) {
    this._showModalContainer();
    var $fsm = $('#feed-share-modal');
    var postBtn = $fsm[0].querySelector('.post'); // TODO NEED SOME OPTIMIZATION
    postBtn.post_url = el.getAttribute('post-url');
    $fsm.addClass('show');
//    if(core.helper.isTablet())
//      $fsm[0].querySelector('.body').focus();
  },

  showComposerPanel:function (el) {
    if(!this.$wallComposer.length)
      this.$wallComposer = $('#feed-composer-modal');
    var key;
    if('string' == typeof el)
      key = el;
    else
      key = $(el).data('key');

    if (!key || !window[key]) {
      return;
    }
    this.form.setWall(window[key]);
    $.each(this.form.getPlugins(), function (key, value) {
      if (Wall.events.form.isEnabledAttachment(value)) {
        $('#wall_form_' + value).show();
      }
    });
    this.form.deactivate();
    this._showModalContainer();
    var $fcp = $('#feed-composer-panel');
    $fcp.addClass('show');
    this.composeInit(el);
  },

  hashtagActivate:function(el){
    var $el = $(el);
    var $activeTags = $(document.querySelector('.ui-page-active .active_hashtags'));
    if($el.hasClass('ht-menu')){
      var $p = $(el.parentNode);
      if($p.attr('id') == el.innerHTML)
        return;
      $p.find('.active').removeClass('active');
      $el.addClass( 'active');
    }else {
      $el.addClass('active');
      var $cloneEls = $(el.parentNode.cloneNode(true)).find('a').addClass('ht-menu');
      $activeTags.empty().show().removeClass('hidden').append($cloneEls);
      $el.removeClass('active');
    }
    var htCode = el.innerHTML;
    var wall = window[$(document.querySelector('.ui-page-active .composeLink')).data('key')];
    $activeTags.attr('id', htCode);
    document.getElementsByClassName('ui-page-active')[0].scrollTop = 0;
    window.scrollTo(0, 0);
    core.helper.preventZoom();
    wall.getHashtagRelatedFeed(htCode);
  },
  hashtagDectivate:function(el, hideOnly){
    var $el = $(el);
    if($el.attr('id')){
    $el.removeAttr('id').addClass('hidden');
    setTimeout(function(){
      $el.hide();
    }, 400);
    if(!hideOnly)
      $(document.querySelectorAll('.ui-page-active select.feed-filter-select')).trigger('change');
    }
  },
  unlike:function (e) {
    $(e).hide();
    $(e).closest('.options').find('.like').show();

    $.post($(e).data('url'));
  },

  mute:function (e) {
    $(e).closest('li').remove();
    $.post($(e).data('url'));
  },

  removeTag:function (e) {
    $.mobile.showPageLoadingMsg();
    $.post($(e).data('url'), {}, function (obj) {
      $.mobile.hidePageLoadingMsg();
      var $li = $(obj.action.html);
      $(e).closest('li').html($li.html());
    });

  },

  remove:function (e) {
    if (!confirm($(e).data('message'))) {
      return;
    }
    $(e).closest('li').remove();
    $.post($(e).data('url'));
  },

  showMenu:function (e) {
    $(e).closest('li').find('.action').hide();
    Wall.customAnimate($(e).closest('li').find('.menu').show());
  },

  hideMenu:function (e) {
    $(e).closest('li').find('.menu').hide();
    Wall.customAnimate($(e).closest('li').find('.action').show());
  },


//  showForm:function (e) {
//    this.composeInit(e);
//  },

  composeInit:function (e) {
    var self = this;
    // Set params of composer
    if ($(e) && $(e).data('subject_guid')){
      this.form.subject_guid = $(e).data('subject_guid');
    }
    // Render Form

    if (!this.$form){

      var $form = UIComponent.layout.$renderComponent({
        'name':'form',
        'params': this.$wallComposer.find('.feed-composer.main').find('form')
      }).find('form');

      $form.prependTo(this.$wallComposer.find('.feed-composer.main'));
      this.$form = $form;
      /**
       * Show active items
       */
    }
    if (wall_form.params.composer.composes){
      if ($.inArray("people", wall_form.params.composer.composes) != -1){

        this.$wallComposer.find('.feed-composer.main').find('.feed-content').find('.feed-people').show().find('a, input').bind('vclick', function (){
          Wall.events.form.activate('people');
        });
      }
      if ($.inArray("checkin", wall_form.params.composer.composes) != -1){
        this.$wallComposer.find('.feed-composer.main').find('.feed-content').find('.feed-checkin').show().find('a, input').bind('vclick', function (){
          Wall.events.form.activate('checkin');
        });
      }
    }

    this.form.reset();
    /**
     * Share
     */
    var $share = self.form.getComposer().find('.share_services');
    $(this.form.getServices()).each(function (key, value) {

      var service = window.wall_form.params.services[value];

      if (!service) {
        return;
      }

      $share.show().find('.wall-share-' + value).show().attr('href', service.url).data('type', value).attr('target', '_blank').unbind().bind('vclick', function (event) {

        // to login
        if (!window.wall_form.params.services[$(this).data('type')].enabled) {
          return;
        }

        event.preventDefault();
        event.stopPropagation();

        self.form.enableShareButton($(this).data('type'), !$(this).hasClass('active'));

      });
    });


    this.form.checkServicesActive();


    /**
     * Privacy
     */

    if (window.wall_form.params.composer.privacy) {


      var html = '<select name="privacy">';
      var active = '';
      $(window.wall_form.params.composer.privacy).each(function (key, value) {
        html += '<option value="'+value.type+'" '+((value.active) ? 'checked="checked"' : '')+'>'+value.title+'</option>';
        if (value.active) {
          active = value.type;
        }
      });
      html+= '</select>';


      var $e = $('<div />');
      $e.html(html);
      $e.find('select').selectmenu();
      $e.find('input').val(active);

      $e.find('a').bind('vclick', function () {
        $e.find('a').removeClass('active');
        $(this).addClass('active');
        $e.find('input').val($(this).data('type'));
      });

      this.form.getComposer().find('.privacy').empty().append($e);
      this.form.getComposer().find('.privacy-container').show();

    }


    var active_length = this.$wallComposer.find('.shareItem').children(':visible').length;
    if (active_length%2 == 0){
      this.$wallComposer.addClass('children_odd');
    }
    this.$wallComposer.addClass('children'+active_length);


    // Auto Detect Link

    var check = function (txt) {
      txt = txt.replace('&amp;', '&');
      var matches = txt.match(/(https?\:\/\/|www\.)+([a-zA-Z0-9._-]+\.[a-zA-Z.]{2,5})?[^\s]*/i);
      if (!matches) {
        return;
      }
      if (matches.length != 3) {
        return;
      }
      if (!matches[0] || !matches[1] || !matches[2]) {
        return;
      }

      var is_video = wall_form.isEnabledAttachment('video');
      var is_link = wall_form.isEnabledAttachment('link');

      if ((matches[0].match(/(http|https)\:\/\/(www\.|m\.|)youtube\.com\/watch/ig) || matches[0].match(/(http|https)\:\/\/(www\.|m\.|)youtu\.be/ig)) && is_video) {

        Wall.events.form.showPostForm(this, 'video');
        wall_form.getComposer().find('.feed-video').find('.attachment_uri').val(matches[0]);
        wall_form.video.trigger('vclick');

      } else if (matches[0].match(/(http|https)\:\/\/(www\.|)vimeo\.com\/(m\/|)[0-9]{1,}/ig) && is_video) {

        Wall.events.form.showPostForm(this, 'video');
        wall_form.getComposer().find('.feed-video').find('.attachment_uri').val(matches[0].trim());
        wall_form.video.trigger('vclick');

      } else if (is_link) {
        Wall.events.form.showPostForm(this, 'link');
        wall_form.getComposer().find('.feed-link').find('.attachment_uri').val(matches[0].trim());
        wall_form.link.trigger('vclick');
      }


    };

    var ta = $('.feed-composer-modal').find('.feed-composer.main').find('.feed-textarea');
    ta.unbind('keydown').keydown(function (e) {
      if (e.keyCode == 32) { // space
        check(ta.val());
      }
    }).bind('paste', function (e) {
        setTimeout(function (){
          check(ta.val());
        }, 1000);
    });



  },

  hideForm:function () {
//    this.$wallComposer.hide();
//    $(document.body).append(this.$wallComposer);
    this.form.deactivate();
  },
//  toggleForm: function(el){
//    this.form.deactivate();
//    if(!this.$wallComposer.length)
//      this.$wallComposer = $('#feed-composer-modal');
//    Wall.events.showForm(el);// : Wall.events.hideForm();
//  },

  form:{
    showChooseForm:function (e) {
      var $wm = Wall.events.$wallComposer;
      Wall.customAnimate($wm.find('.choose').show());
      $wm.find('.shareForm').hide();
    },

    showPostForm:function (e, type) {
      var $wm = Wall.events.$wallComposer;
      $('.ui-page-active').addClass('ui-page-hide');
      $('#feed-composer-panel').removeClass('show');
//      $wm.find('.picup-button').button();
//      $wm.find('.popover-btn-share').show();
//      $wm.find('.popover-btn-back').show();
//      $wm.find('.popover-btn-cancel').hide();
//      Wall.customAnimate($wm.find('.shareForm').show());
      $wm.addClass('show');
      if (type) {
        this.activate(type);
      }
    },


    activate:function (type) {
      var self = this;

      var $composer = this.getComposer();

      if (!self.isEnabledAttachment(type)) {
        return;
      }

      var plugin = this[type];

      if (!plugin) {
        return;
      }

      if (!plugin.is_init) {
        plugin.init();
        plugin.is_init = true;
      }


      // show block
      if (!plugin.isCompose) {
        $composer.find('.feed-content').find('.content').not('.content_compose').hide();
      }
      $composer.find('.feed-' + type).show();


      var plugins = this.getPlugins();

      $.each(plugins, function (index, item) {
        if (!self.isEnabledAttachment(item) || !self[item]) {
          return;
        }
        var plugin_other = self[item];
        if (plugin_other.isCompose || plugin.isCompose) {
          return;
        }
        if (plugin_other.deactivate) {
          plugin_other.deactivate();
        }
      });


      // reset values
      if (!plugin.isCompose) {
        self.setAttachedType();
      }
      if ((!plugin.isCompose) && plugin.reset) {
        plugin.reset();
      }
      if (plugin.activate) {
        plugin.activate();
      }


    },

    deactivate:function () {
      var self = this;


      $(this.getPlugins()).each(function (key, type) {

        self.getComposer().find('.feed-content').find('.feed-' + type).hide();

        var plugin = self[type];
        if (!plugin) {
          return;
        }

        if (plugin.deactivate) {
          plugin.deactivate();
        }

        // reset values
        if (!plugin.isCompose) {
          self.setAttachedType();
        }
        if (plugin.reset) {
          plugin.reset();
        }

      });

    },


    params:{},
    $achts:null,
    subject_guid: '',

    wall:null,

    setWall:function (wall) {
      this.wall = wall;
      this.params = wall.params;
      window.wall_form = this;
    },

    getWall:function () {
      return this.wall;
    },


    getComposer:function () {
      return Wall.events.$wallComposer.find('.feed-composer.main');
    },
    getQuestionComposer:function () {
      return Wall.events.$wallComposer.find('.feed-composer.hequestion');
    },
    getForm:function () {
      return this.getComposer().find('form');
    },

    getBody:function () {
      return this.getForm().find('.feed-textarea');
    },

    checkValidForm:function () {
      var is_people = !!this.getComposer().find('.feed-people').find('.people_input').val();
      return (!this.isEmpty() || this.isAttached() || is_people);
    },

    setAttachedType:function (type) {
      this.getForm().find('.attachment_type').val(type || '');
    },
    getAttachedType:function () {
      return this.getForm().find('.attachment_type').val();
    },
    isAttachedFile:function () {
      return (this.getAttachedType() == 'photo');
    },


    isAttached:function () {
      return !!this.getForm().find('.attachment_type').val();
    },

    isEmpty:function () {
      return !this.getBody().val();
    },


    /**
     * It need to call to set feed settings for currently a page
     */
    setParams:function (params) {
      this.params = params;
    },

    getParams:function () {
      return this.params;
    },
    getParam:function (key) {
      return this.params[key];
    },
    getSubmitButton:function () {
      return this.getComposer().find('.feed-submit');
    },

    removeAttachmentInputs:function () {
      if (this.$achts) {
        this.$achts.remove();
        delete this.$achts;
      }
    },

    makeAttachmentInputs:function () {
      if (!this.isAttached()) {
        return;
      }
      var type = this.getAttachedType();
      this.removeAttachmentInputs();

      this.$achts = this.getComposer().find('.feed-' + type).find('input,textarea,select').clone();
      this.$achts.hide();
      this.$achts.appendTo(this.getForm());
    },

    getServices:function () {
      return ['facebook', 'twitter', 'linkedin'];
    },

    getWorkServices:function () {
      var self = this;

      var services = [];
      $(this.getServices()).each(function (key, value) {
        var service = self.params.services[value];
        if (!service) {
          return;
        }
        services[services.length] = value;
      });
      return services;
    },

    checkServicesActive:function () {
      var self = this;

      var services = this.getWorkServices();

      var data = {};
      $.each(services, function (index, item) {
        if (self.params.services[item].enabled){
          return ;
        }
        data[item] = true;
      });

      $.post(this.params.serviceRequestUrl, data, function (obj) {
        if (!obj) {
          return;
        }
        $.each(obj, function (index, item) {
          if (!item || !$.inArray(services, index)) {
            return;
          }
          if (obj[index] && obj[index].enabled) {
            self.enableShareButton(index, (self.params.services[index].enabled));
          }
        });
      });
    },

    enableShareButton:function (index, is_active) {
      var $a = this.getComposer().find('.wall-share-' + index);

      if (is_active) {
        $a.addClass('active');
        $a.find('input').val(1);
      } else {
        $a.removeClass('active');
        $a.find('input').val(0);
      }

      // Params
      if (this.params.services[index]) {
        this.params.services[index].enabled = $a.find('input').val();
      }

      // Ajax
      $.post(this.params.services[index].serviceShareUrl, {
        provider:index,
        status:$a.find('input').val()
      });

    },


    callbackFromWindow:function (name) {
      this.enableShareButton(name, true);
    },

    getSubject:function () {
      return this.subject_guid;
    },
    getActivePrivacy:function () {
      return this.getComposer().find('select[name=privacy]').val();
    },

    getShareData:function () {
      return this.getComposer().find('.share_input').serialize();
    },
    getCheckinData:function () {
      return this.getComposer().find('.feed-checkin').find('input').serialize();
    },
    getPeopleData:function () {
      return this.getComposer().find('.feed-people').find('.people_input').serialize();
    },

    reset:function () {
      this.getBody().val('');
      this.resetAttachments();
    },

    resetAttachments:function () {
      var self = this;
      var plugins = this.getPlugins();

      $.each(plugins, function (index, item) {
        if (!self.isEnabledAttachment(item)) {
          return;
        }

        if (!self[item]) {
          return;
        }
        try {
          var plugin = self[item];
          self.getForm().find('.feed-content').find('.content').hide();
          self.getForm().find('.feed-photo-form').hide();
          self.getForm().find('#picup_files').val(JSON.stringify({photo:null}));
          self.getForm().find('#picup_photo').val('Choose file...').button('refresh');
          self.setAttachedType();
          if (plugin.reset) {
            plugin.reset();
          }
        } catch (e) {

        }
      });
    },


    isEnabledAttachment:function (plugin) {
      var params = wall_form.getParams();
      return (params.composer && $.inArray(plugin, params.composer.composes) !== -1);
    },

    getPlugins:function () {
      return ['photo', 'link', 'video', 'people', 'hequestion', 'checkin'];
    },

    photo:{

      init:function () {

      },

      activate:function () {
        var $composer = wall_form.getComposer();
        $composer.find('.feed-photo-form').show();
        var pb = document.querySelector('#picup_photo');
        if(pb)
          $(pb).button();
        $composer.find('.feed-photo').hide();
        wall_form.setAttachedType('photo');
      },

      deactivate:function () {
        var $composer = wall_form.getComposer();
        $composer.find('.feed-photo-form').hide();
        $composer.find('.feed-photo').hide();
        wall_form.setAttachedType();
      },

      reset:function () {
        var $composer = wall_form.getComposer();
        $composer.find('.feed-photo-form').find('input[name=photo]').val('');
      }

    },

    link:{

//      activeImgEl: null,
      init:function () {



      },

      click:function () {

        var $composer = wall_form.getComposer();
        var params = wall_form.getParams();

        var $preview = $composer.find('.feed-link').find('.feed-link-preview');
        var $form_link_compose = $composer.find('.feed-link').find('.form-compose');


        var el = $composer.find('.feed-link').find('.attachment_uri');
        var value = el.val();

        if (!value.match(/^[a-zA-Z]{1,5}:\/\//)) {
          value = 'http://' + value;
        }

        el.val(value);

        if (el.data('completed') === false) {
          return;
        }
        el.data('completed', false);

        $.mobile.showPageLoadingMsg();

        $.post(params.composer.linkPreview, {
          uri:value,
          format:'json'
        }, function (obj) {

          $.mobile.hidePageLoadingMsg();
          el.data('completed', true);

          if (obj.status == false) {
            alert(params.composer.linkError);
            return;
          }
          $form_link_compose.hide();

          wall_form.setAttachedType('link');

          $preview.show();
          $preview.find('.item_photo').empty().show();
          $preview.find('.feed-link-options-none').show();

          $preview.find('.attachment_title').val(obj.title);
          $preview.find('.attachment_description').val(obj.description);


          if (!obj.images) {
            obj.images = [];
          }

          var images = '';
          for (var i = 0; i < obj.images.length; i++) {
            if (i == 0) {
              $preview.find('[name=description]').val(obj.images[i]);
              images += '<img src="' + obj.images[i] + '" alt="" class="link_active"/>';
            } else {
              images += '<img src="' + obj.images[i] + '" alt="" />';
            }
          }
          $preview.find('.item_photo').append(images);
          $preview.find('.item_photo').delegate('img', 'vclick', function(event){
            var $this = $(this);
            var active = "link_active";
            if($this.hasClass(active))
              return;
            if(this.parentNode._selectedImg)
              $(this.parentNode._selectedImg).removeClass(active);
            this.parentNode._selectedImg = this;
            $this.addClass(active);
            $('#composer_attachment_thumb').val(this.src);
          });

          if (obj.images.length) {
            $preview.find('.feed-link-count').show().find('.current').html(1);
            $preview.find('.feed-link-count').show().find('.total').html(obj.images.length);
          }


          if (obj.images.length == 0) {
            $preview.find('.item_photo').hide();
            $preview.find('.feed-link-options-none').hide();
          }


          // Set Attachment Thumb
          var $active_tpl = $preview.find('.item_photo img.link_active');
          $active_tpl[0].parentNode._selectedImg = $active_tpl[0];
          $preview.find('.attachment_thumb').val($active_tpl.attr('src'));
          setTimeout(function(){window.scrollTo(0,0);},100);
        });
      },


      selectImg:function (img) {
        var $composer = wall_form.getComposer();

        var $preview = $composer.find('.feed-link').find('.feed-link-preview');
        var $previous = $preview.find('.feed-link-previous');

        var $active = $preview.find('.item_photo').find('.link_active');
        if ($active.next()) {
          $active.removeClass('link_active').hide();
          $active.next().addClass('link_active').show();
          if (!$active.next().next().length) {
            $(e).hide();
          }
          $previous.show();
        }

        $preview.find('.feed-link-count').find('.current').html(
          parseInt($preview.find('.feed-link-count').find('.current').html()) + 1
        );

        // Set Attachment Thumb
        var $active_tpl = $preview.find('.item_photo').find('.link_active');
        $preview.find('.attachment_thumb').val($active_tpl.attr('src'));

      },

      checkboxChange:function (e) {
        var $composer = wall_form.getComposer();

        var $preview = $composer.find('.feed-link').find('.feed-link-preview');

        if ($(e).attr('checked')) {
          $preview.find('.item_photo').hide();
//          $preview.find('.image_choose').hide();

          // Set Attachment Thumb
          $preview.find('.attachment_thumb').val('');

        } else {
          $preview.find('.item_photo').show();
//          $preview.find('.image_choose').show();

          // Set Attachment Thumb
          var $active_tpl = $preview.find('.item_photo').find('.link_active');
          $preview.find('.attachment_thumb').val($active_tpl.attr('src'));

        }
      },


      previous:function (e) {
        var $composer = wall_form.getComposer();

        var $preview = $composer.find('.feed-link').find('.feed-link-preview');
        var $next = $preview.find('.feed-link-next');

        var $active = $preview.find('.item_photo').find('.link_active');
        if ($active.prev()) {
          $active.removeClass('link_active').hide();
          $active.prev().addClass('link_active').show();
          if (!$active.prev().prev().length) {
            $(e).hide();
          }
          $next.show();
        }

        $preview.find('.feed-link-count').find('.current').html(
          parseInt($preview.find('.feed-link-count').find('.current').html()) - 1
        );

        // Set Attachment Thumb
        var $active_tpl = $preview.find('.item_photo').find('.link_active');
        $preview.find('.attachment_thumb').val($active_tpl.attr('src'));

      },


      activate:function () {
//        var $composer = wall_form.getComposer();
//        $composer.find('.feed-link').find('.attachment_uri').focus();
      },

      reset:function () {
        var $composer = wall_form.getComposer();
        $composer.find('.feed-link').find('.form-compose').show();
        $composer.find('.feed-link').find('.feed-link-preview').hide();
        $composer.find('.feed-link').find('.attachment_uri').val('');
        $composer.find('.feed-link').find('.attachment_title').val('');
        $composer.find('.feed-link').find('.attachment_description').val('');
        $composer.find('.feed-link').find('.attachment_thumb').val('');
        $composer.find('.feed-link').find('.options_none').attr('checked', false);
//        $composer.find('.feed-link').find('.image_choose').hide();
      }

    },

    video:{


      init:function () {

      },


      click:function () {
        var $composer = wall_form.getComposer();
        var params = wall_form.getParams();

        var $preview = $composer.find('.feed-video').find('.feed-video-preview');
        var $form_video_compose = $composer.find('.feed-video').find('.form-compose');

        var el = $composer.find('.feed-video').find('.attachment_uri');
        var value = el.val();

        if (!value.match(/^[a-zA-Z]{1,5}:\/\//)) {
          value = 'http://' + value;
        }

        if (el.data('completed') === false) {
          return;
        }
        el.data('completed', false);


        var type = 1;
        /**
         * Link of Youtube
         */
        if (value.match(/(http|https)\:\/\/(www\.|)youtube\.com\/watch/ig) || value.match(/(http|https)\:\/\/(www\.|)youtu\.be/ig)) {
          type = 1;
        }
        /**
         * Link of Vimeo
         */
        if (value.match(/(http|https)\:\/\/(www\.|)vimeo\.com\/(m\/|)[0-9]{1,}/ig)) {
          type = 2;
        }
        if (value.match(/(http|https)\:\/\/(www\.|)dailymotion\.com\/[0-9]{1,}/ig)) {
          type = 4;
        }

        $.mobile.showPageLoadingMsg();

        $.post(params.composer.videoComposeUrl, {
          uri:value,
          type:type,
          format:'json'
        }, function (obj) {

          $.mobile.hidePageLoadingMsg();
          el.data('completed', true);

          if (!obj.status) {
            alert(obj.message);
            return;
          }
          $form_video_compose.hide();

          wall_form.setAttachedType('video');

          $preview.show();
          $preview.find('.item_photo').empty().show();

          $preview.find('.attachment_title').val(obj.title);
          $preview.find('.attachment_description').val(obj.description);
          $preview.find('.attachment_video_id').val(obj.video_id);

          var images = '<img src=' + obj.src + ' alt=""/>';
          $preview.find('.item_photo').append(images);

        });
      },

      reset:function () {
        var $composer = wall_form.getComposer();
        $composer.find('.feed-video').find('.form-compose').show();
        $composer.find('.feed-video').find('.feed-video-preview').hide();
        $composer.find('.feed-video-preview').hide();
        $composer.find('.feed-video').find('.attachment_uri').val();
        $composer.find('.feed-video').find('.attachment_title').val('');
        $composer.find('.feed-video').find('.attachment_description').val('');
        $composer.find('.feed-video').find('.attachment_video_id').val('');
      },

      activate:function () {
//        var $composer = wall_form.getComposer();
//        $composer.find('.feed-video').find('.attachment_uri').focus();
      }




    },

    people:{
      isCompose:true,

      init:function () {
        var self = this;

        var $composer = wall_form.getComposer();
        var params = wall_form.getParams();

        var $page = $composer.find('.feed-people');
        var $selected = $page.find('.selected');
        var $searchField = $page.find('input[name=autocompleter]');
        var $list = $page.find('.message-autosuggest');

        $selected.delegate('li', 'vclick', function () {
          var $li = $(this);
          $li.remove();
          $('#' + $li.attr('data-id')).show();
        });

        $searchField.autocomplete({
          method:'POST',
          target:$list,
          source:params.suggestPeopleUrl,
          cancelRequests:true,
          getRequestData:function ($field) {
            return {'value':$field.val()};
          },
          onPush:function (e, value) {
            var id = 'selected-' + value.guid;
            if ($selected.find('li#' + id).length > 0) {
              return;
            }
            var peoples = $page.find('.people_input').val();
            if (peoples) {
              peoples += ',' + value.guid;
            } else {
              peoples += value.guid;
            }
            $page.find('.people_input').val(peoples);

            var $li = $('<li class="ui-bar-e" />');
            $li.attr({
              'id':id,
              'data-id':value.guid
            });
            $('#' + value.guid).hide();

            $li.data('user', value);
            $li.html(value.label);
            $selected.append($li);
            if ($selected.find('li').length >= 50) {
              $page.find('input[name=autocompleter]').disabled = true;
            }
            $searchField.val('');
//            $list.empty();
          },

          injectChoice:function (index, value) {
            var win = window;
            win.clearTimeout(win._apactoid);
            var $a = $('<a />');
            $a.html(value.label).prepend($(value.photo));
            var $li = $('<li class="ui-li ui-li-static ui-btn-up-c" />').append($a);
            $li.attr({
                          'id':value.guid
                        });
            win._apactoid =  setTimeout(function(){$("#add_people").blur()}, 400);
            return $li;
          },
          minLength:1,
          transition:'fade',
          matchFromStart:true
        });
      },

      activate:function () {
        var $composer = wall_form.getComposer();
        $composer.find('.feed-people').find('[name=autocompleter]');
      },

      reset:function () {
        var $composer = wall_form.getComposer();
        $composer.find('.feed-people').find('.message-autosuggest').empty();
        $composer.find('.feed-people').find('.selected').empty();
        $composer.find('.feed-people').find('.people_input').val('');
      }


    },

    hequestion:{

      $options:null,
      $optionTpl:null,

      init:function () {
        this.initializeForm();
      },


      activate:function () {
        var self = this;

        var $composer = wall_form.getComposer();
        var $qcomposer = wall_form.getQuestionComposer();

        $qcomposer.show();
        $composer.hide();

        $qcomposer.focus();

      },

      deactivate:function () {
        var $composer = wall_form.getComposer();
        var $qcomposer = wall_form.getQuestionComposer();

        $qcomposer.hide();
        $composer.show();
      },

      reset:function () {

        var $qcomposer = wall_form.getQuestionComposer();

        $qcomposer.find('.feed-textarea').attr('value', '').blur();
        $qcomposer.find('.hqAllowAddOptions').find('[type=checkbox]').attr('checked', true);
        this.createDefault();
      },


      click:function () {

        var data = '';
        var $composer = wall_form.getQuestionComposer();
        var params = wall_form.params;
        var body = $composer.find('.feed-textarea');

        data += $composer.find('form').serialize();
        data += '&' + $composer.find('.feed-hequestion input').serialize();

        var url = params.questionUrl;
        url += '?';
        url += '&subject=' + wall_form.getSubject();
        url += '&auth_view=' + $composer.find('select[name=privacy]').val();

        if (body.data('completed') === false) {
          return;
        }
        body.data('completed', false);


        $.mobile.showPageLoadingMsg();

        $.post(url, data, function (obj) {

          $.mobile.hidePageLoadingMsg();

          body.data('completed', true);

          if (!obj.status) {
            alert(obj.message || obj.error);
            return;
          }

          wall_form.reset();
          wall_form.deactivate();

          var $li = $(obj.action.html);

          Wall.events.hideForm();
          wall_form.getWall().$wall.prepend($li);
          Wall.customAnimate($li);

        });
      },


      initializeForm:function () {
        var self = this;

        var $composer = wall_form.getQuestionComposer();

        self.$options = $composer.find('.hqFormOptions');
        self.$optionTpl = self.$options.find('li').clone();
        self.$options.find('li').remove();

        self.$optionTpl.show();

        self.createDefault();


        /**
         * Privacy
         */

        var html = '<select name="privacy">';
        var active = '';
        $(wall_form.params.questionPrivacy).each(function (key, value) {
          html += '<option value="'+value.type+'" '+((value.active) ? 'checked="checked"' : '')+'>'+value.title+'</option>';
          if (value.active) {
            active = value.type;
          }
        });
        html+= '</select>';

        var $e = $('<div />');
        $e.html(html);
        $e.find('input').val(active);
        $e.find('select').selectmenu();

        $e.find('a').bind('vclick', function () {
          $e.find('a').removeClass('active');
          $(this).addClass('active');
          $e.find('input').val($(this).data('type'));
        });

        $composer.find('.privacy').empty().append($e);
        $composer.find('.privacy-container').show();


      },


      createDefault:function () {

        if (!this.$options) {
          return;
        }

        this.$options.children().remove();

        for (var i = 1; i < 4; i++) {
          this.createOption(i);
        }
      },


      createOption:function (key) {
        var self = this;


        var option = self.$optionTpl.clone();
        var text = self.$optionTpl.find('input').data('value');

        option.attr(({'class':'hqOption'}));
        option.find('input').attr({'class':'hqTextDisactive', 'name':'options[' + key + ']', 'value':text});


        option.appendTo(this.$options);

        option.find('input').focus(function () {

          $(this).removeClass('hqTextDisactive');

//          if (this.value == text) {
//          } else {
//
//          }

          if (!$(this).parent().next().length && self.$options.children().length < wall_form.params.questionMaxOption) {
            self.createOption($(this).parent().prevAll().length + 2);
          }

        });

        option.find('input').blur(function () {
          $(this).removeClass('hqTextDisactive');
          if (this.value == '') {
            $(this).addClass('hqTextDisactive');
          } else {
          }
        });


      }





    },

    checkin:{
      isCompose:true,
      position:{},
      default_location:null,

      init:function () {
        var self = this;
        var $composer = wall_form.getComposer();
        var params = wall_form.getParams();


        if (params.checkinDefaultLocation && params.checkinDefaultLocation.length != 0) {
          this.default_location = params.checkinDefaultLocation;
        }

        var $page = $composer.find('.feed-checkin');

        $page.find('.checkinDetect').unbind().bind('vclick', function () {

          $.mobile.showPageLoadingMsg();

          if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
              function (position) {

                var data = {
                  'accuracy':position.coords.accuracy,
                  'latitude':position.coords.latitude,
                  'longitude':position.coords.longitude,
                  'name':(position.address) ? (position.address.street + delimiter + position.address.city) : '',
                  'vicinity':(position.address) ? (position.address.street + delimiter + position.address.city) : ''
                };

                if (data.name.length == 0 && data.latitude && data.longitude) {

                  var $checkin_map = $composer.find('.feed-checkin').find('.checkin-autosuggest-map');
                  $checkin_map.show();

                  var latLong = new google.maps.LatLng(data.latitude, data.longitude);
                  var map = new google.maps.Map($checkin_map[0], {mapTypeId:google.maps.MapTypeId.ROADMAP, center:latLong, zoom:15});

                  var request = {location:latLong, radius:500};

                  var service = new google.maps.places.PlacesService(map);
                  service.search(request, function (results, status) {

                    if (status == 'OK') {
                      data.name = results[0].name;
                      data.vicinity = results[0].vicinity;
                    }

                    self.setPosition(data);
                    self.showSelectedMarker(data);

                    $.mobile.hidePageLoadingMsg();

                  });

                } else {
                  self.setPosition(data);
                }

              },
              function (msg) {

                $.mobile.hidePageLoadingMsg();

                alert(self.params.checkinError);

                var data = {
                  'accuracy':0,
                  'latitude':0,
                  'longitude':0,
                  'name':'',
                  'vicinity':''
                };

                self.setPosition(data);
                self.showSelectedMarker(data);
              }
            );
          }

        });


        var $page = $composer.find('.feed-checkin');
        var $selected = $page.find('.selected');
        var $searchField = $page.find('input[name=autocompleter]');
        var $list = $page.find('.message-autosuggest');

        $searchField.autocomplete({
          method:'POST',
          target:$list,
          source:params.checkinUrl,
          cancelRequests:true,
          getRequestData:function ($field) {
            return {'value':$field.val()};
          },
          onPush:function (e, value) {

            var $li = $('<li />');
            $li.attr({'id':value.id});
            $li.html('<a href="javascript:void(0);" class="btn btn-primary"></a><a href="javascript:void(0)" class="btn btn-primary"><i class="icon-remove"></i></a>');
            $li.data('user', value);
            $li.find('a').first().html(value.name).attr('href', 'javascript:void(0)');
            $li.find('input').val(value.id);
            $li.find('a').last().bind('vclick', function () {
              $(this).closest('li').remove();
              self.hideMap();
              self.reset();
            });
            $selected.find('li').remove();
            $selected.append($li);
            $searchField.val('');
            $list.empty();

            self.setPosition(value);
            self.showSelectedMarker(value);

            $selected.find('li').remove();

          },
          injectChoice:function (index, value) {
            var icon = (value.icon) ? value.icon : wall_form.getParams().checkinDefaultIcon;
            var $a = $('<a />').attr({
              'id':value.id
            });
            $a.html(value.name).prepend($(icon).attr('class', 'ac-icon ui-li-icon'));
            var $li = $('<li />').append($a);
            $li.attr('class', 'ui-li ui-li-static ui-btn-up-c');
            return $li;
          },
          minLength:1,
          transition:'fade',
          matchFromStart:true
        });
      },


      showSelectedMarker:function (user_checkin) {
        var self = this;

        var $composer = wall_form.getComposer();

        var $checkin_map = $composer.find('.feed-checkin').find('.checkin-autosuggest-map');
        $checkin_map.show();

        if (user_checkin.latitude == undefined) {
          var map = new google.maps.Map($checkin_map[0], {mapTypeId:google.maps.MapTypeId.ROADMAP, center:new google.maps.LatLng(0, 0), zoom:15, draggable:false});
          var service = new google.maps.places.PlacesService(map);
          service.getDetails({'reference':user_checkin.reference}, function (place, status) {
            if (status == 'OK') {
              user_checkin.name = place.name;
              user_checkin.google_id = place.id;
              user_checkin.latitude = place.geometry.location.lat();
              user_checkin.longitude = place.geometry.location.lng();
              user_checkin.vicinity = (place.vicinity) ? place.vicinity : place.formatted_address;
              user_checkin.icon = place.icon;
              user_checkin.types = place.types.join(',');
              self.showSelectedMarker(user_checkin);
            }
          });
          $checkin_map.addClass('display_none');
          return;
        }

        var $checkin_map = $composer.find('.feed-checkin').find('.checkin-autosuggest-map');
        $checkin_map.show();

        self.setPosition(user_checkin);
        var data = self.position;
        var latLong = new google.maps.LatLng(data.latitude, data.longitude);
        var map = new google.maps.Map($checkin_map[0], {mapTypeId:google.maps.MapTypeId.ROADMAP, center:latLong, zoom:15, draggable:false});
        this.map = map;

        var marker = new google.maps.Marker({position:latLong, map:map});
        map.setCenter(latLong);

        this.bindMapEvents();
      },

      bindMapEvents:function () {

        var self = this;

        var $composer = wall_form.getComposer();
        var $page = $composer.find('.feed-people');
        var $searchField = $page.find("input[name=autocompleter]");

        google.maps.event.addListener(this.map, 'maptypeid_changed', function () {
          $searchField.focus();
        });
        google.maps.event.addListener(this.map, 'zoom_changed', function () {
          $searchField.focus();
        });
        google.maps.event.addListener(this.map.getStreetView(), 'closeclick', function () {
          $searchField.focus();
        });
        google.maps.event.addListener(this.map.getStreetView(), 'pov_changed', function () {
          $searchField.focus();
        });
      },

      hideMap:function () {
        var $composer = wall_form.getComposer();
        var $checkin_map = $composer.find('.feed-checkin').find('.checkin-autosuggest-map');
        $checkin_map.hide();
      },

      isValidPosition:function (position, check_coordinates) {
        var position = (position) ? position : this.position;
        var isValid = (check_coordinates)
          ? (position && position.latitude && this.position.longitude)
          : (position && position.name != undefined && position.name != '');
        return isValid;
      },


      getLocation:function () {
        var location = {'latitude':0, 'longitude':0};
        if (this.isValidPosition(false, true)) {
          location.latitude = this.position.latitude;
          location.longitude = this.position.longitude;
        }
        return location;
      },


      setPosition:function (position) {
        var $composer = wall_form.getComposer();

        this.position = position;

        if (this.isValidPosition(position)) {
        } else {
        }
        var label = this.getLocationText();
        if (label) {
          $composer.find('.feed-checkin').find('.checkinLabel').show().html(this.getLocationText());
        }
        $composer.find('.feed-checkin').find('.checkin_input').val(arrayToUrl(this.position));
      },

      getLocationText:function () {
        var locationText = '';
        if (this.position.name) {
          locationText = this.position.name;
        }
        return locationText;
      },

      activate:function () {
        var self = this;
        var $composer = wall_form.getComposer();

        if (self.default_location && self.default_location.latitude) {
          self.setPosition(self.default_location);
          self.showSelectedMarker(self.default_location);
        }
      },

      reset:function () {
        var $composer = wall_form.getComposer();
        var self = this;
        $composer.find('.feed-checkin').find('.message-autosuggest').empty();
        $composer.find('.feed-checkin').find('.selected').empty();
        $composer.find('.feed-checkin').find('.checkin_input').val('');
        this.hideMap();
        $composer.find('.feed-checkin').find('.checkinLabel').hide();
      }

    },


    submit:function (e) {
      var self = this;
      if(window._wefss_ajax)
        return;

      // submit the question form
      if ($('.feed-composer-modal').find('.feed-composer.hequestion:visible').length){
        Wall.events.form.hequestion.click();
        return;
      }

      if (!this.checkValidForm()) {
        alert(this.params.composer.postError);
        return;
      }
      var $b = this.getSubmitButton();
      if ($b.hasClass('active')) {
        return;
      }
      $b.addClass('active');

      this.makeAttachmentInputs();

      var $form = this.getForm();
      var data = $form.serialize();

      var current_url = UIComponent.helper.$getActivePage().data('url');
      $form.find('input[name="return_url"]').val(current_url);

      /* Build Query */
      var url = this.params.composer.postUrl;
      url += '?';
      url += '&subject=' + this.getSubject();
      url += '&privacy=' + this.getActivePrivacy();
      url += '&' + this.getShareData();
      url += '&' + this.getCheckinData();
      url += '&' + this.getPeopleData();

      $form.attr('action', url);

      // send with POST
      if (this.isAttachedFile() && !window.isApplication) {
        $form.submit();
        return;
      }

      url += '&format=' + 'json';

      /**
       * Ajax Posting
       */
      window._wefss_ajax = true;
      $.post(url, data, function (obj) {
        self.removeAttachmentInputs();
        $b.removeClass('active');
        if('string' == typeof obj){
          try{
          obj = $.parseJSON(obj);
          }catch(e){
            console.log(e);
          }
        }
        if (!obj.status) {
          alert(obj.message || obj.error);
          return;
        }

        self.reset();

        var $li = $(obj.action.html);
        wall_form.getWall().inlineVideo($li[0]);
        Wall.events.hideForm();
        wall_form.getWall().$wall.prepend($li);
        Wall.customAnimate($li);


        // share to facebook, twitter and etc ...
        $.post(self.params.composer.postServiceUrl + '?' + self.getShareData(), {
          action_id: obj.action_id
        });
        window._wefss_ajax = false;
        $('.ui-page-active .feed-new-updates').data('nextid', obj.action_id);

      });


    }

  }






};

Wall.customAnimate = function ($e) {
};