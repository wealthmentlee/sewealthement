var ASid = setTimeout(changeSearch, 1000);
var asBaseUrl = document.head.getElement('base');
function changeSearch() {
    if ($('global_search_field') && (!$('global_search_field').get('data-type') || $('global_search_field').get('data-type') != 'mini')) {
        var parent = $('global_search_field').getParent();
        parent.setStyle('min-width', '200px');
        var div = '<span id="as_global_default_icon"><i style="display: inline-block;width: 1.25em; text-align: center;" class="icon-globe"></i></span> ' +
            '<input type="text" class="text suggested as_global_search" name="query" autocomplete="off" id="global_search_field" size="20" maxlength="100" placeholder="Search" alt="Search">' +
            '<input type="hidden" value="all" id="as_global_type">' +
            '<div style="position: absolute">' +
            '<img id="as_global_loading" src="' + asBaseUrl.get('href') + '/application/modules/Core/externals/images/loading.gif" style="left: 178px;position: relative;top: -19px;visibility: hidden;z-index: 101;">' +
            '</div><div style="position: absolute">' +
            '<div style="margin: 0px; position: relative;"><div class="advancedsearch_types_list" id="advancedsearch_global_types_list" style="margin: 0px; overflow: hidden;">' +
            '<div class="as_type_global_container active">' +
            '<span data-type="all"><i class="icon-globe"></i>Everywhere</span>' +
            '<div style="clear: both"></div>' +
            '</div>' + ASTypes +
            '</div></div>' +
            '<div id="as_global_found_items" class="advancedsearch_types_list"></div>' +
            '</div>';
        parent.set('html', div);
        $$('.as_global_search+label.overTxtLabel').set('html', '');
        featuresAS();
        window.clearTimeout(ASid);
    }
}

