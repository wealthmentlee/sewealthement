<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>
<div class="layout_core_menu_mini">
  <div id='core_menu_mini_menu'>
    <?php
    // Reverse the navigation order (they're floating right)
    $count = count($this->navigation);
    foreach ($this->navigation->getPages() as $item) $item->setOrder(--$count);
    ?>
    <ul>
      <?php if ($this->viewer->getIdentity()) : ?>
        <li id='core_menu_mini_menu_update'>
      <span onclick="toggleUpdatesPulldown(event, this, '4');" style="display: inline-block;" class="updates_pulldown">
        <div class="pulldown_contents_wrapper">
          <div class="pulldown_contents">
            <ul class="notifications_menu" id="notifications_menu">
              <div class="notifications_loading" id="notifications_loading">
                <img
                  src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif'
                  style='float:left; margin-right: 5px;'/>
                <?php echo $this->translate("Loading ...") ?>
              </div>
            </ul>
          </div>
          <div class="pulldown_options">
            <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'activity', 'controller' => 'notifications'),
              $this->translate('View All Updates'),
              array('id' => 'notifications_viewall_link')) ?>
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Mark All Read'), array(
              'id' => 'notifications_markread_link',
            )) ?>
          </div>
        </div>
        <a href="javascript:void(0);"
           id="updates_toggle" <?php if ($this->notificationCount): ?> class="new_updates"<?php endif;?>><?php echo $this->translate(array('%s Update', '%s Updates', $this->notificationCount), $this->locale()->toNumber($this->notificationCount)) ?></a>
      </span>
        </li>
      <?php endif; ?>
      <?php foreach ($this->navigation as $item): ?>
        <li><?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), array_filter(array(
            'class' => (!empty($item->class) ? $item->class : null),
            'alt' => (!empty($item->alt) ? $item->alt : null),
            'target' => (!empty($item->target) ? $item->target : null),
          ))) ?></li>
      <?php endforeach; ?>
      <?php if ($this->search_check): ?>
        <li id="global_search_form_container">
          <span id="as_global_default_icon"><i class="icon-globe"></i></span>
          <input type='text' data-type="mini" class='text suggested as_global_search' name='query' autocomplete="off"
                 id='global_search_field' size='20' maxlength='100'
                 placeholder="<?php echo $this->translate('Search') ?>" alt='<?php echo $this->translate('Search') ?>'/>
          <input type="hidden" value="all" id="as_global_type">

          <div style="position: absolute">
            <img id="as_global_loading"
                 src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif'
                 style="left: 178px;position: relative;top: -19px;visibility: hidden;z-index: 101;"/>
          </div>
          <div style="position: absolute">
            <div class="advancedsearch_types_list" id="advancedsearch_global_types_list">
              <div class="as_type_global_container active">
                <span data-type="all"><i class="icon-globe"></i><?php echo $this->translate('Everywhere'); ?></span>

                <div style="clear: both"></div>
              </div>
              <?php foreach ($this->types as $type): ?>
                <div class="as_type_global_container">
                  <span data-type="<?php echo $type; ?>"><i
                      class="<?php if (isset($this->itemicons[$type])) echo $this->itemicons[$type]; else echo 'icon-globe'; ?>"></i><?php echo $this->translate(strtoupper('ITEM_TYPE_' . $type));?></span>

                  <div style="clear: both"></div>
                </div>
              <?php endforeach;?>
            </div>
            <div id="as_global_found_items" class="advancedsearch_types_list">

            </div>
          </div>
        </li>
      <?php endif;?>
    </ul>
  </div>
</div>


