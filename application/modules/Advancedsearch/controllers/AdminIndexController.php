<?php
/***/
class Advancedsearch_AdminIndexController extends Core_Controller_Action_Admin {
  protected $icons = array(
    'icon-adjust',
    'icon-asterisk',
    'icon-ban-circle',
    'icon-bar-chart',
    'icon-barcode',
    'icon-beaker',
    'icon-beer',
    'icon-bell',
    'icon-bell-alt',
    'icon-bolt',
    'icon-book',
    'icon-bookmark',
    'icon-bookmark-empty',
    'icon-briefcase',
    'icon-bullhorn',
    'icon-calendar',
    'icon-camera',
    'icon-camera-retro',
    'icon-certificate',
    'icon-check',
    'icon-check-empty',
    'icon-circle',
    'icon-circle-blank',
    'icon-cloud',
    'icon-cloud-download',
    'icon-cloud-upload',
    'icon-coffee',
    'icon-cog',
    'icon-cogs',
    'icon-comment',
    'icon-comment-alt',
    'icon-comments',
    'icon-comments-alt',
    'icon-credit-card',
    'icon-dashboard',
    'icon-desktop',
    'icon-download',
    'icon-download-alt',
    'icon-edit',
    'icon-envelope',
    'icon-envelope-alt',
    'icon-exchange',
    'icon-exclamation-sign',
    'icon-external-link',
    'icon-eye-close',
    'icon-eye-open',
    'icon-facetime-video',
    'icon-fighter-jet',
    'icon-film',
    'icon-filter',
    'icon-fire',
    'icon-flag',
    'icon-folder-close',
    'icon-folder-open',
    'icon-folder-close-alt',
    'icon-folder-open-alt',
    'icon-food',
    'icon-gift',
    'icon-glass',
    'icon-globe',
    'icon-group',
    'icon-hdd',
    'icon-headphones',
    'icon-heart',
    'icon-heart-empty',
    'icon-home',
    'icon-inbox',
    'icon-info-sign',
    'icon-key',
    'icon-leaf',
    'icon-laptop',
    'icon-legal',
    'icon-lemon',
    'icon-lightbulb',
    'icon-lock',
    'icon-unlock',
    'icon-magic',
    'icon-magnet',
    'icon-map-marker',
    'icon-minus',
    'icon-minus-sign',
    'icon-mobile-phone',
    'icon-money',
    'icon-move',
    'icon-music',
    'icon-off',
    'icon-ok',
    'icon-ok-circle',
    'icon-ok-sign',
    'icon-pencil',
    'icon-picture',
    'icon-plane',
    'icon-plus',
    'icon-plus-sign',
    'icon-print',
    'icon-pushpin',
    'icon-qrcode',
    'icon-question-sign',
    'icon-quote-left',
    'icon-quote-right',
    'icon-random',
    'icon-refresh',
    'icon-remove',
    'icon-remove-circle',
    'icon-remove-sign',
    'icon-reorder',
    'icon-reply',
    'icon-resize-horizontal',
    'icon-resize-vertical',
    'icon-retweet',
    'icon-road',
    'icon-rss',
    'icon-screenshot',
    'icon-search',
    'icon-share',
    'icon-share-alt',
    'icon-shopping-cart',
    'icon-signal',
    'icon-signin',
    'icon-signout',
    'icon-sitemap',
    'icon-sort',
    'icon-sort-down',
    'icon-sort-up',
    'icon-spinner',
    'icon-star',
    'icon-star-empty',
    'icon-star-half',
    'icon-tablet',
    'icon-tag',
    'icon-tags',
    'icon-tasks',
    'icon-thumbs-down',
    'icon-thumbs-up',
    'icon-time',
    'icon-tint',
    'icon-trash',
    'icon-trophy',
    'icon-truck',
    'icon-umbrella',
    'icon-upload',
    'icon-upload-alt',
    'icon-user',
    'icon-user-md',
    'icon-volume-off',
    'icon-volume-down',
    'icon-volume-up',
    'icon-warning-sign',
    'icon-wrench',
    'icon-zoom-in',
    'icon-zoom-out',
    'icon-file',
    'icon-file-alt',
    'icon-cut',
    'icon-copy',
    'icon-paste',
    'icon-save',
    'icon-undo',
    'icon-repeat',
    'icon-text-height',
    'icon-text-width',
    'icon-align-left',
    'icon-align-center',
    'icon-align-right',
    'icon-align-justify',
    'icon-indent-left',
    'icon-indent-right',
    'icon-font',
    'icon-bold',
    'icon-italic',
    'icon-strikethrough',
    'icon-underline',
    'icon-link',
    'icon-paper-clip',
    'icon-columns',
    'icon-table',
    'icon-th-large',
    'icon-th',
    'icon-th-list',
    'icon-list',
    'icon-list-ol',
    'icon-list-ul',
    'icon-list-alt',
    'icon-angle-left',
    'icon-angle-right',
    'icon-angle-up',
    'icon-angle-down',
    'icon-arrow-down',
    'icon-arrow-left',
    'icon-arrow-right',
    'icon-arrow-up',
    'icon-caret-down',
    'icon-caret-left',
    'icon-caret-right',
    'icon-caret-up',
    'icon-chevron-down',
    'icon-chevron-left',
    'icon-chevron-right',
    'icon-chevron-up',
    'icon-circle-arrow-down',
    'icon-circle-arrow-left',
    'icon-circle-arrow-right',
    'icon-circle-arrow-up',
    'icon-double-angle-left',
    'icon-double-angle-right',
    'icon-double-angle-up',
    'icon-double-angle-down',
    'icon-hand-down',
    'icon-hand-left',
    'icon-hand-right',
    'icon-hand-up',
    'icon-circle',
    'icon-circle-blank',
    'icon-play-circle',
    'icon-play',
    'icon-pause',
    'icon-stop',
    'icon-step-backward',
    'icon-fast-backward',
    'icon-backward',
    'icon-forward',
    'icon-fast-forward',
    'icon-step-forward',
    'icon-eject',
    'icon-fullscreen',
    'icon-resize-full',
    'icon-resize-small',
    'icon-phone',
    'icon-phone-sign',
    'icon-facebook',
    'icon-facebook-sign',
    'icon-twitter',
    'icon-twitter-sign',
    'icon-github',
    'icon-github-alt',
    'icon-github-sign',
    'icon-linkedin',
    'icon-linkedin-sign',
    'icon-pinterest',
    'icon-pinterest-sign',
    'icon-google-plus',
    'icon-google-plus-sign',
    'icon-sign-blank',
    'icon-ambulance',
    'icon-beaker',
    'icon-h-sign',
    'icon-hospital',
    'icon-medkit',
    'icon-plus-sign-alt',
    'icon-stethoscope',
    'icon-user-md'
  );
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('advancedsearch_admin_main', array(), 'advancedsearch_admin_main_types');
    if ($this->getRequest()->isPost()) {

      $values = $this->_getParam('types');
      // Check license
      $hecoreApi = Engine_Api::_()->getApi('core', 'hecore');
      $product_result = $hecoreApi->checkProduct('advancedsearch');

      if (isset($product_result['result']) && !$product_result['result']) {
        $this->view->formSaved  = $product_result['message'];
        $this->view->headScript()->appendScript($product_result['script']);

        return;
      }
      $joinedTypes = array(
        'album' => array(
          'album','pagealbum', 'pagealbumphoto'
        ),
        'video' => array(
          'video', 'pagevideo'
        ),
        'music' => array(
          'music','song', 'playlist', 'music_playlist','music_playlist_song',
        ),
        'blog' => array(
          'blog', 'pageblog'
        ),
        'discussion' => array(
          'discussion','pagediscussion_pagepost','pagediscussion_pagetopic', 'forum_post', 'forum_topic'
        ),
        'review' => array(
          'review', 'pagereview', 'offerreview'
        ),
        'event' => array(
          'event', 'pageevent'
        )
      );
      if(empty($values)){return;}
      foreach ($values as &$value) {
        if (isset($joinedTypes[$value])) {
          $value = implode(',', $joinedTypes[$value]);
        }
      }
      $showList = implode(',', $values);
      Engine_Api::_()->getApi('settings', 'core')->setSetting('advancedsearch.typeslist', $showList);
      $this->view->formSaved = 'AS_Your changes have been saved.';
    }
    $list = Engine_Api::_()->getApi('settings', 'core')->getSetting('advancedsearch.typeslist');
    $this->view->viewList = explode(',', $list);
    $this->view->types = Engine_Api::_()->advancedsearch()->getAvailableTypesAdmin();
  }

  public function iconsAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('advancedsearch_admin_main', array(), 'advancedsearch_admin_main_icons');
    $this->view->types = Engine_Api::_()->advancedsearch()->getAvailableTypesAdmin();
    $this->view->icons = $this->icons;

    $db = Engine_Db_Table::getDefaultAdapter();
    $iconTable = Engine_Api::_()->getDbTable('icons', 'advancedsearch');
    $itemIcons = $iconTable->select()
    ->from(array('i' => $iconTable->info('name')), array('item','icon'));
    $this->view->itemicons = $itemIcons = $db->fetchPairs($itemIcons);
  }

  public function iconchangeAction()
  {
    $translate = Zend_Registry::get('Zend_Translate');
    $this->view->icons = $this->icons;
    $item = $this->_getParam('item');
    if (!in_array($item, Engine_Api::_()->advancedsearch()->getAvailableTypesAdmin())) {
      print_die($item);
    }
    $iconTable = Engine_Api::_()->getDbTable('icons', 'advancedsearch');
    $itemIcon = $iconTable->select()
      ->where("item=?", $item);
    $itemType = $iconTable->fetchRow($itemIcon);
    if ($itemType) {
      $this->view->item = $itemType;
    }
    if (!$this->getRequest()->isPost()){
      return;
    }
    $icon = $this->_getParam('icon');
    if (in_array($icon, $this->icons)) {
      $db = $iconTable->getAdapter();
      try {
        if ($itemType) {
          $itemType['icon'] = $icon;
          $itemType->save();
        } else {
          $itemIcon = $iconTable->createRow();
          $itemIcon->setFromArray(
            array(
              'item' => $item,
              'icon' => $icon
            )
          );
          $itemIcon->save();
        }
        $db->commit();
        $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => true,
          'parentRefresh' => true,
          'messages' => array($translate->translate('AS_Changes saved'))
        ));
      } catch (Exception $e) {
        throw $e;
        $db->rollBack();
      }
    }
  }
}