function featuresAS() {
    var asGlobal;
    asGlobal = new Fx.Slide($('advancedsearch_global_types_list')).hide();
    $('global_search_field').addEvent('focus', function () {
        if ($(this).get('value').trim() == '') {
            $('advancedsearch_global_types_list').setStyle('display', 'block');
            $('advancedsearch_global_types_list').setStyle('opacity', '1');
            asGlobal.show();
            $('as_global_found_items').set('html', '');
            $('advancedsearch_global_types_list').getParent().setStyle('overflow', 'visible');
        } else if (parseInt($('as_global_found_items').getParent().getStyle('height')) == 0) {
            searchGlobal();
        }
    });

    $$('.as_type_global_container').addEvent('click', function () {
        $$('.as_type_global_container').removeClass('active');
        $(this).toggleClass('active');
        $('global_search_field').focus();
        var icon = $(this).getChildren('span i').getProperty('class');
        var type = $(this).getChildren('span').getProperty('data-type');
        $('as_global_default_icon').getChildren('i').set('class', icon);
        $('as_global_type').set('value', type);
    });

    $(document.body).addEvent('keydown', function (event) {
        if (event.key == 'f' && event.shift && $(event.target).get('tag') == 'body') {
            console.log( $(event.target).get('tag'));
            $('global_search_field').focus();
            if ($(event.target).get('id') != 'global_search_field') {
                setTimeout(function () {
                    $('global_search_field').set('value', '');
                }, 1);
            }
        }
    });

    $(document.body).addEvent('click', function (e) {
        if ($(e.target).get('id') != 'global_search_field' && !$(e.target).hasClass('as_global_search_result') && !$(e.target).hasClass('as_global_found_more') && !$(e.target).hasClass('as_type_global_container') && !$(e.target).getParent().hasClass('as_type_global_container') && !$(e.target).getParent().hasClass('as_global_search_photo') && !$(e.target).getParent().hasClass('as_global_search_info') && $(e.target).get('tag') != 'i') {
            $('advancedsearch_global_types_list').setStyle('opacity', '0');
            asGlobal.hide();
            $('advancedsearch_global_types_list').setStyle('display', 'none');
            $('advancedsearch_global_types_list').getParent().setStyle('overflow', 'hidden');
            $('as_global_found_items').set('html', '');
        }
    });
    $('global_search_field').addEvent('keyup', function (event) {
        if (event.key == 'enter') {
            if ($$('div.as_global_search_result.active').length > 0) {
                window.location = $$('div.as_global_search_result.active')[0].getParent('a').get('href');
            } else if ($$('div.as_global_found_more.active').length > 0) {
                window.location = $$('div.as_global_found_more.active')[0].getParent('a').get('href');
            }
        } else if ((event.key == 'up' || event.key == 'down')) {
            if ($('advancedsearch_global_types_list').getParent().getStyle('overflow') == 'visible') {
                var that = $$('.as_type_global_container.active');
                var activate = false;
                if (event.key == 'down') {
                    activate = that.getNext();
                } else {
                    activate = that.getPrevious();
                }
                if (activate && activate[0] != null) {
                    $$('.as_type_global_container').removeClass('active');
                    activate.toggleClass('active');
                    var icon = activate.getChildren('span i')[0].get('class');
                    var type = activate.getChildren('span')[0].get('data-type');
                    $('as_global_default_icon').getChildren('i').set('class', icon);
                    $('as_global_type').set('value', type);
                }
            } else if (parseInt($('as_global_found_items').getParent().getStyle('height')) > 0) {
                if ($('as_global_found_items').getChildren('.as_global_found_more').length == 0) {
                    if ($$('div.as_global_search_result.active').length > 0 || $$('div.as_global_found_more.active').length > 0) {
                        var changeActive = false;
                        if (event.key == 'up') {
                            if ($$('div.as_global_found_more.active').length > 0) {
                                changeActive = $$('div.as_global_found_more.active')[0].getParent().getPrevious('a');
                            } else if ($$('div.as_global_search_result.active').length > 0) {
                                changeActive = $$('div.as_global_search_result.active')[0].getParent().getPrevious('a');
                            }
                        } else if ($$('div.as_global_search_result.active').length > 0) {
                            changeActive = $$('div.as_global_search_result.active')[0].getParent().getNext('a');
                        }
                        if (changeActive) {
                            $$('div.as_global_search_result').removeClass('active');
                            $$('div.as_global_found_more').removeClass('active');
                            if (changeActive.hasClass('as_global_found_more_link')) {
                                changeActive.getChildren('div.as_global_found_more').addClass('active');
                            } else {
                                changeActive.getChildren('div.as_global_search_result').addClass('active');
                            }
                        }
                    } else {
                        $$('div.as_global_search_result')[0].addClass('active');
                        $$('div.as_global_found_more').removeClass('active');
                    }
                }
            }
        } else if (event.key == 'esc') {
            if ($(this).get('value').length > 0) {
                asGlobal.show();
                $('advancedsearch_global_types_list').setStyle('opacity', '1');
                $('advancedsearch_global_types_list').setStyle('display', 'block');
                $('advancedsearch_global_types_list').getParent().setStyle('overflow', 'visible');
                $('as_global_found_items').set('html', '');
                $(this).set('value', '');
            } else {
                $('advancedsearch_global_types_list').setStyle('opacity', '0');
                asGlobal.hide();
                $('advancedsearch_global_types_list').setStyle('display', 'none');
                $('advancedsearch_global_types_list').getParent().setStyle('overflow', 'hidden');
                $(this).blur();
            }
        } else if ($(this).get('value').length > 2) {
            searchGlobal();
        } else {
            asGlobal.show();
            $('advancedsearch_global_types_list').setStyle('opacity', '1');
            $('advancedsearch_global_types_list').setStyle('display', 'block');
            $('advancedsearch_global_types_list').getParent().setStyle('overflow', 'visible');
            $('as_global_found_items').set('html', '');
        }
    });
    function searchGlobal() {
        var query = $('global_search_field').get('value');
        var type = $('as_global_type').get('value');
        if (query != '') {
            $('advancedsearch_global_types_list').setStyle('opacity', '0');
            asGlobal.hide();
            $('advancedsearch_global_types_list').setStyle('display', 'none');
            $('advancedsearch_global_types_list').getParent().setStyle('overflow', 'hidden');
            $('as_global_loading').setStyle('visibility', 'visible');
            var url = asBaseUrl.get('href') + 'advancedsearch/index/search';

            var jsonRequest = new Request.JSON({
                url: url,
                method: 'post',
                data: {
                    'query': query,
                    'type': type,
                    'global': true,
                    'format': 'json'
                },
                onSuccess: function (data) {
                    if (data.html.trim() != '') {
                        var found = data.html;
                        $('as_global_found_items').set('html', data.html);
                        var myFx = new Fx.Tween('as_global_found_items');
                        $('as_global_found_items').setStyle('opacity', 0);
                        myFx.start('opacity', 0, 1);
                    } else if (data.html.trim() == '') {
                        check = true;
                        var div = new Element('div');
                        div.addClass('as_global_found_more');
                        var el = new Element('span').set('text', en4.core.language.translate("AS_Nothing found"));
                        el.inject(div);
                        $('as_global_found_items').set('html', '');
                        if ($('advancedsearch_global_types_list').getParent().getStyle('overflow') == 'hidden') {
                            div.inject($('as_global_found_items'));
                        }
                    }
                    $('as_global_loading').setStyle('visibility', 'hidden');
                }
            }).send();

        } else {
            asGlobal.show();
            $('advancedsearch_global_types_list').setStyle('opacity', '1');
            $('advancedsearch_global_types_list').setStyle('display', 'block');
            $('advancedsearch_global_types_list').getParent().setStyle('overflow', 'visible');
            $('as_global_loading').setStyle('visibility', 'hidden');
            $('as_global_found_items').set('html', '');

        }
    }
}