/**
 * Created by Hire-Expert LLC.
 * Author: Ulan
 * Date: 28.08.12
 * Time: 18:14
 * initializer.js
 */

Initializer = {
    init: function (page) {
        var methodName = 'init';
        for (var module in this) {
            if ('object' == typeof this[module] && 'function' == typeof this[module][methodName]) {
                this[module][methodName](page);
            }
        }
    },
    core: {
        boardIndex: function (page, jQPageData, $page) {
            var $lang = $page.find('#language');
            if ($lang.length) {
                var FnOnChange = function () {
                    $lang.closest('.ui-btn').find('.ui-btn-text span').text($lang.val().toUpperCase())
                    if (this.form) {
                        if (core.device.platform.isPhoneGap()) {
                            if (confirm(core.lang.get('To apply changes you should restart application'))) {
                                $.post(this.form.action,
                                    $(this.form).serialize(),
                                    function () {
                                        window.location.href = localStorage.getItem('index_href');
                                    });
                            } else return false;
                        } else
                            this.form.submit();
                    }
                }
                FnOnChange();
                $page.find('#language').change(FnOnChange);
            }
        }
    },
    activity: {
        init: function (response) {
            var self = this;
            if (response.page && response.page.info.viewer.id) {
                $(document).bind('usersessionstart', function () {
                    $('.badge_css').remove();
                });
                this.helper.createNotificationPopup();
                this.helper.notificationUpdater();
            }
        },
        indexShare: function (page, jQPageData, $page) {
            ShareForm.$form = $page.find('.global_form');
            ShareForm.serviceRequestUrl = $page.find('.wallShareMenu').data('request-url');
            ShareForm.services = $page.find('.wallShareMenu').data('services');
            ShareForm.init();
        },

        notificationsIndex: function (page, jQPageData, $page) {
            var self = this;
            var $switcher = $page.find('.switcher');
            $page.find('ul.notifications_leftside').children('li').bind('vclick', function () {
                var $this = $(this);
                $.ajax({
                    url: page.info.notifications.markreadAction,
                    data: {
                        'actionid': $this.attr('actionid'),
                        'format': 'json'
                    },
                    success: function (response) {
                        $this.attr('data-theme', 'c').closest('ul.ui-listview').listview().listview('refresh');
                        if (response.viewerId) {
                            self.helper.updateNotifier(response);
                        }
                    }
                })
            });
            $page.find('ui-listview-filter.ui-bar-a').show();
            $switcher.find('.showNotifications').bind('vclick', function (e) {
                $switcher.removeClass('ui-btn-active');
                $('.notifications_leftside').prev('.ui-listview-filter').show();
                $('.notifications_rightside').prev('.ui-listview-filter').hide();
                $(this).addClass('ui-btn-active');
                $('.notifications_leftside').show();
                $('.notifications_rightside').hide();
            })

            $switcher.find('.showRequests').bind('vclick', function (e) {
                $switcher.removeClass('ui-btn-active');
                $('.notifications_rightside').prev('.ui-listview-filter').show();
                $('.notifications_leftside').prev('.ui-listview-filter').hide();
                $(this).addClass('ui-btn-active');
                $('.notifications_rightside').show();
                $('.notifications_leftside').hide();
            });

            $page.find('.notifications_leftside.mark-all-read').bind('vclick', function () {
                self.helper.updateNotifier({notificationCount: '0'});
            });

            if (page.info.params.show)
                $switcher.find('.showRequests').trigger('vclick');
            else
                $switcher.find('.showNotifications').trigger('vclick');
        },

        helper: {

            notificationPopupId: 'notificationPopup',
            updateCheckTime: false,
            notificationWait: 30000,
            $notifierGroup: $('#core_board_index_rewrite').find('[data-role="header"]').find('.notifier-group'),
            interval: -1,
            $getNotifierGroup: function () {
                if (this.$notifierGroup.length == 0) {
                    this.$notifierGroup = $('#core_board_index_rewrite').find('[data-role="header"]').find('.notifier-group')
                }
                return this.$notifierGroup;
            },

            createNotificationPopup: function () {
                var self = this;
                var $popup = $('<div />').attr({
                    'id': self.notificationPopupId,
                    'data-role': 'popup',
                    'data-overlay-theme': 'a'
                }).append($('<a />').html('Close').attr({
                        'href': '#',
                        'data-rel': 'back',
                        'data-role': 'button',
                        'data-theme': 'a',
                        'data-icon': 'delete',
                        'data-iconpos': 'notext',
                        'class': 'ui-btn-right'
                    }).button()).append($('<h3 />')).append($('<p />')).popup();

                var $prevBtn = $('<a/>').attr({
                    'data-role': 'button',
                    'data-icon': 'arrow-l',
                    'class': 'previous'
                }).html('Prev');
                var $nextBtn = $('<a/>').attr({
                    'data-role': 'button',
                    'data-icon': 'arrow-r',
                    'data-iconpos': 'right',
                    'class': 'next'
                }).html('Next');
                var $btnGroup = $('<div />').attr({
                    'data-role': 'controlgroup',
                    'data-mini': true,
                    'data-type': 'horizontal',
                    'class': 'paginator'
                }).css({'text-align': 'center'}).append($prevBtn, $nextBtn);
                $popup.append($btnGroup);
                $prevBtn.button();
                $nextBtn.button();
                $btnGroup.controlgroup();
                $btnGroup.hide();
            },

            notificationUpdater: function () {
                var self = this;
                this.updateCheckTime = 0;
                window.clearInterval(this.interval);
                var fnInterval = function () {
                    $.ajax({
                            type: 'get',
                            url: core.baseUrl + 'activity/notifications/update',
                            data: {
                                format: 'json',
                                updateCheckTime: self.updateCheckTime
                            },
                            success: function (response) {
                                if (response.viewerId) {
                                    self.updateNotifier(response);
                                    self.showNotificationPopup(response);
                                }
                                else
                                    window.clearInterval(self.interval);
                            },
                            error: function (response) {
                                if (response && response.status == 403) {
                                    window.clearInterval(self.interval);
                                }
                            },
                            dataType: 'json'
                        }
                    );
                };
                setTimeout(function () {
                    fnInterval()
                }, 1000);
              this.interval = window.setInterval(fnInterval, this.notificationWait);
            },

            showNotificationPopup: function (response) {
                var self = this;
                this.updateCheckTime = 1;
                $('div.ui-page[module="activity"][controller="notifications"][action="index"]').not('.ui-page-active').remove();
                var latestUpdates = response.latestUpdates;
                var hideTime = 10000;
                var $popup = $('#' + this.notificationPopupId);
                $popup.find('h3').html(response.text);
                if (latestUpdates && latestUpdates.length) {
                    core.helper.beep([
                        core.baseUrl + 'application/modules/Apptouch/externals/audios/DingLing.wav',
                        core.baseUrl + 'application/modules/Apptouch/externals/audios/DingLing.mp3'
                    ]);
                    var i = 0;
                    $popup.data('latestUpdates', latestUpdates);
                    if (latestUpdates.length > 1) {
                        var $paginator = $popup.find('.paginator').show();
                        window.nUIndex = 0;
                        $paginator.find('.ui-btn').bind('vclick', function () {
                            var $this = $(this);
                            var isNext = $this.hasClass('next');
                            var incrementor = isNext ? 1 : -1;
                            var index = window.nUIndex + incrementor;
                            var $nPopup = $('#' + self.notificationPopupId);
                            var contents = $nPopup.data('latestUpdates');
                            if (index >= contents.length)
                                index = contents.length - 1;

                            if (index < 0)
                                index = 0;
                            window.nUIndex = index;

                            var content = contents[index];
                            if (content)
                                $nPopup.children('p').html(content.content);

                            $nPopup.popup();
                        })
                    } else {
                        $popup.find('.paginator').hide();
                    }
//          for(i in latestUpdates)
                    $popup.children('p').html(latestUpdates[i].content);
                    $popup.popup().popup('open', {'transition': 'slidedown'});
                }
                var hideTimeOut = window.setTimeout(function () {
                    $popup.popup().popup('close');
                    self.updateCheckTime = 0;
                }, hideTime);
                $popup.unbind('vclick').bind('vclick', function () {
                    window.clearTimeout(hideTimeOut);
                });
            },
            updateNotifier: function (data) {
                var $notificationsBtn = this.$getNotifierGroup().find('.notifications-btn');
                var $messagessBtn = this.$getNotifierGroup().find('.messages-btn');
                var $requestsBtn = this.$getNotifierGroup().find('.requests-btn');
                var $storeBtn = this.$getNotifierGroup().find('.store-btn');

                $notificationsBtn.find('.ui-btn-text').html(data.notificationCount);
                core.helper.setIconBadgeNumber('updates', data.notificationCount);
                $messagessBtn.find('.ui-btn-text').html(data.messagesCount);
                core.helper.setIconBadgeNumber('messages', data.messagesCount);
                $requestsBtn.find('.ui-btn-text').html(data.requestCount);
              $storeBtn.find('.ui-btn-text').html(data.productCount);


                if (data.notificationCount == 0) {
                    $notificationsBtn.find('.ui-btn-text').css('opacity', 0);
                } else {
                  $notificationsBtn.find('.ui-btn-text').css('opacity', 1);
                }
                if (data.messagesCount == 0) {
                  $messagessBtn.find('.ui-btn-text').css('opacity', 0);
                } else {
                  $messagessBtn.find('.ui-btn-text').css('opacity', 1);
                }
                if (data.requestCount == 0) {
                  $requestsBtn.find('.ui-btn-text').css('opacity', 0);
                } else {
                  $requestsBtn.find('.ui-btn-text').css('opacity', 1);
                }
                if (data.productCount == 0) {
                  $storeBtn.find('.ui-btn-text').css('opacity', 0);
                } else {
                  $storeBtn.find('.ui-btn-text').css('opacity', 1);
                }

                $notificationsBtn.button();
                $messagessBtn.button();
                $requestsBtn.button();

                this.$getNotifierGroup().controlgroup('refresh');
            }
        }
    },

    album: {
        indexUpload: function (page, jQPageData, $page) {
            var fieldToggleGroup = ['#title-wrapper', '#category_id-wrapper', '#description-wrapper', '#search-wrapper',
                '#auth_view-wrapper', '#auth_comment-wrapper', '#auth_tag-wrapper'];
            fieldToggleGroup = $page.find('form').find(fieldToggleGroup.join(','));
            var $albumEl = $page.find('#album');

            $albumEl.removeAttr('onchange').bind('change', function (event) {
                var $this = $(this);
                if ($this.val() == 0)
                    fieldToggleGroup.show();
                else
                    fieldToggleGroup.hide();
            });
            if (page.info.params['album_id']) {
                $albumEl.val(page.info.params['album_id']).trigger('change');
            }/* else {
                $page.find('#submit-wrapper').css('display', 'none');
            }*/
            $page.find('#photosFakeButton').find('span.ui-btn-inner').css('background-color', '#afafaf');
        }
    },

    advgroup: {
        pollCreate: function (page, jQPageData, $page) {
            var $form = $page.find('.global_form');
            var $pollOptionInputElement = $form.find('#pollOptionInput-element').find('input');
            $form.find('#addOption').bind('vclick', function (e) {
                $pollOptionInputElement.clone().insertAfter($pollOptionInputElement);
            });
        },

        pollView: function (page, jQPageData, $page) {
            var $aqSwitcher = $page.find('.aqSwitcher');
            var $questions = $page.find('form.poll_form');
            var $answers = $page.find('.poll_view_single');

            $aqSwitcher.unbind().bind('vclick', function (e) {
                var $this = $(this);
                var uiIcon = $this.find('.ui-icon');
                if ($this.hasClass('showQ')) {
                    $answers.hide();
                    $questions.show();
                    $this.removeClass('showQ');
                    uiIcon.removeClass('ui-icon-question');
                    uiIcon.addClass('ui-icon-page');
                    $this.find('.ui-btn-text').html(core.lang.get('Show Results'));
                } else {
                    $answers.show();
                    $questions.hide();
                    $this.addClass('showQ');
                    uiIcon.removeClass('ui-icon-page');
                    uiIcon.addClass('ui-icon-question');
                    $this.find('.ui-btn-text').html(core.lang.get('Show Questions'));
                }
            });
        }
    },
    credit: {
        indexManage: function (page, jQPageData, $page) {
            var $form = $page.find('#send_credit_form');
            var $list = $('<ul />').attr({'data-role': 'listview', 'data-inset': true, 'class': 'message-autosuggest'});
            var $searchField = $form.find('#username');
            $list.insertAfter($searchField);
            $list.listview().listview('refresh');
            $searchField.autocomplete({
                method: 'POST', // allows POST as well
                icon: 'arrow-r', // option to specify icon
                target: $list, // the listview to receive results
                source: page.info.credit.suggestUrl, // URL return JSON data
                getRequestData: function ($field) {
                    return {
                        'text': $field.val()
                    };
                },

                // optional callback function fires upon result selection
                onPush: function (e, value) {
                    $searchField.val(value.label);
                    $form.find('#user_id').val(value.id);
                    $list.empty();

                },

                injectChoice: function (index, value) {
                    var $a = $('<a />').attr({
                        'id': value.guid
                    });
                    $a.html(value.label).prepend($(value.photo).attr('class', 'ac-icon ui-li-icon'));
                    var $li = $('<li />').append($a);
                    return $li;
                },
//          link: '', // link to be attached to each result
                minLength: 1, // minimum length of search string
                transition: 'fade', // page transition, default is fade
                matchFromStart: true // search from start, or anywhere in the string

            });
        },
        storeIndex: function (page, jQPageData, $page) {
            $('a#cart-checkout-confirm').bind('vclick', function () {

                $.ajax({
                    'url': page.info.pay_params.pay_url,
                    'data': {
                        'ukey': page.info.pay_params.order_ukey,
                        'format': 'json'
                    },
                    'dataFormat': 'json',
                    'method': 'post',
                    'success': function (response) {
                        var $form = $("<form>", {
                            "id": "credit-checkout-success-form",
                            "style": "display:none;",
                            "action": page.info.pay_params.return_url
                        });

                        for (var key in response.data) {
                            $form.append(
                                $("<input>", {
                                    "typ": "hidden",
                                    'name': key,
                                    'value': response.data[key]
                                })
                            );
                        }
                        $('a#cart-checkout-confirm').parent().append($form);
                        $form.submit();
                    },
                    'error': function (error) {
                        console.log(error);
                        UIComponent.helper.showMessage(error.message, 'e', 2000);
                    }
                });
            });
        }
    },

    event: {
        memberInvite: function (page, jQPageData, $page) {
            Initializer.helper.groupEventMemberInvite($page.find('form'));
        }
    },

    group: {
        memberInvite: function (page, jQPageData, $page) {
            Initializer.helper.groupEventMemberInvite($page.find('form'));
        }
    },
    grandopening: {
        emailAdd: function (page, jQPageData, $page) {
            clearInterval(window._goci);
            var gt = $page.find('.go-time');
            window._glt = page.info.launchTime * 1000;
            window._gst = new Date().getTime();
            window._gd = gt.find('.go-days');
            window._gh = gt.find('.go-hours');
            window._gm = gt.find('.go-minutes');
            window._gs = gt.find('.go-seconds');

            window._goci = setInterval(function () {
                var ct = window._glt - new Date().getTime() + window._gst;
                window._gs.html(((100 + Math.floor(ct / 1000) % 60) + '').substr(1));
                window._gm.html(((100 + Math.floor(ct / 60000) % 60) + '').substr(1));
                window._gh.html(((100 + Math.floor(ct / 3600000) % 24) + '').substr(1));
                window._gd.html(((1000 + Math.floor(ct / 86400000)) + '').substr(1));
            }, 1000);
        }
    },
    hecore: {
        indexContacts: function (page, jQPageData, $page) {
            var $form = $page.find('form');
            var $btnGroup = $page.find('div[data-role="controlgroup"]');
            var $contactsList = $form.find('.ui-listview');
            $btnGroup.find('a').bind('vclick', function () {
                $btnGroup.find('a').find('.ui-icon').hide();
                $(this).find('.ui-icon').show();
                $btnGroup.find('a').removeClass('active');
                $(this).addClass('active');
                if ($(this).hasClass('showSelected')) {

                    $form.find('.form-options-wrapper').find('li label').not('.ui-checkbox-on').closest('li').hide();
                } else {
                    $form.find('.form-options-wrapper').find('li').show();
                }
                $contactsList.listview('refresh');
            });
            $btnGroup.find('.showAll').find('.ui-icon').trigger('vclick');
            /*      for(var  subject_id in page.info.hecontacts.photos){
             var photourl = page.info.hecontacts.photos[subject_id];
             $form.find('#uids-' + subject_id).closest('li').prepend($('<img class="ui-li-icon">').attr('src', photourl));
             }*/

            $contactsList.listview('refresh');
            setTimeout(function () {
                $contactsList.find("input[type='checkbox']").checkboxradio().checkboxradio("refresh");
            }, 1000);
//      $form.find('.form-options-wrapper').find('li').bind('vclick', function(){
//        var $this = $(this);
//        if(!$this.hasClass('ui-checkbox-on')){
//          $this.closest('li').addClass('selected');
//        } else {
//          $this.closest('li').removeClass('selected');
//        }
//        $btnGroup.find('.active.showSelected').trigger('vclick');
//
//      });

            if ($form.length > 0) {

                $form.find('#all').bind('vclick', function (event) {

                    var $this = $page.find(this);
                    var el = $page.find(event.target);

                    $form.find('[type=checkbox]').not('#all').attr('checked', this.checked).checkboxradio('refresh');
                    $btnGroup.find('.active.showSelected').trigger('vclick');

                });

            }
        }
    },

    hegift: {
        indexSend: function (page, jQPageData, $page) {

            if ($page.find('#gift_amount').length) {
                var amount = parseInt($page.find('#gift_amount').html());
                $page.find('input[type=checkbox].user_ids').bind('vclick', function () {
                    if ($(this).attr('checked')) {
                        if (amount > 0) {
                            $page.find('#gift_amount').html(amount - 1);
                        } else {
                            $(this).removeAttr('checked');
                        }
                    } else {
                        $page.find('#gift_amount').html(amount + 1)
                    }
                });
            }

            if ($page.find('#gift_price').length) {
                var price = parseInt($page.find('#gift_price').html());
                var credits = parseInt($page.find('#current_balance').html());
                $page.find('#gift_price').html(0);

                $page.find('input[type=checkbox].user_ids').bind('vclick', function () {
                    var cost = parseInt($page.find('#gift_price').html());
                    if ($(this).attr('checked')) {
                        if (cost + price <= credits) {
                            $page.find('#gift_price').html(cost + price);
                        } else {
                            $(this).removeAttr('checked');
                        }
                    } else {
                        $page.find('#gift_price').html(cost - price)
                    }
                });
            }


            $page.find('#send-gift').bind('vclick', function () {
                var ids = '';
                $page.find('.user_ids:checked').each(function () {
                    ids += $(this).val() + ',';
                });
                $page.find('#user-ids').val(ids);

                $page.find('#send-gift-form').submit();
            });
        }
    },

    hebadge: {
        indexIndex: function (page, jQPageData, $page) {
            $page.find('#badges-show-popular').bind('vclick', function (event) {
                $page.find('.badges-tab-button').removeClass('ui-btn-active');
                $page.find('#badges-show-popular').addClass('ui-btn-active');

                $page.find('.badges-tab').css('display', 'none');
                $page.find('#badges-popular').css('display', 'block');
            });
            $page.find('#badges-show-friends').bind('vclick', function (event) {
                $page.find('.badges-tab-button').removeClass('ui-btn-active');
                $page.find('#badges-show-friends').addClass('ui-btn-active');

                $page.find('.badges-tab').css('display', 'none');
                $page.find('#badges-friends').css('display', 'block');
            });
            $page.find('#badges-show-recent').bind('vclick', function (event) {
                $page.find('.badges-tab-button').removeClass('ui-btn-active');
                $page.find('#badges-show-recent').addClass('ui-btn-active');

                $page.find('.badges-tab').css('display', 'none');
                $page.find('#badges-recent').css('display', 'block');
            });
        },

        indexView: function (page, jQPageData, $page) {
            var $link = $page.find('a.approved');
            $link.bind('vclick', function (event) {

                jQuery.post(
                    'badges/index/approved',
                    {
                        'approved': ($link.hasClass('active')) ? 0 : 1,
                        'badge_id': page.info.params.id,
                        'format': 'json'
                    },
                    function (data, textStatus, jqXHR) {
                        if (data.status) {
                            $link.html(data.lang);
                        } else {
                            $link.removeClass('active');
                        }
                    },
                    'json'
                );

                if ($link.hasClass('active'))
                    $link.removeClass('active');
                else
                    $link.addClass('active');
            });

            if (page.info.params.page) {

            } else {
                $page.find('.component-paginator').css('display', 'none');
            }

            $page.find('#badge-show-requirements').bind('vclick', function (event) {
                $page.find('.badge-tab-button').removeClass('ui-btn-active');
                $page.find('#badge-show-requirements').addClass('ui-btn-active');

                $page.find('.badge-tab').css('display', 'none');
                $page.find('#badge-requirements').css('display', 'block');
                $page.find('.component-paginator').css('display', 'none');
            });
            $page.find('#badge-show-learn').bind('vclick', function (event) {
                $page.find('.badge-tab-button').removeClass('ui-btn-active');
                $page.find('#badge-show-learn').addClass('ui-btn-active');

                $page.find('.badge-tab').css('display', 'none');
                $page.find('#badge-learn').css('display', 'block');
                $page.find('.component-paginator').css('display', 'none');
            });
            $page.find('#badge-show-members').bind('vclick', function (event) {
                $page.find('.badge-tab-button').removeClass('ui-btn-active');
                $page.find('#badge-show-members').addClass('ui-btn-active');

                $page.find('.badge-tab').css('display', 'none');
                $page.find('#badge-members').css('display', 'block');
                $page.find('.component-paginator').css('display', 'block');
            });
        },

        indexManage: function (page, jQPageData, $page) {
            var $link = $page.find('a.approved');
            $link.each(function (item) {
                var tmp = $($link[item]);
                tmp.bind('vclick', function () {

                    jQuery.post(
                        'badges/index/approved',
                        {
                            'approved': (tmp.hasClass('active')) ? 0 : 1,
                            'badge_id': tmp.attr('id'),
                            'format': 'json'
                        },
                        function (data, textStatus, jqXHR) {
                            if (data.status) {
                                tmp.html(data.lang);
                            } else {
                                tmp.removeClass('active');
                            }
                        },
                        'json'
                    );

                    if (tmp.hasClass('active'))
                        tmp.removeClass('active');
                    else
                        tmp.addClass('active');
                });
            });

        }
    },

    inviter: {
        indexIndex: function (page, jQPageData, $page) {
            //FB load
            core.helper.loadScript('http://connect.facebook.net/en_US/all.js', function () {
                FB.init({
                    appId: page.info.fb_app_id,
                    status: true,
                    cookie: true,
                    xfbml: true
                });
            });
            //FB load

            // write contacts tab
            $page.find('#submit_contacts').bind('vclick', function () {
                var $recipients = $page.find('#recipients').attr('value');
                var $message = $page.find('#message').attr('value');
                var data = {};

                if ($recipients.length == 0) {
                    UIComponent.helper.showMessage('INVITER_Failed!, please check your contacts and try again.', 'e', 1000);
                    $page.find('#recipients').focus();
                }
                else {
                    jQuery.post(
                        'inviter/index/write-contacts',
                        {
                            'recipients': $recipients,
                            'message': $message,
                            'format': 'json'
                        },
                        function (data, textStatus, jqXHR) {
                            if (!data) return;
                            switch (data.status) {
                                case 0:
                                    UIComponent.helper.showMessage(data.message, 'e', 2000);
                                    break;
                                case 1:
                                    UIComponent.helper.showMessage(data.message, 'a', 2000);
                                    $('recipients').value = '';
                                    break;
                                case 2:
                                    UIComponent.helper.showMessage(data.message, 'a', 2000);
                                    break;
                            }
                        }
                    );
                }

            });
            // write contacts tab

            $page.find('.component-form').attr('style', 'display:none;');

            $page.find('#inviter-show-import').bind('vclick', function () {
                $page.find('.component-inviter ').attr('style', 'display:block;');
                $page.find('.component-form').attr('style', 'display:none;');

                $page.find('#inviter-show-import').addClass('ui-btn-active');
                $page.find('#inviter-show-write').removeClass('ui-btn-active');
                //ui-btn-active
            });
            $page.find('#inviter-show-write').bind('vclick', function () {
                $page.find('.component-form').attr('style', 'display:block;');
                $page.find('.component-inviter ').attr('style', 'display:none;')

                $page.find('#inviter-show-import').removeClass('ui-btn-active');
                $page.find('#inviter-show-write').addClass('ui-btn-active');
            });
        },
        // all/checked contacts
        checkSelected: function (flag, $page) {
            $page.find('.inviter-contact').each(function () {
                var checkbox = $page.find(this);
                var $parent = checkbox.parent().parent();
                if (flag == 1) {
                    if (checkbox.attr('checked'))
                        $parent.attr('style', 'display:block;')
                    else
                        $parent.attr('style', 'display:none;')
                } else {
                    $parent.attr('style', 'display:block;')
                }
            });
        },
        // all/checked contacts

        // send invites
        sendInvites: function ($page, provider) {
            var ids = '';
            $page.find('.inviter-contact').each(function () {
                if ($(this).attr('checked')) {
                    ids += $(this).attr('id') + '&'; // checked contacts
                }
            });
            if (ids != '') {
                jQuery.post(
                    'inviter/index/invitationsend',
                    {
                        'contacts': ids,
                        'message': $page.find('#invite_msg').html(),
                        'provider': provider,
                        'format': 'json'
                    },
                    function (data, textStatus, jqXHR) {
                        if (data.status) {
                            UIComponent.helper.showMessage(data.msg, '', 2000);
                            core.location.href = data.url;
                        } else {
                            UIComponent.helper.showMessage(data.msg, 'e', 2000);
                        }

                    },
                    'json'
                );
            } else {
                UIComponent.helper.showMessage('No contacts selected.', 'e', 2000);
            }
        },
        // send invites

        indexContacts: function (page, jQPageData, $page) {
            var $this = this;

            // send invites
            $page.find('#send_invites').bind('vclick', function () {
                $this.sendInvites($page, page.layout.content[0].params.provider);
            });
            // send invites

            //search contacts by name
            $page.find('#search_filter').bind('keyup', function () {
                var filter = $page.find('#search_filter').attr('value');
                $page.find('.list_item').each(function () {
                    var $item = $(this);
                    var name = $item.find('.ui-btn-text').html();
                    if (name.search(filter, 'i') == -1) {
                        $item.attr('style', 'display: none;');
                    } else {
                        $item.attr('style', 'display: block;');
                    }
                });
            });
            //search contacts by name

            //all and selected buttons
            $page.find('a#all').bind('vclick', function () {
                $page.find('#all').toggleClass('active');
                $page.find('#selected').toggleClass('active');
                $this.checkSelected(0, $page);
            });
            $page.find('#selected').bind('vclick', function () {
                $page.find('#all').toggleClass('active');
                $page.find('#selected').toggleClass('active');
                $this.checkSelected(1, $page);
            });
            //all and selected buttons

            // selected contacts increment/decrement
            $page.find('.inviter-contact').bind('vclick', function () {
                var t = $page.find('#selected_contacts_count').html() * 1;
                if ($(this).attr('checked')) {
                    t++;
                } else {
                    t--;
                }
                $page.find('#selected_contacts_count').html(t);
            });
            // selected contacts increment/decrement

            // check/uncheck all contacts
            $page.find('#check_all').bind('vclick', function () {
                var checked_main = $page.find('#check_all').attr('checked');
                $page.find('.inviter-contact').each(function () {
                    var $elem = $page.find(this);
                    var id = $elem.attr('id');

                    $elem = $page.find('[for="' + id + '"]');

                    if (checked_main) {
                        if (( $elem.hasClass('ui-checkbox-off') && $elem.hasClass('ui-checkbox-off') ))
                            $elem.trigger('vclick');
                    } else {
                        if (( $elem.hasClass('ui-checkbox-on') && $elem.hasClass('ui-checkbox-on') ))
                            $elem.trigger('vclick');
                    }
                });

            });
            // check/uncheck all contacts
        }
    },

    messages: {

        messagesCompose: function (page, jQPageData, $page) {
            var $list = $('<ul />').attr({'data-role': 'listview', 'data-inset': true, 'class': 'message-autosuggest'});
            var $selected = $('<ul />').attr({
                'data-role': 'listview',
                'data-inset': true,
                'data-theme': 'b',
                'data-split-icon': 'delete',
//        'data-mini': true,
                'class': 'message-autosuggest'
            });
            var $selectionTpl = $('<li />').append($('<a />')).append($('<input type="hidden" name="toValues[]" />')).append($('<a href="javascript://" />'));
            var $searchField = $page.find("#to");
            $page.find('#toValues').remove();
            $selected.insertAfter($page.find('#toValues-label')).listview();
            $list.insertAfter($searchField);
            $list.listview().listview('refresh');
            var onPush = function (e, value) {
                var id = 'selected-' + value.guid;
                if ($selected.find('li#' + id).length > 0) {
                    return;
                }
                //          var v_arr = $toValues.val().split(',');
                //          v_arr.push(value.id);
                //          if(!v_arr)
                //            v_arr = [];
                //          $toValues.val(v_arr.join());
                var $li = $selectionTpl.clone();
                $li.attr({'id': id});
                $li.data('user', value);
                $li.find('a').first().html(value.label).attr('href', value.url);
                $li.find('input').val(value.id);
                $li.find('a').last().bind('vclick', function () {
                    //            var $cli = $(this).closest('li');
                    //            var data = $(this).closest('li').data('user');
                    //            $toValues.val($toValues.val().split(',').splice( $.inArray(data.id + "", $toValues.val().split(',')), 1).join());
                    $(this).closest('li').remove();
                    $selected.listview('refresh');

                });
                $selected.append($li);
                $selected.listview('refresh');
                if ($selected.find('li').length >= page.info.messages.maxRecipients) {
                    $page.find('#to').disabled = true;
                }
                $searchField.val('');
                $list.empty();
            };
            var injectChoice = function (index, value) {
                var $a = $('<a />').attr({
                    'id': value.guid
                });
                $a.html(value.label).prepend($(value.photo).attr('class', 'ac-icon ui-li-icon'));
                var $li = $('<li />').append($a);
                return $li;
            };


            $searchField.autocomplete({
                method: 'POST', // allows POST as well
                icon: 'arrow-r', // option to specify icon
                target: $list, // the listview to receive results
                source: page.info.messages.suggestUrl, // URL return JSON data
                getRequestData: function ($field) {
                    return {
                        'value': $field.val()
                    };
                },

                // optional callback function fires upon result selection
                onPush: onPush,

                injectChoice: injectChoice,
//          link: '', // link to be attached to each result
                minLength: 1, // minimum length of search string
                transition: 'fade', // page transition, default is fade
                matchFromStart: true // search from start, or anywhere in the string
            });
            if (page.info.messages.to) {
                setTimeout(function () {
                    onPush(null, page.info.messages.to);
                }, 100);

            }


        }
    },

    page: {
        pagePhoto: function (page, jQPageData, $page) {
            $page.find('#Filedata').removeAttr('onchange').bind('change', function (event) {
                $('#EditPagePhoto').submit();
            });
        },
        packageProcess: function (page, jQPageData, $page) {
            $page.find('form').submit();
        },
        indexCreate: function (page, jQPageData, $page) {
            var category_set = $page.find('#category-wrapper');
            var category = $page.find('#category');

            var isMultiMode = (page.info.isMultiMode > 1);

            if (!isMultiMode) {
                category_set.hide();
                category.val(1);
                return;
            }

            $page.find('#0_0_1-wrapper').hide();
            isVis = false;

            category.change(function () {
                var val = this.val();
                var set = page.info.setInfoJSON ? $.parseJSON(page.info.setInfoJSON) : 1;
                if (this.val() == 0) {
                    $page.find('#0_0_1-wrapper').hide();
                    isVis = false;
                    return;
                }

                if (!isVis) {
                    $page.find('#0_0_1-wrapper').show();
                    isVis = true;
                }

                var defaultOption = $('<option></option>');
                defaultOption.attr('value', 1);
                defaultOption.html('Default');

                $page.find('#fields-0_0_1').empty().append(defaultOption);

                $.each(set[this.value]['items'], function (i, item) {
                    var option = $('<option></option>');
                    option.attr('label', item['caption']);
                    option.val(i);

                    option.html(item['caption']);
                    $page.find('#fields-0_0_1').append(option);
                });
            });
        },
        pageEdit: function (page, jQPageData, $page) {
            var category_set = $page.find('#category-wrapper');
            var category = $page.find('#category');

            var isMultiMode = (page.info.isMultiMode > 1);

            if (!isMultiMode) {
                category_set.hide();
                category.val(1);
                return;
            }

            if (category.val() == 0)
                $page.find('#0_0_1-wrapper').hide();

            category.change(function () {
                var set = page.info.setInfoJSON ? $.parseJSON(page.info.setInfoJSON) : $.parseJSON(page.info.setInfoJSON);
                if (category.val() == 0)
                    $page.find('#0_0_1-wrapper').hide();
                else
                    $page.find('#0_0_1-wrapper').show();
                var defaultOption = $('<option></option>');
                defaultOption.attr('value', 1);
                defaultOption.html('Default');
                $page.find('#fields-0_0_1').empty().append(defaultOption);
                var o = this;
                $.each(set[this.value]['items'], function (i, item) {
                    var option = $('<option></option>');
                    option.attr('label', item['caption'])
                    option.val(i);
                    option.html(item['caption']);
                    $page.find('#fields-0_0_1').append(option);
                });
            });
        }
    },

    pagealbum: {
        indexUpload: function (page, jQPageData, $page) {
            $page.find('#album').removeAttr('onchange').bind('change', function (event) {
                var album = $("#album");
                var name = $("#title-wrapper");
                var description = $("#description-wrapper");
                var tags = $("#tags-wrapper");

                if (album.val() == 0) {
                    name.css('display', "block");
                    description.css('display', "block");
                    tags.css('display', "block");
                } else {
                    name.css('display', "none");
                    description.css('display', "none");
                    tags.css('display', "none");
                }
            });
        }
    },

    pagevideo: {
        indexCreate: function (page, jQPageData, $page) {
            $("form #video_file-wrapper").css('display', 'none');
            $("form #video_url-wrapper").css('display', 'none');

            $page.find("#video_type-wrapper").removeAttr('onchange').bind('change', function (e) {

                var type = $("#video_type");
                var file = $("#video_file-wrapper");
                var url = $("#video_url-wrapper");

                if (type.val() == 1 || type.val() == 2) {
                    url.css('display', 'block');
                    file.css('display', 'none');
                }

                if (type.val() == 3) {
                    url.css('display', 'none');
                    file.css('display', 'block');
                }

                if (type.val() == 0) {
                    url.css('display', 'none');
                    file.css('display', 'none');
                }

            });
        }
    },

    poll: {
        pollView: function (page, jQPageData, $page) {
            var $aqSwitcher = $page.find('.aqSwitcher');
            var $questions = $page.find('.poll_form');
            var $answers = $page.find('.poll_view_single');

            $aqSwitcher.unbind().bind('vclick', function (e) {
                var $this = $(this);
                var uiIcon = $this.find('.ui-icon');
                if ($this.hasClass('showQ')) {
                    $answers.hide();
                    $questions.show();
                    $this.removeClass('showQ');
                    uiIcon.removeClass('ui-icon-question');
                    uiIcon.addClass('ui-icon-page');
                    $this.find('.ui-btn-text').html(core.lang.get('Show Results'));
                } else {
                    $answers.show();
                    $questions.hide();
                    $this.addClass('showQ');
                    uiIcon.removeClass('ui-icon-page');
                    uiIcon.addClass('ui-icon-question');
                    $this.find('.ui-btn-text').html(core.lang.get('Show Questions'));
                }
            });
        }
    },

    rate: {
        reviewCreateReview: function (page, jQPageData, $page) {
            var $stars = $page.find('.review_stars').find('.rate_star')
            $stars.unbind('mouseover').bind('mouseover', function (event) {
                var $star = $(this);
                $stars.removeClass('rate');

                var $previous = $star.prevAll();
                if ($previous) {
                    $previous.addClass('rate');
                }
                $star.addClass('rate');
            });

            $stars.unbind('mouseout').bind('mouseout', function () {
                $stars.removeClass('rate');
            });

            $stars.unbind('vclick').bind('vclick', function (event) {

                var $star = $(this);
                $star.removeClass('rated');
                $star.nextAll().removeClass('rated');

                var $previous = $star.prevAll();
                if ($previous) {
                    $previous.addClass('rated');
                }
                $star.addClass('rated');

                var score = $star.attr('id').substr(10);

                var parent = $($star.parent());
                parent.prev('input').val(score);
            });
        },

        reviewEdit: function (page, jQPageData, $page) {
            var $stars = $page.find('.review_stars').find('.rate_star')
            $stars.unbind('mouseover').bind('mouseover', function (event) {
                var $star = $(this);
                $stars.removeClass('rate');

                var $previous = $star.prevAll();
                if ($previous) {
                    $previous.addClass('rate');
                }
                $star.addClass('rate');
            });

            $stars.unbind('mouseout').bind('mouseout', function () {
                $stars.removeClass('rate');
            });

            $stars.unbind('vclick').bind('vclick', function (event) {

                var $star = $(this);
                $star.removeClass('rated');
                $star.nextAll().removeClass('rated');

                var $previous = $star.prevAll();
                if ($previous) {
                    $previous.addClass('rated');
                }
                $star.addClass('rated');

                var score = $star.attr('id').substr(10);

                var parent = $($star.parent());
                parent.prev('input').val(score);
            });
        },

        offerReviewCreateReview: function (page, jQPageData, $page) {
            var $stars = $page.find('.review_stars').find('.rate_star')
            $stars.unbind('mouseover').bind('mouseover', function (event) {
                var $star = $(this);
                $stars.removeClass('rate');

                var $previous = $star.prevAll();
                if ($previous) {
                    $previous.addClass('rate');
                }
                $star.addClass('rate');
            });

            $stars.unbind('mouseout').bind('mouseout', function () {
                $stars.removeClass('rate');
            });

            $stars.unbind('vclick').bind('vclick', function (event) {

                var $star = $(this);
                $star.removeClass('rated');
                $star.nextAll().removeClass('rated');

                var $previous = $star.prevAll();
                if ($previous) {
                    $previous.addClass('rated');
                }
                $star.addClass('rated');

                var score = $star.attr('id').substr(10);

                var parent = $($star.parent());
                parent.prev('input').val(score);
            });
        },

        offerReviewEdit: function (page, jQPageData, $page) {
            var $stars = $page.find('.review_stars').find('.rate_star')
            $stars.unbind('mouseover').bind('mouseover', function (event) {
                var $star = $(this);
                $stars.removeClass('rate');

                var $previous = $star.prevAll();
                if ($previous) {
                    $previous.addClass('rate');
                }
                $star.addClass('rate');
            });

            $stars.unbind('mouseout').bind('mouseout', function () {
                $stars.removeClass('rate');
            });

            $stars.unbind('vclick').bind('vclick', function (event) {

                var $star = $(this);
                $star.removeClass('rated');
                $star.nextAll().removeClass('rated');

                var $previous = $star.prevAll();
                if ($previous) {
                    $previous.addClass('rated');
                }
                $star.addClass('rated');

                var score = $star.attr('id').substr(10);

                var parent = $($star.parent());
                parent.prev('input').val(score);
            });
        }

    },

    store: {
        loading: function ($count) {
            var $this = this;
            var $point = ' .';
            if ($count == 2) {
                $point = ' ..';
            } else if ($count == 3) {
                $point = ' ...';
            }
            $('#payment_loading').text('Please Wait' + $point);

            setTimeout(function () {
                $count++;
                if ($count == 4) {
                    $count = 1;
                }
                $this.loading($count);
            }, 300);
        },

        transactionProcess: function (page, jQPageData, $page) {
            this.loading(1);
            var $form = $('#transaction_form');
            $form.submit();
        },

        indexIndex: function (page, jQPageData, $page) {
            $('input.product-quantity').bind('vclick', function (event) {

            });
            $('input.product-quantity').bind('keyup', function (event) {
                var value = $(this).val();
                var re = /[^0-9\-\.]/gi;
                if (re.test(value)) {
                    value = value.replace(re, '');
                    $(this).val(value);
                }
            });
            $('input.product-quantity').bind('blur', function (event) {
                if ($(this).val() == '') {
                    $(this).val(1);
                }
            });
        },

        cartIndex: function (page, jQPageData, $page) {
            var gateway_params = page.info.gateway_params;
            for (var key in gateway_params.gateways) {
                var gateway_id = gateway_params.gateways[key].id;
                $('#gateway_button_' + gateway_id).data('gateway_id', gateway_id).data('cart_id', gateway_params.cart_id).bind('vclick', function (event) {
                    $.ajax({
                        'type': 'post',
                        'data': {
                            'format': 'json',
                            'cart': $(this).data('cart_id'),
                            'gateway_id': $(this).data('gateway_id')
                        },
                        'dataType': 'json',
                        'url': gateway_params.url,
                        'success': function (response) {
                            if (response.status) {
                                if (core.device.platform.isPhoneGap()) {
                                    window.plugins.meclPayPal.fetchDeviceReferenceTokenWithAppID(function (token) {
                                        var webview = window.open(default_settings.siteinfo.protocol + '//' + default_settings.siteinfo.host + response.link + '?ppmeclToken=' + token);
                                        webview.addEventListener('loadstart', function (e) {
                                            if (e.url.split('store/transaction/finish/status/completed').length > 1) {
                                                alert("Successfully completed!");
                                            } else if (e.url.split('store/panel/purchase/').length > 1) {
                                                $.mobile.changePage(e.url);
                                            } else if (e.url.split('store/transaction/finish/status/failed').length > 1) {
                                                alert("Payment Failed!");
                                                webview.close();
                                            }
                                        })
                                    });
                                } else if (gateway_id == 3) {
                                    response.link
                                }
                                $.mobile.changePage(response.link);
                            }
                        },
                        'error': function (error) {
                            UIComponent.helper.showMessage(response.errorMmessage, 'e', 2000);
                        }
                    });
                });
            }

        },

        productsEdit: function (page, jQPageData, $page) {

            var switchType = function () {
                if ($('#price_type').val() == 'simple') {
                    $('#list_price-wrapper').css('display', 'none');
                    $('#discount_expiry_date-wrapper').css('display', 'none');
                    $('#discount_expiry_date-date').val('');
                    $('#discount_expiry_date-hour').val('');
                    $('#discount_expiry_date-minute').val('');
                } else {
                    $('#list_price-wrapper').css('display', 'block');
                    $('#discount_expiry_date-wrapper').css('display', 'block');
                }
            }

            var switchAmount = function () {
                if ($('#type').val() == 'digital') {
                    $('#additional_params-wrapper').css('display', 'none');
                    $('#quantity-wrapper').css('display', 'none');
                } else {
                    $('#additional_params-wrapper').css('display', 'block');
                    $('#quantity-wrapper').css('display', 'block');
                }
            }

            switchAmount();
            switchType();

            $page.find('#type').removeAttr('onchange').bind('change', switchAmount);
            $page.find('#price_type').removeAttr('onchange').bind('change', switchType);
            $page.find('#discount_expiry_date-element').children('p.description').children('a').removeAttr('onclick');


            var addParams = function (num) {
                var $div = $('#additional-params').find('.param-block').first();
                var $clone = $div.clone().attr('id', 'param-block-' + num);
                var $delete = $('<a/>').attr('class', 'param-delete').attr('delete-id', num).attr('href', 'javascript:void(0);').html('X');

                $clone.find('input').val('');
                $clone.append($delete);
                $('#additional-params').append($clone);
            }

            var initDelete = function () {
                $('.param-delete').unbind('vclick').bind('vclick', function (event) {
                    var num = $(this).attr('delete-id');
                    $('#param-block-' + num).remove();
                });
            }

            $page.find('#add-more').bind('vclick', function (evet) {
                var id = $(this).attr('next');
                addParams(id);
                initDelete();
                $(this).attr('next', parseInt(id) + 1);
            });

            initDelete();
        },
        productsCopy: function (page, jQPageData, $page) {

            var switchType = function () {
                if ($('#price_type').val() == 'simple') {
                    $('#list_price-wrapper').css('display', 'none');
                    $('#discount_expiry_date-wrapper').css('display', 'none');
                    $('#discount_expiry_date-date').val('');
                    $('#discount_expiry_date-hour').val('');
                    $('#discount_expiry_date-minute').val('');
                } else {
                    $('#list_price-wrapper').css('display', 'block');
                    $('#discount_expiry_date-wrapper').css('display', 'block');
                }
            }

            var switchAmount = function () {
                if ($('#type').val() == 'digital') {
                    $('#additional_params-wrapper').css('display', 'none');
                    $('#quantity-wrapper').css('display', 'none');
                } else {
                    $('#additional_params-wrapper').css('display', 'block');
                    $('#quantity-wrapper').css('display', 'block');
                }
            }

            switchAmount();
            switchType();

            $page.find('#type').removeAttr('onchange').bind('change', switchAmount);
            $page.find('#price_type').removeAttr('onchange').bind('change', switchType);
            $page.find('#discount_expiry_date-element').children('.description').children('a').removeAttr('onclick');


            var addParams = function (num) {
                var $div = $('#additional-params').find('.param-block').first();
                var $clone = $div.clone().attr('id', 'param-block-' + num);
                var $delete = $('<a/>').attr('class', 'param-delete').attr('delete-id', num).attr('href', 'javascript:void(0);').html('X');

                $clone.find('input').val('');
                $clone.append($delete);
                $('#additional-params').append($clone);
            }

            var initDelete = function () {
                $('.param-delete').unbind('vclick').bind('vclick', function (event) {
                    var num = $(this).attr('delete-id');
                    $('#param-block-' + num).remove();
                });
            }

            $page.find('#add-more').bind('vclick', function (evet) {
                var id = $(this).attr('next');
                addParams(id);
                initDelete();
                $(this).attr('next', parseInt(id) + 1);
            });

            initDelete();
        },
        productsCreate: function (page, jQPageData, $page) {

            var switchType = function () {
                if ($('#price_type').val() == 'simple') {
                    $('#list_price-wrapper').css('display', 'none');
                    $('#discount_expiry_date-wrapper').css('display', 'none');
                    $('#discount_expiry_date-date').val('');
                    $('#discount_expiry_date-hour').val('');
                    $('#discount_expiry_date-minute').val('');
                } else {
                    $('#list_price-wrapper').css('display', 'block');
                    $('#discount_expiry_date-wrapper').css('display', 'block');
                }
            }

            var switchAmount = function () {
                if ($('#type').val() == 'digital') {
                    $('#additional_params-wrapper').css('display', 'none');
                    $('#quantity-wrapper').css('display', 'none');
                } else {
                    $('#additional_params-wrapper').css('display', 'block');
                    $('#quantity-wrapper').css('display', 'block');
                }
            }

            switchAmount();
            switchType();

            $page.find('#type').removeAttr('onchange').bind('change', switchAmount);
            $page.find('#price_type').removeAttr('onchange').bind('change', switchType);
            $page.find('#discount_expiry_date-element').children('.description').children('a').removeAttr('onclick');


            var addParams = function (num) {
                var $div = $('#additional-params').find('.param-block').first();
                var $clone = $div.clone().attr('id', 'param-block-' + num);
                var $delete = $('<a/>').attr('class', 'param-delete').attr('delete-id', num).attr('href', 'javascript:void(0);').html('X');

                $clone.find('input').val('');
                $clone.append($delete);
                $('#additional-params').append($clone);
            }

            var initDelete = function () {
                $('.param-delete').unbind('vclick').bind('vclick', function (event) {
                    var num = $(this).attr('delete-id');
                    $('#param-block-' + num).remove();
                });
            }

            $page.find('#add-more').bind('vclick', function (evet) {
                var id = $(this).attr('next');
                addParams(id);
                initDelete();
                $(this).attr('next', parseInt(id) + 1);
            });

            initDelete();
        },
        videoCreate: function (page, jQPageData, $page) {

            $('#url-wrapper').hide();
            var video_type = $page.find('#type');
            video_type.bind('change', function () {
                if ($(this).val() == 1 || $(this).val() == 2) {
                    $('#url-wrapper').show();
                }

                if ($(this).val() == 0) {
                    $('#url-wrapper').hide();
                }
            });
        },
        videoEdit: function (page, jQPageData, $page) {
            var video_type = $page.find('form #type');
            video_type.unbind('onchange').bind('change', function () {
                if ($(this).val() == 1 || $(this).val() == 2) {
                    $('#url-wrapper').show();
                }

                if ($(this).val() == 0) {
                    $('#url-wrapper').hide();
                }
            });
        },
        removeFromCart: function (item) {
            var $item = $(item);
            $item.closest('li').remove();
            $.ajax($item.attr('href'), function () {
                $.mobile.changePage(location.pathname, {reloadPage: true})
            })
            return false;
        }
    },

    user: {
        editPhoto: function (page, jQPageData, $page) {
            $page.find('#EditPhoto').find('.component-gallery').find('a').photoSwipe({ enableMouseWheel: false, enableKeyboard: false });
        },

        signupIndex: function (page, jQPageData, $page) {
            $page.find('button[onclick="javascript:finishForm();"]').removeAttr('onclick').bind('vclick', function () {
                $page.find('#nextStep').val("finish");
            });
            $page.find('a#skiplink').removeAttr('onclick').bind('vclick', function () {
                $page.find('#skip').val("skipForm");
                $(this).closest('form').trigger('submit');
            });
            var $uploadPhoto = $page.find('#uploadPhoto');
            $uploadPhoto.closest('form').bind('picupafterupload', function (e) {
                $.mobile.showPageLoadingMsg();
                $uploadPhoto.val(true);
                var self = this;
                setTimeout(function () {
                    $(self).trigger('submit');
                }, 1000);
            });
            if ($uploadPhoto.length == 1) {
                if ($page.find('#SignupForm').find('.component-gallery').find('a').length > 0)
                    $page.find('#SignupForm').find('.component-gallery').find('a').photoSwipe({ enableMouseWheel: false, enableKeyboard: false });
                $page.find('#Filedata').removeAttr('onchange').bind('change', function () {
                    if ($(this).val()) {
                        $uploadPhoto.val(true);
                        $(this.form).trigger('submit');
                    }
                })
    };
        },

        indexHome: function (page, jQPageData, $page) {
            if (!Chat.initialized) {
                Chat.getSettings();
            }
        },

        authLogin: function (page, jQPageData, $page) {
            var email = $page.find('#email')[0];
            if (email)
                email.setAttribute('type', 'email');
            Chat.destroy();
        }
    },

    video: {
        indexCreate: function (page, jQPageData, $page) {
            var tagsUrl = page.info.videoUpload.tagsUrl;
            var validationUrl = page.info.videoUpload.validationUrl;
            var validationErrorMessage = page.info.videoUpload.validationErrorMessage;
            var checkingUrlMessage = page.info.videoUpload.checkingUrlMessage;

            var current_code;

            var ignoreValidation = function () {
                $page.find('#upload-wrapper').show();
                $page.find('#validation').hide();
                $page.find('#code').val(current_code);
                $page.find('#ignore').val(true);
            }

            var updateTextFields = function () {
                var video_element = $page.find("#type");
                var url_element = $page.find("#url-wrapper");
                var file_element = $page.find("#file-wrapper");
                var submit_element = $page.find("#upload-wrapper");

                // clear url if input field on change
                //$('code').value = "";
                $page.find('#upload-wrapper').hide();
                var $urlElement = $page.find('#url');
                // If video source is empty
                if (video_element.val() == 0) {
                    $urlElement.val('');
                    file_element.hide();
                    url_element.hide();
                    return;
                } else if ($page.find('#code').val() && $urlElement.val()) {
                    $page.find('#type-wrapper').hide();
                    file_element.hide();
                    $page.find('#upload-wrapper').show();
                    return;
                } else if (video_element.val() == 1 || video_element.val() == 2) {
                    // If video source is youtube or vimeo
                    $urlElement.val('');
                    $page.find('#code').val('');
                    file_element.hide();
                    url_element.show();
                    return;
                } else if (video_element.val() == 3) {
                    // If video source is from computer
                    $urlElement.val('');
                    $page.find('#code').val('');
                    file_element.show();
                    url_element.hide();
                    return;
                } else if ($page.find('#id').val()) {
                    // if there is video_id that means this form is returned from uploading
                    // because some other required field
                    $page.find('#type-wrapper').hide();
                    file_element.hide();
                    $page.find('#upload-wrapper').show();
                    return;
                }
            }

            $page.find('form').find('#type').removeAttr('onchange').bind('change', updateTextFields);

            var video = {
                active: false,

                debug: false,

                currentUrl: null,

                currentTitle: null,

                currentDescription: null,

                currentImage: 0,

                currentImageSrc: null,

                imagesLoading: 0,

                images: [],

                maxAspect: (10 / 3), //(5 / 2), //3.1,

                minAspect: (3 / 10), //(2 / 5), //(1 / 3.1),

                minSize: 50,

                maxPixels: 500000,

                monitorInterval: null,

                monitorLastActivity: false,

                monitorDelay: 500,

                maxImageLoading: 5000,

                attach: function () {
                    var bind = this;
                    var $urlElement = $page.find('#url');
                    $urlElement.bind('keyup', function () {
                        bind.monitorLastActivity = (new Date).valueOf();
                    });

                    var url_element = $page.find("#url-element");
                    var myElement = $("<p />");
                    myElement.html(core.lang.get('Checking URL...'));
                    myElement.addClass("description");
                    myElement.attr({'id': "validation"});
                    myElement.hide();
                    url_element.append(myElement);

                    var body = $urlElement;
                    var lastBody = '';
                    var lastMatch = '';
                    var video_element = $page.find('#type');
                    body.bind('change', function () {
                        // Ignore if no change or url matches
                        if (body.val() == lastBody || bind.currentUrl) {
                            return;
                        }

                        // Ignore if delay not met yet
                        if ((new Date).valueOf() < bind.monitorLastActivity + bind.monitorDelay) {
                            return;
                        }

                        // Check for link
                        var m = body.val().match(/https?:\/\/([-\w\.]+)+(:\d+)?(\/([-#:\w/_\.]*(\?\S+)?)?)?/);
                        if ($.type(m) == 'array' && $.type(m[0]) && lastMatch != m[0]) {

                            if (video_element.val() == 1) {
                                video.youtube(body.val());
                            } else {
                                video.vimeo(body.val());
                            }
                        }
                        lastBody = body.val();
                    });
                },

                youtube: function (url) {
                    // extract v from url
                    var youtube_code = core.helper.getUrlParam(url, 'v');
                    if (youtube_code === undefined) {
                        youtube_code = $.mobile.path.parseUrl(url).filename;
                    }
                    if (youtube_code) {
                        $page.find('#validation').show();
                        $page.find('#validation').html(checkingUrlMessage);
                        $page.find('#upload-wrapper').hide();
                        $.mobile.showPageLoadingMsg('a', core.lang.get('Checking URL...'));
                        $.ajax({
                            type: 'post',
                            url: validationUrl,
                            data: {
                                'ajax': true,
                                'code': youtube_code,
                                'type': 'youtube',
                                'format': 'json'
                            },
                            success: function (response) {
                                $.mobile.hidePageLoadingMsg();
                                if (response.valid) {
                                    $page.find('#upload-wrapper').show();
                                    $page.find('#validation').hide();
                                    $page.find('#code').val(youtube_code);
                                } else {
                                    $page.find('#upload-wrapper').hide();
                                    current_code = youtube_code;
                                    $page.find('#validation').html(validationErrorMessage);
                                }
                            },
                            dataType: 'json'
                        });
                    }
                },

                vimeo: function (url) {
                    var vimeo_code = $.mobile.path.parseUrl(url).filename;
                    if (vimeo_code.length > 0) {
                        $page.find('#validation').show();
                        $page.find('#validation').html(checkingUrlMessage);
                        $page.find('#upload-wrapper').hide();
                        $.mobile.showPageLoadingMsg('a', core.lang.get('Checking URL...'));
                        $.ajax({
                            type: 'post',
                            url: validationUrl,
                            data: {
                                'ajax': true,
                                'code': vimeo_code,
                                'type': 'vimeo',
                                'format': 'json'
                            },
                            success: function (response) {
                                $.mobile.hidePageLoadingMsg();
                                if (response.valid) {
                                    $page.find('#upload-wrapper').show();
                                    $page.find('#validation').hide();
                                    $page.find('#code').val(vimeo_code);
                                } else {
                                    $page.find('#upload-wrapper').hide();
                                    current_code = vimeo_code;
                                    $page.find('#validation').html(validationErrorMessage);
                                }
                            },
                            dataType: 'json'
                        });
                    }
                }
            }

          $page.find('form #file').change(function(){
            var fileName = $(this).val();
            console.log(fileName);
            if( fileName ) {
              $page.find('#upload-wrapper').show();
            } else {
              $page.find('#upload-wrapper').hide();
            }
          });

            // Run stuff
            updateTextFields();
            video.attach();

        },

        videoEmbed: function (page, jQPageData, $page) {
            $($page[0].querySelectorAll('.embed_code')[0]).html(page.info.embed_code);
        }
    },

    helper: {
        groupEventMemberInvite: function ($form) {
            var $page = UIComponent.helper.$getActivePage();
            if ($form.length > 0)
                $form.find('[for="selectall"]').bind('vclick', function (event) {
                    var $this = $page.find(this);
                    var el = $page.find(event.target);
                    $form.find('[type=checkbox]').each(function () {
                        var $elem = $page.find(this);
                        var id = $elem.attr('id');
                        if (id == "selectall")
                            return true;

                        $elem = $page.find('[for="' + id + '"]');
                        if (
                            (
                                $this.hasClass('ui-checkbox-off') &&
                                    $elem.hasClass('ui-checkbox-off')
                                ) ||
                                (
                                    $this.hasClass('ui-checkbox-on') &&
                                        $elem.hasClass('ui-checkbox-on')
                                    )
                            )
                            $elem.trigger('vclick');
                    })
                });
        }
    },

    timeline: {
        photoUpload: function (page, jQPageData, $page) {
            var $this = this;
            // picupafterupload
            // picupuploadsuccess
            $page.find('form').bind('picupafterupload', function () {
                //this.submit();
            });
            $page.find('#Filedata').bind('change', function () {
                //$page.find('form#UploadTimelinePhoto').submit();
            });
        },

        set_photo: function (photo_id) {
//            jQuery.post(
//                'badges/index/approved',
//                {
//                    'approved':($link.hasClass('active')) ? 0 : 1,
//                    'badge_id':page.info.params.id,
//                    'format':'json'
//                },
//                function (data, textStatus, jqXHR) {
//                    if (data.status) {
//                        $link.html(data.lang);
//                    } else {
//                        $link.removeClass('active');
//                    }
//                },
//                'json'
//            );
//                    var self = this;
//                    new Request.JSON({
//                        'method':'get',
//                        'data':{'format':'json'},
//                        'url':'<?php echo $this->url(array(
//                            'action' => 'set',
//                            'id' => $this->subject()->getIdentity(),
//                            'type' => $this->type,
//                        ), 'timeline_photo', true); ?>/photo_id/' + photo_id,
//                        'onRequest':function () {
//                            self.he_list.toggleClass('hidden');
//                            self.loader.toggleClass('hidden');
//                        },
//                        'onSuccess':function (response) {
//                            if (response.status) {
//                                eval('parent.document.tl_' + '<?php echo $this->type; ?>' + '.load_photo(' + response.photo_id + ')');
//                                parent.Smoothbox.close();
//                            } else {
//                                //self.loader.toggleClass('hidden');
//                                //self.he_list.toggleClass('hidden');
//                            }
//                        }
//                    }).send();
        },

        profileIndex: function (page, jQPageData, $page) {
            var $actions_button = $page.find('.cover_actions');
            $actions_button.bind('vclick', function () {
                var $actions = $page.find('.some_wrapper');
                if ($actions.hasClass('display_actions_list')) {
                    $actions.removeClass('display_actions_list');
                    $actions_button.attr('style', 'opacity:0.5;');
                }
                else {
                    $actions.addClass('display_actions_list')
                    $actions_button.attr('style', 'opacity:1;');
                }
            });
            var img = $('#cover-photo');
            img.attr('data-top', img.css('top'));

            var oImg = $('<img>')
                .attr('src', img.attr('src'))
                .attr('id', 'wtf')
                .css('display', 'none');

            img.parent().append(oImg);
            oImg.load(function(e) {
                var oH = oImg.height();

                var oT = img.css('top');
                oT = oT.substr(0, oT.length - 2);
                oT = Number(oT);

                var p = oT * 100 / oH;
                img.attr('data-p', p);

                $(window).trigger('resize');
            });

            $(window).bind('resize', function () {
                var h = Number(img.height());
                var p = Number(img.attr('data-p'));
                var tT = (h * p) / 100;

                var d = Math.abs(Math.abs(h) - Math.abs(tT));

                if(d < 315) {
                    tT = 0;
                }
                img.css('top', tT + 'px');
            });
        }
    },

    hequestion: {
        indexView: function (page, jQPageData, $page) {

            $page.find('.hqAnswerButton').bind('vclick', function () {
                var b = $(this);
                if (b.data('complete') === false) {
                    return;
                }
                b.data('complete', false);
                $.mobile.showPageLoadingMsg();
                $.post(b.data('url'), {title: $page.find('.hqAnswerBody').val()}, function (obj) {
                    b.data('complete', true);
                    $.mobile.hidePageLoadingMsg();
                    if (!obj.status) {
//            if (obj.message){
//            }
                    } else {
                        $.mobile.changePage(core.location.pathname, {reloadPage: true});
                    }
                });
            });

            $page.find('.hqFollow').bind('vclick', function () {
                var e = $(this);
                $.post(e.data('url'));
                e.closest('li').hide();
                $page.find('.hqUnfollow').show();
            });

            $page.find('.hqUnfollow').bind('vclick', function () {
                var e = $(this);
                $.post(e.data('url'));
                e.closest('li').hide();
                $page.find('.hqFollow').show();
            });

            $page.find('.hqDelete').bind('vclick', function () {
                var e = $(this);
                if (confirm(e.data('message'))) {
                    $.post(e.data('url'), {cache: false}, function () {
                        $('div.ui-page[module=hequestion]').not('.ui-page-active').remove();
                        $.mobile.changePage(e.data('url-browse'));
                    });
                }
            });


            $page.find('.hqPrivacy').bind('vclick', function () {
                $page.find('.hqPrivacyForm').toggle();
            });


            $page.find('.hqPrivacyRadio').change(function () {
                var e = $(this);
                if (e.data('complete') === false) {
                    return;
                }
                e.data('complete', false);
                $.post($(this).data('url'), {
                    privacy: $page.find('.hqPrivacyForm').find('input:checked').val()
                }, function () {
                    e.data('complete', true);
                });
            });


            $page.find('.hqContentTitle').find('a').bind('vclick', function () {
                var e = $(this).closest('li').find('.hqUserChoose [type=checkbox], [type=radio]')[0];
                if (e) {
                    e.checked = !e.checked;
                }
                $(e).change();
            });

            $page.find('.hqUserChoose [type=checkbox], [type=radio]').change(function () {
                var b = $(this);
                if (b.data('complete') === false) {
                    return;
                }
                b.data('complete', false);
                $.mobile.showPageLoadingMsg();
                var vote = 1;
                if (!this.checked) {
                    vote = 0;
                }
                $.post(b.data('url'), {vote: vote}, function (obj) {
                    b.data('complete', true);
                    $.mobile.hidePageLoadingMsg();
                    if (!obj.status) {
//            if (obj.message){
//            }
                    } else {
                        $.mobile.changePage(core.location.pathname, {reloadPage: true});
                    }
                });
            });

            $page.find('.hqUnvote').bind('vclick', function () {
                var b = $(this);
                if (b.data('complete') === false) {
                    return;
                }
                b.data('complete', false);
                $.mobile.showPageLoadingMsg();
                $.post(b.data('url'), {}, function (obj) {
                    b.data('complete', true);
                    $.mobile.hidePageLoadingMsg();
                    if (!obj.status) {
//            if (obj.message){
//            }
                    } else {
                        $.mobile.changePage(core.location.pathname, {reloadPage: true});
                    }
                });
            });


        }
    },

    chat: {
        init: function (response) {
            if (response.chatSettings)
                Chat.setParams(response.chatSettings);
        },

        indexIndex: function (page, jQPageData, $page) {
            var chat_header = $page.find('#chat-header');
            //chat_header.remove();
            $page.find('.ui-header').append(chat_header);

            Chat.initRoomHeader(chat_header);
        }
    },

    offers: {
        subscriptionProcess: function (page, jQPageData, $page) {
            $page.find('form').submit();
        }
    }
}
