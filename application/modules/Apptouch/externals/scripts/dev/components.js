UIComponent = {
  pageResponseData: null,
  jQPageData: null,
  layout: null,

  construct: function (layout) {
    this.layout = layout;
    this.helper.parent = this;
  },

  refreshPageData: function (data, options) {
    this.pageResponseData = data;
    this.jQPageData = options;
  },

  /*************************************************************************************
   * COMPONENTS {
   *************************************************************************************/
  dashboard: function (params, $template) {
    var $optionMore = $template.find('.more-options').clone();
    var to = $optionMore.length > 0 && params.length > 3 ? 2 : params.length;
    $template.find('.more-options').remove();
    var $menuItemTpl = $template.find('li').clone();
    var $ul = $template.find('ul').empty();

    var isListview = $ul.attr('data-role') == 'listview';
    for (var i = 0; i < to; i++) {
      var menu_item = params[i];
      var attrs = menu_item.attrs;
      if (attrs['class'] && attrs['class'].split('custom').length == 2 && core.helper.isExternalUrl(attrs['href'])) {
        attrs['onclick'] = ["window.open('", attrs['href'], "', '_blank', 'location=no'); return false"].join('');
        delete attrs['href'];
//      } else {
//        alertLog(menu_item);
      }
      var $menu_item = $menuItemTpl.clone();
      var $a = $menu_item.find('a');
      if (isListview && menu_item.data_attrs && menu_item.data_attrs['role'] == 'list-divider') {
        $menu_item
          .attr('data-role', menu_item.data_attrs['role'])
          .html(menu_item.label);
        $a.remove();
      } else {
        $a.attr(menu_item.attrs)
          .html(menu_item.label)
          .attr(menu_item.data_attrs);
        if (menu_item.active) {
          $a.addClass('ui-btn-active');
        }
        if (menu_item.data_attrs) {
          var iconDiv = document.createElement('div');
          iconDiv.setAttribute('class', 'ui-li-icon ui-li-thumb ui-icon-' + menu_item.data_attrs['data-icon']);
          $a.prepend(iconDiv);
        }
      }

      $ul.append($menu_item);
    }
    if (params.length <= 3)
      $optionMore.remove();
    else {
      $optionMore.find('a').attr({'href': this.helper.createPopupMenu(params, $optionMore.text(), 2)});
      $ul.append($optionMore);
    }
    var $dashboard = $template;
    var dashboard = $dashboard[0];
    var phth = dashboard.querySelector('.user-photo-thumb .ui-li-thumb');

    if (phth)
      $(phth).css('background-image', ['url(', this.pageResponseData.info.viewer.photo.icon, ')'].join(''));
    return $dashboard;
  },

  adCampaign: function (params, $template) {
    var ad = params.ad;
    var play = function (ad, $adv) {
      $adv.css('opacity', 1)
      var anim_types = ['slide', 'pop', 'fade'];
      var delay;
      var duration;
      var anim_type;
      delay = ad.anim_delay + 1;
      duration = ad.anim_duration;
      anim_type = anim_types[ad.anim_type];
      if (anim_type == 'slide') {
        var dir = $adv.hasClass('adv-bottom') ? '+' : '-';
        $adv.css({
          'transform': 'translate(0, ' + dir + $adv[0].offsetHeight + 'px)',
          '-webkit-transform': 'translate(0,' + dir + $adv[0].offsetHeight + 'px)',
          'transition-property': 'transform',
          '-webkit-transition-property': 'transform'
        });
      }
      if (anim_type == 'pop') {
        $adv.css({
          'transform': 'scale(0)',
          '-webkit-transform': 'scale(0)',
          'transition-property': 'transform',
          '-webkit-transition-property': 'transform'
        });
      }
      if (anim_type == 'fade') {
        $adv.css({
          'opacity': 0,
          'transition-property': 'opacity',
          '-webkit-transition-property': 'opacity'
        });
      }
      setTimeout(function () {
        $adv.css({
          'transition-delay': delay + 's',
          'transition-duration': duration + 's',
          '-webkit-transition-delay': delay + 's',
          '-webkit-transition-duration': duration + 's'
        });
      }, 100);
      setTimeout(function () {
        $adv.addClass('start-animation');
      }, 200);
      setTimeout(function () {
        $adv.attr('style', '');
      }, 1000 * (delay + duration + 1));
    };
    $template.bind('vclick', function (e) {
      $.ajax({
        url: params.url,
        'data': {
          'format': 'json',
          'adcampaign_id': params.campaign_id,
          'ad_id': ad.ad_id
        }
      });
    });
    $template.find('.close-btn').bind('vclick', function (e) {
      $(this.parentNode).hide();
    });
    $template.addClass(ad.position ? 'adv-bottom' : 'adv-top');
    if (ad.fixed) {
      $template.addClass("adv-fixed");
      $template.append($(params.ad.html_code));
      $template.css('opacity', 0);
      setTimeout(function () {
        play(ad, $template);
      }, 1000);
    } else {
      $template.html(params.ad.html_code);
    }
    return $template;
  },

  footerMenu: function (params, $template) {
    return this.navigation(params, $template);
  },

  quickLinks: function (params, $template) {
    var $link = $(document.createElement('a')).attr({
      href: "javascript://",
      'data-role': "button",
      id: "apptouch_quick_icon",
      'data-icon': "plus",
      'data-rel': "dialog",
      'data-iconpos': "notext",
      'data-theme': "a"
    });
    if (params.linkId && typeof params.linkId == 'string') {
      $link = $('#' + params.linkId);
    }
    if (params.menu.length == 0) {
      return;
    } else if (params.menu.length == 1) {
      $link.attr(params.menu[0].attrs);
//        .removeAttr('data-iconpos')
      $link.removeAttr('data-rel');
      $link.attr(params.menu[0].data_attrs);
      $link.html(params.menu[0].label);
      return $link;
    }
    var menu_id = ['apptouch_local_quick_menu_', this.pageResponseData.info.key].join('');
    var menu_id_parts = menu_id.split('_');
    if (parseInt(menu_id_parts[menu_id_parts.length - 1]) == menu_id_parts[menu_id_parts.length - 1]) {
      menu_id = menu_id_parts.join('_');
    }

    $link.attr('data-icon', 'options');
    $link.attr('href', '#' + menu_id);

    var menu_url = this.pageResponseData.info.url;

    var $page = this.layout.$createBase('dialog', {'id': menu_id, 'title': params.title, 'url': menu_url}, true);
    var $content = $page.children("div:jqmData(role='content')");
    var $menu_item_tpl = $template.find('li').clone();
    var $nav_box = $template.empty();
    for (var key in params.menu) {
      var menu_item = params.menu[key];
      var $menu_item = $menu_item_tpl.clone();
      var $a = $menu_item.find('a');
      $a.attr(menu_item.attrs)
        .html(menu_item.label)
        .attr(menu_item.data_attrs);
      if (menu_item.active) {
        $a.addClass('ui-btn-active');
      }

      $nav_box.append($menu_item);
    }
//      this.menus.push(menu_id);
    $content.append($nav_box);
    return $link;
  },

  navigation: function (params, $template) {

    var $optionMore = $template.find('.more-options').clone();
    var to = $optionMore.length > 0 && params.length > 3 ? 2 : params.length;
    $template.find('.more-options').remove();
    var $menuItemTpl = $template.find('li').clone();
    var $ul = $template.find('ul').empty();

    var isListview = $ul.attr('data-role') == 'listview';
    for (var i = 0; i < to; i++) {
      var menu_item = params[i];
      var attrs = menu_item.attrs;
      if (attrs['class'] && attrs['class'].split('custom').length == 2 && core.helper.isExternalUrl(attrs['href'])) {
        attrs['onclick'] = ["window.open('", attrs['href'], "', '_blank', 'location=no'); return false"].join('');
        delete attrs['href'];
//      } else {
//        alertLog(menu_item);
      }
      var $menu_item = $menuItemTpl.clone();
      var $a = $menu_item.find('a');
      if (isListview && menu_item.data_attrs && menu_item.data_attrs['role'] == 'list-divider') {
        $menu_item
          .attr('data-role', menu_item.data_attrs['role'])
          .html(menu_item.label);
        $a.remove();
      } else {
        $a.attr(menu_item.attrs)
          .html(menu_item.label)
          .attr(menu_item.data_attrs);
        if (menu_item.active) {
          $a.addClass('ui-btn-active');
        }
      }

      $ul.append($menu_item);
    }
    if (params.length <= 3)
      $optionMore.remove();
    else {
      $optionMore.find('a').attr({'href': this.helper.createPopupMenu(params, $optionMore.text(), 2)});
      $ul.append($optionMore);
    }

    return $template;
  },

  subjectPhoto: function (params, $template) {

    var subject;
    if (params) {
      subject = params;
    } else if (this.pageResponseData.info.subject) {
      subject = this.pageResponseData.info.subject;
    }

    if (subject) {
      if (subject.photo.profile) {
        $template.find('img').attr('src', subject.photo.profile);
      }
      $template.find('.subject_title').html(subject.title);
    }

    return $template;
  },

  form: function (params, $template) {
    var $form = $(params);
    //multiple select
    if (core.helper.isDesktopBrowser())
      $($form[0].querySelectorAll('select[multiple=multiple]')).attr('data-native-menu', false);

    var $fileInputs = $form.find('input:file');

    for (var i = 0; i < $fileInputs.length; i++) {
      var input = $($fileInputs[i]);
      if (!input.attr('accept')) {
        input.attr('accept', 'image/*');
      }
    }

    var $cancel = $form.find('#cancel');
    var $submit = $form.find('*[type="submit"]');
    if ($cancel[0]) {
      $cancel.removeAttr('onclick');
      $cancel.attr('data-rel', 'back');
    }
    if ($submit[0]) {
      $submit.bind('vclick', function (e) {
        var $this = $(this);
        if ($this.data('submitted')) {
          e.preventDefault();
          $this.unbind();
          return;
        }
        $this.data('submitted', true);
      });
    }
    $form.find('.form-options-wrapper').attr({'data-role': 'listview', 'data-inset': 'true'});
//    $form.find('select').attr('data-native-menu', 'false');
    var $calendars = $form.find('input.calendar');
    $calendars.each(function () {
      var $this = $(this);
      var $calendarElement = $this.closest('.form-element');
      var $selects = $calendarElement.addClass('calendar-element').find('select');

      $calendarElement.append($('<div />').attr({'data-role': 'fieldcontain'}).append($('<fieldset />').attr({
        'data-role': 'controlgroup',
        'data-type': 'horizontal'
      }).append($selects)));
    });
    $form.find('ul.form-notices').attr({
      'data-role': 'listview',
      'data-inset': true,
      'data-theme': 'b'
    });

    var $formErrors = $form.find('ul.form-errors');
    $formErrors.attr({
      'data-role': 'listview',
      'data-inset': true,
      'data-theme': 'e'
    }).children('li').each(function () {
      var $this = $(this);
      $this.each(function () {
        var $li = $(this);
        $li.find('li').each(function () {
          $li.append($('<br/>'))
            .append($('<p/>').html($(this).text()));
        });
        $li.find('ul').remove();
      });
    });

    $form.find('.calendar_output_span').remove();
    for (var order = 0; order < $calendars.length; order++) {
//      $calendars[order].type = 'text';
      $($calendars[order]).attr('data-type', 'date');
    }

    if (core.device.platform.isPhoneGap() || (core.device.platform.isIOS() && core.device.platform.getIOSVersion() < 6.0))
      $form.attr('data-ajax', true);
    else if ($fileInputs.length)
      $form.attr('data-ajax', false);

    if ($fileInputs.length > 0) {

      // Removing format json
      $form.attr('action', $form.attr('action')
          .replace('&format=json', '')
          .replace('?format=json&', '?')
          .replace('?format=json', '')
      );
      if (core.device.getGrade() == 'a')
        var multiUpload = function ($fileInput, isPicup, data) {
          var inputName = $fileInput.attr('name').replace('[]', '');
          var $fileList = $fileInput.closest('form').find('#' + inputName + 'fileList');
          if ($fileList.length == 0) {
            $fileList = $template.find('.fileList').clone();
            $fileList.attr('id', inputName + 'fileList');
            if (!isPicup)
              $fileList.insertAfter($('.ui-page-active').find('form #' + inputName + 'FakeButton'));
            else
              $fileList.insertAfter($fileInput.closest('.ui-btn'));
          }
          var $divider = isPicup ? $fileList.find('.picupUpload') : $fileList.find('.normalUpload');
          $divider.show();
          $fileList.show();
          var fileCount = $fileList.find('li.file').length;
          var $file = $fileList.find(fileCount == 0 ? 'li.fileTplLast' : 'li.fileTpl').clone().show();
          $divider.find('span.ui-li-count').html(fileCount + 1);
          $file.removeClass('fileTpl');

          $file.addClass('file');

          $file.insertAfter($divider);

          var $fileInputClone = $(document.createElement('input'));
          $fileInputClone.attr($fileInput.attr());
          var attrs = {};
          var attributes = $fileInput[0].attributes;
          var l = attributes.length;
          for (var attr, i = 0; i < l; i++) {
            attr = attributes.item(i);
            attrs[attr.nodeName] = attr.nodeValue;
          }
          $fileInputClone.attr(attrs);
          if (!isPicup) {
            $fileInputClone.removeAttr('value');
            $fileInputClone.attr('id', inputName + '-' + (fileCount + 1));
            $fileInputClone.insertAfter($fileInput);
            $fileInput.unbind('change').bind('change', function () {
              var $this = $(this);
              var val = $this.attr('value');
              if (val)
                $this.closest('.file').find('.value').html(val);
            });
            $file.find('a.value .ui-btn-text').append($fileInput);
            var val = $fileInput.attr('value');
            if (val)
              $file.find('.value').html(val);
          } else {
            $file.find('a.value').html(data.photo.input.filename);
            $file.find('a.delete').data('photoValue', data.photo.input.value);
          }
          //          var dialogId = inputName + (fileCount + 1) + 'Dialog';
          $file.find('.delete').unbind().bind('vclick', function () {
            var $this = $(this);
            if ($fileList.find('.file').length == 1) {
              $this.closest('li').closest('ul').hide();
            } else {
              $fileList.find('.normalUpload').find('.ui-li-count').html($fileList.find('.file').length - 1);
            }
            if (isPicup) {
              $this.closest('form').data('picup').removeFiles(inputName, $this.data('photoValue'));
            }
            $this.closest('li').remove();

          });
          if (!isPicup)
            $fileInputClone.bind('change', function () {
              var $this = $(this);
              multiUpload($this);
            });
        }
      if (core.device.platform.isIOS() && core.device.platform.getIOSVersion() < 6.0) {
        new core.Picup($form);
        $form.bind('picupuploadsuccess', function (e, data) {
          UIComponent.helper.showMessage(core.lang.get('APPTOUCH_Upload Success'));
        });
        $form.bind('picupuploadfailed', function (e, data) {
          UIComponent.helper.showMessage(core.lang.get('APPTOUCH_Upload Failed'), 'e');
        });
        $form.unbind('picupafterupload').bind('picupafterupload', function (e, data) {
          var response = data.response;
          var $this = data.target;
          if ($this.attr('name').indexOf('[]') > -1)
            for (var i = 0; i < response.length; i++) {
              var photo = response[i];
              multiUpload($this, true, {'index': i, 'photo': photo});
            }

          else {
            if (response.length > 1) {
              alert('You have uploaded ' + response.length + " photos instead of 1. Last photo will accept"); //todo lang
              $this.val(
                //                $.mobile.path.parseUrl(data.response.input.value).filename
                response[response.length - 1].input.filename
              );
            } else {
              $this.val(
                //                $.mobile.path.parseUrl(data.response.input.value).filename
                data.response[0].input.filename
              );
            }
            $this.button().button('refresh');
          }

        });
      } else {

        for (var i = 0; i < $fileInputs.length; i++) {
          var $fileInput = $($fileInputs[i]);
          var inputName = $fileInput.attr('name').replace('[]', '');
          var $fakeButton = $('<span data-role="button" data-corners="true" data-shadow="true" data-iconshadow="true" data-wrapperels="span" data-theme="c" class="ui-btn ui-shadow ui-btn-corner-all ui-btn-up-c"><span class="ui-btn-inner ui-btn-corner-all"><span class="ui-btn-text"></span></span></span>');
          $fakeButton.attr({
            'id': inputName + 'FakeButton'
          });
          var $btnLabel = $form.find('label[for="' + inputName + '"]');
          $fakeButton.find('span.ui-btn-text').html($btnLabel.text());
          $btnLabel.remove();
          $fakeButton.insertBefore($fileInput);
          $fileInput.insertAfter($fakeButton.find('.ui-btn-inner'));
          $fileInput.bind('change', function () {
            var $this = $(this);
            var name = $this.attr('name');
            if (name.indexOf('[]') > -1)
              multiUpload($this);
            else {
              $($fileInput[0].form).find(['#', name, 'FakeButton .ui-btn-text'].join('')).html($this.attr('value'));
            }
          });
          $fakeButton.unbind();


          // Styles via js
          $fakeButton.css('overflow', 'hidden');
          $fileInput.css({
            'position': 'absolute',
            'opacity': 0,
            '-moz-transform': 'scale(100)',
            '-webkit-transform': 'scale(100)',
            '-o-transform': 'scale(100)'
          });
        }
        ;
      }
    }

    $template.append($form);
    return $template;
  },

  html: function (params, $template) {
    if ($.type(params) === 'array') {
      for (var i = 0; i < params.length; i++) {
        $template.append(this.helper.$objectToHtmlEl(params[i]));
      }
    } else if ($.type(params) === 'object')
      $template.append(this.helper.$objectToHtmlEl(params));
    else if ($.type(params) === 'string')
      $template.html(params);
    this.helper.initExternalUrls($template);
//    $template.removeAttr('class');
    return $template;
  },

  inviter: function (params, $template) {
    var $itemTpl = $template.find('.list_item').clone();
    if (params.attrs) {
      $template.attr(params.attrs);
    }

    $template.find('.list_item').remove();

    for (var key in params.items) {
      var item = params.items[key];
      var $item = $itemTpl.clone();

      if (item.provider != 'facebook') {
        var $acontent = $item.find('a.content').attr('href', item.href);
      } else {
        $item.find('a.content').attr('href', 'javascript:void(0);');
        $item.bind('vclick', function () {
          FB.login(function (response) {
            if (response.authResponse) {
              FB.ui(
                item.data,
                function (response) {
                  if (!response) {
                    UIComponent.helper.showMessage(item.fail_message, 'e');
                  } else {
                    $.mobile.changePage(item.redirect_url);
                  }
                }
              );
            } else {
              console.log('User cancelled login or did not fully authorize.');
            }
          });
        });
      }
      if (item.attrsLi) {
        $item.attr(item.attrsLi);
      }
      if (item.attrsA) {
        $acontent.attr(item.attrsA);
      }
      if (item.photoUrl)
        $item.find('img').attr('src', item.photoUrl);
      else if (item.photoUrl === false)
        $item.find('img').remove();
      $template.append($item);
    }
    return $template;
  },

  inviterInvitesList: function (params, $template) {
    var $itemTpl = $template.find('li.list_item').clone();
    var $search_result = $template.find('li.search-result');
    if (params.attrs) {
      $template.attr(params.attrs);
    }
    if (params.search && params.search.keyword) {
      $search_result.find('div.keyword').html(params.search.keyword);
      if (params.search.count) {
        $search_result.find('span.ui-li-count').html(params.search.count);
        $search_result.find('b.no-result').hide();
      }
      else {
        $search_result.find('b.result').hide();
        $search_result.find('b.no-result').css('display', 'inline');
      }
    } else {
      $search_result.hide();
      $search_result.hide();
    }

    $template.find('li.list_item').remove();

    for (var key in params.items) {
      var item = params.items[key];
      var $item = $itemTpl.clone();
      $item.find('a.content').attr('href', item.href);
      if (item.attrsLi) {
        $item.attr(item.attrsLi);
      }

      if (item.attrsA) {
        $item.find('a.content').attr(item.attrsA);
      }
      if (item.photo)
        $item.find('img').attr('src', item.photo);
      else if (item.photo === false)
        $item.find('img').remove();

      if (item.delete_href) {
        $item.find('a#delete_button').attr('href', item.delete_href);
      }
//            if (item.sendnew_href) {
//                $item.find('a#sendnew_button').attr('href', item.sendnew_href);
//            }

      $item.find('.recipient').find('span').html(item.recipient);
      $item.find('.sent_date').html(item.sent_date);

      if (item.counter) {
        $item.find('.ui-li-count').html(item.counter);
      } else {
        $item.find('.ui-li-count').detach();
      }

      var $descTpl = $item.find('.ui-li-desc').clone().empty();
      if (item.descriptions) {
        for (var key in item.descriptions) {
          var desc = item.descriptions[key];
          if (key == 0) {
            $item.find('.ui-li-desc').find('strong').html(desc);
          } else {
            $descTpl.html(desc);
            $descTpl.insertBefore($item.find('.ui-li-aside'));
          }
        }
      }

      if (item.creation_date) {
        $item.find('.ui-li-aside').html(item.creation_date);
      }
      if (!item.manage || item.manage.length == 0 || !item.manage[0].label)
        $item.find('a.options').remove();
      else {
        var dialog_id = 'apptouch_local_manage_' + parseInt(Math.random() * 1000000);
        var $dialog = this.layout.$createBase('dialog', {
          'data-rel': 'dialog',
          'id': dialog_id,
          'data-url': '#' + dialog_id,
          'data-title': item.title
        }, true);

        var $nav_box = $('<ul data-role="listview" data-theme="c" data-dividertheme="d"></ul>');

        for (var key in item.manage) {
          var menu_item = item.manage[key];
          var $menu_item = $('<li><a href="" data-transition="slide"></a></li>');
          var $a = $menu_item.find('a').attr(menu_item.attrs);
          $a.html(menu_item.label);
          $nav_box.append($menu_item);
        }

        //add cancel button
        var $menu_item = $('<li><a href="" data-rel="back" data-transition="flip" data-direction="reverse"></a></li>');
        var $a = $menu_item.find('a');
        $a.attr('class', 'cancel');
        $a.html(core.lang.get('Cancel'));

        $nav_box.append($menu_item);

        $dialog.children("div:jqmData(role='content')").append($nav_box);

        var $link = $item.find('a.options');
        $link.attr('href', '#' + dialog_id);
        $link.data('item', item);
      }

      $template.append($item);
    }


    if (params.items.length) {
      $template.find('.inviterInvitesListResult').hide();
    } else {
      if (params.titleNoItems) {
        $template.find('.inviterInvitesListResult').html(params.titleNoItems);
      }
      $template.find('.inviterInvitesListResult').show();
    }

    return $template;
  },

  inviterContactsList: function (params, $template) {
    var $itemTpl = $template.find('li.list_item').clone();
    var $search_result = $template.find('li.search-result');
    if (params.attrs) {
      $template.attr(params.attrs);
    }
    if (params.search && params.search.keyword) {
      $search_result.find('div.keyword').html(params.search.keyword);
      if (params.search.count)
        $search_result.find('span.ui-li-count').html(params.search.count);
      else {
        $search_result.find('b.result').hide();
        $search_result.find('b.no-result').css('display', 'inline');
      }
    } else {
      $search_result.hide();
      $search_result.hide();
    }

    $template.find('li.list_item').remove();
    var $div = $template.find('#contacts_container');

    for (var key in params.items) {
      var item = params.items[key];
      var $item = $itemTpl.clone();
      $item.find('label.contact-label').html(item.name);
      $item.find('label.contact-label').attr('for', item.id);
      $item.find('input.inviter-contact').attr('id', item.id);
      $item.appendTo($div);
//            $div.append($item);
    }


    if (params.items.length) {
      $template.find('.inviterContactsListResult').hide();
    } else {
      if (params.titleNoItems) {
        $template.find('.inviterContactsListResult').html(params.titleNoItems);
      }
      $template.find('.inviterContactsListResult').show();
    }
    $template.find('.inviterContactsListResult').hide();
    return $template;
  },

  badgesList: function (params, $template) {
    var $itemTpl = $template.find('li.list_item').clone();
    var $search_result = $template.find('li.search-result');
    if (params.attrs) {
      $template.attr(params.attrs);
    }
    if (params.search && params.search.keyword) {
      $search_result.find('div.keyword').html(params.search.keyword);
      if (params.search.count)
        $search_result.find('span.ui-li-count').html(params.search.count);
      else {
        $search_result.find('b.result').hide();
        $search_result.find('b.no-result').css('display', 'inline');
      }
    } else {
      $search_result.hide();
    }

    $template.find('li.list_item').remove();

    for (var key in params.items) {
      var item = params.items[key];
      var $item = $itemTpl.clone();
      $item.find('a.content').attr('href', item.href);
      if (item.attrsLi) {
        $item.attr(item.attrsLi);
      }

      if (item.attrsA) {
        $item.find('a.content').attr(item.attrsA);
      }
      if (item.photo)
        $item.find('img').attr('src', item.photo);
      else if (item.photo === false)
        $item.find('img').remove();

      if (item.show_approved) {
        $item.find('span.approved').addClass(item.approved);
        $item.find('span.approved').html(item.approved_text);
      } else {
        $item.find('span.approved').remove();
      }

      $item.find('h3').html(item.title);
      $item.find('span.badge_members').html(item.members);

      if (item.complete && item.complete != "") {
        $item.find('div.badge_loader_line').attr('style', 'width:' + item.complete);
        $item.find('div.badge_loader_description').html(item.complete);
      } else {
        $item.find('div.badge_loader').remove();
        $item.find('div.badge_loader_line').remove();
        $item.find('div.badge_loader_description').remove();
      }

      if (item.counter) {
        $item.find('.ui-li-count').html(item.counter);
      } else {
        $item.find('.ui-li-count').detach();
      }

      var $descTpl = $item.find('.ui-li-desc').clone().empty();
      if (item.descriptions) {
        for (var key in item.descriptions) {
          var desc = item.descriptions[key];
          if (key == 0) {
            $item.find('.ui-li-desc strong').html(desc);
          } else {
            $descTpl.html(desc);
            $descTpl.insertBefore($item.find('.ui-li-aside'));
          }
        }
      }

      if (item.creation_date) {
        $item.find('.ui-li-aside').html(item.creation_date);
      }
      if (!item.manage || item.manage.length == 0 || !item.manage[0].label)
        $item.find('a.options').remove();
      else {
        var dialog_id = 'apptouch_local_manage_' + parseInt(Math.random() * 1000000);
        var $dialog = this.layout.$createBase('dialog', {
          'data-rel': 'dialog',
          'id': dialog_id,
          'data-url': '#' + dialog_id,
          'data-title': item.title
        }, true);

        var $nav_box = $('<ul data-role="listview" data-theme="c" data-dividertheme="d"></ul>');

        for (var key in item.manage) {
          var menu_item = item.manage[key];
          var $menu_item = $('<li><a href="" data-transition="slide"></a></li>');
          $menu_item.find('a').attr(menu_item.attrs);
          $menu_item.find('a').html(menu_item.label);
          $nav_box.append($menu_item);
        }

        //add cancel button
        var $menu_item = $('<li><a href="" data-rel="back" data-transition="flip" data-direction="reverse"></a></li>');
        $menu_item.find('a').attr('class', 'cancel');
        $menu_item.find('a').html(core.lang.get('Cancel'));

        $nav_box.append($menu_item);

        $dialog.children("div:jqmData(role='content')").append($nav_box);

        var $link = $item.find('a.options');
        $link.attr('href', '#' + dialog_id);
        $link.data('item', item);
      }

      $template.append($item);
    }


    if (params.items.length) {
      $template.find('.itemListResult').hide();
    } else {
      if (params.titleNoItems) {
        $template.find('.itemListResult').html(params.titleNoItems);
      }
      $template.find('.itemListResult').show();
    }

    return $template;
  },

  manageBadgesList: function (params, $template) {
    var $itemTpl = $template.find('li.list_item').clone();
    var $search_result = $template.find('li.search-result');
    if (params.attrs) {
      $template.attr(params.attrs);
    }
    if (params.search && params.search.keyword) {
      $search_result.find('div.keyword').html(params.search.keyword);
      if (params.search.count)
        $search_result.find('span.ui-li-count').html(params.search.count);
      else {
        $search_result.find('b.result').hide();
        $search_result.find('b.no-result').css('display', 'inline');
      }
    } else {
      $search_result.hide();
    }

    $template.find('li.list_item').remove();

    for (var key in params.items) {
      var item = params.items[key];
      var $item = $itemTpl.clone();
      $item.find('a.content').attr('href', item.href);
      if (item.attrsLi) {
        $item.attr(item.attrsLi);
      }

      if (item.attrsA) {
        $item.find('a.content').attr(item.attrsA);
      }
      if (item.photo)
        $item.find('img').attr('src', item.photo);
      else if (item.photo === false)
        $item.find('img').remove();

      if (item.show_approved) {
        $item.find('a.approved').addClass(item.approved);
        $item.find('a.approved').html(item.approved_text);
        $item.find('a.approved').attr('id', item.id);
      } else {
        $item.find('a.approved').remove();
      }

      $item.find('a.badge_title').html(item.title);
      $item.find('a.badge_title').attr('href', item.href);
      $item.find('a.badge_members').html(item.members);
      $item.find('a.badge_members').attr('href', item.members_href);

      $template.append($item);
    }


    if (params.items.length) {
      $template.find('.itemListResult').hide();
    } else {
      if (params.titleNoItems) {
        $template.find('.itemListResult').html(params.titleNoItems);
      }
      $template.find('.itemListResult').show();
    }

    return $template;
  },

  profileBadgesList: function (params, $template) {
    var $itemTpl = $template.find('li.list_item').clone();
    if (params.attrs) {
      $template.attr(params.attrs);
    }

    $template.find('li.list_item').remove();

    for (var key in params.items) {
      var item = params.items[key];
      var $item = $itemTpl.clone();
      $item.find('a.content').attr('href', item.href);
      if (item.attrsLi) {
        $item.attr(item.attrsLi);
      }

      if (item.attrsA) {
        $item.find('a.content').attr(item.attrsA);
      }
      if (item.photo)
        $item.find('img').attr('src', item.photo);
      else if (item.photo === false)
        $item.find('img').remove();
      $template.append($item);
    }
    return $template;
  },

  badgeProfile: function (params, $template) {
    var $itemTpl = $template.find('li.list_item').clone();

    $template.find('li.list_item').remove();

    var item = params.item;
    var $item = $itemTpl.clone();
    $item.find('a.content').attr('href', item.href);
    $item.find('a.badge_title').attr('href', item.href);
    $item.find('a.badge_title').html(item.title);

    if (item.photo)
      $item.find('img').attr('src', item.photo);
    else if (item.photo === false)
      $item.find('img').remove();

    $item.find('a.badge_members').html(item.members);
    $item.find('a.badge_members').attr('href', item.members_href);

    if (item.show_approved) {
      $item.find('a.approved').addClass(item.approved);
      $item.find('a.approved').html(item.approved_text);
    } else {
      $item.find('a.approved').remove();
    }

    $item.find('span#badge-profile-information').html(item.body);

    $item.find('span#badge-profile-complete-text').html(item.complete_text);
    $item.find('div#badge-profile-completeness-line').attr('style', 'width:' + item.complete + '%');

    if (item.counter) {
      $item.find('.ui-li-count').html(item.counter);
    } else {
      $item.find('.ui-li-count').detach();
    }
    $template.append($item);
    return $template;
  },

  timelineCover: function (params, $template) {
    var cover_photo = params.cover_photo;

    if (params.user) {
      if (params.user.photo.profile) {
        $template.find('.subject_photo img').attr('src', params.user.photo.profile);
      }
      $template.find('.subject_title').html(params.user.title);
    }

    if (params.canChange) {
      $template.find('.cover_actions_wrapper').show();
    }

    if (params.choose) {
      $template.find('#cover_choose').attr('href', params.choose);
    }
    if (params.upload) {
      $template.find('#cover_upload').attr('href', params.upload);
    }
    if (params.remove) {
      $template.find('#cover_remove').attr('href', params.remove);
    } else {
      $template.find('#cover_remove').remove();
    }

    $template.find('.cover_photo_href').html(cover_photo);
    $template.find('.cover_photo_href').find('img').attr('style',
      'width: 100%; position: relative;' + $template.find('.cover_photo_href').find('img').attr('style')
    );

    return $template;
  },

  timelineCoverAlbums: function (params, $template) {
    var items = params.items;
    var $itemTpl = $template.find('li.item').clone();
    $template.find('li.item').remove();
    var $wrTpl = $itemTpl.find('.album-wrapper').clone();
    var $ph = $itemTpl.find('.photo').clone();
//        var $ln = $itemTpl.find('.link').clone();
    $itemTpl.find('.album-wrapper').remove();
    $wrTpl.find('.photo').remove();
//        $wrTpl.find('.link').remove();
    var i = 0;
    var $item = $itemTpl.clone();
    var $wr = $wrTpl.clone();
    for (var key in items) {
      var item = items[key];
      if (i > 2) {
        $template.find('ul').append($item);
        $item = $itemTpl.clone();
        $wr = $wrTpl.clone();
        i = 0;
      }
      var ph = $ph.clone();
//            var ln = $ln.clone();

      ph.find('img').attr('src', item.photo);
      ph.find('a').attr('href', item.href);
//            ln.find('a').attr('href', item.href);
//            ln.find('span').html(item.title);
      $wr.append(ph);
//            $wr.append(ln);

      $item.append($wr);
      $wr = $wrTpl.clone();
      i++;
    }
    $template.find('ul').append($item);

    // working copy
//        for(var key in items) {
//            var item = items[key];
//            var $item = $itemTpl.clone();
//
//            $item.find('a').attr('href', item.href);
//            $item.find('img').attr('src', item.photo);
//            $item.find('span').html(item.title);
//
//            $template.find('ul').append($item);
//        }
    return $template;
  },

  timelineCoverPhotos: function (params, $template) {
    var items = params.items;
    var $itemTpl = $template.find('li.item').clone();
    var $div = $itemTpl.find('div').clone();
    $itemTpl.find('div').remove();
    $template.find('li.item').remove();
    var i = 0;
    var $item = $itemTpl.clone();
    for (var key in items) {
      var item = items[key];
      if (i > 2) {
        $template.find('ul').append($item);
        $item = $itemTpl.clone();
        i = 0;
      }
      var div = $div.clone();
      div.find('a').attr('href', item.href);
      div.find('a').find('img').attr('src', item.photo);
      $item.append(div);
      i++;
    }
    $template.find('ul').append($item);
    return $template;
  },

  cartTotal: function (params, $template) {
    $template.find('span#store-cart-total-items').html(params.prices.items);
    $template.find('span#store-cart-total-tax').html(params.prices.tax);
    $template.find('span#store-cart-total-shipping').html(params.prices.shipping);
    $template.find('span#store-cart-total-price').html(params.prices.total);

    var gateways = params.gateway.gateways;
    for (var key in gateways) {
      var gateway = gateways[key];
      $template.find('div.checkout-item').append(gateway.button);
    }
    return $template;
  },

  creditCheckout: function (params, $template) {
    $template.find('span#store-cart-total-items').html(params.prices.items);
    $template.find('span#store-cart-total-tax').html(params.prices.tax);
    $template.find('span#store-cart-total-shipping').html(params.prices.shipping);
    $template.find('span#store-cart-total-price').html(params.prices.total);

    $template.find('a#cart-checkout-cancel').attr('href', params.cancel_url);
//        var gateways = params.gateway.gateways;
//        for (var key in gateways) {
//            var gateway = gateways[key];
//            $template.find('div.checkout-item').append(gateway.button);
//        }
    return $template;
  },

  transactionFinish: function (params, $template) {
    $template.find('form').attr('action', params.params.url);
    $template.find('h3').html(params.params.caption);
    $template.find('p.form-description').html(params.params.description);

    if (params) {
      if (params.params.status == 'failed') {
        $template.find('#status').val('false');
        $template.find('button').html(params.params.button);
        $template.find('button').bind('vclick', function () {
          core.cache.clear();
        });
      } else {
      }
    }
    return $template;
  },

  itemSearch: function (params, $template) {

    var $filterForm = $(params);
    $filterForm.unbind();
    $filterForm.addClass('ui-listview-filter');

    if (this.jQPageData) {
      $filterForm.attr({
        'data-rel': this.jQPageData.options.role,
        'role': 'search'
      });
    }

    $filterForm.find('#search-wrapper').find('#search, #query').each(function () {
      this.type = 'search';
    });

    $template.html($filterForm);

    return $template;
  },

  itemList: function (params, $template) {
    var self = this;
//    var is_manage_list = (this.pageResponseData.info.type == 'manage');
    var template = $template[0];
    var $itemTpl = template.querySelector('.list_item').cloneNode(true);
    var $search_result = template.querySelector('.search-result');
    if (params.attrs) {
      if (params.attrs['class'])
        params.attrs['class'] = [params.attrs['class'], $template.attr('class')].join(' ');
      $template.attr(params.attrs);
    }
    if (params.search && params.search.keyword) {
      $search_result.querySelector('.keyword').innerHTML = params.search.keyword;
      if (params.search.count)
        $search_result.querySelector('.ui-li-count').innerHTML = params.search.count;
      else {
        $search_result.querySelector('.result').style.display = 'none';
        $search_result.querySelector('.no-result').style.display = 'inline';
      }
    } else {
      $search_result.style.display = 'none';
    }

    $(template.querySelector('.list_item')).remove();

    for (var key in params.items) {
      var item = params.items[key];
      var $item = $($itemTpl.cloneNode(true));
      var $itemContent = $($item[0].querySelector('.content'));
      if (!params.no_href)
        $itemContent[0].setAttribute('href', item.href);
      else
        $itemContent[0].setAttribute('href', "javascript:void(0);");
      if (item.attrsLi) {
        $item.attr(item.attrsLi);
      }

      if (item.attrsA) {
        $itemContent.attr(item.attrsA);
      }
      if (item.photo)
        $itemContent[0].querySelector('img').style.backgroundImage = ['url("', item.photo, '")'].join('');
      else if (item.photo === false)
        $($itemContent[0].querySelector('img')).remove();
      $item[0].querySelector('h3').innerHTML = item.title;

      if (item.counter) {
        $item[0].querySelector('.ui-li-count').innerHTML = item.counter;
      } else {
        $($item[0].querySelector('.ui-li-count')).remove();
      }

      var $descTpl = $($item[0].querySelector('.ui-li-desc').cloneNode());
      if (item.descriptions) {
        for (var key = 0; key < item.descriptions.length; key++) {
          var desc = item.descriptions[key];
          desc = ('string' == typeof desc) ? desc : '';
          if (key == 0) {
            $item[0].querySelector('.ui-li-desc strong').innerHTML = desc;
          } else {
            $descTpl.html(desc);
            $descTpl.insertBefore($item.find('.ui-li-aside'));
          }
        }
      }

      if (item.creation_date) {
        $item.find('.ui-li-aside').html(item.creation_date);
      }
      if (!item.manage || item.manage.length == 0 || !item.manage[0].label)
        $item.find('a.options').remove();
      else if (item.manage.length == 1) {
        var $link = $item.find('.options');
        $link.data('item', item);
        $link.attr(item.manage[0].attrs)
          .html(item.manage[0].label);
      } else {
        var dialog_id = 'apptouch_local_manage_' + parseInt(Math.random() * 1000000);
        var $dialog = this.layout.$createBase('dialog', {
          'data-rel': 'dialog',
          'id': dialog_id,
          'data-url': '#' + dialog_id,
          'data-title': item.title
        }, true);

        var $nav_box = $('<ul data-role="listview" data-theme="c" data-dividertheme="d"></ul>');

        for (var key in item.manage) {
          var menu_item = item.manage[key];
          var $menu_item = $('<li><a href="" data-transition="slide"></a></li>');
          $menu_item.find('a').attr(menu_item.attrs);
          $menu_item.find('a').html(menu_item.label);
          $nav_box.append($menu_item);
        }

        //add cancel button
        var $menu_item = $('<li><a href="" data-rel="back" data-transition="flip" data-direction="reverse"></a></li>');
        $menu_item.find('a').attr('class', 'cancel');
        $menu_item.find('a').html(core.lang.get('Cancel'));

        $nav_box.append($menu_item);

        $dialog.children('[data-role="content"]').append($nav_box);

        var $link = $item.find('.options');
        $link.attr('href', '#' + dialog_id);
        $link.data('item', item);

      }

      if (item.type == 'pagevideo' || item.type == 'video') {
        $itemContent.append($('<div class="icon-play-circle"></div>'));
      }

      $template.append($item);
    }


    if (params.items && params.items.length) {
      $template.find('.itemListResult').hide();
    } else {
      if (params.titleNoItems) {
        $template.find('.itemListResult').html(params.titleNoItems);
      }
      $template.find('.itemListResult').show();
    }

    if( params.listPaginator && params.next) {
      var viewMore = $('<a class="listPagination btn">View More<a/>');
      $template.append(viewMore);

      var viewMoreFunction = function()
      {
        $.mobile.showPageLoadingMsg();
        $.ajax({
          url: next_url,
          data: {format: 'json'},
          success: function(response){
            for( var i = 0; i < response.page.layout.content.length; i++ ) {
              if( response.page.layout.content[i].name == 'itemList' && (!params.forList || response.page.layout.content[i].params.attrs.listName == params.forList)) {
                var template = $('#component-itemList > ul').clone();
                response.page.layout.content[i].params.listPaginator = false;
                var cont = self.itemList(response.page.layout.content[i].params, template);
                //if(params.forList) {
                //  var listview = $.mobile.activePage.find('.component-itemList.' + params.forList);
                //} else {
                //  var listview = $.mobile.activePage.find('ul.component-itemList');
                //}

                var listview = $template;

                listview.append(cont.find('li.list_item'));
                listview.listview('refresh');

                var $viewMore = $.mobile.activePage.find('.listPagination');

                $viewMore.remove();
                listview.append($viewMore);

                params.next++;
                if( params.next > params.pageCount ) {
                  window.autoscrolling = false;
                  $viewMore.unbind('vclick');
                  $viewMore.hide();
                  $.mobile.hidePageLoadingMsg();
                  return;
                }
                next_url = self.helper.getPaginatorUrl(next_url, params.next);

                $viewMore.unbind('vclick').bind('vclick', function(){
                  $(this).unbind('vclick');
                  window.autoscrolling = true;
                  viewMoreFunction();
                });

                break;
              }
            }
            $.mobile.hidePageLoadingMsg();
            window.autoscrolling = false;
          },

          error: function(){
            $.mobile.hidePageLoadingMsg();
            window.autoscrolling = false;
          }
        });
      };

      var next_url = this.helper.getPaginatorUrl(this.pageResponseData.info.url, params.next);
      viewMore.unbind('vclick').bind('vclick', function(){
        $(this).unbind('vclick');
        window.autoscrolling = true;
        viewMoreFunction();
      });

      if( params.autoscroll ) {
        viewMore.hide();
      }
    }

    return $template;
  },

  paginator: function (params, $template) {
    var totalCount = params.totalItemCount;
    var firstItem = (params.current - 1) * params.itemCountPerPage + 1;
    var lastItem = firstItem + params.itemCountPerPage - 1;
    lastItem = (lastItem <= totalCount) ? lastItem : totalCount;

    var $prevButton = $template.find('.paginator-prev');
    var $paginatorInfo = $template.find('.paginator-info');
    var $nextButton = $template.find('.paginator-next');
    if (params.prev) {
      $prevButton.find('a').attr({'href': this.helper.getPaginatorUrl(this.pageResponseData.info.url, params.prev)});
    }

    if (this.jQPageData) {
      $prevButton.find('a').attr('data-rel', this.jQPageData.options.role);
      $nextButton.find('a').attr('data-rel', this.jQPageData.options.role);
    }

    $paginatorInfo.find('.firstItem').html(firstItem);
    $paginatorInfo.find('.lastItem').html(lastItem);
    $paginatorInfo.find('.totalCount').html(totalCount);

    if (params.next) {
      $nextButton.find('a').attr({'href': this.helper.getPaginatorUrl(this.pageResponseData.info.url, params.next)});
    }

    return $template;
  },

  gallery: function (params, $template) {
    var template = $template[0];
    var active = params.active;
    var photos = params.photos;

    if (photos.length == 0)
      return false;

//    cloning tpl {
    var ul_thumbs = $(template.querySelectorAll('.thumbs'));
    var li = ul_thumbs.find('li');
//    } cloning tpl

//    empty html {
    $template.empty();
    ul_thumbs.empty();
//    } empty html
    for (var key in photos) {
      var clone_li = li.clone();
      var subject = photos[key];
      var a_thumbs_photo = clone_li.find('.thumbs_photo');
      var span_photo = a_thumbs_photo.find('span');
      var subjectPhoto = subject.photo;
      a_thumbs_photo.attr('href', subjectPhoto.full);
      a_thumbs_photo.data('subject', subject.href);
      a_thumbs_photo.data('itemtype', subject.type);
      a_thumbs_photo.data('id', subject.id);

      var bgi = ['url(', subjectPhoto.normal, ')'].join('');
      var helper = core.helper;
      if (helper.isTablet()) {
        bgi = ['url(', subjectPhoto.profile, ')'].join('');
      }
      span_photo.css('background-image', bgi);
      a_thumbs_photo.attr('title', subject.title);
      ul_thumbs.append(clone_li);
      if (subject.id == active) {
        active = key;
      }
    }

    $template.append(ul_thumbs);

    $template.find('a').photoSwipe({
      enableMouseWheel: false,
      enableKeyboard: false,
      enableComment: params.options.canComment,
      enableTags: params.options.canViewTags
    });
    if (active != undefined) {
      ul_thumbs.find('li').hide();
    }
    var $active;
    if (active == 0)
      $active = ul_thumbs.find('li').first().show().find('a');
    else
      $active = ul_thumbs.find('li:eq(' + active + ')').show().find('a');

    if (active != undefined) {
      $active.addClass('active_photo');
    }

    if (this.pageResponseData.info.params.comments != 'write')
      $active.trigger('vclick');

    return $template;
  },

  registryWall: function (params, $template) {
    try {
      params.$template = $template;
      var $feed = $template.find('.social-feed');
      var ins = new ActivityFeed($feed, params);
      window.__activityFeed = ins;
    } catch (e) {
    }

    /**
     * Create objects in window
     */
    if (!window._wall_keys) {
      window._wall_keys = [];
    }
    var n = window._wall_keys.length;
    window._wall_keys[n] = '_wall_' + n;
    window[window._wall_keys[n]] = ins;
    window.wall = ins;
    var $cl = $template.find('.composeLink');
    $cl.data('key', window._wall_keys[n]);
    var FnVClickWE = function (e) {
      var el = this;
      var we = Wall.events;
      var action = el.getAttribute('class').split('we-')[1].split(' ')[0];
      switch (action) {
        case 'remove':
          we.remove(this);
          break;
        case 'mute':
          we.mute(this);
          break;
        case 'removeTag':
          we.removeTag(this);
          break;
        case 'hideMenu':
          we.hideMenu(this);
          break;
        case 'showMenu':
          we.showMenu(this);
          break;
        case 'unlike':
          we.unlike(this);
          break;
        case 'showCommentModal':
          we.showCommentModal(this);
          break;
        case 'showShareModal':
          we.showShareModal(this);
          break;
        case 'like':
          we.like(this);
          break;
        case 'hashtag':
          we.hashtagActivate(this);
          break;
      }
      e.preventDefault();
      return false;
    };
    var FnVClickWEF = function (e) {
      var el = this;
      var wef = Wall.events.form;
      var type = el.getAttribute('class').split('wef-')[1].split(' ')[0];
      wef.showPostForm(this, type);
      e.preventDefault();
      return false;
    };
    $template.undelegate('.wall-event', 'click', FnVClickWE);
    $template.delegate('.wall-event', 'click', FnVClickWE);
    var $wfm = $('#feed-composer-panel');
    var $composerBtns = $wfm.find('.wall-event-form');
    $composerBtns.unbind('click', FnVClickWEF);
    $composerBtns.bind('click', FnVClickWEF);
    $cl.bind('click', function (e) {
      Wall.events.showComposerPanel(this);
      e.preventDefault();
      return false;
    });
    var aht = $template.find('.active_hashtags');
    if (!sessionStorage.getItem('tipcount') || parseInt(sessionStorage.getItem('tipcount')) < 3)
      aht.bind('click', function () {
        var $this = $(this);
        var c = $this.data('tipcount');
        c = c ? c : 0;
        if (c > 15) {
          $this.data('tipcount', undefined);
          var ssc = sessionStorage.getItem('tipcount');
          ssc = ssc ? ssc : 0;
          ssc++;
          sessionStorage.setItem('tipcount', ssc);
          $this.unbind('click');
        }
        c++;
        $this.data('tipcount', c);
        if (c % 3 == 0) {
          setTimeout(function () {
            $this.addClass('tip');
            UIComponent.helper.showMessage(core.lang.get('APPTOUCH_Swipe to clear hashtag filter'), 'a', 5000);
          }, 3000);
          setTimeout(function () {
            $this.removeClass('tip');
          }, 8000);
        }
      });
    aht.bind('swiperight', function (e) {
      Wall.events.hashtagDectivate(this);
      e.preventDefault();
      return false;
    });
  },

  feed: function (params, $template) {
    this.registryWall(params, $template);
    return $template;
  },

  checkinMap: function (params, $template) {
    checkin_map.$template = $template;
    $template.find('.map_canvas').empty();
    checkin_map.map_canvas = $template.find('.map_canvas')[0];
    checkin_map.construct(null, params.markers, 4, params.bounds);

    $template.find('.checkin_list_cont').hide();
    $template.find('.map_canvas').css('position', 'relative');
    $template.find('.map_canvas').css('top', '0px');
    google.maps.event.trigger(checkin_map.map, 'resize');
    checkin_map.setMapCenterZoom();


    google.maps.event.addListenerOnce(checkin_map.map, 'tilesloaded', function () {
      google.maps.event.trigger(checkin_map.map, 'resize');
      checkin_map.setMapCenterZoom();
    });

    return $template;
  },

  comments: function (params, $template) {
    var pageResponseData = this.pageResponseData;
    var widget = params;
    var subject = params.subject;

    var $widget = $template;
    var $btn_post_comment = $widget.find('.post_comment');
    var $btn_like_this = $widget.find('.like_this');
    var $btn_unlike_this = $widget.find('.unlike_this');
    var $ul_comments = $widget.find('ul.comments');

    $btn_post_comment.find('a').append($('<span />').attr({'class': 'ui-li-count comment-count'}).html(widget.comment_count));
    // Functions {
    var toggleLikeBtn = function (liked) {
      var $controlgroup = $('.component-like');
      var like_like_btn = $controlgroup.find('.like');
      var like_unlike_btn = $controlgroup.find('.unlike');
      if (liked) {

        $btn_like_this.hide();
        $btn_unlike_this.show();

        like_like_btn.parent().hide();
        like_unlike_btn.parent().css('display', 'block');
      } else {
        $btn_like_this.show();
        $btn_unlike_this.hide();

        like_unlike_btn.parent().hide();
        like_like_btn.parent().css('display', 'block');
      }
//      $controlgroup.controlgroup().controlgroup('refresh');
    }

    var toggleCommentLikeBtn = function (liked, $li) {
      var $like_comment = $li.find('.like-comment');
      var $unlike_comment = $li.find('.unlike-comment');
      // todo shit code
      if (liked) {
        $like_comment.hide();
        $unlike_comment.css('display', 'inline-block');
        $unlike_comment.addClass('ui-corner-right').find('ui-controlgroup-last');
        $unlike_comment.find('.ui-btn-inner').addClass('ui-corner-right ui-controlgroup-last');
      } else {
        $like_comment.css('display', 'inline-block');
        $like_comment.addClass('ui-corner-right').find('ui-controlgroup-last');
        $like_comment.find('.ui-btn-inner').addClass('ui-corner-right ui-controlgroup-last');
        $unlike_comment.hide();
      }
//      $li.find('div.comment-like').controlgroup().controlgroup('refresh');
      // todo shit code
    }

    var like_unlike = function ($like) {


      $like.unbind('click');
      var url = $like.attr('href');
      $like.removeAttr('href');
      $like.bind('click', function (e) {

        if ($like.data('completed') === false)
          return false;

        $.mobile.showPageLoadingMsg();

        $like.data('completed', false);
        $.post(url, {'format': 'json', 'id': subject.id, 'type': subject.type}, function (response) {
          if (response.status) {
            toggleLikeBtn(response.like);
            $like.data('completed', true);
          }

          $.mobile.hidePageLoadingMsg();
        }, 'json');

      });

    }

    var comment_like_unlike = function ($li, comment) {
      toggleCommentLikeBtn(comment.options.like, $li);
      var query_data = {'format': 'json', 'id': subject.id, 'type': subject.type, 'comment_id': comment.id};
      var $like_comment = $li.find('.comment-like').find('.like-comment');
      var $unlike_comment = $li.find('.comment-like').find('.unlike-comment');
      var url_like = $like_comment.attr('href');
      var response_handler = function (response) {
        if (response.status)
          toggleCommentLikeBtn(response.like, $li);
      };
      $li.find('.comment-like').controlgroup().controlgroup('refresh');
      // todo shit code
      //        like
      $like_comment.unbind('vclick');
      $like_comment.bind('vclick', function (e) {
        $.post(url_like, query_data, response_handler, 'json');
      });
      $like_comment.removeAttr('href');

      //        unlike
      $unlike_comment.unbind('vclick');
      var url_unlike = $unlike_comment.attr('href');
      $unlike_comment.bind('vclick', function (e) {
        $.post(url_unlike, query_data, response_handler, 'json');
      });
      $unlike_comment.removeAttr('href');

      //        delete if allowed
      if (comment.options && comment.options['delete']) {
        var $delete_comment = $li.find('.comment-like').find('.delete-comment');
        $delete_comment.unbind('vclick');
        var url_delete = $delete_comment.attr('href');
        $delete_comment.bind('vclick', function (e) {
          //            todo confirm delete

          $.post(url_delete, query_data, function (response) {
            if (response.status && response.deleted) {
              alert(response.message);
              $li.remove();
            }
            var $divider = $ul_comments.find('.comment-count');
            //    set comment count
            if (response.comment_count == 0)
              $divider.hide();
            else
              $divider.find('span').html(response.comment_count);
          }, 'json');

        });
        $delete_comment.removeAttr('href');
      }

      // todo shit code
    }

    var create_comment_item = function ($tpl, comment) {
      var $li = $tpl.clone();
      $li.attr('id', 'comment-' + comment.id);
      var $img = $li.find('img');
      $img.attr({
        'href': comment.poster.href
      }).css('background-image', ['url("', comment.poster.photo.normal, '")'].join(''))
        .bind('vclick', function (e) {
          $.mobile.changePage($(this).attr('href'));
        });
      $li.find('.comment-author').html(comment.poster.title);
      $li.find('.comment-body').html(comment.body);
      $li.find('.ui-li-aside').html(comment.creation_date);
      $li.find('.ui-li-aside').html(comment.creation_date);
      var $delete_comment = $li.find('.comment-like').find('.delete-comment');
      if (!widget.can_comment)
        $li.find('.comment-like').remove();
      else {
        if (!comment.options['delete'])
          $delete_comment.remove();
        comment_like_unlike($li, comment);
      }
      return $li;
    }

    // } Functions

    $btn_post_comment.bind('vclick', function (e) {
      var $ui_icon = $btn_post_comment.find('.ui-icon');
      if ($ul_comments.css('display') == 'none') {
        $ul_comments.show().find('textarea').scroll().focus();
        $ui_icon.addClass('ui-icon-minus');
        $ui_icon.removeClass('ui-icon-plus');
        $ul_comments.find('.comment-like').controlgroup().controlgroup('refresh');
      } else {
        $ul_comments.hide();
        $ui_icon.addClass('ui-icon-plus');
        $ui_icon.removeClass('ui-icon-minus');
      }
    });
    var $li_tpl = $ul_comments.find('.comment').clone();

    //    set Form
    if (widget.main_form) {
      $ul_comments.find('.form').html(widget.main_form).show();

    }

    //    get Form
    var $form = $ul_comments.find('li.form form');
    //    populating
    $form.find('#identity').attr('value', subject.id);
    $form.find('#type').attr('value', subject.type);

    $form.attr('data-ajax', "false");
    $form.find('#submit').bind('vclick', function (e) {
      var $this = $(this);
      var $form = $(this.form);
      if ($this.data('completed') === false)
        return false;

      var body = $form.find("#body").val();
      if (body.length > 50 || body.split(' ').join('').length) {
        $this.data('completed', false);
        $.post($form.attr('action'), {
          'format': 'json',
          'id': subject.id,
          'type': subject.type,
          'body': body
        }, function (response) {
          var $item;
          if (response.status) {
            var $first = $ul_comments.find('.comment').first();
            $item = create_comment_item($li_tpl, response.comment);
            var $divider = $ul_comments.find('.comment-count');
            $item.insertAfter($divider);
            if (response.comment_count > 0) {
              $divider.show();
              $divider.find('span').html(response.comment_count);
              widget.comment_count = response.comment_count;
            }

            $ul_comments.listview('refresh');
            $form.find("#body").val('');
            $btn_post_comment.find('a').find('.comment-count').html(response.comment_count);
          }
          $this.data('completed', true);
        }, 'json');
      }

      e.preventDefault();
      return false;
    });
    $ul_comments.find('.comment').remove();


    // divider
    var $divider_comment = $ul_comments.find('.comment-count');

    //    set comment count
    if (widget.comment_count == 0)
      $divider_comment.hide();
    else
      $divider_comment.find('span').html(widget.comment_count);

    if (pageResponseData.info.viewer.id) {
      var $like = $btn_like_this.find('a');
      var $unlike = $btn_unlike_this.find('a');
      toggleLikeBtn(widget.liked);
      like_unlike($like);
      like_unlike($unlike);
    } else {
      $btn_like_this.hide();
    }
    var $likers = $template.find('.likers');
    if (widget.likes && widget.likes.length > 0) {
      $likers.find('h2').html(widget.liker_text);
      var $likerTpl = $likers.find('li').clone();
      var $likersUl = $likers.find('ul');
      $likersUl.empty();
      for (var key in widget.likes) {
        var $liker = $likerTpl.clone();
        var user = widget.likes[key];
        $liker.find('a').html(user.title);
        $liker.find('a').attr('href', user.href);
        $likersUl.append($liker);
      }
    } else {
      $likers.hide();
    }
    if (widget.comments.length > 0)
      for (var key in widget.comments) {
        var comment = widget.comments[key];
        $ul_comments.append(create_comment_item($li_tpl, comment));
      }

    //    Handle Actions {
    if (params.action == 'write') {
      setTimeout(function () {
        $btn_post_comment.trigger('vclick');
      }, 1000)
    }
    //    } Handle Actions

    return $widget;
  },

  rate: function (params, $template) {
    if (params) {
      var rate = params;
      $template.find('.he_rate_cont').attr('id', 'rate_uid_' + rate.rate_uid);

      var html = '';
      var star_value = '';
      for (var i = 0; i < 5; i++) {
        if ((i + 0.125) > rate.item_score) {
          star_value = 'no_rate';
        } else if ((i + 0.375) > rate.item_score) {
          star_value = 'quarter_rated';
        } else if ((i + 0.625) > rate.item_score) {
          star_value = 'half_rated';
        } else if ((i + 0.875) > rate.item_score) {
          star_value = 'fquarter_rated';
        } else {
          star_value = 'rated';
        }
        html += '<div class="rate_star ' + star_value + '" id="rate_star_' + (i + 1) + '"></div>';
      }
      $template.find('.rate_stars_cont').html(html);
      $template.find('.item_rate_info').find('.item_score').html(rate.item_score + '/5');
      $template.find('.item_rate_info').find('.item_votes').html((rate.rate_info) ? rate.rate_info.rate_count : 0);
      $template.find('.item_rate_info').find('.item_voters').text(rate.label);
      $template.find('.item_voters').attr('href', rate.href);

      var rateVar = new Rate(rate.item_id, rate.item_type, rate.rate_uid, rate.can_rate, $template.find('.he_rate_cont'));
      rateVar.rate_url = rate.rate_url;

      return $template;
    }
  },

  like: function (params, $template) {
    var like_a = $template.find('a.like');
    var unlike_a = $template.find('a.unlike');
    var $membershipBtn;

    // Setup Membership Button
    if (params.membership) {
      $template.addClass('has-membership');
      $membershipBtn = $(params.membership);
      $membershipBtn.attr({
        'data-role': 'button',
        'data-mini': true,
        'data-rel': 'dialog'
      });

      // Cancel Request Btn
      if ($membershipBtn.hasClass('icon_friend_cancel')) {
        $membershipBtn.attr({
          'data-icon': 'back'
        });
      }

      // Add Friend & Accept Request Btns
      if ($membershipBtn.hasClass('icon_friend_add')) {
        $membershipBtn.attr({
          'data-icon': 'plus'
        });
      }

      // Remove Friend Btn
      if ($membershipBtn.hasClass('icon_friend_remove')) {
        $membershipBtn.attr({
          'data-icon': 'minus'
        });
      }

      $template.append($('<div>').attr('class', 'ui-block-b').append($membershipBtn));
    }
    if (params.action == 'like') {
      unlike_a.parent().hide();
    } else {
      like_a.parent().hide();
    }

    like_a.unbind('vclick');
    like_a.removeAttr('href');
    unlike_a.unbind('vclick');
    unlike_a.removeAttr('href');

    like_a.bind('vclick', function (e) {
      if (like_a.data('completed') === false)
        return;
      $.mobile.showPageLoadingMsg();

      like_a.data('completed', false);

      $.post(params.like_url, {
        'format': 'json',
        'object_id': params.id,
        'object': params.type
      }, function (response) {

        if (response.status) {
          like_a.parent().hide();
          unlike_a.parent().css('display', 'block');

          var comment_like = $('.component-comments').find('.like_this');
          var comment_unlike = $('.component-comments').find('.unlike_this');

          comment_like.hide();
          comment_unlike.css('display', 'block');
        }

        like_a.data('completed', true);
//        $template.controlgroup().controlgroup('refresh');
        $.mobile.hidePageLoadingMsg();
      }, 'json');
    });

    unlike_a.bind('vclick', function (e) {
      $.mobile.showPageLoadingMsg();
      if (like_a.data('completed') === false)
        return;

      like_a.data('completed', false);

      $.post(params.unlike_url, {
        'format': 'json',
        'object_id': params.id,
        'object': params.type
      }, function (response) {

        if (response.status) {
          unlike_a.parent().hide();
          like_a.parent().css('display', 'block');

          var comment_like = $('.component-comments').find('.like_this');
          var comment_unlike = $('.component-comments').find('.unlike_this');

          comment_unlike.hide();
          comment_like.css('display', 'block');

        }
        like_a.data('completed', true);
//        $template.controlgroup().controlgroup('refresh');
        $.mobile.hidePageLoadingMsg();
      }, 'json');
    });

    if (!params.is_enabled || !params.is_allowed) {
      like_a.parent().hide();
      unlike_a.parent().hide();
    }

    if (!params.auth) {
      var msg = $template.find('.like-status');
      msg.html(params.warn_text);
      msg.css('display', 'block');
    }

    return $template;
  },

  fieldsValues: function (params, $template) {
    var $fieldTpl = $template.find('.field').clone();
    $template.empty();
    for (var order in params) {
      var field = params[order];
      var $field = $fieldTpl.clone();
      $field.find('h3').html(field.title);

      var $fieldTextTpl = $field.find('table').find('tr').clone();
      $field.find('tbody').empty();
      for (var textOrder in field.content) {
        var $fieldText = $fieldTextTpl.clone();
        var fieldText = field.content[textOrder];
        $fieldText.find('th').html(fieldText.label);
        $fieldText.find('td').html(fieldText.value);
        $field.find('tbody').append($fieldText);
      }
      $template.append($field);
    }
    return $template;
  },

  tabs: function (params, $template) {
    var template = $template[0];
    var menu_item_tpl = template.getElementsByTagName('li')[0].cloneNode(true);

    menu_item_tpl.querySelector('.tab_title span').innerHTML = '';
    var listview = template.getElementsByTagName('ul')[0];
    listview.innerHTML = '';
    for (var key in params) {
      var menu_item = params[key];
      var $menu_item = menu_item_tpl.cloneNode(true);
      var menuItemLink = $menu_item.getElementsByTagName('a')[0];
      $(menuItemLink).attr(menu_item.attrs);
      menuItemLink.className += ' tab ' + menu_item.attrs['class'];
      $menu_item.querySelector('.tab_title span').innerHTML = menu_item.label;
      if (menu_item.active) {
        menuItemLink.className += ' ui-btn-active ui-active-tab';
        menuItemLink.setAttribute('data-order', key);
      }
      listview.appendChild($menu_item);
    }
    var profiletabWidth = 105;
    listview.style.width = params.length * profiletabWidth + 'px';
    var iscroll = new iScroll(template, {hScroll: true, vScroll: false, hideScrollbar: true});
    iscroll._resize();
    var tomove = profiletabWidth * (parseInt(template.querySelectorAll('.ui-btn-active')[0].getAttribute('data-order')) + 1);
    if (tomove > document.body.clientWidth) {
      iscroll._pos(-1 * tomove, 0);

    }
    return $(template);
  },

  playlist: function (params, $template) {

    var $trackTpl = $template.find('.track').clone();

    $template.find('.tracks').empty();

    var $tracks = $template.find('.tracks').empty();
    var tracks = params.items;

    for (var order in tracks) {

      var $track = $trackTpl.clone();
      var track = tracks[order];
      var $trackPlayCount = $track.find('.ui-li-count');
      var trackAudioElement = document.createElement('audio');
      var $trackAudio = $(trackAudioElement);
      trackAudioElement.addEventListener("loadeddata", function (e) {
        this.volume = 1;
        var $this = $(this);
        $this.closest('li').find('.ui-li-aside').html(UIComponent.helper.milliSecsToMinuteString(this.duration));
      }, true);

      trackAudioElement.addEventListener("waiting", function (e) {
        var can = this.canPlayType ? this.canPlayType('audio/mpeg') : false;
        if (!can)
          $.mobile.showPageLoadingMsg('a', 'Buffering...'); // todo langs
      });

      trackAudioElement.addEventListener("seeking", function (e) {
        $.mobile.showPageLoadingMsg('a', 'Buffering...'); // todo langs
      });

      trackAudioElement.addEventListener("seeked", function (e) {
        $.mobile.hidePageLoadingMsg();
      });

      trackAudioElement.addEventListener("error", function (e) {
        var can = this.canPlayType ? this.canPlayType('audio/mpeg') : false;
        if (!can) {
          UIComponent.helper.showMessage('Your Browser can\'t play mp3 media', 'e'); // todo langs
        }
      });

      trackAudioElement.addEventListener("ended", function (e) {
        var $this = $(this);
        setTimeout(function () {
          $this.closest('li').next('.track').trigger('click');
        }, 1000);

      });

      $track.append($trackAudio);
      $trackAudio.attr({
        'src': track.href
      });

      $track.find('a').prepend(track.title);
      $track.attr('title', track.title);
      $trackPlayCount.html(track.play_count);

      $(window).bind('scroll', function (e) {

        var page = UIComponent.helper.$getActivePage();
        var filterForm = page.find('.component-playlist').children('.ui-listview-filter');

        if (filterForm.length == 0)
          return;

      });

      $track.bind('click', function (e) {
        $.mobile.hidePageLoadingMsg();
        var $this = $(this);
        var activeTheme = 'b';
        var baseTheme = 'c';
        var $playlist = $this.closest('ul.tracks');
        var $audio = $this.find('audio');

        var $mediaControls = UIComponent.helper.$getActivePage().find('.component-mediaControls');

        $playlist.find('li.track.active').removeClass('active ui-btn-up-' + activeTheme + ' ui-btn-hover-' + activeTheme).addClass('ui-btn-up-' + baseTheme);//.find('*').removeClass('ui-btn-up-b ui-btn-hover-b').addClass('ui-btn-up-' + baseTheme);
        $this.addClass('active').removeClass('ui-btn-up-a ui-btn-up-b ui-btn-up-c ui-btn-up-d ui-btn-up-e ui-btn-hover-a ui-btn-hover-b ui-btn-hover-c ui-btn-hover-d ui-btn-hover-e')
          .addClass('ui-btn-up-' + activeTheme)
          .attr('data-theme', activeTheme);
        $playlist.listview('refresh');
        if ($mediaControls.length > 0) {
//        $audio[0].addEventListener("loadedmetadata", function (e) {
//          $audio.attr('metadataloaded', true);
//        });
//          $audio.attr('controls', 'controls');
          $audio[0].play();
          $audio[0].pause();
//          console.log($audio[0].play());
//          console.log($audio[0].played);

          var inv = setInterval(function () {
//          if($audio.attr('metadataloaded')){
            $mediaControls.trigger('init', {
              'media': $audio,
              'title': $this.attr('title'),
              'action': 'play',
              'reset': true,
              'stopOthers': true
            });
            clearInterval(inv);
//          }
          }, 100);
        }
      });

      $tracks.append($track);
    }

    return $template;
  },

  video: function (params, $template) {
    if (!params.iframeUrl) {
      //$template.html(params.flashObject);
      var img = $(params.video_thumb);
      img.css('width', '260');
      img.css('height', '220');
      var link = $('<a class="video_type_3"></a>').append(img);
      link.append($('<div class="icon-play-circle"></div>'));
      link.attr('href', params.video_location);
      link.attr('data-ajax', 'false');
      $template.html(link);
    } else {
      if (params.video_type < 3) {
        $template.find('iframe').attr('src', params.iframeUrl)
      } else
        $template.html(params.flashObject);
    }

//    var videoElement = document.createElement('video');
//    var $videoElement = $(videoElement);
//    $videoElement.attr('src', params.videoLinks['video/webm']['medium']);
//
//    videoElement.addEventListener("loadeddata", function (e) {
//      this.volume = 1;
//      var $this = $(this);
//      videoElement.play();
//    }, true);
//
//    videoElement.addEventListener("waiting", function (e) {
////      var can = this.canPlayType ? this.canPlayType('audio/mpeg') : false;
////      if (!can)
//        $.mobile.showPageLoadingMsg('a', 'Buffering...'); // todo langs
//    });
//
//    videoElement.addEventListener("seeking", function (e) {
//      $.mobile.showPageLoadingMsg('a', 'Buffering...'); // todo langs
//    });
//    videoElement.addEventListener("seeked", function (e) {
//      $.mobile.hidePageLoadingMsg();
//    });
//
//    videoElement.addEventListener("canplay", function (e) {
//      var $mediaControls = UIComponent.helper.$getActivePage().find('.component-mediaControls');
//      var $this = $(this);
//      if ($mediaControls.length > 0)
//        $mediaControls.trigger('init', {
//          'media':$this,
//          'title':params.title,
//          'action':'play',
//          'reset':true,
//          'stopOthers':true
//        });
//
//      $.mobile.hidePageLoadingMsg();
//    });
//
//    videoElement.addEventListener("error", function (e) {
////      var can = this.canPlayType ? this.canPlayType('audio/mpeg') : false;
////      if (!can) {
////        $.mobile.showPageLoadingMsg('e', 'Your Browser can\'t play mp3 media', true); // todo langs
////      }
//      setTimeout(function () {
//        $.mobile.hidePageLoadingMsg();
//      }, 2500);
//    });
//    videoElement.addEventListener("ended", function (e) {
//      var $this = $(this);
//      setTimeout(function () {
////        $this.closest('li').next().trigger('vclick');
//      }, 1000);
//
//    });
//
//    $template.append(videoElement);
    return $template;
  },

  mediaControls: function (params, $template) {
//    ------------------ Control Functions ------------------
    var playPause = function ($mediaControls, play) {
      var params = $mediaControls.data('mediaData');
      if (!params || !params.media){
        $('.track').first().trigger('vclick');
        return;
      }

      var media = params.media[0];
      if (play) {
        pauseAll();
        media.play();
        $mediaControls.find('.playBtn').hide();
        $mediaControls.find('.pauseBtn').show();
//        UIComponent.helper.$getActivePage().children("div:jqmData(role='footer')").attr('data-position', 'fixed');
//        UIComponent.helper.$getActivePage().trigger('create');
      } else {
        media.pause();
        $mediaControls.find('.pauseBtn').hide();
        $mediaControls.find('.playBtn').show();
      }

    }

    var pauseAll = function (mediaType, stop) {
      var $medias = $('video, audio');
      try {
        for (var order = 0; order < $medias.length; order++) {
          $medias[order].pause();
          clearInterval($($medias[order]).data('timeupdater'));
        }
      } catch (e) {
      }
    };

//    ------------------ Control Functions ------------------
    $template.find('.playBtn').data('playPauseFn', playPause).unbind('click').bind('click', function () {
      var $this = $(this);
      $this.data('playPauseFn')($this.closest('.component-mediaControls'), true);

    });

    $template.find('.pauseBtn').data('playPauseFn', playPause).unbind('click').bind('click', function () {
      var $this = $(this);
      $this.data('playPauseFn')($this.closest('.component-mediaControls'), false);
    });
    setTimeout(function () {
      var $this = $template.find("form input.slider");
      var $slide = $this.closest('li').find('[role="application"]');
      var $handle = $this.closest('li').find('[role="application"]').find('.ui-slider-handle');
      $slide.bind('mousedown', function () {
        $this.closest('li').find('.slider').data('sliding', true);
      });

      $slide.bind('click', function (e) {
        $this.closest('li').find('.slider').data('sliding', false);
      });

      $slide.bind('mouseup', function () {
        var $params = $this.closest('.component-mediaControls').data('mediaData');
        if (!$params || !$params.media.length)
          return;
        $params.media[0].currentTime = parseFloat($this.attr('value'));
        $this.closest('li').find('.slider').data('sliding', false);
      });
    }, 1000);

    $template.bind('init', function (e, params) {
      var $this = $(this);

      $this.data('mediaData', params);
      pauseAll(params.mediaType, params.stopOthers);

      var media = params.media[0];
      var $slider = $this.find('form').find('.slider');
      var $nowplaying = $this.find('.nowPlaying').show();

      $nowplaying.find('marquee').html(params.title);
      $slider.attr('max', media.duration);
      var timeupdaterFn = function (e) {
        $this.find('.timeTick').find('.ui-btn-text').html(UIComponent.helper.milliSecsToMinuteString(media.currentTime));
        UIComponent.helper.$getActivePage().page();

//        if (!$slider.data('sliding')) {
          $slider.attr('value', media.currentTime);
          $slider.slider('refresh');
//        }
      };
      media.addEventListener("play", function (e) {
        window.clearInterval($(this).data('timeupdater'));
        params.media.data('timeupdater', setInterval(timeupdaterFn, 500));
      });
      media.addEventListener("pause", function (e) {
        window.clearInterval($(this).data('timeupdater'));
      });
      playPause($this, true);
    });
    return $template;
  },

  tip: function (params, $template) {
    $template.find('h3').html(params.title);
    $template.find('p').html(params.message);
    $template.attr(params.attrs);
    return $template;
  },

  discussion: function (params, $template) {
    var posts = params.posts;
    var $posts = $template.find('ul.topic-posts');
    var $postTpl = $posts.find('li').clone();
    $posts.find('li').remove();
    if (params.options) {

      var $optionTpl = $template.find('.topic-option').clone();
      $template.find('.topic-option').remove();
      var $optionMore = $template.find('.more-topic-options');
      var to = params.options.length > 3 ? 2 : params.options.length;

      for (var i = 0; i < to; i++) {
        var $option = $optionTpl.clone();
        var option = params.options[i];
        $option.html(option.label);
        $option.attr(option.attrs);
        $option.insertBefore($optionMore);
      }

      if (params.options.length <= 3)
        $optionMore.remove();
      else {
        $optionMore.attr({'href': this.helper.createPopupMenu(params.options, $optionMore.text(), 2)});
      }
    }

    for (var order in posts) {
      var $post = $postTpl.clone();
      var post = posts[order];
      post.body = post.body.replace(/<blockquote>/g, '<div class="quote-reply" data-role="collapsible" data-collapsed="false"   data-theme="e" data-content-theme="d">')
        .replace(/<\/blockquote>/g, '</div>')
        .replace(/<strong>/g, '<h3>')
        .replace(/<\/strong>/g, '</h3>');

      $post.find('.post-owner').find('a').attr({'href': post.owner.href}).html(post.owner.title);
      $post.find('.post-owner-photo').attr({'src': post.owner.photo.normal});
      if (post.owner.postCount)
        $post.find('.owner-post-count').html(post.owner.postCount).show();
      if (post.options) {

        var $optionTpl = $post.find('.post-options').find('.post-option').clone();
        $post.find('.post-options').find('.post-option').remove();
        var $optionMore = $post.find('.post-options').find('.more-post-options');
        var to = post.options.length > 3 ? 2 : post.options.length;

        for (var i = 0; i < to; i++) {
          var $option = $optionTpl.clone();
          var option = post.options[i];
          $option.html(option.label);
          $option.attr(option.attrs);
          $option.insertBefore($optionMore);
        }

        if (post.options.length <= 3)
          $optionMore.remove();
      }

      if (post.owner.status)
        $post.find('.post-owner-status').show().html(post.owner.status);
      $($post[0]).html(post.creation_date);
      $post.find('.post-body').html(post.body);
      if (post.photo) {
        $post.find('.post-body').append(this.layout.$renderComponent(post.photo));
      }
      $posts.append($post);
    }
    if (params.postForm)
      $template.append(this.layout.$renderComponent(params.postForm));
    $template.prepend(this.layout.$renderComponent(params.title));
    return $template;
  },

  crumb: function (params, $template) {
    var $nav = this.navigation(params, $template);
    return $nav;
  },

  map: function (params, $template) {

    var map_container = new google.maps.Map($template.find("#map_container").get(0), {
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      disableDefaultUI: false,
      center: new google.maps.LatLng(37.0902400, -95.7128910),
      zoom: 4
    });

    var marker = {lat: 37.0902400, lng: -95.7128910};

    for (var key in params.markers) {
      marker = params.markers[key];
      var point = new google.maps.LatLng(marker.lat, marker.lng);
      var marker_obj = new google.maps.Marker({
        map: map_container,
        position: point,
        html: 'My Location!',
        title: 'My Location!'
      });
    }

    map_container.setCenter(new google.maps.LatLng(marker.lat, marker.lng));
    map_container.setZoom(4);

    if (params.bounds && params.bounds.min_lat && params.bounds.max_lng && params.bounds.min_lat && params.bounds.max_lng) {
      var bds = new google.maps.LatLngBounds(new google.maps.LatLng(params.bounds.min_lat, params.bounds.min_lng), new google.maps.LatLng(params.bounds.max_lat, params.bounds.max_lng));
    }
    if (params.bounds && params.bounds.map_center_lat && params.bounds.map_center_lng) {
      map_container.setCenter(new google.maps.LatLng(params.bounds.map_center_lat, params.bounds.map_center_lng));
      map_container.setZoom(4);
    } else {
      map_container.setCenter(new google.maps.LatLng(marker.lat, marker.lng));
      map_container.setZoom(4);
    }
    if (bds) {
      map_container.fitBounds(bds);
    }

    return $template;
  },

  chatRoom: function (params, $template) {
    var rooms = $template.find('select');

    for (var key in params.rooms) {
      var room = $('<option></option>');
      room.attr('id', 'room-' + params.rooms[key].identity);
      room.attr('value', params.rooms[key].identity);
      room.html(params.rooms[key].title + '<span> (' + core.lang.get('%1$s person', parseInt(params.rooms[key].people)) + ')</span>');

      rooms.append(room);
    }

    $template.find('.chat-header').attr('id', 'chat-header');
    $template.find('.show-room-users').attr('id', 'show-room-users');
    $template.find('.show-room-messages').attr('id', 'show-room-messages');
    $template.find('.chat-main').attr('id', 'chat-main');
    $template.find('.chat-users').attr('id', 'chat-users');
    $template.find('.chat-input').attr('id', 'chat-input');
    $template.find('.chat-main-input').attr('id', 'chat-main-input');
    $template.find('.chat-main-messages').attr('id', 'chat-main-messages');

    rooms.attr('id', 'chat-rooms');
    Chat.initChatRoom($template, params.viewer);

    return $template;
  },

  heEventCover: function (params, $template) {
    var cover_photo = params.cover_photo;

    if (params.title) {

      $template.find('.subject_title').html(params.title);
    }
    $template.find('.cover_photo_href').html(cover_photo);
    $template.find('.cover_photo_href').find('img').attr('style',
      'width: 100%; position: relative;' + $template.find('.cover_photo_href').find('img').attr('style')
    );

    return $template;
  },

  /*************************************************************************************
   } COMPONENTS                                                                     *
   *************************************************************************************/

  helper: {
    parent: null,

    $getActivePage: function () {
      return $('.ui-page-active');
    },

    milliSecsToMinuteString: function (milliSecs) {
      return Math.floor(milliSecs / 60) + ':' + (Math.floor(milliSecs % 60) < 10 ? '0' + Math.floor(milliSecs % 60) : Math.floor(milliSecs % 60));
    },

    refresh: function ($element) {


      /**
       * TODO
       * This function need to rebuild some UI widgets of JQM
       * If got $element than it will be apply only for children of the element (future)
       */

      var self = this;

      if ($element) { //todo
        $element = $($element);
        if ($element.jqmData('role')) {
          $element[$element.jqmData('role')]('refresh');
        }
      }

      if (window.refreshTimeout) {
        clearTimeout(window.refreshTimeout);
      }

      this.$getActivePage().trigger('create');

      window.refreshTimeout = setTimeout(function () {
        self.$getActivePage().trigger('create');
      }, 500); //todo


    },

    showMessage: function (message, theme, showtime) {
      if (!message)
        return;

      if (!showtime)
        showtime = 2000;

      if (!theme)
        theme = 'a';

      $.mobile.showPageLoadingMsg(theme.charAt(0), message, true);
      window.loadingHide = setTimeout(function () {
        $.mobile.hidePageLoadingMsg();
      }, showtime);
    },

    getPaginatorUrl: function (page_url, page_number) {
      if (page_number == 1) {
        var new_url = page_url.replace(/\?page\=\d*$/, '');
        new_url = new_url.replace(/\?page\=\d*\&/, '?');
        new_url = new_url.replace(/\&?page\=\d*/, '');
        new_url = new_url.replace(/page\=\d*\&?/, '');

        return new_url;
      }

      var new_url = page_url.replace(/page\=\d*/, 'page=' + page_number);
      if (new_url === page_url) {
        var delimiter = (new_url.indexOf('?') < 0) ? '?' : '&';
        new_url += delimiter + 'page=' + page_number;
      }

      return new_url;
    },

    tabHorizontalScroll: function (sliderContainer) {
      var sliderContent = sliderContainer.find('.profile_tabs');
      if (sliderContainer.width() >= sliderContent.width()) return;
      var bMouseDown = false;
      var mouseDownTime = new Date().getTime();
      var bMouseDrag = false;
      var bMouseUp = true;
      var iStartPixelsX = 0;
      var iCurrentNavbarPixelsX = 0;
      var changePixels = 0;
      var leftMostOffsetPixels = 0;
      var funcMoveNavBar = function (pixels, restore) {
        if (restore === null) {
          restore = 0;
        }
        var pixelsTo = restore * 30 + pixels;
        sliderContent.css("margin-left", pixelsTo + "px");
        if (restore != 0)
          setTimeout(function () {
            sliderContent.css("margin-left", pixels + "px");
          }, 400);
      }

      var onOrientationChange = function () {
        setTimeout(function () {
          funcMoveNavBar(0);
          iStartPixelsX = 0;
          iCurrentNavbarPixelsX = 0;
          changePixels = 0;
          leftMostOffsetPixels = sliderContent.width();
        }, 500);
      }

      if (window.addEventListener) {
        window.addEventListener("orientationchange", onOrientationChange, false);
      } else if (window.attachEvent) {
        window.attachEvent("onorientationchange", onOrientationChange);
      }

      sliderContent.bind("vmousedown", function (e) {
        mouseDownTime = new Date().getTime();
        bMouseDown = true;
        bMouseUp = false;
        iStartPixelsX = e.pageX;
      });
      sliderContent.bind("mousedown", function (e) {
        e.preventDefault();
      });

      sliderContent.bind("vmousemove", function (e) {
        if (bMouseDown && !bMouseUp) {
          var delta = e.pageX - iStartPixelsX;
          if (Math.abs(delta) > 30) {
            bMouseDrag = true;
            e.preventDefault();
            changePixels = delta + iCurrentNavbarPixelsX;
//            funcMoveNavBar(changePixels);
          }
        }
      });

      sliderContent.bind("vmouseup", function (e) {
        var tConst = 400;
        var s = (e.pageX - iStartPixelsX) * tConst / ((new Date().getTime()) - mouseDownTime);
        changePixels = s + iCurrentNavbarPixelsX;
        funcMoveNavBar(changePixels);
//        e.preventDefault();
        bMouseUp = true;
        bMouseDown = false;
        if (changePixels > 0) {
          funcMoveNavBar(0, 1);
          changePixels = 0;
          iCurrentNavbarPixelsX = 0;
        } else if (changePixels + sliderContent.width() - sliderContainer.width() <= 0) {
          changePixels = 0;
          var restore = sliderContainer.width() - sliderContent.width();
          restore = restore > 0 ? 0 : restore;
          funcMoveNavBar(restore, -1);
          iCurrentNavbarPixelsX = restore;
        } else {
          iCurrentNavbarPixelsX = changePixels;
        }

        setTimeout(function () {
          bMouseDrag = false;
        }, 150);
      });
      sliderContent.bind('vclick', function (e) {
        if (bMouseDrag) {
          e.preventDefault();
          bMouseDrag = false;
          return false;
        }
      })
    },

    openInBrowser: function ($link, getEventStr) {
      if (!$link)
        return;
      var theme = 'default';
      var url = '#';

      if ('string' == typeof $link)
        url = $link;
      else {
        url = $($link).attr('href');
      }

      if (core.device.platform.isPhoneGap()) {
        var site = phonegap.settings.get('siteinfo');
        theme = site && site.theme ? site.theme : theme;
      }

      var event = ["window.open('", url, "', '_blank', 'location=yes,theme=", theme, "'); return false;"].join('');

      if ('object' == typeof $link)
        $($link).attr('onclick', event);

      return getEventStr ? event : undefined;
    },

    createPopupMenu: function (menu, title, from, to) {
      if (!from) {
        from = 0;
      }

      if (!to) {
        to = menu.length;
      }

      if (!title) {
        title = 'No Title' // todo translate
      }
      var dialog_id = 'apptouch_local_popup_menu_' + parseInt(Math.random() * 1000000);
      var dialog_href = '#' + dialog_id;
      var $dialog = this.parent.layout.$createBase('dialog', {
        'data-rel': 'dialog',
        'id': dialog_id,
        'data-url': '#' + dialog_id,
        'data-title': title
      }, true);

      var $nav_box = $('<ul data-role="listview" data-theme="c" data-dividertheme="d"></ul>');

      for (var key = from; key < to; key++) {
        var menu_item = menu[key];
        var $menu_item = $('<li><a></a></li>');
        $menu_item.find('a').attr(menu_item.attrs);
        $menu_item.find('a').html(menu_item.label);
        $nav_box.append($menu_item);
      }

      //add cancel button
      var $menu_item = $('<li><a href="" data-rel="back" data-transition="flip" data-direction="reverse"></a></li>');
      $menu_item.find('a').attr('class', 'cancel');
      $menu_item.find('a').html(core.lang.get('Cancel')); //todo use translate

      $nav_box.append($menu_item);

      $dialog.children("div:jqmData(role='content')").append($nav_box);
      return dialog_href;
    },

    $objectToHtmlEl: function (object) {
      var $element = $('<' + object.name + '/>').attr(object.attrs).html(object.text);
      if (object.html)
        for (var i = 0; i < object.html.length; i++) {
          var child = object.html[i];
          $element.append(this.$objectToHtmlEl(child));
        }
      return $element;
    },

    initExternalUrls: function ($el) {
      var links = $el.find('a');
      var linksCount = links.length;
      for (var i = 0; i < linksCount; i++) {
        var link = links[i];
        if (core.helper.isExternalUrl(link.href)) {
          this.openInBrowser(link);
        }
      };
    }
  }
}


