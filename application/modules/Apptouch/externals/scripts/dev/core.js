var core = {

  baseUrl:false,
  location:{},
  renderer:null,
  _isTablet:false,
  Picup:function ($form) {
    return core.picup.setup($form);
  },

  picup:{

    $form:null,
    $files:null,
//    $filesSelectorTpl:null,
    files:{},
    pref:'picup_',

    _save:function () {
      var value = JSON.stringify(this.files);
      this.$files.attr('value', value);
    },

    addFiles:function ($target, response) {
      var isMulti = ($target[0].getAttribute('name').indexOf('[]')) > -1;
      for (var i in response) {
        var photo = response[i];
        var name = photo.input.name.replace('[]', '');
        if (!this.files[name])
          this.files[name] = [];
        if ($.inArray(photo.input.value, this.files[name]) == -1) {
          if (isMulti) {
            this.files[name].push(photo.input.value);
          } else {
            this.files[name] = [];
            this.files[name].push(photo.input.value);
          }
        }
      }
      this.$form.trigger('picupafterupload', {'target':$target, 'response':response});
      this._save();
    },

    removeFiles:function (name, value, howmany) {
      name = name.replace(this.pref, '');
      howmany = howmany > 1 ? howmany : 1;
      if (name && this.files[name]) {
        var index = this.files[name].indexOf(value);
        if (value && index > -1) {
          this.$form.trigger('picupremovefiles', this.files[name][index]);
          this.files[name].splice(index, howmany);
          this.files[name] = core.helper.getArrayValues(this.files[name]);
          this._save();
          return;
        }
        this.$form.trigger('picupremovefiles', this.files[index]);
        delete this.files[index];
        this._save();
      }
    },

    onSuccess:function ($target, response) {
      window.history.back();
      this.$form.trigger('picupuploadsuccess', {'target':$target, 'response':response});
      this.addFiles($target, response);
    },

    onFailure:function ($target, response) {
      this.$form.trigger('picupuploadfailed', {'target':$target, 'response':response});
    },

    defaultParams:{
      callbackURL:'',
      debug:true,
      postUrl:'', // will overwrite
      returnStatus:true,
      postValues:escape('format=json'), // todo
      postImageParam:'', // will overwrite
      purpose:"APPTOUCH_Select a sample image to add to ", // todo
      referrerName:'', //todo
      returnServerResponse:true
    },

    setup:function ($form) {
      this.files = {};
      this.$form = $form;
      this.$form.data('picup', this);
      this.$files = $(document.createElement('input')).attr({type:'hidden', id:'picup_files', name:'picup_files', value:'{}'});
      this.$form.append(this.$files);
//      this.$filesSelectorTpl = $(document.getElementById('template-picupFileSelector').querySelectorAll('.picupFileSelector')[0].cloneNode(true));

      var $fileInputs = $form.find('input:file');

      for (var i = 0; i < $fileInputs.length; i++) {

        var $fileInput = $($fileInputs[i]);
        var picupParams = this.setupParams($fileInput, $form);
        var $picupButton = $fileInput.clone();
        this.files[$fileInput[0].getAttribute('name')] = null;
        $picupButton.attr({
          'id':this.pref + $picupButton[0].getAttribute('id'),
          'name':this.pref + $picupButton[0].getAttribute('name')
        });
        $picupButton.addClass('picup-button');
        $picupButton.insertAfter($fileInput);
        $fileInput[0].style.display = 'none';

        Picup.convertFileInput($picupButton[0], picupParams);

      }
      this._save();
      return this;
    },

    setupParams:function ($fileElem, $form) {

      // TODO This lines has been changed by Michael to fix a trouble with wall
      // TODO It generates wrong url (/dashboard instead of /members/home) hz why :((
      var location = core.location;
      var baseUrl = core.baseUrl;
      if ($form.hasClass('feed-form')) {

        var callbackUrl = location.href;
        if (callbackUrl && callbackUrl.indexOf(baseUrl) == 0)
          callbackUrl = location.protocol + '//' + core.location.hostname + callbackUrl;

      } else {
        var jQPageData = UIComponent.jQPageData;
        var callbackUrl = jQPageData ? (jQPageData.toPage ? jQPageData.toPage : jQPageData.dataUrl) : location.href;
        if (callbackUrl && callbackUrl.indexOf(baseUrl) == 0)
          callbackUrl = location.protocol + '//' + location.hostname + callbackUrl;

      }

      var params = this.defaultParams;
      var inputName = $fileElem[0].getAttribute('name');
//      var $filesSelector = this.$filesSelectorTpl.clone();
//      delete $filesSelector.find((!this.isArray($fileElem)) ? 'input:checkbox' : 'input:radio').remove();
//      var fileElemName = $fileElem[0].getAttribute('name').replace('[]', '');
//      $filesSelector[0].setAttribute('id', fileElemName + '_fileSelector');
//      $fileElem.data('fileSelector', $filesSelector);

//      $filesSelector.insertAfter($fileElem);
      params.callbackUrl = escape(callbackUrl);
      params.postUrl = escape(location.protocol + '//' + location.hostname + baseUrl + 'apptouch/picup');
      params.postValues = escape('format=json' + '&' + 'input=' + inputName);
      params.postImageParam = inputName;

      return params;
    },

    isArray:function ($fileElem) {
      return $fileElem[0].getAttribute('name').indexOf('[]') > -1;
    },

    handleUploadResponse:function ($target, response) {
      if (response && 'object' == typeof response)
        this.onSuccess($target, response);
      else
        this.onFailure($target, response);
    }
  },

  activityMonitor:{

    options:{
      observeDelay:300000,
      _requiredActivityCount:1
    },
    _eventPref:'state',
    _lastCheckTime:0,
    _interval:null,
    activities:{
      'scroll':0,
      'click':0,
      'keypress':0,
      'focus':0,
      'blur':0
    },
    states:{
      0:'idle',
      1:'active'
    },
    status:1,

    init:function (options) {
      this._setOptions(options);
      this._bindEvents();
    },

    _setOptions:function (options) {
      this._lastCheckTime = new Date().getTime();
      for (var name in options) {
        var option = this.options[name];
        if (option != undefined) {
          this.options[name] = options[name];
        }
      }
    },

    checkStatus:function (event) {
      if (event && event.type) {
        this.activities[event.type]++;
      }
      var nowStatus = this._getStatus();
      if (nowStatus != this.status) {
        this.status = nowStatus;
        var eventName = this._eventPref + this.states[nowStatus];
        $(document).trigger(eventName, this.activities);
      }
      this._lastCheckTime = new Date().getTime();
      this._emptyActivities();
      return this.status;
    },

    _getStatus:function (assoc) {
      var status = this.status;
      var activityCount = 0;
      for (var event in this.activities) {
        activityCount += this.activities[event];
      }
      var isNowActive = activityCount >= this.options._requiredActivityCount;
      var now = new Date().getTime();
      if (this.activities['focus'] || (!this.status && isNowActive)) {
        status = 1;
      } else if (this.activities['blur'] || (now - this._lastCheckTime >= this.options.observeDelay && this.status && !isNowActive)) {
        status = 0;
      }
      if (assoc)
        status = this.states[status];
      return status;
    },

    _emptyActivities:function () {
      for (var name in this.activities) {
        this.activities[name] = 0;
      }
    },

    _bindEvents:function () {
//      $(document).bind('scroll', function(e){
//        self.checkStatus(e);
//      });
//
//      $(document).bind('click', function(e){
//        self.checkStatus(e);
//      });
//
//      $(document).bind('keypress', function(e){
//        self.checkStatus(e);
//      });
//
//      $(window).bind('resize', function(e){
//        self.checkStatus(e);
//      });

      $(window).focus(function (e) {
        core.activityMonitor.checkStatus(e);
      });

      $(window).blur(function (e) {
        core.activityMonitor.checkStatus(e);
      });
      this._interval = window.setInterval(function () {
        core.activityMonitor.checkStatus();
      }, this.options.observeDelay)
    }
  },
  device:{
    uadb:[

    ],

    construct:function () {
      $.cookie('navigator', this.toJSON());
      this.toJSON();
    },

    getGrade:function (numeric) {

      if (numeric) {
        return 1 // todo temp solution
      }
      return 'a' // todo temp solution
    },

    toJSON:function () {
      var info = {
        'appCodeName':navigator.appCodeName,
        'appName':navigator.appName,
        'appVersion':navigator.appVersion,
        'grade':this.getGrade(),
        'language':navigator.language,
        'oscpu':navigator.oscpu,
        'platform':navigator.platform
      };
      return JSON.stringify(info);
    },

    browser:{
      getInfo:function () {

      },

      isSafari:function () {

      },

      isAndroidWebkit:function () {

      },

      isMozillaMobile:function () {

      },

      isOperaMini:function () {

      },

      isOperaMobile:function () {

      },

      isIEMobile:function () {

      }
    },

    platform:{

      getOSInfo:function () {

      },

      isPhoneGap:function () {
        return window.cordova || window.PhoneGap;
      },

      isIOS:function () {
        return navigator.platform.indexOf('iPhone') > -1 ||
        navigator.platform.indexOf('iPod') > -1 ||
        navigator.platform.indexOf('iPad') > -1;
      },
      getIOSVersion:function () {
        if ((this.isIOS())) {
          var agent = window.navigator.userAgent,
            start = agent.indexOf('OS ');
          return window.Number(agent.substr(start + 3, 3).replace('_', '.'));
        }
        return false;
      },
      isAndroid:function () {
        var i = navigator.userAgent.indexOf('Android');
        if(i > -1){
          var ver = parseFloat(navigator.userAgent.substr(i+8));
          return ver ? ver : 1;
        }
        return navigator.userAgent.indexOf('Android') > -1;
      },

      isBlackBerry:function () {

      },

      isSymbian:function () {

      },

      isWinMobile:function () {

      },

      isPalmOS:function () {

      }
    }


  },

  locale:{
    format:function (name) {
      return document.getElementsByTagName('locale')[0].getAttribute(name);
    }
  },

  lang:{

    data:{},

    get:function (keys, count) {
      var value = null;
      if ($.type(keys) == 'string' && arguments.length == 1 && 'string' == typeof this.data[keys]) {
        return this.data[keys];
      } else if ($.type(keys) == 'string' && arguments.length == 2) {
        value = this.data[keys];
        if ($.type(value) == 'array') {
          value = count == 1 ? value[0] : value[1];
        } else if (value == null) {
          value = keys;
        }
        value = value.replace(/%1\$s/g, count).replace(/%s/g, count).replace(/%/g, count);

      } else if (arguments.length >= 2) {

        value = this.data[keys];
        for (var i = 1; i < arguments.length; i++) {
          var reg = new RegExp('%' + i + '\\$s', 'gi');
          value = value.replace(reg, arguments[i]);
        }
      } else {
        console.warn('Untranslated word: ' + keys);
        return keys;
      }

      return value;
    },

    add:function (data) {
      if ($.type(data) == 'object') {
        for (var key in data) {
          if ($.type(key) == 'string')
            this.data[key] = data[key];
        }
      }
    }
  },

  cache:{
    clear:function (except) {
      console.log('All DOM cache has been cleared');
      var rh = renderer.helper;
      if ('string' == typeof except)
        rh.removeElements($(document.querySelectorAll('.ui-page')).not(['.ui-page-active', except].join()));
      else
        rh.removeElements($(document.querySelectorAll('.ui-page')).not('.ui-page-active'));
      $(document).data('clearCache', 0);
    },

    checkCache:function (except) {
      if ($(document).data('clearCache')) {
        this.clear(except);
      }
    }
  },


  construct:function (data) {
    this.configure();
    this.renderer = renderer.construct(data);
  },

  configure:function (param) {
    var $mobile = $.mobile;
    this.device.construct();
    this.activityMonitor.init();
    this.applyExtends();
    this.location = window.location;
    $mobile.defaultPageTransition = 'none';
    $mobile.defaultDialogTransition = 'none';
    $mobile.loadingMessageTextVisible = true;
    $mobile.loadingMessage = core.lang.get('Loading...');
    $mobile.zoom.disable(true);

//      Event Settings
    $.event.special.swipe.horizontalDistanceThreshold = 180;

//    Check For Tablet
    this._isTablet = !!document.querySelector('body.tablet');

    //Whether the text should be visible when a loading message is shown. The text is always visible for loading errors.
    $(document).bind('pageinit', function (event, data) {
      if ($(event.target).data('responseData'))
        window.setTimeout(function () {
          var core = window.core;
          if (core.helper.isTablet())
            core.cache.checkCache('#core_board_index_rewrite');
          else
            core.cache.checkCache();
          var $page = $(event.target);
          var responseData = $page.data('responseData');
          var jQPageData = $page.data('jQPageData');

          if (!responseData)
            return;
          var page = responseData.page;

          if (!page)
            return;

          var requestParams = page.info.params;

          if (!requestParams)
            return;

          var arr = (requestParams.controller + '-' + requestParams.action).split('-');
          for (var i = 1; i < arr.length; i++) {
            arr[i] = arr[i].capitalize();
          }
          var methodName = arr.join('');
          var init = Initializer;
          if (init[requestParams.module] && 'function' == typeof init[requestParams.module][methodName])
            init[requestParams.module][methodName](page, jQPageData, $page);
//        else {
//            alert(requestParams.module);
//            alert(methodName);
//        }
        }, 1000);
    });
  },

  applyExtends:function () {
    String.prototype.capitalize = function () {
      return this.charAt(0).toUpperCase() + this.slice(1);
    }
    window.alertLog = function (object) {
      alert(JSON.stringify(object));
    }
  },

  setBaseUrl:function (url) {
    this.baseUrl = url;
    var m = this.baseUrl.match(/^(.+?)index[.]php/i);
    this.basePath = ( m ? m[1] : this.baseUrl );
  },

  helper:{

    queryStringToObject:function (queryStr) {
      var query = {};

      queryStr.replace(/\b([^&=]*)=([^&=]*)\b/g, function (m, a, d) {
        if (typeof query[a] != 'undefined') {
          query[a] += ',' + decodeURIComponent((d + '').replace(/\+/g, '%20'));
        } else {
          query[a] = decodeURIComponent((d + '').replace(/\+/g, '%20'));
        }
      });

      return query;
    },
    beep:function (src) {
      var $src = $();
      if ('string' == typeof src)
        $src = document.createElement('source').setAttribute('src', src);
      else
        for (var i in src) {
          var tmpSrc = document.createElement('source');
          tmpSrc.setAttribute('src', src[i]);
          $src.push(tmpSrc);
        }
      try {
        $(document.createElement('audio')).append($src)[0].play();
      } catch (e) {

      }

    },
    detectFlashPlayer:function (alertresult) {
      var hasFlash = false;
      try {
        var fo = new ActiveXObject('ShockwaveFlash.ShockwaveFlash');
        if (fo) hasFlash = true;
      } catch (e) {
        if (navigator.mimeTypes["application/x-shockwave-flash"] && navigator.mimeTypes["application/x-shockwave-flash"].enabledPlugin) hasFlash = true;
      }
      if (alertresult) {
        if (hasFlash)
          alert('Flashplayer is installed on the browser');
        else
          alert('Flash is not installed or not support');
      }
      return hasFlash;
    },
    getUrlParam:function (arg1, arg2) {
      var href;
      var name = null;

      if ($.type(arg2) == 'string' && $.type(arg1) == 'string') {
        href = arg1;
        name = arg2;
      }
      else if ($.type(arg1) == 'string') {
        href = core.location.href;
        name = arg1;
      } else if (!arguments || arguments.length == 0) {
        // todo href = location.href;
        return;
      }

      var results = new RegExp('[\\?&]' + name + '=([^&#]*)').exec(href);
      return results[1] || 0;
    },
    showErrorPopup:function (error, response) {
      alert(response);
    },
    isExternalUrl:function (url) {
      return url && url.search(core.baseUrl) !== 0 && url.search(core.location.protocol + "//" + core.location.hostname) != 0 && url.search('javascript') == -1;
    },
    getArrayValues:function (arr) {
      var tmp_arr = new Array(), cnt = 0;
      for (var key in arr) {
        tmp_arr[cnt] = arr[key];
        cnt++;
      }
      return tmp_arr;
    },
    loadScript:function (src, onload) {
      var $head = document.getElementsByTagName('head')[0];
      var $script = document.createElement('script');
      $script.type = 'text/javascript';
      if ('function' == typeof onload) {
        $script.onreadystatechange = function () {
          if (this.readyState == 'complete') onload();
        }
        $script.onload = onload;
      }
      $script.src = src;
      $head.appendChild($script);
    },
    getErrorEmptyPage:function () {
      return $(document.getElementById('empty-page-error'));
    },
    getErrorBadResponsePage:function (response, jQPageData) {
      response = response + '';
      response = response.split('script').join('abcdf').split('<a ').join('<p ').split('</a>').join('</p>');

      response = $('<div>' + response + '</div>');
      response.find('abcdf').remove();

      var page = window.document.getElementById('bad-response-error');
      page.querySelectorAll('.retry')[0].setAttribute('href', jQPageData.dataUrl);
//      console.log(response);
      page.querySelectorAll('.response-body')[0].innerHTML = response.html();
      return $(page);
    },
    isTablet:function () {
      return core._isTablet;
    },
    boardIn:function ($fromPage) {
      var board = document.getElementById('core_board_index_rewrite');
      if (this.isTablet() && board) {
        board.setAttribute('board', true);
//        $board.data('iscroll').disable();
        $fromPage[0].setAttribute('board', 'in');
        if(core.device.platform.isAndroid())
          $fromPage[0].style.position = 'absolute';
      }
    },
    getUrlParams:function (url) {
      if ('string' == typeof url) {
        var a = url.split('&');
        var o = {};
        a[0] = a[0].split('?').reverse()[0];
        for (var i = 0; i < a.length; i++) {
          var r = a[i].split('=');
          o[r[0]] = r[1];
        }
        return o;
      }
      return {};
    },
    isDesktopBrowser:function () {
      var platform = navigator.platform;
      return platform == 'Win32' || platform == 'Win64' || platform == 'MacIntel';
    },
    setIconBadgeNumber:function (name, num, bgcolor) {

      bgcolor = bgcolor ? bgcolor : 'red';
      var selector = ['.component-dashboard a.core_dashboard_', name, ' .ui-li-icon'].join('');
      if (!core.helper.isTablet() && name == 'updates')
        selector = [' .apptouch-dashboard .ui-icon:after', selector].join();
      if (!document.querySelector(selector))
        return;
      var selectorStyle = ['#core_dashboard_', name, '_badge_css'].join('');
      var css = document.querySelector(selectorStyle);
      if (!css) {
        css = document.createElement('style');
        css.setAttribute('id', selectorStyle.substr(1));
        css.setAttribute('class', 'badge_css');
      } else {
        $(css).remove();
      }
      var cssText = '';
      if (num) {
        cssText = [selector, ':after {',
          'background-color: ', bgcolor, ';',
          'content: \'', num, '\';',
          '}'].join('');
      }
      css.innerHTML = cssText;
      document.body.appendChild(css);
    },
    getImageDimensions:function (path, callback) {
      var dim;
      if (dim = localStorage.getItem(path)) {
        callback(JSON.parse(dim))
      } else {
        var img = new Image();
        img.onload = function () {
          var dim = {
            width:this.width,
            height:this.height
          };
          localStorage.setItem(this.src, JSON.stringify(dim));
          callback(dim);
        };
        img.src = path;
      }
    },
    preventZoom:function () {
      if (!this.viewPort)
        this.viewPort = $('meta[name=viewport]');
      this.viewPort.attr('content', 'width=device-width; initial-scale=1.0; maximum-scale=1.0; minimum-scale=1.0; user-scalable=0;');
    }
  }
};

