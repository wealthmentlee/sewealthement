<?php

/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 09.08.12
 * Time: 10:47
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_Controller_Action_Helper_Components extends Apptouch_Controller_Action_Helper_Abstract
{
    protected $components = null;

    // Navigation related properties {
    protected $navigations = array();
    protected $nav_postfixes = array(
        'main' => '_main',
        'quick' => '_quick',
        'profile' => '_profile',
        'tab' => '_tab',
        'gutter' => '_gutter'
    );
    protected $availableComponents = null;

    // } Navigation related properties
    public function __construct()
    {
    }

    public function customComponent($name, $params = null)
    {
        return $this->formatComponent($name, $params);
    }

    public function dashboard()
    {
        return $this->formatComponent('dashboard', $this->getNavigation(Engine_Api::_()->getApi('menus', 'apptouch')->getNavigation('core_dashboard'), true, 'core_dashboard'));
    }

    public function adCampaign($campaign)
    {
        if(Engine_Api::_()->apptouch()->isApp()) {
            return array();
        }
        // Check limits, start, and expire
        if (!$campaign->isActive()) {
            return;
        }

        // Get viewer
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$campaign->isAllowedToView($viewer)) {
            return;
        }
        $table = Engine_Api::_()->getDbtable('ads', 'apptouch');
        $select = $table->select()->where('ad_campaign = ?', $campaign->adcampaign_id)->order('views ASC');
        // Get ad
        if (!($ad = $table->fetchRow($select))) {
            return;
        }

        // Okay
        $campaign->views++;
        $campaign->save();
        $ad->views++;
        $ad->save();
        print_firebug(Zend_Json::encode($ad->toArray()));
        $campaignFormat = array(
            'ad' => $ad->toArray(),
            'url' => $this->view->url(array('module' => 'core', 'controller' => 'utility', 'action' => 'advertisement'), 'default', true),
            'campaign_id' => $campaign->adcampaign_id
        );
        return array(
            'name' => 'adCampaign',
            'params' => $campaignFormat
        );
    }

    public function footerMenu($nav, $by_name = false, $name = null)
    {
        return $this->formatComponent('footerMenu', $this->getNavigation($nav, $by_name, $name));
    }

    public function quickLinks($menu, $title = null, $nav_by_name = false)
    {
        if ($title === true) {
            $nav_by_name = true;
            $title = null;
        }

        if (!is_array($menu))
            $menu = $this->getNavigation($menu, $nav_by_name, 'menu_quick');

        if (!$title)
            $title = $this->_bridge->getPageTitle();
        foreach ($menu as $menuitem) {
            $menuitem;
        }


        return $this->formatComponent('quickLinks', array(
            'title' => $title,
            'menu' => $menu
        ));
    }

    public function navigation($nav, $by_name = false, $name = null)
    {
        return $this->formatComponent('navigation', $this->getNavigation($nav, $by_name, $name));
    }

    public function subjectPhoto($item = null)
    {
        $subject = null;
        if ($item instanceof Core_Model_Item_Abstract) {
            $subject = $this->_bridge->subject($item);
        } elseif ($item) {
            $subject = $item;
        } else {
            $subject = $this->_bridge->subject();
        }

        return $this->formatComponent('subjectPhoto', $subject);
    }

    public function form(Engine_Form $form)
    {
        return $this->formatComponent('form', $form->render($this->view));
    }

    public function html($html)
    {
        if (is_array($html)) {
            $asStr = '';
            foreach ($html as $elem) {
                if ($elem instanceof Apptouch_Controller_Action_Helper_Dom_Element)
                    $asStr .= $elem->toString();
            }
            $html = $asStr;
        } elseif ($html instanceof Apptouch_Controller_Action_Helper_Dom_Element) {
            $html = $html->toString();
        }
        return $this->formatComponent('html', $html);
    }

    public function date($date, $do_format = false)
    {
      $title = $date['title'];
      $countNum = $date['count'];
      $count = <<<COUNT
      <span class="ui-li-count">
        {$countNum}
      </span>
COUNT;
      $html = <<<HTML
<ul data-role="listview" data-theme="d" data-divider-theme="d" class="component-date">
      <li data-role="list-divider">
         {$title}
        {$count}
      </li>
</ul>
HTML;

        return $this->formatComponent('html', $html);
//        return array();
    }

    public function inviter($providers = array(), $customizerFunction = null, $params = array())
    {
        $component = array();
        $items = array();
        $viewer = Engine_Api::_()->user()->getViewer();
        $data = array();
        $redirect_url = '';
        $fail_message = '';
        foreach ($providers as $key => $value) {
            $params = array();
            if ($key != 'facebook')
                $url = $this->view->url(array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'request', 'provider' => $key), 'default');
            else {
                $codes_tbl = Engine_Api::_()->getDbTable('codes', 'inviter');
                $name = $this->view->translate('INVITER_Join our social network!');
                $host_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
                $redirect_url = $host_url . $this->view->url(array('module' => 'inviter', 'controller' => 'index', 'action' => 'facebookaftersend'), 'default', true);
                $data = array(
                    'method' => 'send',
                    'display' => 'popup',
                    'to' => '',
                    'link' => $host_url,
                    'name' => $name,
                    'description' => Engine_Api::_()->getApi('settings', 'core')->getSetting('invite.message') . ' '
                        . $codes_tbl->getUserReferralLink($viewer->getIdentity(), false),
                    'picture' => $host_url . Engine_Api::_()->inviter()->getItemPhotoUrl($viewer, $this->view->layout()->staticBaseUrl),
                    'show_error' => true
                );
                $fail_message = $this->view->translate('INVITER_Invitations not sent');
            }
            $items[] = array(
                'provider' => $key,
                'data' => Zend_Json_Encoder::encode($data),
                'fail_message' => $fail_message,
                'redirect_url' => $redirect_url,
                'href' => $url,
                'photoUrl' => (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->view->baseUrl() . '/application/modules/Inviter/externals/images/providers_big/' . $key . '_logo.png'
            );
        }
        $component = array_merge($component, $params);
        $component['items'] = $items;
        return $this->formatComponent('inviter', $component);
    }

    public function inviterInvitesList(Zend_Paginator $paginator, $customizerFunction = null, $params = array())
    {
        $component = array();
        $items = array();
        foreach ($paginator as $item) {
            $photo_path = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->view->baseUrl() . '/application/modules/Inviter/externals/images/providers/';
            $std = array(
                'recipient' => $item->recipient,
                'provider' => ($item->provider) ? $item->provider : 'email',
                'photo' => $photo_path . $item->provider . '_logo.png',
                'sent_date' => isset($item->sent_date) ? $this->view->timestamp(strtotime($item->sent_date)) : "",
                'delete_href' => $this->view->url(array('module' => 'inviter', 'controller' => 'invitations', 'action' => 'delete', 'id' => $item->invite_id), 'default'),
                'sendnew_href' => $this->view->url(array('module' => 'inviter', 'controller' => 'invitations', 'action' => 'sendnew', 'id' => $item->invite_id), 'default'),
            );

            $items[] = $std;
        }

        $searchKeyword = $this->_getParam('search', false);
        if ($searchKeyword) {
            $component['search'] = array(
                'keyword' => $searchKeyword . '', // to string
                'count' => $paginator->getTotalItemCount(),
            );
        }

        $component = array_merge($component, $params);
        $component['items'] = $items;

        return $this->formatComponent('inviterInvitesList', $component);
    }

    public function inviterContactsList($contacts = array(), $customizerFunction = null, $params = array())
    {
        $component = array();
        $items = array();
        foreach ($contacts as $contact) {
            if ($params['provider'] == 'twitter') {
                $id = $contact['id'];
            } else {
                $id = $contact['nid'];
            }
            $name = $contact['name'];
            $items[] = array(
                'id' => $id,
                'name' => $name
            );
        }
        $params['list'] = $contacts;
        $component = array_merge($component, $params);
        $component['items'] = $items;
        return $this->formatComponent('inviterContactsList', $component);
    }

    public function itemSearch(Apptouch_Form_Search $form)
    {
        return $this->formatComponent('itemSearch', $form->render($this->view));
    }

    public function itemList(Zend_Paginator $paginator, $customizerFunction = null, $params = array())
    {
        $isTablet = (Engine_Api::_()->apptouch()->isTabletMode() && !empty($params['attrs']) && !empty($params['attrs']['class']) && $params['attrs']['class'] == 'tile-view');

      $params['autoscroll'] = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('apptouch.content.autoscroll', false);

      if( !isset($params['listPaginator']) ) {
        $params['listPaginator'] = false;
      }

      if( $params['listPaginator'] && $params['autoscroll'])
        $this->_bridge->attrPage('class', $this->_bridge->attrPage('class') . ' auto-scroll');

      // for list paginator
      $paginatorPages = $paginator->getPages();
      if ($paginatorPages->pageCount > 1 && $paginatorPages->totalItemCount) {
        $params['pageCount'] = $paginatorPages->pageCount;
        $params['next'] = @$paginatorPages->next;
        $params['paginationParam'] = 'page';
      }

        $component = array();
        $items = array();
        if ($paginator->getTotalItemCount())
            foreach ($paginator as $item) {
                if (!($item instanceof Core_Model_Item_Abstract) && $item) {
                    if ($item instanceof Engine_Db_Table_Row) {
                        $old = $item;
                        $item = $this->view->item($item->type, $item->id);
                    } else {
                        throw new Apptouch_Controller_Action_BridgeException('Invalid items stored into Paginator ' . get_class($item));
                    }
                }
                try {
                    if (!$item) continue;

                    $owner = @$item->getOwner();
                    $has_owner = true;
                } catch (Exception $e) {
                    $has_owner = false;
                }
                $title = $item->getTitle();
                if ($title == '') {
                    $title = $item->getDescription();
                }
                $photoUrl = $item->getPhotoUrl('thumb.normal');
                if ($isTablet) {
                    $photoUrl = $item->getPhotoUrl('thumb.profile');
                }
                if (!isset($item->photo_id) && !isset($item->file_id)) {
                    $photoUrl = false;
                }

                $std = array(
                    'title' => $title,
                    'descriptions' => array(),
                    'href' => $item->getHref(),
                    'photo' => $photoUrl,
                    'creation_date' => isset($item->creation_date) ? $this->view->timestamp(strtotime($item->creation_date)) : "",
                    'type' => $item->getType(),
                );

                if ($has_owner) {
                    $std['descriptions'][] = $this->view->translate('By') . ' ' . $owner->getTitle();
                    $std['owner_id'] = $owner->getIdentity();
                    $std['owner'] = $this->_bridge->subject($owner);
                }

                if (is_string($customizerFunction)) {
                    try {
                        $formatted = array_merge($std, $this->_bridge->$customizerFunction($item));
                    } catch (Exception $e) {
                        throw $e; //new Apptouch_Controller_Action_BridgeException($e->getMessage() . ': You must implement public method named identical with the value of second parameter');
                    }
                } else if ($this->_bridge->getFormat() == 'manage' || $this->_bridge->getFormat() == 'profile') {
                    $std['descriptions'] = null;
                    $std['owner_id'] = null;
                    $std['owner'] = null;
                    $formatted = $std;
                } else $formatted = $std;

                $items[] = $formatted;
            }

        $searchKeyword = $this->_getParam('search', false);
        if ($searchKeyword) {
            $component['search'] = array(
                'keyword' => $searchKeyword . '', // to string
                'count' => $paginator->getTotalItemCount(),
            );
        }

        $component = array_merge($component, $params);
        $component['items'] = $items;

        if (!empty($items))
            return $this->formatComponent('itemList', $component);
        else
            return $this->tip($this->view->translate('APPTOUCH_NO_ITEMS'));
    }


    public function badgesList(Zend_Paginator $paginator, $complete = null, $customizerFunction = null, $params = array())
    {
        $component = array();
        $items = array();
        $members = array();
        if (!empty($params['members']))
            $members = $params['members'];
        foreach ($paginator as $item) {
            if (!($item instanceof Core_Model_Item_Abstract)) {
                if ($item instanceof Engine_Db_Table_Row) {
                    $old = $item;
                    $item = $this->view->item($item->type, $item->id);
                } else {
                    throw new Apptouch_Controller_Action_BridgeException('Invalid items stored into Paginator ' . get_class($item));
                }
            }
            try {
                if (!$item) continue;
                $owner = $item->getOwner();
                $has_owner = true;
            } catch (Exception $e) {
                $has_owner = false;
            }
            //            $has_owner = false;
            $title = $item->getTitle();
            if ($title == '') {
                $title = $item->getDescription();
            }
            $photoUrl = $item->getPhotoUrl('thumb.normal');
            if (!isset($item->photo_id) && !isset($item->file_id)) {
                $photoUrl = false;
            }
            $current_complete = '';
            if ($complete && !empty($complete) && isset($complete[$item->getIdentity()])) {
                $current_complete = $complete[$item->getIdentity()] . '%';
            }
            $class = '';
            if (!empty($members[$item->getIdentity()])) {
                $class = 'badge_active';
            }

            $std = array(
                'title' => $title,
                'attrsLi' => array('class' => $class),
                'descriptions' => array(),
                'href' => $item->getHref(),
                'photo' => $photoUrl,
                'members' => $this->view->translate(array('%1$s member', '%1$s members', $item->member_count), $item->member_count),
                'members_href' => $this->view->url(array('action' => 'view', 'id' => $item->getIdentity(), 'tab' => 'members'), 'hebadge_profile', true),
                'complete' => $current_complete,
                //                'creation_date' => isset($item->creation_date) ? $this->view->timestamp(strtotime($item->creation_date)) : "",
                'show_approved' => true,
                'approved' => 'active',
                'approved_text' => $this->view->translate('HEBADGE_APPROVED')
            );

            if ($has_owner) {
                $std['descriptions'][] = $this->view->translate('By') . ' ' . $owner->getTitle();
                $std['owner_id'] = $owner->getIdentity();
                $std['owner'] = $this->_bridge->subject($owner);
            }

            if (is_string($customizerFunction)) {
                try {
                    $formatted = array_merge($std, $this->_bridge->$customizerFunction($item));
                } catch (Exception $e) {
                    throw $e; //new Apptouch_Controller_Action_BridgeException($e->getMessage() . ': You must implement public method named identical with the value of second parameter');
                }
            } else if ($this->_bridge->getFormat() == 'manage' || $this->_bridge->getFormat() == 'profile') {
                $std['descriptions'] = null;
                $std['owner_id'] = null;
                $std['owner'] = null;
                $formatted = $std;
            } else $formatted = $std;

            $items[] = $formatted;
        }

        $searchKeyword = $this->_getParam('search', false);
        if ($searchKeyword) {
            $component['search'] = array(
                'keyword' => $searchKeyword . '', // to string
                'count' => $paginator->getTotalItemCount(),
            );
        }

        $component = array_merge($component, $params);
        $component['items'] = $items;
        return $this->formatComponent('badgesList', $component);
    }

    public function manageBadgesList(Zend_Paginator $paginator, $customizerFunction = null, $params = array())
    {
        $component = array();
        $items = array();
        $members = array();
        if (!empty($params['members']))
            $members = $params['members'];
        unset($params['members']);
        foreach ($paginator as $item) {
            if (!($item instanceof Core_Model_Item_Abstract)) {
                if ($item instanceof Engine_Db_Table_Row) {
                    $old = $item;
                    $item = $this->view->item($item->type, $item->id);
                } else {
                    throw new Apptouch_Controller_Action_BridgeException('Invalid items stored into Paginator ' . get_class($item));
                }
            }
            $title = $item->getTitle();
            if ($title == '') {
                $title = $item->getDescription();
            }
            $photoUrl = $item->getPhotoUrl('thumb.normal');
            if (!isset($item->photo_id) && !isset($item->file_id)) {
                $photoUrl = false;
            }
            $class = '';
            $approved_class = '';
            if (!empty($members[$item->getIdentity()])) {
                $class = 'active';
                if ($members[$item->getIdentity()]->approved) {
                    $approved_class = 'active';
                }
            }
            if ($approved_class == 'active') {
                $lang = $this->view->translate('Enabled');
            } else {
                $lang = $this->view->translate('Disabled');
            }

            $std = array(
                'title' => $title,
                'id' => $item->getIdentity(),
                'href' => $item->getHref(),
                'photo' => $photoUrl,
                'members' => $this->view->translate(array('%1$s member', '%1$s members', $item->member_count), $item->member_count),
                'members_href' => $this->view->url(array('action' => 'view', 'id' => $item->getIdentity(), 'tab' => 'members'), 'hebadge_profile', true),
                'show_approved' => true,
                'approved' => $approved_class,
                'approved_text' => $lang,
                'attrsLi' => array('class' => $class)
            );

            if (is_string($customizerFunction)) {
                try {
                    $formatted = array_merge($std, $this->_bridge->$customizerFunction($item));
                } catch (Exception $e) {
                    throw $e; //new Apptouch_Controller_Action_BridgeException($e->getMessage() . ': You must implement public method named identical with the value of second parameter');
                }
            } else if ($this->_bridge->getFormat() == 'manage' || $this->_bridge->getFormat() == 'profile') {
                $std['descriptions'] = null;
                $std['owner_id'] = null;
                $std['owner'] = null;
                $formatted = $std;
            } else $formatted = $std;

            $items[] = $formatted;
        }

        $searchKeyword = $this->_getParam('search', false);
        if ($searchKeyword) {
            $component['search'] = array(
                'keyword' => $searchKeyword . '', // to string
                'count' => $paginator->getTotalItemCount(),
            );
        }

        $component = array_merge($component, $params);
        $component['items'] = $items;
        return $this->formatComponent('manageBadgesList', $component);
    }

    public function profileBadgesList(Zend_Paginator $paginator, $complete = null, $customizerFunction = null, $params = array())
    {
        $component = array();
        $items = array();

        foreach ($paginator as $item) {
            if (!($item instanceof Core_Model_Item_Abstract)) {
                if ($item instanceof Engine_Db_Table_Row) {
                    $old = $item;
                    $item = $this->view->item($item->type, $item->id);
                } else {
                    throw new Apptouch_Controller_Action_BridgeException('Invalid items stored into Paginator ' . get_class($item));
                }
            }

            $photoUrl = $item->getPhotoUrl('thumb.normal');
            if (!isset($item->photo_id) && !isset($item->file_id)) {
                $photoUrl = false;
            }
            $items[] = array(
                'href' => $item->getHref(),
                'photo' => $photoUrl,
            );
        }

        $component = array_merge($component, $params);
        $component['items'] = $items;
        return $this->formatComponent('profileBadgesList', $component);
    }

    public function badgeProfile(Hebadge_Model_Badge $badge, $params = array())
    {
        $viewer = Engine_Api::_()->user()->getVIewer();
        $member = $badge->getMember($viewer);

        $component = array();
        /*
         * Структура - так же массив items, из одного элемента
         * Этот элемент содержит
         *  * массив requirements
         *  * описание
         *  * доп. инфу (типа фотки, ссылки и т.п.)
        */
        $item = array();
        $content = array();
        $content['id'] = $badge->getIdentity();
        $content['body'] = $badge->description;
        $content['descr'] = $badge->body;
        $content['href'] = $badge->getHref();
        $content['title'] = ($badge->getTitle() != '') ? $badge->getTitle() : $badge->getDescription();
        $content['photo'] = $badge->getPhotoUrl('thumb.normal');
        $content['members'] = $this->view->translate(array('%1$s member', '%1$s members', $badge->member_count), $badge->member_count);
        $content['members_href'] = $this->view->url(array('id' => $badge->getIdentity(), 'tab' => 'members'), 'hebadge_profile', true);
        if ($member) {
            $content['show_approved'] = true;
            $content['approved'] = ($member->approved) ? 'active' : '';
            if ($member->approved) {
                //                $content['approved'] = 'active';
                $lang = $this->view->translate('Enabled');
            } else {
                $content['approved'] = '';
                $lang = $this->view->translate('Disabled');
            }
            $content['approved_text'] = $lang;
        }

        $require = $badge->getRequire();
        $require_complete = Engine_Api::_()->getDbTable('require', 'hebadge')->getCompleteRequireIds($viewer, $badge);


        if ($member) {
            $complete = 100;
        } else {
            $complete = floor(count($require_complete) / count($require) * 100);
        }
        $content['complete'] = $complete;
        $content['complete_text'] = $this->view->translate('HEBADGE_LOADER_DESCRIPTION', $complete . '%');

        $item[] = $content;
        $component = array_merge($component, $params);
        $component['item'] = $content;
        return $this->formatComponent('badgeProfile', $component);
    }

    public function cartTotal($params = array(), $gateway)
    {
        $component = array(
            'prices' => $params,
            'gateway' => $gateway
        );
        return $this->formatComponent('cartTotal', $component);
    }

    public function creditCheckout($params = array())
    {
        $component = array();
        foreach ($params as $key => $value) {
            $component[$key] = $value;
        }
        return $this->formatComponent('creditCheckout', $component);
    }

    public function transactionFinish($params = array())
    {
        $component = array(
            'params' => $params
        );
        return $this->formatComponent('transactionFinish', $component);
    }

    public function timelineCover()
    {
        /**
         * @var $user Users_Model_User
         */
        $user = Engine_Api::_()->core()->getSubject();

        $component = array();
        $coverPhoto = Engine_Api::_()->timeline()->getTimelinePhoto($user->getIdentity(), 'user', 'cover');

        $position = $coverPhoto['position'];
        try {
            $position = json_decode($position);
            $component['position'] = array(
              'left' => $position->left,
              'top' => $position->top
            );
        } catch (Exception $e) {
            $component['position'] = array('top' => 0, 'left' => 0);
        }

        $component['cover_photo'] = $user->getTimelinePhoto('cover');
        $component['user'] = $this->_bridge->subject($user);
        $component['choose'] = $this->view->url(array('id' => $user->getIdentity()), 'timeline_photo');
        $component['upload'] = $this->view->url(array('action' => 'upload', 'id' => $user->getIdentity()), 'timeline_photo');

        $component['canChange'] = 0;
        if ($user && $user->isSelf(Engine_Api::_()->user()->getViewer())) {
            $component['canChange'] = 1;
        }

        $component['remove'] = $this->view->url(array('action' => 'remove', 'id' => $user->getIdentity()), 'timeline_photo');
        return $this->formatComponent('timelineCover', $component);
    }
    public function heEventCover()
    {
        $modules = Engine_Api::_()->getDbTable('modules', 'core');
        if (!$modules->isModuleEnabled('heevent')) return $this->setNoRender();
        $viewer = Engine_Api::_()->user()->getViewer();
        if( !Engine_Api::_()->core()->hasSubject() ) {
            return $this->setNoRender();
        }

        $event = Engine_Api::_()->core()->getSubject();
        $event = Engine_Api::_()->core()->getSubject();
        // Get subject and check auth
       $event = Engine_Api::_()->core()->getSubject('event');
        try {
            $component['position'] = array(
                'left' => 0,
                'top' => 0
            );
        } catch (Exception $e) {
            $component['position'] = array('top' => 0, 'left' => 0);
        }
        $component['cover_photo'] = '<img id="cover-photo"  src='.$event->getPhotoUrl().'/>';
        $component['photo'] = $event->getPhotoUrl();
        $component['title'] = $event->getTitle();
        return $this->formatComponent('heEventCover', $component);
    }

    public function timelineCoverAlbums(Zend_Paginator $paginator, $user)
    {
        $component = array();
        $items = array();
        foreach ($paginator as $item) {
            $href = $item->getHref();
            $href = $this->view->url(array('action' => 'photos', 'id' => $user->getIdentity(), 'type' => 'cover', 'album_id' => $item->getIdentity()), 'timeline_photo');
            $items[] = array(
                'id' => $item->getIdentity(),
                'href' => $href,
                'photo' => $item->getPhotoUrl('thumb.normal'),
                'title' => $item->getTitle()
            );
        }

        $component = array('items' => $items);
        return $this->formatComponent('timelineCoverAlbums', $component);
    }

    public function timelineCoverPhotos(Zend_Paginator $paginator, $user)
    {
        $items = array();
        foreach ($paginator as $item) {
            $href = $this->view->url(array('action' => 'set', 'id' => $user->getIdentity(), 'type' => 'cover', 'photo_id' => $item->getIdentity()), 'timeline_photo');
            $items[] = array(
                'id' => $item->getIdentity(),
                'href' => $href,
                'photo' => $item->getPhotoUrl('thumb.normal'),
                'title' => $item->getTitle()
            );
        }
        $component = array('items' => $items);
        return $this->formatComponent('timelineCoverPhotos', $component);
    }

    public function playlist($audios, $params = array())
    {
        $component = array();
        $items = array();
        if ($audios instanceof Engine_Db_Table_Rowset) {

        } elseif ($audios instanceof Zend_Paginator) {
            $audios->setItemCountPerPage($audios->getTotalItemCount());
        } else
            throw new Apptouch_Controller_Action_BridgeException('Invalid items stored');

        foreach ($audios as $item) {
            if (!($item instanceof Core_Model_Item_Abstract))
                throw new Apptouch_Controller_Action_BridgeException('Invalid items stored');

            $std = array(
                'title' => $item->getTitle(),
            );
            $file = Engine_Api::_()->getItem('storage_file', $item->file_id);
            if ($file) {
                $std['href'] = $file->map();
            }

            if (isset($item->play_count))
                $std['play_count'] = $item->play_count;

            $formatted = $std;

            $items[] = $formatted;
        }

        $component = array_merge($component, $params);
        $component['items'] = $items;


        return $this->formatComponent('playlist', $component);
    }

    public function video(Core_Model_Item_Abstract $video)
    {
        $prefix = (constant('_ENGINE_SSL') ? 'https://' : 'http://');

        $videoFormat = array();
        if ($video instanceof Video_Model_Video || $video instanceof Pagevideo_Model_Pagevideo || $video instanceof Store_Model_Video) {
            /**
             * @var $video  Video_Model_Video
             */

            $videoFormat['video_type'] = $video->type;

            if ($video->type == 1) {
                $videoFormat['iframeUrl'] = $prefix . 'www.youtube.com/embed/' . $video->code;
            }
            if ($video->type == 2) {
                $videoFormat['iframeUrl'] = $prefix . 'player.vimeo.com/video/' . $video->code;
            }


            if ($video->type == 3) {
                $videoFormat['video_thumb'] = $this->view->itemPhoto($video);
                $videoFormat['video_location'] = $video_location = Engine_Api::_()->storage()->get($video->file_id, $video->getType())->getHref();
//        $videoFormat['flashObject'] = "<object class=\"flowplayer\" width=\"480\" height=\"386\" type=\"application/x-shockwave-flash\"
//          data=\"" . Zend_Registry::get('StaticBaseUrl') . "externals/flowplayer/flowplayer-3.1.5.swf\"><param value=\"true\" name=\"allowfullscreen\">
//            <param value=\"always\" name=\"allowscriptaccess\">
//            <param value=\"high\" name=\"quality\">
//            <param value=\"transparent\" name=\"wmode\">
//            <param value=\"config={'clip':{'url':'" . $video_location . "','autoPlay':false,'duration':'" . $video->duration . "','autoBuffering':true},'plugins':{'controls':{'background':'#000000','bufferColor':'#333333','progressColor':'#444444','buttonColor':'#444444','buttonOverColor':'#666666'}},'canvas':{'backgroundColor':'#000000'}}\" name=\"flashvars\">
//          </object>";
            } else
                $videoFormat['flashObject'] = $video->getRichContent(true);
        }

        return $this->formatComponent('video', $videoFormat);
    }

    public function mediaControls()
    {
        return $this->formatComponent('mediaControls', array());
    }

    public function gallery(Zend_Paginator $paginator, Core_Model_Item_Abstract $activeSubject = null, $options = array())
    {
        $gallery = array();
        $photo_gallery = array();
        $options = array_merge(array('canComment' => false), $options);
        /**
         * @var $paginator Zend_Paginator
         */

        $paginator->setItemCountPerPage($paginator->getTotalItemCount());

        foreach ($paginator as $item_photo) {
            $tmp = $this->_bridge->subject($item_photo);
            $tmp['href'] = $item_photo->getHref(array('comments' => 'write'));
            $photo_gallery[] = $tmp;
            unset($tmp);
        }

        $options['canViewTags'] = Engine_Api::_()->hasModuleBootstrap('photoviewer') && Engine_Api::_()->user()->getViewer()->getIdentity();

        $gallery['photos'] = $photo_gallery;
        $gallery['options'] = $options;
        if ($activeSubject)
            $gallery['active'] = $activeSubject->getIdentity();

        return $this->formatComponent('gallery', $gallery);
    }

    public function paginator(Zend_Paginator $paginator, $paginationParam = 'page ')
    {
        $params = null;
        $paginatorPages = $paginator->getPages();
        if ($paginatorPages->pageCount > 1 && $paginatorPages->totalItemCount) {
            $params = array(
                'pageCount' => $paginatorPages->pageCount,
                'current' => $paginatorPages->current,
                'prev' => @$paginatorPages->previous,
                'next' => @$paginatorPages->next,
                'itemCountPerPage' => $paginatorPages->itemCountPerPage,
                'totalItemCount' => $paginatorPages->totalItemCount,
                'paginationParam' => $paginationParam,
            );
            return $this->formatComponent('paginator', $params);
        }
        return null;
    }

    // Feed
    public function feed(array $params = array())
    {

        $this->_bridge->attrPage('class', $this->_bridge->attrPage('class') . ' smooth-scroll');
        $feed = array();

        // Don't render this if not authorized
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = null;
        if (Engine_Api::_()->core()->hasSubject()) {

            // Get subject
            $subject = Engine_Api::_()->core()->getSubject();
            if (!$subject->authorization()->isAllowed($viewer, 'view')) {
                return;
            }
            if (Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('timeline'))
                if ($subject instanceof User_Model_User) {
                    $subject = Engine_Api::_()->getDbTable('users', 'timeline')->findRow($subject->getIdentity());
                    $feed['dates'] = Engine_Api::_()->timeline()->timelineDates($subject);
                } else if ($subject instanceof Timeline_Model_User) {
                    $feed['dates'] = Engine_Api::_()->timeline()->timelineDates($subject);
                } else {
                }
        }

        $isWall = 0;
        if (Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('wall')) {
            $isWall = 1;
        }

        $request = Zend_Controller_Front::getInstance()->getRequest();

        if ($isWall) {
            //@TODO Check, if timeline
            if (isset($feed['dates'])) {
                $actionTable = Engine_Api::_()->getDbtable('actions', 'timeline');
            } else {
                $actionTable = Engine_Api::_()->getDbtable('actions', 'wall');
            }
        } else {
            $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
        }

        // Get some options
        $feedOnly = $request->getParam('feedOnly', false);
        $length = $request->getParam('limit', Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.length', 15));
        $itemActionLimit = Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.userlength', 5);

        $updateSettings = (int)Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.liveupdate', 30000);
        $viewAllLikes = $request->getParam('viewAllLikes', $request->getParam('show_likes', false));
        $viewAllComments = $request->getParam('viewAllComments', $request->getParam('show_comments', false));
        $getUpdate = $request->getParam('getUpdate');
        $checkUpdate = $request->getParam('checkUpdate');
        $action_id = (int)$request->getParam('action_id');
        $post_failed = (int)$request->getParam('pf');


        if ($length > 50) {
            $length = 50;
        }

        // Get all activity feed types for custom view?
        //    $actionTypesTable = Engine_Api::_()->getDbtable('actionTypes', 'activity');
        //    $this->view->groupedActionTypes = $groupedActionTypes = $actionTypesTable->getEnabledGroupedActionTypes();
        //    $actionTypeGroup = $request->getParam('actionFilter');
        //    $actionTypeFilters = array();
        //    if( $actionTypeGroup && isset($groupedActionTypes[$actionTypeGroup]) ) {
        //      $actionTypeFilters = $groupedActionTypes[$actionTypeGroup];
        //    }

        // Get config options for activity
        $config = array(
            'action_id' => (int)$request->getParam('action_id'),
            'max_id' => (int)$request->getParam('maxid'),
            'min_id' => (int)$request->getParam('minid'),
            'limit' => (int)$length,
            //'showTypes' => $actionTypeFilters,
        );

        if (isset($feed['dates'])) {
            $config = array(
                'action_id' => (int)$request->getParam('action_id'),
                'max_id' => (int)$request->getParam('maxid'),
                'min_id' => (int)$request->getParam('minid'),
                'max_date' => $request->getParam('maxdate'),
                'min_date' => $request->getParam('mindate'),
                'limit' => (int)$length,
                //'showTypes' => $actionTypeFilters,
            );
            $birthdate = $subject->getBirthdate();
            if (!isset($config['min_date']) || (strtotime($config['min_date']) < strtotime($birthdate))) {
                $time = strtotime($birthdate) - 1;
                $config['min_date'] = date('Y-m-d H:i:s', $time);
            }
        }

        /**
         * Modifications of the wall {
         */

        $feed['list'] = '';

        if ($isWall) {

            $userSetting = null;
            if ($viewer->getIdentity()) {
                $userSetting = Engine_Api::_()->getDbTable('userSettings', 'wall')->getUserSetting($viewer);
            }


            // get mute actions
            if ($viewer->getIdentity()) {
                $config['hideIds'] = Engine_Api::_()->getDbTable('mute', 'wall')->getActionIds($viewer);
            }

            $list_params = array(
                'mode' => 'recent',
                'list_id' => 0,
                'type' => ''
            );

            // Lists
            if (empty($subject) && $viewer->getIdentity()) {

                $default_type = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.list.default');
                if ($default_type != '') {
                    $list_params['mode'] = 'type';
                    $list_params['type'] = $default_type;

                    if (Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.list.user_save', false)) {
                        $list_params['mode'] = $userSetting->mode;
                        $list_params['type'] = $userSetting->type;
                        $list_params['list_id'] = $userSetting->list_id;
                    }

                }
                if ($request->getParam('mode')) {

                    if (Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.list.user_save', false)) {
                        $userSetting->setParams($request);
                    }
                    $list_params['mode'] = $request->getParam('mode', 'recent');
                    $list_params['list_id'] = $request->getParam('list_id');
                    $list_params['type'] = $request->getParam('type');
                }


                if ($list_params['mode'] == 'type') {

                    try {

                        $types = Engine_Api::_()->wall()->getManifestType('wall_type');

                        if (in_array($list_params['type'], array_keys($types))) {
                            $typeClass = Engine_Api::_()->loadClass(@$types[$list_params['type']]['plugin']);
                            if ($typeClass instanceof Wall_Plugin_Type_Abstract) {
                                $config['items'] = $typeClass->getItems($viewer);
                                $config['showTypes'] = $typeClass->getTypes($viewer);
                                if ($typeClass->customStream) {
                                    $customStreamClass = $typeClass;
                                }
                            }
                        }

                    } catch (Exception $e) {
                    }

                } else if ($list_params['mode'] == 'list' && Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.content.listenable', true)) {

                    $list = Engine_Api::_()->getDbTable('lists', 'wall')->getList($list_params['list_id']);
                    if ($list) {
                        $config['items'] = $list->getItems();
                    }

                } else if ($list_params['mode'] == 'friendlist' && Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.content.frendlistenable', true)) {

                    $list = Engine_Api::_()->getDbTable('lists', 'user')->fetchRow(array('list_id = ?' => $list_params['list_id']));

                    if ($list) {

                        $table = Engine_Api::_()->getDbTable('users', 'user');
                        $select = $table->select()
                            ->from(array('li' => Engine_Api::_()->getDbTable('listItems', 'user')->info('name')), array())
                            ->join(array('u' => $table->info('name')), 'li.child_id = u.user_id', new Zend_Db_Expr('u.*'))
                            ->where('li.list_id = ?', $list->getIdentity());

                        $data = array();
                        foreach ($table->fetchAll($select) as $item) {
                            $data[] = array('type' => $item->getType(), 'id' => $item->getIdentity());
                        }

                        $config['items'] = $data;
                    }

                }

                $list_disabled = explode(',', Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.list.disabled', ''));

                $types = array_keys(Engine_Api::_()->wall()->getManifestType('wall_type'));
                $lists = Engine_Api::_()->getDbTable('lists', 'wall')->getPaginator($viewer);
                $friendlists = Engine_Api::_()->getDbTable('lists', 'user')->fetchAll(array('owner_id = ?' => $viewer->getIdentity()));


                $wall_list = array();
                if (count($types)) {
                    foreach ($types as $type) {
                        if (in_array($type, $list_disabled)) {
                            continue;
                        }
                        $wall_list[] = array(
                            'title' => $this->view->translate('WALL_TYPE_' . strtoupper($type)),
                            'active' => ($list_params['mode'] == 'type' && $type == $list_params['type']),
                            'mode' => 'type',
                            'type' => $type,
                            'list_id' => 0
                        );
                    }
                }
//                if (Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.content.frendlistenable', true)) {
//                    if (count($friendlists)) {
//                        foreach ($friendlists as $list) {
//                            $wall_list[] = array(
//                                'title' => $list->title,
//                                'active' => ($list_params['mode'] == 'friendlist' && $list->list_id == $list_params['list_id']),
//                                'mode' => 'friendlist',
//                                'type' => '',
//                                'list_id' => $list->list_id
//                            );
//                        }
//                    }
//                }
                if (Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.content.listenable', true)) {
                    if (count($lists)) {
                        foreach ($lists as $list) {
                            $wall_list[] = array(
                                'title' => $list->label,
                                'active' => ($list_params['mode'] == 'list' && $list->list_id == $list_params['list_id']),
                                'mode' => 'list',
                                'type' => '',
                                'list_id' => $list->list_id
                            );
                        }
                    }
                }

                $feed['list'] = $wall_list;


            }

        }

        /**
         * } Modifications of the wall
         */

        // Pre-process feed items
        $selectCount = 0;
        $nextid = null;
        $firstid = null;

        $lastdate = null;
        $firstdate = null;
// HT {

        if ($name = $this->_getParam('ht_name')) {
            $type_name = $this->_getParam('ht_type');
            $name = $this->_getParam('ht_name');
            $vowels = array(".", ",", ";", "!", "?", ":", "*", "#", "'", " ");
            $name = str_replace($vowels, "", $name);
            $update = $this->_getParam('update');
            $this->view->update = $update;


            $id = $this->_getParam('id');
            if ($type_name == 'page') {
                $pTable = Engine_Api::_()->getDbTable('pages', 'page');
                $page_id = $pTable->fetchRow($pTable->select()->where('url = ?', $id));
            } else {
                $page_id = array('page_id' => -1);
            }

            $config['hashtag'] = 1;
            $config['hashtag_name'] = $name;
            $config['hashtag_type'] = $type_name;
            $config['id'] = $page_id['page_id'];
            $config['update'] = $update;
        }
// } HT

        $tmpConfig = $config;
        $activity = array();
        $endOfFeed = false;

        $friendRequests = array();
        $itemActionCounts = array();
        $enabledModules = Engine_Api::_()->apptouch()->getEnabledModuleNames();


        do {
            // Get current batch
            $actions = null;
            if (!empty($subject) && !$action_id) { // special for view page
                $actions = $this->_bridge->getHelper('activity')->direct()->getActivityAbout($actionTable, $subject, $viewer, $tmpConfig);
            } else {
                $actions = $this->_bridge->getHelper('activity')->direct()->getActivity($actionTable, $viewer, $tmpConfig);
            }


            $selectCount++;

            // Are we at the end?
            if (count($actions) < $length || count($actions) <= 0) {
                $endOfFeed = true;
            }

            // Pre-process
            if (count($actions) > 0) {
                foreach ($actions as $action) {
                    // get next id
                    if (null === $nextid || $action->action_id <= $nextid) {
                        $nextid = $action->action_id - 1;
                        $lastdate = $action->date;
                    }
                    // get first id
                    if (null === $firstid || $action->action_id > $firstid) {
                        $firstid = $action->action_id;
                        $firstdate = $action->date;
                    }
                    // skip disabled actions
                    if (!$action->getTypeInfo() || !$action->getTypeInfo()->enabled) continue;
                    // skip items with missing items
                    if (!$action->getSubject() || !$action->getSubject()->getIdentity()) continue;
                    if (!$action->getObject() || !$action->getObject()->getIdentity()) continue;

                    /**
                     * Except likes actions
                     */
                    /*          if ($action->type == 'like_item' || $action->type == 'like_item_private'){
                      continue ;
                    }*/

                    // track/remove users who do too much (but only in the main feed)
                    if (empty($subject)) {
                        $actionSubject = $action->getSubject();
                        $actionObject = $action->getObject();
                        if (!isset($itemActionCounts[$actionSubject->getGuid()])) {
                            $itemActionCounts[$actionSubject->getGuid()] = 1;
                        } else if ($itemActionCounts[$actionSubject->getGuid()] >= $itemActionLimit) {
                            continue;
                        } else {
                            $itemActionCounts[$actionSubject->getGuid()]++;
                        }
                    }
                    // remove duplicate friend requests
                    if ($action->type == 'friends') {
                        $id = $action->subject_id . '_' . $action->object_id;
                        $rev_id = $action->object_id . '_' . $action->subject_id;
                        if (in_array($id, $friendRequests) || in_array($rev_id, $friendRequests)) {
                            continue;
                        } else {
                            $friendRequests[] = $id;
                            $friendRequests[] = $rev_id;
                        }
                    }

                    // remove items with disabled module attachments
                    try {
                        $attachments = $action->getAttachments();
                    } catch (Exception $e) {
                        // if a module is disabled, getAttachments() will throw an Engine_Api_Exception; catch and continue
                        continue;
                    }

                    // add to list
                    if (count($activity) < $length) {
                        $activity[] = $action;
                        if (count($activity) == $length) {
                            $actions = array();
                        }
                    }
                }
            }

            // Set next tmp max_id
            if ($nextid) {
                $tmpConfig['max_id'] = $nextid;
                $tmpConfig['min_id'] = $firstid;
                $tmpConfig['min_date'] = $firstdate;
                $tmpConfig['max_date'] = $lastdate;
            }
            if (!empty($tmpConfig['action_id'])) {
                $actions = array();
            }
        } while (count($activity) > $length && $selectCount <= 3 && !$endOfFeed);

        $activityCount = count($activity);

        // Get some other info
        if (!empty($subject)) {
            $subjectGuid = $subject->getGuid(false);
        }

        $enableComposer = false;
        if (!$action_id) {
            if ($viewer->getIdentity()) {
                if (!$subject || ($subject instanceof Core_Model_Item_Abstract && $subject->isSelf($viewer))) {
                    if (Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'user', 'status')) {
                        $enableComposer = true;
                    }
                } else if ($subject) {
                    if (Engine_Api::_()->authorization()->isAllowed($subject, $viewer, 'comment')) {
                        $enableComposer = true;
                    }
                }
            }
        }

        // Assign the composing values
        $composes = array();
        $composer_privacy = '';
        $composer_share = '';

        if ($enableComposer) {

            /**
             * Modifications of the wall
             */

            if ($isWall) {

                $videoPlugin = null;

                $composers_disabled = explode(',', Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.composers.disabled', 'smile'));

                foreach (Engine_Api::_()->wall()->getManifestType('wall_composer') as $type => $config) {
                    if (!empty($config['auth']) && !Engine_Api::_()->authorization()->isAllowed($config['auth'][0], null, $config['auth'][1])) {
                        continue;
                    }
                    if (in_array($type, $composers_disabled)) {
                        continue;
                    }
                    if ($type == 'music') {
                        continue; // At the moment it is not integrated
                    }
                    if ($type == 'avp') {
                        continue; // At the moment it is not integrated
                    }

                    if ($type == 'video') {
                        $videoPlugin = $config;
                    }

                    $composes[] = $type;

                }

                $privacy_type = ($subject) ? $subject->getType() : 'user';

                $privacy = array();
                $privacy_disabled = explode(',', Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.privacy.disabled', ''));
                foreach (Engine_Api::_()->wall()->getPrivacy($privacy_type) as $item) {
                    if (in_array($privacy_type . '_' . $item, $privacy_disabled)) {
                        continue;
                    }
                    $privacy[] = $item;
                }

                if ($viewer->getIdentity() && $privacy) {

                    $composer_privacy = array();
                    $privacy_active = (empty($privacy[0])) ? null : $privacy[0];

                    $last_privacy = Engine_Api::_()->getDbTable('userSettings', 'wall')->getLastPrivacy($subject, $viewer);
                    if ($last_privacy && in_array($last_privacy, $privacy)) {
                        $privacy_active = $last_privacy;
                    }

                    if (count($privacy) > 1) {
                        foreach ($privacy as $item) {
                            $composer_privacy[] = array(
                                'type' => $item,
                                'title' => $this->view->translate('WALL_PRIVACY_' . strtoupper($privacy_type) . '_' . strtoupper($item)),
                                'active' => ($item == $privacy_active)
                            );
                        }
                    }

                }


                if ($this->view->viewer()->getIdentity()) {

                    foreach (Engine_Api::_()->wall()->getManifestType('wall_service', true) as $service) {
                        $class = Engine_Api::_()->wall()->getServiceClass($service);
                        if (!$class || !$class->isActiveShare()) {
                            continue;
                        }
                        $composer_share[] = array(
                            'title' => $this->view->translate('WALL_SHARE_' . strtoupper($service) . ''),
                            'type' => $service,
                            'active' => 0
                        );

                    }
                }


            } else {

                /**
                 * } Modifications of the wall
                 */


                foreach (Zend_Registry::get('Engine_Manifest') as $data) {
                    if (empty($data['composer'])) {
                        continue;
                    }
                    foreach ($data['composer'] as $type => $config) {
                        if (!empty($config['auth']) && !Engine_Api::_()->authorization()->isAllowed($config['auth'][0], null, $config['auth'][1])) {
                            continue;
                        }
                        if ($type == 'music') {
                            continue; // While is not integrated
                        }

                        $composes[] = $type;
                    }
                }
            }

        }

        // Form token
        $session = new Zend_Session_Namespace('ActivityFormToken');
        //$session->setExpirationHops(10);
        if (empty($session->token)) {
            $formToken = $session->token = md5(time() . $viewer->getIdentity() . get_class($this));
        } else {
            $formToken = $session->token;
        }

        ///========================================== Feed Format =================================== {


        $router = Zend_Controller_Front::getInstance()->getRouter();

        if ($enableComposer) {


            // Data of the compose form
            $videoFullUploaderUrl = $router->assemble(array('module' => 'video', 'controller' => 'index', 'action' => 'create', 'type' => 3), 'default', true);
            $videoComposeUrl = $router->assemble(array('module' => 'video', 'controller' => 'index', 'action' => 'compose-upload'), 'default', true);

            if (!empty($videoPlugin) && $videoPlugin['module'] == 'ynvideo') {
                $videoFullUploaderUrl = $router->assemble(array('module' => 'ynvideo', 'controller' => 'index', 'action' => 'create', 'type' => 3), 'default', true);
                $videoComposeUrl = $router->assemble(array('module' => 'ynvideo', 'controller' => 'index', 'action' => 'compose-upload'), 'default', true);


                $videoTypes = Ynvideo_Plugin_Factory::getAllSupportTypes();
                unset($videoTypes[Ynvideo_Plugin_Factory::getUploadedType()]);

                $labels = array();
                foreach ($videoTypes as $key => $type) {
                    $labels[$key] = $this->view->translate($type);
                }
                $feed['video_types'] = $labels;

            }


            $feed['composer'] = array(
                'composes' => $composes,
                'privacy' => $composer_privacy,
                'share' => $composer_share,
                'postUrl' => $router->assemble(array('module' => 'activity', 'controller' => 'index', 'action' => 'post'), 'default', true),
                'postServiceUrl' => $router->assemble(array('module' => 'activity', 'controller' => 'index', 'action' => 'post-service', 'format' => 'json'), 'default', true),
                'postError' => $this->view->translate('APPTOUCH_INVALID_POST'),
                'returnUrl' => $this->view->url(),
                'linkPreview' => $router->assemble(array('module' => 'core', 'controller' => 'link', 'action' => 'preview'), 'default', true),
                'linkError' => $this->view->translate('APPTOUCH_INVALID_LINK'),
                'videoFullUploaderUrl' => $videoFullUploaderUrl,
                'videoComposeUrl' => $videoComposeUrl
            );
        }

        $feed['settings'] = array(
            'nextid' => $nextid,
            'max_date' => $lastdate,
            'firstid' => $firstid,
            'min_date' => $firstdate,
            'endOfFeed' => $endOfFeed,
            'updateSettings' => $updateSettings
        );


        if ($checkUpdate) {
            if ($activityCount) {
                $feed['activityCount'] = $activityCount;
                $feed['updateTitle'] = $this->view->translate(array(
                        '%d new update is available - click this to show it.',
                        '%d new updates are available - click this to show them.',
                        $activityCount),
                    $activityCount);
            }
            return $this->formatComponent('feed', $feed);
        }


        $feed['isViewPage'] = 0;
        if ($action_id && $this->_bridge->getRequest()->getControllerName() == 'activity') {
            $feed['isViewPage'] = 1;
        }
        $feed['memberHomeUrl'] = $router->assemble(array('module' => 'user', 'controller' => 'index', 'action' => 'home'), 'default', true);
        $feed['feedUrl'] = $router->assemble(array('module' => 'apptouch', 'controller' => 'component', 'action' => 'index', 'component' => 'feed'), 'default', true);

        $feed['isWall'] = $isWall;


        // sharing

        $feed['serviceRequestUrl'] = '';
        $feed['services'] = '';

        if ($isWall) {


            $feed['serviceRequestUrl'] = $router->assemble(array('module' => 'wall', 'controller' => 'index', 'action' => 'services-request', 'format' => 'json'), 'default', true);
            $feed['services'] = array();

            $services = Engine_Api::_()->wall()->getManifestType('wall_service', true);
            foreach ($services as $service) {
                $class = Engine_Api::_()->wall()->getServiceClass($service);
                if (!$class || !$class->isActiveStream()) {
                    continue;
                }
                $feed['services'][$service] = array(
                    'url' => $this->view->url(array('module' => 'wall', 'controller' => $service, 'action' => 'index'), 'default', true),
                    'serviceShareUrl' => $this->view->url(array('module' => 'wall', 'controller' => 'index', 'action' => 'service-share', 'format' => 'json'), 'default', true),
                    'enabled' => false,
                );

                $setting_key = 'share_' . $service . '_enabled';
                $setting = Engine_Api::_()->wall()->getUserSetting($this->view->viewer());
                $feed['debug_' . $service] = $setting->{$setting_key};
                if (isset($setting->{$setting_key}) && $setting->{$setting_key}) {
                    $feed['services'][$service]['enabled'] = true;
                }
            }

        }


        /**
         * People
         */

        $feed['suggestPeopleUrl'] = '';

        if ($isWall) {
            $feed['suggestPeopleUrl'] = $router->assemble(array('module' => 'wall', 'controller' => 'index', 'action' => 'suggest-people'), 'default', true);
        }


        /**
         * Question Form
         */


        $feed['questionPrivacy'] = '';
        $feed['questionUrl'] = '';
        $feed['questionMaxOption'] = '';

        if ($isWall && Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('hequestion')) {

            $feed['questionUrl'] = $this->view->url(array('module' => 'hequestion', 'controller' => 'index', 'action' => 'create', 'format' => 'json'), 'default', true);
            $feed['questionMaxOption'] = (int)Engine_Api::_()->getDbTable('settings', 'core')->getSetting('hequestion.maxoptions', 15);


            /**
             * Get Privacy of Questions
             */

            $availableLabels = array(
                'everyone' => 'HEQUESTION_Everyone',
                'owner_network' => 'HEQUESTION_Friends and Networks',
                'owner_member' => 'HEQUESTION_Friends Only',
                'owner' => 'HEQUESTION_Just Me'
            );

            $viewOptions = (array)Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('hequestion', $this->view->viewer(), 'auth_view');
            $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));

            $privacy_active = '';
            $privacy_active_key = '';


            if (!empty($viewOptions)) {
                $keys = array_keys($viewOptions);
                $privacy_active_key = $keys[0];
                $privacy_active = $viewOptions[$privacy_active_key];
            }

            foreach ($viewOptions as $key => $item) {
                $feed['questionPrivacy'][] = array(
                    'title' => $this->view->translate($item),
                    'active' => ($key == $privacy_active_key),
                    'type' => $key
                );
            }


        }


        /**
         * Checkin
         */


        $feed['checkinUrl'] = '';
        $feed['checkinDefaultIcon'] = '';
        $feed['checkinDefaultLocation'] = '';
        $feed['checkinError'] = '';

        if ($isWall && Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('checkin')) {


            $feed['checkinUrl'] = $this->view->url(array('module' => 'checkin', 'controller' => 'index', 'action' => 'suggest', 'format' => 'json'), 'default', true);
            $feed['checkinDefaultIcon'] = $this->view->layout()->staticBaseUrl . 'application/modules/Checkin/externals/images/map_icon.png';
            $feed['checkinError'] = $this->view->translate('APPTOUCH_CHECKIN_ERROR');

            $location = $this->view->subject()
                ? $this->view->checkinDefaultLocation($this->view->subject())
                : $this->view->checkinDefaultLocation($this->view->viewer());

            if ($location) {
                $location = Zend_Json::decode($location); // :D
            }

            $feed['checkinDefaultLocation'] = $location;

            $this->_bridge->lang()->add('CHECKIN_Where are you?');

        }


        $feed['noItemsMessage'] = $this->view->translate('Nothing has been posted here yet - be the first!');


        if ($isWall) {

            $tabs = Engine_Api::_()->wall()->getManifestType('wall_tabs');


            // show only feed

            if ($subject || !$viewer->getIdentity()) {

                $tab_disabled = array_diff(array_keys($tabs), array('social'));
                $tab_default = 'social';

                // show tabs

            } else {

                $tab_disabled = explode(',', Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.tab.disabled'));
                $tab_default = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.tab.default', 'social');

            }

            $tab_labels = array();

            foreach ($tabs as $tab => $value) {
                if (in_array($tab, $tab_disabled)) {
                    continue;
                }
                if (in_array($tab, Engine_Api::_()->wall()->getManifestType('wall_service', true))) {
                    $class = Engine_Api::_()->wall()->getServiceClass($tab);
                    if (!$class || !$class->isActiveStream()) {
                        continue;
                    }
                }

                if ($tab == 'welcome') {
                    continue;
                }

                $tab_labels[] = array(
                    'title' => $this->view->translate('WALL_STREAM_' . strtoupper($tab)),
                    'active' => ($tab == $tab_default),
                    'type' => $tab
                );
            }


            $feed['tabLabels'] = $tab_labels;

            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('wall.content.rolldownload', true)) {
                //$feed['rolldownload'] = 1;
            }

        }


        $feed['scrollajax'] = 0;
        if (Engine_Api::_()->getDbTable('settings', 'core')->getSetting('apptouch.activity.scrollajax', true)) {
            $feed['scrollajax'] = 1;
        }

        $feed['actions'] = $this->_bridge->getHelper('activity')->direct()->loop($activity, array(
            'action_id' => $action_id,
            'viewAllComments' => $viewAllComments,
            'viewAllLikes' => $viewAllLikes,
            'getUpdate' => $getUpdate,
        ));
        $feed['canViewTags'] = Engine_Api::_()->hasModuleBootstrap('photoviewer') && Engine_Api::_()->user()->getViewer()->getIdentity();
        return $this->formatComponent('feed', $feed);

    }

    // Checkin
    public function checkinMap(array $params = array())
    {
        $place_id = 0;
        if (!empty($params['place_id'])) {
            $place_id = $params['place_id'];
        }

        $noPhoto = 'application/modules/Checkin/externals/images/nophoto.png';
        if (!$place_id) {
            return;
        }

        $format = array();

        $placesTbl = Engine_Api::_()->getDbTable('places', 'checkin');
        $checksTbl = Engine_Api::_()->getDbTable('checks', 'checkin');

        $format['checkin'] = $checkin = $placesTbl->findRow($place_id);

        if (!$checkin) {
            return;
        }

        if ($checkin->object_type == 'page' || $checkin->object_type == 'event') {
            $viewer = Engine_Api::_()->user()->getViewer();
            $subject = Engine_Api::_()->getItem($checkin->object_type, $checkin->object_id);

            if (!$subject || !$viewer || !$this->_helper->requireAuth()->setAuthParams($subject, $viewer, 'view')->isValid()) {
                return;
            }
        }

        $markers = array();
        $markers[0] = array(
            'lat' => $checkin->latitude,
            'lng' => $checkin->longitude,
            'checkin_icon' => ($checkin->icon) ? $checkin->icon : $noPhoto,
            'title' => $checkin->name,
        );

        //$format['users'] = $checksTbl->getPlaceVisitors($place_id, 9);
        $format['markers'] = $markers;
        $format['bounds'] = Engine_Api::_()->checkin()->getMapBounds($markers);

        return $this->formatComponent('checkinMap', $format);

    }

    // Comments
    public function comments(array $params = array())
    {
        // Get subject
        $subject = null;

        if (isset($params['subject'])) {
            $subject = $params['subject'];
        }

        if (!($subject instanceof Core_Model_Item_Abstract))
            if (Engine_Api::_()->core()->hasSubject()) {
                $subject = Engine_Api::_()->core()->getSubject();
            } else if (($subject = $this->_getParam('subject'))) {
                list($type, $id) = explode('_', $subject);
                $subject = Engine_Api::_()->getItem($type, $id);
            } else if (($type = $this->_getParam('type')) &&
                ($id = $this->_getParam('id'))
            ) {
                $subject = Engine_Api::_()->getItem($type, $id);
            }

        if (!($subject instanceof Core_Model_Item_Abstract) ||
            !$subject->getIdentity() ||
            (!method_exists($subject, 'comments') && !method_exists($subject, 'likes'))
        ) {
            return null;
        }

        // Perms
        $viewer = Engine_Api::_()->user()->getViewer();
        $canComment = $subject->authorization()->isAllowed($viewer, 'comment');
        $canDelete = $subject->authorization()->isAllowed($viewer, 'edit');

        // Likes
        $viewAllLikes = $this->_getParam('viewAllLikes', false);
        $likes = $subject->likes()->getLikePaginator();
        $likes->setItemCountPerPage($likes->getTotalItemCount());
        // Comments

        // If has a page, display oldest to newest
        if (null !== ($page = $this->_getParam('commentPage'))) {
            $commentSelect = $subject->comments()->getCommentSelect();
            $commentSelect->order('comment_id ASC');
            $comments = Zend_Paginator::factory($commentSelect);
            $comments->setCurrentPageNumber($page);
            $comments->setItemCountPerPage(10);
        } // If not has a page, show the
        else {
            $commentSelect = $subject->comments()->getCommentSelect();
            $commentSelect->order('comment_id DESC');
            $comments = Zend_Paginator::factory($commentSelect);
            $dbt = new Zend_Db_Table();
            $comments->setCurrentPageNumber(1);
        }
        //$comments->setItemCountPerPage($comments->getTotalItemCount());


        //    Preparing data
        $widget_content = array();
        $widget_content['liked'] = $subject->likes()->isLike($this->view->viewer());
        $widget_content['comment_count'] = $subject->comments()->getCommentCount();
        $widget_content['like_count'] = $this->view->locale()->toNumber($likes->getTotalItemCount());
        $widget_content['liker_text'] = $this->view->translate(array('%s person likes this', '%s people like this', $likes->getTotalItemCount()), $this->view->locale()->toNumber($likes->getTotalItemCount()));
        $widget_content['can_comment'] = $canComment;
        $widget_content['action'] = $this->_getParam('comments');

        if ($viewer->getIdentity() && $canComment) {
            $form = new Core_Form_Comment_Create();
            $form->setAction($this->view->url(array('module' => 'core', 'controller' => 'comment', 'action' => 'create'), 'default', true));
            $widget_content['main_form'] = $form->render($this->view);
        }

        $comments_format = array();
        foreach ($comments as $comment) {
            $comment_format = array();
            $comment_format['id'] = $comment->getIdentity();
            $comment_format['poster'] = $this->_bridge->subject($comment->getPoster());
            $comment_format['likes'] = $comment->likes();


            /**
             * Wall Modification {
             */
            $isWall = Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('wall');
            if ($isWall) {
                $page = null;
                $poster = Engine_Api::_()->getItem($comment->poster_type, $comment->poster_id);

                if (Engine_Api::_()->wall()->isOwnerTeamMember($subject, $poster)) {
                    $page = Engine_Api::_()->wall()->getSubjectPage($subject);
                }

                if ($page && $page->getType() == 'page' && Engine_Api::_()->wall()->isOwnerTeamMember($page, $comment->getPoster())) {
                    $comment_format['poster'] = $this->_bridge->subject($page);
                }
            }

            /**
             * } Wall Modification
             */

            $comment_format['body'] = $comment->body;
            $comment_format['creation_date'] = $this->view->timestamp($comment->creation_date);
            $comment_format['options'] = array();
            if ($canComment)
                $comment_format['options']['like'] = $comment->likes()->isLike($this->view->viewer());

            $comment_format['options']['delete'] = $canDelete;
            $comments_format[] = $comment_format;
        }

        $widget_content['comments'] = $comments_format;
        $widget_content['subject'] = $this->_bridge->subject($subject);
        foreach ($likes as $like) {
            $widget_content['likes'][] = $this->_bridge->subject($like->getPoster());
        }

        return $this->formatComponent('comments', $widget_content);
    }

    // Rate
    public function rate(array $params = array())
    {
        $subject = null;

        if (!empty($params['subject'])) {
            $subject = $params['subject'];
        }

        if (!$subject || !$subject->getIdentity()) {
            return null;
        }

        $item_type = strtolower($subject->getType());
        $item_id = $subject->getIdentity();

        if (!Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('rate')
            || !Engine_Api::_()->rate()->isSupportedPlugin($item_type)
        ) {
            return null;
        }


        $table = Engine_Api::_()->getDbtable('rates', 'rate');
        $rate_info = $table->fetchRateInfo($item_type, $item_id);
        $item_score = ($rate_info && $rate_info['rate_count']) ? $rate_info['total_score'] / $rate_info['rate_count'] : 0;

        $front_router = Zend_Controller_Front::getInstance()->getRouter();


        $href = $front_router->assemble(array(
            'module' => 'hecore',
            'controller' => 'index',
            'action' => 'list',
        ), 'default', true);

        $query = http_build_query(array(
            'm' => 'rate',
            'l' => 'getItemVoters',
            'params' => array(
                'item_type' => $item_type,
                'item_id' => $item_id,
            )
        ));

        $href .= '?' . $query;


        $format = array(
            'item_type' => $item_type,
            'item_id' => $item_id,
            'rate_info' => $rate_info,
            'item_score' => round($item_score, 2),
            'rate_url' => $front_router->assemble(array('module' => 'apptouch', 'controller' => 'rate', 'action' => 'index-rate'), 'default', true),
            'rate_uid' => uniqid('rate_'),
            'can_rate' => Zend_Json::encode(array('can_rate' => true, 'error_msg' => '')),
            'label' => $this->view->translate(array('vote', 'votes', $rate_info['rate_count'])),
            'href' => $href
        );


        return $this->formatComponent('rate', $format);

    }

    // Like
    public function like(array $params = array())
    {
        if (empty($params['subject']) || !$params['subject']) {
            return null;
        }

        if (!Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('like')) {
            return null;
        }

        $viewer = Engine_Api::_()->user()->getViewer();

        $subject = $params['subject'];

        $info = array();
        if (!Engine_Api::_()->like()->isLike($subject)) {
            $info['label'] = $this->view->translate('like_Like');
            $info['action'] = 'like';
        } else {
            $info['label'] = $this->view->translate('like_Unlike');
            $info['action'] = 'unlike';
        }
        /**
         * @var $subject User_Model_User
         */
        if ($subject instanceof User_Model_User && !$subject->isSelf(Engine_Api::_()->user()->getViewer())) {
            $info['membership'] = $this->view->userFriendship($subject);
        }
        $info['like_url'] = $this->view->url(array('action' => 'like'), 'like_default');
        $info['unlike_url'] = $this->view->url(array('action' => 'unlike'), 'like_default');
        $info['id'] = $subject->getIdentity();
        $info['type'] = $subject->getType();

        $info['auth'] = ($subject->authorization()->isAllowed(null, 'view'));
        $info['is_enabled'] = (bool)($viewer->getIdentity());
        $info['is_allowed'] = (bool)(Engine_Api::_()->like()->isAllowed($subject));
        $info['warn_text'] = $this->view->translate('This profile is private - only friends of this member may view it.');

        return $this->formatComponent('like', $info);
    }

    // Tab Container
    public function tabs(array $params = array(), $tabletContent = null)
    {
//    $isTablet = false; //Engine_Api::_()->getApi('core', 'apptouch')->isTabletMode();
        $tabs = $this->getNavigation('tab');
        if (!$this->_hasParam('tab')) {
            $tabs[0]['active'] = true;
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        foreach (@$tabs as $order => $tab) {
            $method = 'tab';
            $class = explode(' ', $tab['attrs']['class']);
            $tabName = explode('_', $class[1]);
            foreach ($tabName as $key => $part) {
                if ($key < 2) continue;
                $method .= ucfirst($part);
            }

            //    Init Tab Content
            if (method_exists($this->_bridge, $method) && Engine_Api::_()->core()->hasSubject() && ($subject->authorization()->isAllowed($viewer, 'view') || @$params['ignorePermissions'])) {
                $resp = $this->_bridge->$method($tab['active']); // todo may cause exception

                $showContent = true;
                $icpp = 5;
                if (is_array($resp) && isset($resp['response']) && isset($resp['showContent'])) {
                    $showContent = $resp['showContent'];
                    $icpp = isset($resp['icpp']) && is_numeric($resp['icpp']) ? $resp['icpp'] : $icpp;
                    $resp = $resp['response'];
                }

                if ($resp === null || $resp === false || $resp === 0)
                    unset($tabs[$order]);
                else if ($resp instanceof Zend_Paginator) {
                    /**
                     * @var $resp Zend_Paginator
                     * */
                    $totalCount = $resp->getTotalItemCount();
//          if ($isTablet) {
//            $tabs[$order]['count'] = $totalCount;
//            $tabs[$order]['label'] = $this->view->translate($tab['label']);
//          } else {
                    $tabs[$order]['label'] = $this->view->translate($tab['label']) . '<span> ' . $totalCount . '</span>';
//          }
                    $tabs[$order]['icons'] = array();

                    $count = 0;
                    $resp->setItemCountPerPage($totalCount);
                    $rand = rand(1, 6); // todo random count

                    //                    if (Engine_Api::_()->core()->hasSubject()) {
                    //                        $subject = Engine_Api::_()->core()->getSubject();
                    //                    }
                    //
                    //                    if ($subject && get_class($subject) != 'Page_Model_Page') {
                    //                        foreach ($resp as $item) {
                    //                            if ($count == $rand)
                    //                                break;
                    //                            $icon = null;
                    //                            if (method_exists($item, 'getPhotoUrl'))
                    //                                $icon = $item->getPhotoUrl('thumb.icon');
                    //                            if ($icon) {
                    //                                $tabs[$order]['icons'][] = $icon;
                    //                                $count++;
                    //                            }
                    //                        }
                    //                    }

                    if ($showContent && $tab['active']) {
                        $resp->setItemCountPerPage($icpp); // todo tmp
                        if ($resp->getTotalItemCount() && strstr($resp->getItem(0, 1)->getType(), 'photo'))
                            $this->_bridge
                                ->add($this->gallery($resp), 7);
                        else
                            $this->_bridge
                                ->add($this->itemSearch($this->_bridge->getSearchForm()), 6)
                                ->add($this->itemList($resp), 7)
                                ->add($this->paginator($resp), 8);
                    }

                } elseif (is_numeric($resp)) {
                    $tabs[$order]['label'] = $this->view->translate($tab['label']) . '<span> ' . $resp . '</span>';
                }
            }
        }
        $tabs = array_values($tabs);
        if (!$this->_hasParam('tab')) {
            $tabs[0]['active'] = true;
        }
//    if ($isTablet) {
//      $tabs['info'] = $this->getFields($subject);
//    }
        return $this->formatComponent('tabs', $tabs);
    }

    private function getFields($subject = null)
    {
        if (!$subject) return array();
        if (get_class($subject) == 'Page_Model_Page') {
            return array(
                'fields' => array(),
                'flag' => false
            );
        } elseif (get_class($subject) == 'Group_Model_Group') {
            return array(
                'fields' => array(),
                'flag' => true
            );
        } elseif (get_class($subject) == 'Event_Model_Event') {
            return array(
                'content' => array(),
                'flag' => true
            );
        } elseif (get_class($subject) == 'Offers_Model_Offer') {
            return array(
                'content' => array(),
                'flag' => false
            );
        } else {
            return array(
                'fields' => array(), //$this->_bridge->getHelper('fields')->toArray($subject, null),
                'flag' => false
            );
        }
    }

    // Fields Values
    public function fieldsValues($subject = null, $structure = null)
    {
        if (!($subject instanceof Core_Model_Item_Abstract) && Engine_Api::_()->core()->hasSubject())
            $subject = Engine_Api::_()->core()->getSubject();
        $fieldsArr = $this->_bridge->getHelper('fields')->toArray($subject, $structure);
        if ($fieldsArr)
            return $this->formatComponent('fieldsValues', $this->_bridge->getHelper('fields')->toArray($subject, $structure));
    }

    // Message Tips
    public function tip($message, $title = null, $attrs = null)
    {

        if (!$attrs)
            $attrs = array(
                'data-theme' => 'e',
                'data-content-theme' => 'e'
            );

        if (!$title)
            $title = $this->view->translate('APPTOUCH_Notice');

        return $this->formatComponent('tip', array(
            'title' => $title,
            'message' => $message,
            'attrs' => $attrs
        ));
    }

    // Discussions
    public function discussion(
        Core_Model_Item_Abstract $topic,
        Zend_Paginator $posts, $options = null, $customizePostFunction = null)
    {
        $topicOptions = null;
        if ($options) {
            $topicOptions = @$options['options'];
        }


        $topicFormat = array();

        $topicFormat['options'] = $topicOptions;
        $topicFormat['title'] = $this->html('<h3>' . $topic->getTitle() . '</h3>');
        foreach ($posts as $post) {
            $postFormat = array();
            $postOwner = $post->getOwner();
            $user = $this->_bridge->subject($postOwner);

            $body = $this->view->BBCode($post->body);
            if (strip_tags($body) == $body) {
                $body = nl2br($body);
            }

            $postFormat['id'] = $post->post_id;
            $postFormat['body'] = $body;
            $postFormat['owner'] = $user;
            $postFormat['photo'] = $post->getPhotoUrl();
            $postFormat['creation_date'] = $this->view->locale()->toDateTime(strtotime($post->creation_date));

            if (isset($post->edit_id) && $post->edit_id && !empty($post->modified_date)) {
                $postFormat['edit'] = $this->view->translate('This post was edited by %1$s at %2$s', $this->view->user($post->edit_id)->__toString(), $this->view->locale()->toDateTime(strtotime($post->modified_date)));
            }

            if ($post->getPhotoUrl()) {
                $postFormat['photo'] = $this->gallery(Zend_Paginator::factory(array($post)));
            }

            if (method_exists($this->_bridge, $customizePostFunction)) {
                $topicFormat['posts'][] = array_merge_recursive($postFormat, $this->_bridge->$customizePostFunction($post));
            } else
                $topicFormat['posts'][] = $postFormat;

        }

        if (!empty($options['postForm']) && $options['postForm'])
            $topicFormat['postForm'] = $this->form($options['postForm']);
        return $this->formatComponent('discussion', $topicFormat);
    }

    public function crumb($links, $byname = false)
    {
        if ($links instanceof Zend_Navigation) {
            $links = $this->getNavigation($links);
        } else if (is_string($links)) {
            $links = $this->getNavigation($links, $byname);
        }
        return $this->formatComponent('crumb', $links);
    }

    public function map($markers)
    {
        return $this->formatComponent('map', $markers);
    }

    public function chatRoom($rooms = array())
    {
        $info = array();
        $info['rooms'] = $rooms;
        $info['join_url'] = $this->view->url(array('module' => 'chat', 'controller' => 'ajax', 'action' => 'join'));
        $info['leave_url'] = $this->view->url(array('module' => 'chat', 'controller' => 'ajax', 'action' => 'leave'));
        $info['list_url'] = $this->view->url(array('module' => 'chat', 'controller' => 'ajax', 'action' => 'list'));
        $info['ping_url'] = $this->view->url(array('module' => 'chat', 'controller' => 'ajax', 'action' => 'ping'));
        $info['status_url'] = $this->view->url(array('module' => 'chat', 'controller' => 'ajax', 'action' => 'status'));
        $info['send_url'] = $this->view->url(array('module' => 'chat', 'controller' => 'ajax', 'action' => 'send'));

        // Viewer info
        $info['viewer'] = array();
        $viewer = Engine_Api::_()->user()->getViewer();
        $info['viewer']['id'] = $viewer->getIdentity();
        $info['viewer']['photo'] = $viewer->getPhotoUrl('thumb.icon');
        $info['viewer']['name'] = $viewer->displayname;
        $info['viewer']['href'] = $viewer->getHref();

        return $this->formatComponent('chatRoom', $info);
    }

    public function notifications($popup = false)
    {
        if ($popup) {

        } else {
            $notificationsFormat = array();
            $updates = array();
            $requests = array();

        }
    }

    //-------------------------- Private Methods ----------------------- {

    private function formatComponent($name, $params = null)
    {
        if (!array_key_exists($name, $this->components)) {
            throw new Apptouch_Controller_Action_BridgeException('Unknown component: ' . $name);
        }

        if ($this->components[$name]['parent'] != 'content' || in_array($name, $this->availableComponents)) {
            return array(
                'name' => $name,
                'params' => $params
            );
        }
    }

    // } -------------------------- Private Methods -----------------------

    //---------------------------- Helper Methods ------------------------ {
    public function getNavigation($nav, $by_name = false, $name = null)
    {
        if (!$nav instanceof Zend_Navigation) {
            if ($by_name) {
                $name = $nav;
            } else {
                $name = $this->_bridge->getModuleName() . $this->nav_postfixes[$nav];
            }
        }
        $navigationMenus = array();
        if (!@$this->navigations[$name]) {
            if ($nav instanceof Zend_Navigation) {
                $navigation = $nav;
            } else {
                $navigation = Engine_Api::_()->getApi('menus', 'apptouch')->getNavigation($name);
            }

            if (strpos($name, '_quick', 0) === false && $this->_bridge->getModuleSetting('create_action')) {
                $quickItem = $navigation->findOneBy('action', $this->_bridge->getModuleSetting('create_action'));
                $navigation->removePage($quickItem);
            }

            foreach ($navigation->getPages() as $mainItem) {
                $params = $mainItem->toArray();
                $attrs = array_filter(array(
                    'href' => str_replace('/format/smoothbox', '', $mainItem->getHref()),
                    'class' => $params['class'],
                    //                    'target' => $params['active'],
                    'title' => $params['title'],
                    'id' => $params['id'],
                ));
                $navigationMenus[] = array(
                    'label' => $this->view->translate($params['label']),
                    'attrs' => $attrs,
                    'data_attrs' => @$params['data_attrs'] ? $params['data_attrs'] : null,
                    'active' => $params['active'],
                );
            }
            $this->navigations[$name] = $navigationMenus;
        }
        return $navigationMenus;
    }

    public function renderComponent($component, $params = array())
    {
        if (!method_exists($this, $component)) {
            throw new Apptouch_Controller_Action_BridgeException('Unknown component: ' . $component);
        }
        try {
            $comp = $this->$component($params);
            $this->view->status = true;
            return $comp;
        } catch (Exception $e) {
            $this->view->error = $this->view->translate('APPTOUCH_Component Is Not Dynamically Renderable');
            return;
        }
    }

    // } ---------------------------- Helper Methods ------------------------

    //-------------------------------- Getters And Setters ------- {

    // } -------------------------------- Getters And Setters -------

    public function direct()
    {
        parent::direct();

        // load available component list
        if (!$this->components)
            $this->components = Apptouch_Content::getManifestData('ui_components');
        if (!$this->availableComponents) {
            $pagekey = $this->getRequest()->getControllerName() . "_" . implode('_', explode('-', $this->getRequest()->getActionName()));
            $pageTable = Engine_Api::_()->getDbTable('pages', 'apptouch');
            $contentTable = Engine_Api::_()->getDbTable('content', 'apptouch');
            $page = $pageTable->fetchRow($pageTable->select()->where('name=?', $pagekey));
            if ($page) {
                $page_id = $page->page_id;
                $availableComponents = $contentTable->fetchAll($contentTable->select()->from($contentTable->info('name'), 'component_name')->where('page_id=?', $page_id)->where('enabled=?', 1)->group('component_name')->order('order'))->toArray();
                foreach ($availableComponents as $component) {
                    $this->availableComponents[] = $component['component_name'];
                    if ($component['component_name'] == 'tabs') {
                        $this->availableComponents = array_merge($this->availableComponents, array(
                            'html',
                            'form',
                            'itemList',
                            'paginator',
                            'navigation',
                            'gallery',
                            'checkinMap',
                            'fieldsValues',
                            'tip',
                            'feed',
                            'map',
                            'discussion',
                            'itemSearch'
                        ));
                    }
                }
            } else {
                $this->availableComponents = array_keys($this->components);
            }
        }
        return $this;
    }

}