<script type="text/javascript" data-cfasync="false">
var notificationUpdater;
en4.core.runonce.add(function () {

  if ($('global_search_field')) {
    new OverText($('global_search_field'), {
      poll: true,
      pollInterval: 500,
      positionOptions: {
        position: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
        edge: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
        offset: {
          x: ( en4.orientation == 'rtl' ? -4 : 4 ),
          y: 2
        }
      }
    });
  }

  if ($('notifications_markread_link')) {
    $('notifications_markread_link').addEvent('click', function () {
      //$('notifications_markread').setStyle('display', 'none');
      en4.activity.hideNotifications('<?php echo $this->string()->escapeJavascript($this->translate("0 Updates"));?>');
    });
  }

  <?php if ($this->updateSettings && $this->viewer->getIdentity()): ?>
  notificationUpdater = new NotificationUpdateHandler({
    'delay': <?php echo $this->updateSettings;?>
  });
  notificationUpdater.start();
  window._notificationUpdater = notificationUpdater;
  <?php endif;?>
});


var toggleUpdatesPulldown = function (event, element, user_id) {
  if (element.className == 'updates_pulldown') {
    element.className = 'updates_pulldown_active';
    showNotifications();
  } else {
    element.className = 'updates_pulldown';
  }
}

var showNotifications = function () {
  en4.activity.updateNotifications();
  new Request.HTML({
    'url': en4.core.baseUrl + 'activity/notifications/pulldown',
    'data': {
      'format': 'html',
      'page': 1
    },
    'onComplete': function (responseTree, responseElements, responseHTML, responseJavaScript) {
      if (responseHTML) {
        // hide loading icon
        if ($('notifications_loading')) $('notifications_loading').setStyle('display', 'none');

        $('notifications_menu').innerHTML = responseHTML;
        $('notifications_menu').addEvent('click', function (event) {
          event.stop(); //Prevents the browser from following the link.

          var current_link = event.target;
          var notification_li = $(current_link).getParent('li');

          // if this is true, then the user clicked on the li element itself
          if (notification_li.id == 'core_menu_mini_menu_update') {
            notification_li = current_link;
          }

          var forward_link;
          if (current_link.get('href')) {
            forward_link = current_link.get('href');
          } else {
            forward_link = $(current_link).getElements('a:last-child').get('href');
          }

          if (notification_li.get('class') == 'notifications_unread') {
            notification_li.removeClass('notifications_unread');
            en4.core.request.send(new Request.JSON({
              url: en4.core.baseUrl + 'activity/notifications/markread',
              data: {
                format: 'json',
                'actionid': notification_li.get('value')
              },
              onSuccess: function () {
                window.location = forward_link;
              }
            }));
          } else {
            window.location = forward_link;
          }
        });
      } else {
        $('notifications_loading').innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate("You have no new updates."));?>';
      }
    }
  }).send();
};
var asGlobal;
window.addEvent('domready', function () {
  $$('.as_global_search+label.overTxtLabel').set('html', '');
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

  $(document.body).addEvent('keydown', function(event){
    if (event.key == 'f' && event.shift && $(event.target).get('tag') == 'body')
    {
      $('global_search_field').focus();
      if ($(event.target).get('id') != 'global_search_field') {
        setTimeout(function(){$('global_search_field').set('value', '');}, 1);
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
      } else if ($$('div.as_global_found_more.active').length > 0){
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
        if ($('as_global_found_items').getChildren('.as_global_found_more').length == 0)
        {
          if($$('div.as_global_search_result.active').length > 0 || $$('div.as_global_found_more.active').length > 0)
          {
            var changeActive = false;
            if (event.key == 'up') {
              if ($$('div.as_global_found_more.active').length > 0) {
                changeActive = $$('div.as_global_found_more.active')[0].getParent().getPrevious('a');
              } else if($$('div.as_global_search_result.active').length > 0) {
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
    } else if (event.key == 'esc'){
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
      var url = '<?php echo $this->url(array('module' => 'advancedsearch', 'controller' => 'index', 'action' => 'search'), 'default')?>';
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
            var el = new Element('span').set('text', '<?php echo $this->translate("AS_Nothing found")?>');
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
});
</script>