var renderer = {
  construct:function (data) {
    var $d = $(document);
    $d.data('loaded', true);
    this.layout.construct(this);
    this._bindEvents();
    $d.trigger('appready', data);
    if (data) {
      Initializer.init(data);
      var $page = this.layout.render(data);
      console.log([(new Date().getTime() - window._R_startTime), 'ms'].join(''));
      if ($page)
        $.mobile.changePage($page, {reloadPage:true});
    }
  },

  _bindEvents:function () {
    var $doc = $(document);
    $doc.bind("pagebeforeload", function (event, jQPageData) {
      var $d = $(document);
      event.preventDefault();
      if ($d.data('loaded')) {
        $d.data('loaded', false);
        renderer.process(jQPageData.dataUrl, jQPageData);
      } else {
        jQPageData.deferred.reject(jQPageData.absUrl, jQPageData.options);
      }
    });

    $doc.bind("pagebeforechange", function (e, jQPageData) {
      console.log(jQPageData);
//    Hide Modal container {
//      Wall.events._hideModalContainer();
//    } Hide Modal container
      // Hide PhotoSwipe {
      $(this.querySelectorAll('.ps-toolbar-close')).trigger('vclick');
      // } Hide PhotoSwipe
      $.mobile.hidePageLoadingMsg();
      var opt = jQPageData.options;
      var $fromPage = opt.fromPage;
      if (!$fromPage)
        return;
      var coreHelper = core.helper;
      var $tp = jQPageData.toPage;
      if (coreHelper.isTablet() && 'object' == typeof $tp) {
        var fpId = $fromPage.attr('id');
        if (!$tp.is('#core_board_index_rewrite')) {
          $('[board="in"]').removeAttr('board');
          if (fpId == 'core_board_index_rewrite') {
            $fromPage[0].setAttribute('board', 'mini');
          }
        } else if (
          fpId != 'core_board_index_rewrite' &&
          fpId != 'initial_page'
        ) {
          core.helper.boardIn($fromPage);
        }
      }

      var fpId = $fromPage[0].getAttribute('id');
      var rh = renderer.helper;
      if (opt.reloadPage) {
        var $pages = $(document.body).children('.ui-page').not('#' + fpId);
        $doc.data('clearCache', 1);
        rh.removeElements($pages);
      } else if (opt.type == 'post' && opt.data) {
        var $pages = $(document.body).children(['module', $fromPage[0].getAttribute('module')].join('-')).not('#' + fpId);
        rh.removeElements($pages);
      }

      if( typeof $tp == 'object' )
        $tp.data('firstTime', 0);
    });
//    this.helper.bindSwipes();

    window.addEventListener('scroll', function (e) {
      if( !$.mobile.activePage.hasClass('auto-scroll') )
        return;

      if( !$.mobile.activePage.data('firstTime') ) {
        $.mobile.activePage.data('firstTime', 1);
        return;
      }

      var c = $.mobile.activePage[0].scrollHeight - (window.scrollY + $(window).height());
      if (c > 100 || window.autoscrolling) {
        return;
      }
      $.mobile.activePage.find('.listPagination').trigger('vclick');
    });
  },

  process:function (page_url, jQPageData) {
    if (!this.helper.isUrlValid(page_url)) {
      this.observePage(jQPageData);
      console.log('url is not valid');
      return;
    }

    var options = jQPageData.options;
    if (options.showLoadMsg)
      $.mobile.showPageLoadingMsg();
    var $page = false;

    if (options && options.data) {
      var post_data = options.data + '&format=json';
    } else {
      var post_data = {'format':'json'};
    }
    $.ajax({
      type:options.type,
      url:page_url,
      data:post_data,

      success:function (response) {
        var jqpdl = jQPageData;
        renderer.dataSuccess(response, jqpdl);
        $(document).trigger('pagedatasuccess', {responseString:response, jQPageData:jqpdl});
      },

      error:function (response) {
        var jqpdl = jQPageData;
        var options = jqpdl.options;
        var rendererL = renderer;
        if (window.phonegap && phonegap.offlineManager.isOffline()) {
          var data = phonegap.offlineManager.getPageData(jqpdl.dataUrl);
          if (data) {
            rendererL.dataSuccess(data, jqpdl);
            return;
          }
        }
        if (!response.responseText && options.type.toLowerCase() == 'post') {
          $(document).data('loaded', true);
          $.mobile.hidePageLoadingMsg();
          jqpdl.deferred.reject(jqpdl.absUrl, options);
          return;
        }
        try {
          response = $.parseJSON(response.responseText);
          $page = rendererL.layout.render(response, jqpdl);
          rendererL.observePage(jqpdl, $page);
        } catch (e) {
          console.log(e);
          $(document).data('loaded', true);
          rendererL.observePage(jqpdl, core.helper.getErrorBadResponsePage(response, jqpdl));
          jqpdl.deferred.resolve(jqpdl.absUrl, options, '');
        }
      },

      statusCode:{
        404:function (response) {
//            todo
          response = $.parseJSON(response.responseText);
          $(document).data('loaded', true);
          UIComponent.helper.showMessage(response.error, 'e');
          jQPageData.deferred.reject(jQPageData.absUrl, jQPageData.options);
        },

        403:function (response) {
//            todo
          response = $.parseJSON(response.responseText);
          $(document).data('loaded', true);
          UIComponent.helper.showMessage(response.error, 'e');
          jQPageData.deferred.reject(jQPageData.absUrl, jQPageData.options);
        }
      },
      dataType:'html'
    });
  },

  dataSuccess:function (responseString, jQPageData) {
    var rendererL = renderer;
    try {
      var response = $.parseJSON(responseString);
      if (response.domCache == 'clearAll')
        $(document).data('clearCache', 1);
      // Rendering response data
      var $page = rendererL.layout.render(response, jQPageData);
      // console.log([jQPageData.dataUrl, " ", (new Date().getTime() - window._R_startTime), 'ms'].join(''));
      rendererL.observePage(jQPageData, $page);
      // Rendering response data
    } catch (e) {
      $(document).data('loaded', true);
      rendererL.observePage(jQPageData, core.helper.getErrorBadResponsePage(responseString, jQPageData));
      jQPageData.deferred.resolve(jQPageData.absUrl, jQPageData.options, '');
    }
  },

  observePage:function (jQPageData, $page) {
    $(document).data('loaded', true);
    if (jQPageData && jQPageData.deferred) {
      if ($page) {
        jQPageData.deferred.resolve(jQPageData.absUrl, jQPageData.options, $page);
        if ($(document.body).children('.ui-page').length > 2) {
          var $initialPage = $(document.body).children('#initial_page');
          if ($initialPage[0])
            this.helper.removeElements($initialPage);
        }
      } else {
        jQPageData.deferred.reject(jQPageData.absUrl, jQPageData.options);
      }
    }
  },
  /*************************************************************************************
   *
   *  LAYOUT {
   *
   * ***********************************************************************************/
  layout:{

    page:null,
    jQPageData:null,
    renderer:null,
    $pageBase:null,
    layout:null,
    generalTemplates:null,
    componentTemplates:null,


    construct:function (renderer) {
      this.renderer = renderer;
      this._setTemplates();
      UIComponent.construct(this);
    },

    render:function (response, jQPageData) {
      window._R_startTime = new Date().getTime();
      var minWait = 1500;
      if (response != undefined) {
        var wait = 0;
        if (response.message) {
          if ($.type(response.message) === 'array') {
            wait = minWait * response.message.length;
            for (var i = 0; i < response.message.length; i++) {
              setTimeout(function () {
                $.mobile.showPageLoadingMsg('a', $(document.createElement('p')).html(response.message[i]).text(), true);
              }, minWait * i);
            }
          } else {
            wait = minWait;
            $.mobile.showPageLoadingMsg('a', $(document.createElement('p')).html(response.message).text(), true);
          }
        }
        this.helper.checkSession(response);
        if (response.redirect_url) {
          setTimeout(function () {
            var poptions = {reloadPage:true};
            if (response.redirect_url == 'refresh' || response.redirect_url == 'parentRefresh') {
              if (UIComponent.helper.$getActivePage().jqmData('role') == 'dialog') {
                response.redirect_url = core.location.pathname;
              } else {
                core.cache.clear();
                var backurl = jQPageData.options.fromPage.data('url');
                if ('string' == typeof backurl) {
                  $.mobile.changePage(backurl, {reloadPage:true});
                } else {
                  window.history.back();
                }
                return;
              }
            } else {
              if (core.helper.isExternalUrl(response.redirect_url)) {
                $.mobile.hidePageLoadingMsg();
                if (!window.open(response.redirect_url))
                  alert(core.lang.get('APPTOUCH_Please enable pop-up windows for this site and try again.'));
                return;
              }
            }
            $.mobile.changePage(response.redirect_url, poptions);
          }, wait);
          return false;
        }
        if (response.error) {
          response.page = this.helper._404PageData(jQPageData, response);
        }
      }

      var page = response.page;


      var options = {};
      if (jQPageData)
        options = jQPageData.options;
      var pageType = options.role ? options.role : 'page';

      UIComponent.refreshPageData(page, jQPageData);
      this.page = page;
      this.jQPageData = jQPageData;

      if (page && page.layout)
        this.layout = page.layout;
      else
        this.layout = false;
      if (this.layout) {
        if (page.info.lang)
          core.lang.add(page.info.lang);
        this.$pageBase = this.$createBase(pageType, this.helper.preparePageOptions(page), options && options.role && options.role == 'dialog');
        if (pageType == "page") {
          var header = this.$pageBase[0].querySelector('.ui-header');
          var fpid = options.fromPage ? options.fromPage.attr('id') : '';
          var isApp = core.device.platform.isPhoneGap();
          if (isApp && fpid && (
            fpid != 'core_board_index_rewrite' &&
            !options.reloadPage &&
            fpid != 'user_auth_login_rewrite' &&
            fpid.split('user_signup_index_rewrite').length == 1 &&
            fpid != 'initial_page'
            )) {
            $(header.querySelector('.apptouch-dashboard')).remove();
          } else {
            $(header.querySelector('.main-back-btn')).remove();
          }
        }
        var header = this.$pageBase[0].querySelector('.ui-header');

        this.$pageBase.data('responseData', response);
        this.$pageBase.data('jQPageData', jQPageData);
        try {
          for (var key in this.layout) {
            var $parentContent = this.$pageBase.children("div:jqmData(role='" + key + "')");
            if ($parentContent.length > 0)
              for (var order in this.layout[key]) {
                var component = this.$renderComponent(this.layout[key][order]);
                if (component)
                  $parentContent[0].appendChild(component[0]);
              }
          }
        } catch (e) {
          console.log(e);
          return false;
        }
        if (!jQPageData) {
          $.mobile.hidePageLoadingMsg();
          $.mobile.changePage(this.$pageBase, options);
        }
        return this.$pageBase;
      } else if (response.error) {
        this.$pageBase = this.$createBase(pageType, {
          'id':'error_' + response.error_code,
          'data-title':response.error_code,
          'data-theme':'e',
          'data-url':jQPageData && jQPageData.url ? jQPageData.url : (jQPageData && jQPageData.toPage ? jQPageData.toPage : response.error_code)
        }, options && options.role && options.role == 'dialog');
        var $header = this.$pageBase.children("[data-role='header']");
        $header[0].querySelectorAll('.page-title')[0].innerHTML = response.error_code;
        var $content = this.$pageBase.children("[data-role='content']");
        $content[0].innerHTML = '<div data-theme="e">' + response.error + '</div>';

//        $.mobile.hidePageLoadingMsg();
        if (!jQPageData) {
          $.mobile.hidePageLoadingMsg();
          $.mobile.changePage(this.$pageBase, options);
        }
      } else {
        return core.helper.getErrorEmptyPage();
      }
      return this.$pageBase;
    },

    $renderComponent:function (component) {
      if (component.name) {
        console.log('Rendering: ' + component.name);
        return UIComponent[component.name](component.params, this.$getComponentTemplate(component));
//      } else {
//        console.warn('Invalid Component: ');
      }
    },

    $getComponentTemplate:function (component) {

      var templateName = 'component-' + component.name;
      var tabletTemplate = this.componentTemplates.querySelector('#tablet-' + templateName);
      var clone;
      if (tabletTemplate) {
        clone = tabletTemplate.querySelectorAll('.' + templateName)[0].cloneNode(true);
        clone.className += ' page-component';
        return $(clone);
      }
      clone = this.componentTemplates.querySelector('#' + templateName).querySelectorAll('.' + templateName)[0].cloneNode(true);
      clone.className += ' page-component';
      return $(clone);
    },

    getGeneralTemplate:function (name) {
      var templateName = 'template-' + name;
      var tpl = this.generalTemplates.querySelector('#' + templateName).querySelectorAll('.' + templateName)[0];
      tpl.setAttribute('data-create-time', new Date().getTime());
      return $(tpl);
    },

    $createBase:function (type, dataAttrs, emptyPage) {
      dataAttrs = !dataAttrs ? {} : dataAttrs;
      var page = this.getGeneralTemplate(type)[0].cloneNode(true);
      if (this.page) {
        if (this.page.info.contentTheme) {
          page.querySelectorAll('.ui-content')[0].setAttribute('data-theme', this.page.info.contentTheme);
        }
        if (this.page.info.headerTheme) {
          page.querySelectorAll('.ui-header')[0].setAttribute('data-theme', this.page.info.headerTheme);
        }
        if (this.page.info.footerTheme) {
          page.querySelectorAll('.ui-footer')[0].setAttribute('data-theme', this.page.info.footerTheme);
        }
        if (this.page.info.params) {
          page.className += (' ' + ['module', this.page.info.params.module].join('-'));
          page.setAttribute('module', this.page.info.params.module);
        }
      }

      var header = page.querySelectorAll(".ui-header")[0];
      header.querySelectorAll('.page-title')[0].innerHTML = dataAttrs['data-title'] ? dataAttrs['data-title'] : '';
      if (emptyPage) {
        $(header.querySelectorAll('.apptouch_dashboard_icon')).remove();
        $(header.querySelector('#apptouch_quick_icon')).remove();
      }
      if (!dataAttrs['data-url'] && dataAttrs.id) {
        page.setAttribute('data-url', (dataAttrs.href ? dataAttrs.href : '') + '#' + dataAttrs.id);
      }
      dataAttrs['class'] = [page.getAttribute('class'), (dataAttrs['class'] ? dataAttrs['class'] : '')].join(' ');


      document.body.appendChild(page);
      return $(page).attr(dataAttrs);
    },

    _setTemplates:function () {
      this.generalTemplates = window.document.getElementById('general-templates');
      this.componentTemplates = window.document.getElementById('component-templates');
    },

    helper:{
      preparePageOptions:function (page) {
        if (!page.info.attrs) {
          page.info.attrs = {};
        }
        var options = page.info.attrs;
        options['id'] = page.info.key;
        options['data-title'] = page.info.title;
        options['data-url'] = page.info.url;
        return options;
      },

      _404PageData:function (jQPageData, response) {

        var message = response.message ? response.message : response.error;
        var tip = {
          'name':'tip',
          params:{
            'title':core.lang.get('Page Not Found'),
            'message':message,
            'attrs':{
              'data-theme':'e',
              'data-content-theme':'e'
            }
          }
        };
        var page = response.page;
        if (page) {
          if (page.layout) {
            var c = page.layout.content;
            c.reverse().push(tip);
            c.reverse();
          }
          return page;
        } else
          return {
            'info':{
              'title':core.lang.get('Page Not Found'),
              'key':'page_not_found',
              'url':jQPageData && jQPageData.url ? jQPageData.url : (jQPageData && jQPageData.toPage ? jQPageData.toPage : response.error_code)
            },
            'layout':{
              'content':[tip]
            }
          }
      },

      checkSession:function (response) {
        if (response.userSession) {

          if (response.userSession == 'start') {
            $(document).trigger('usersessionstart', response);
          } else if (response.userSession == 'close') {
            $(document).trigger('usersessionclose', response);
          }
        }
      }
    }
  },


  helper:{
    isUrlValid:function (page_url) {
      var u = $.mobile.path.parseUrl(page_url);
      var filename = u.filename;
      var ext = (filename ? filename.split('.').pop() : '').split('?')[0].toLowerCase();
      return  (ext &&
      ext.length > 4) ||
      !(ext == 'jpg' ||
      ext == 'png' ||
      ext == 'jpeg' ||
      ext == 'gif' ||
      ext == 'ico' ||
      ext == 'bmp');
    },
    bindSwipes:function () {
      var checkForAllow = function (event) {
        return $(event.target).closest('.component-tabs').length == 0;
      };
//        forward or next Page
      $(document.body).delegate('.ui-page-active', 'swipeleft', function (e) {
        var allow = checkForAllow(e);
        if (allow) {
          var $nextBtn = $(this.querySelector('.component-paginator .paginator-next a'));
          if ($nextBtn.length)
            $nextBtn.trigger('vclick');
          else {
            history.forward();
          }
        }
      });

//        back
      $(document.body).delegate('.ui-page-active', 'swiperight', function (e) {
        var allow = checkForAllow(e);
        if (allow) {
          var $prevBtn = $(this.querySelector('.component-paginator .paginator-prev a'));
          if ($prevBtn.length)
            $prevBtn.trigger('vclick');
          else {
            history.back();
          }
        }
      });
    },
    removeElements:function ($elements) {
      if ($elements.length && $elements[0].parentNode) {
        var parent = $elements[0].parentNode;

        for (var i = 0; i < $elements.length; i++) {
          parent.removeChild($elements[i]);
          delete $elements[i];
        }
      }
    }
  }
}