function Rate(id, type, uid, options, $template) {
  this.id = id;
  this.type = type;
  this.uid = uid;
  this.can_rate = (options && options.can_rate != undefined) ? options.can_rate : true;
  this.error_msg = (options && options.error_msg) ? options.error_msg : '';
  this.$stars_cont = $template;

  this.construct();
}

Rate.prototype =
{
  construct: function () {
    var self = this;

    this.disabled_rate = false;
    this.$stars = this.$stars_cont.find('.rate_star');
    this.$stars.mouseover(function () {
      self.$stars.removeClass('rate');

      $star = $(this);
      var $previous = $star.prevAll();
      if ($previous) {
        $previous.addClass('rate');
      }
      $star.addClass('rate');
    })
      .mouseout(function () {
        self.$stars.removeClass('rate');
      })
      .bind('vclick', function () {
        if (this.disabled_rate) {
          return false;
        }

        if (!self.can_rate) {
          he_show_message(self.error_msg, 'error');
          return;
        }

        $star = $(this);
        var score = $star.attr('id').substr(10);
        self.rate(score);
      });
  },

  rate: function (score) {
    var self = this;

    $.mobile.showPageLoadingMsg();

    this.disabled_rate = true;

    $.ajax({
      url: this.rate_url,
      data: {format: 'json', type: this.type, id: this.id, score: score, noCache: Math.random()},
      success: function (data) {
        if (data && data.result) {
          self.setScore(data);
          UIComponent.helper.showMessage(data.message);
        } else {
          UIComponent.helper.showMessage(data.message, 'e');
        }

        self.disabled_rate = false;
      }
    });
  },

  setScore: function (rate_info) {
    this.$stars.removeClass('rated')
      .removeClass('half_rated')
      .removeClass('no_rate')
      .removeClass('quarter_rated')
      .removeClass('fquarter_rated');

    this.$stars_cont.find('.item_score').html(rate_info.item_score + '/' + rate_info.maxRate);
    this.$stars_cont.find('.item_votes').html(rate_info.rate_count);

    if (this.$stars_cont.find('.item_voters')) {
      this.$stars_cont.find('.item_voters').text(rate_info.label);
    }

    for (var i = 0; i < this.$stars.length; i++) {
      if ((i + 0.125) > rate_info.item_score) {
        $(this.$stars[i]).addClass('no_rate');
      } else if ((i + 0.375) > rate_info.item_score) {
        $(this.$stars[i]).addClass('quarter_rated');
      } else if ((i + 0.625) > rate_info.item_score) {
        $(this.$stars[i]).addClass('half_rated');
      } else if ((i + 0.875) > rate_info.item_score) {
        $(this.$stars[i]).addClass('fquarter_rated');
      } else {
        $(this.$stars[i]).addClass('rated');
      }
    }
  }
};