<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 24.07.12
 * Time: 11:30
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_HebadgeController extends Apptouch_Controller_Action_Bridge
{
  public function indexInit()
  {
    $this
      ->addPageInfo('contentTheme', 'd');
  }

    public function indexIndexAction()
    {
        $table = Engine_Api::_()->getDbTable('badges', 'hebadge');
        $viewer = Engine_Api::_()->user()->getViewer();

        $this->setFormat('browse');

        if (!$viewer->getIdentity()) {
            return $this->view->url(array(), 'default');
        }

        $request = Zend_Controller_Front::getInstance()->getRequest();

        $params = array(
            'text' => $request->getParam('text')
        );

        $paginator = $table->getOwnerNextBadges($viewer);

        $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 12));
        $paginator->setCurrentPageNumber($request->getParam('page'));

        $ids = array();
        $item_ids = array();
        $complete = array();
        foreach ($paginator->getCurrentItems() as $item) {
            $ids[] = $item->badge_id;
            $item_ids[] = array(
                'type' => 'hebadge_badge',
                'id' => $item->badge_id
            );
            $complete[$item->badge_id] = $item->procent;
        }

        if ($viewer->getIdentity()) {
            $members = $table->getOwnerMembersByBadgeIds($ids, $viewer);
        }

        $items = Engine_Api::_()->hebadge()->getItems($item_ids);
        $pag = Zend_Paginator::factory($items);
        $pag->setItemCountPerPage($this->_getParam('itemCountPerPage', 5));
        $pag->setCurrentPageNumber($request->getParam('page'));

        $title = $this->view->translate('My Next Badges');
        $caption = "<h2><p align='center'>{$title}</p></h2>";

        $this->add($this->component()->html($caption));
        $this->add($this->component()->badgesList($pag, $complete));
        $this->add($this->component()->paginator($pag));

        $this->add($this->component()->html($this->_getBadgesMenu()));
        $popular = $table->getPaginator($params);
        $friends = $table->getFriendMemberPaginator($viewer, $params);
        $ids = array();
        $tmp = array();
        foreach ($friends->getCurrentItems() as $item) {
            $ids[] = $item->badge_id;
            $tmp[] = $item->toArray();
        }
        $items = Engine_Api::_()->hebadge()->getTableItems($table, $ids);

        $members = array();
        if ($viewer->getIdentity()) {
            $members = $table->getOwnerMembersByBadgeIds($ids, $viewer);
        }

        $friends = Zend_Paginator::factory($items);

        $recent = $table->getPaginator($params, 'recent');
        $this->add($this->component()->badgesList($popular, null, null,
            array('attrs' => array('id' => 'badges-popular', 'class' => 'badges-tab', 'style' => 'display:block'))
        ));
        $this->add($this->component()->badgesList($friends, null, null,
            array('attrs' => array('id' => 'badges-friends', 'class' => 'badges-tab', 'style' => 'display:none'),
                'members' => $members
            )
        ));
        $this->add($this->component()->badgesList($recent, null, null,
            array('attrs' => array('id' => 'badges-recent', 'class' => 'badges-tab', 'style' => 'display:none'))
        ));

        $this->renderContent();
    }

    public function indexViewAction()
    {
        $page = $this->_getParam('page', 0);
        $tab = $this->_getParam('tab', '');
        $show_members = ($page != 0 || $tab != '') ? true : false;
        $badge = Engine_Api::_()->getItem('hebadge_badge', $this->_getParam('id'));
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$badge) return $this->view->url(array(), 'hebadge_general', true);

        Engine_Api::_()->core()->setSubject($badge);

        $this->add($this->component()->badgeProfile($badge));

        $this->add($this->component()->html($this->_getBadgesProfileMenu(true, $show_members)));
        $requirements_div = $this->dom()->new_('div', array('id' => 'badge-requirements', 'class' => 'badge-tab', 'data-role' => 'controlgroup', 'data-mini' => 'true', 'data-type' => 'horizontal',
            'style' => ($show_members) ? 'display: none; text-align: left' : 'display: block; text-align: left'));

        $require = $badge->getRequire();
        $require_complete = array();
        if ($viewer->getIdentity()) {
            $require_complete = Engine_Api::_()->getDbTable('require', 'hebadge')->getCompleteRequireIds($viewer, $badge);
        }
        $member = $badge->getMember($viewer);
        $requirements = $this->dom()->new_('ul', array('data-role' => 'listview'));
        foreach ($require as $item) {
            $r = Engine_Api::_()->hebadge()->getRequire($item->type);
            if (empty($r)) continue;
            $link = 'javascript:void(0);';
            if (!empty($r['require_link'])) {
                $link = $r['require_link'];
            }
            $params = array();
            if (!$member && in_array($item->getIdentity(), $require_complete)) $params['class'] = 'complete';
            $requirements->append($this->dom()->new_('li',
                $params,
                $this->view->translate('HEBADGE_REQUIRE_' . strtoupper($item->type), $item->params)
            ));
        }
        $requirements_div->append($requirements);
        $this->add($this->component()->html($requirements_div));

        $members_pag = $badge->getMembersPaginator();
        $members_pag->setItemCountPerPage(20);
        $members_pag->setCurrentPageNumber($page);
        $items = array();
        foreach ($members_pag->getCurrentItems() as $item) {
            $items[] = array(
                'type' => $item->object_type,
                'id' => $item->object_id
            );
        }
        $members = Engine_Api::_()->hebadge()->getItems($items);

        $learn_div = $this->dom()->new_('div',
            array('id' => 'badge-learn', 'class' => 'badge-tab', 'data-role' =>
            'controlgroup', 'data-mini' => 'true', 'data-type' => 'horizontal',
                'style' => 'display:none; text-align: left'));
        $learn_div->text = $badge->body;
        $this->add($this->component()->html($learn_div));

        $paginator = Zend_Paginator::factory($members);
        $paginator->setItemCountPerPage(5);
        $paginator->setCurrentPageNumber($page);

        if ($paginator->getTotalItemCount() > 0) {
            $this->add($this->component()->itemList($paginator, null,
                array(
                  'listPaginator' => true,
                  'attrs' => array(
                    'class' => 'badge-tab',
                    'id' => 'badge-members',
                    'style' => (!$show_members) ? 'display: none' : 'display: block'
                )
                )
            ));
//            $this->add($this->component()->paginator($paginator))
            ;
        }
        $this->renderContent();
    }

    private function _getBadgesProfileMenu($members = false, $show_members = false)
    {
        $profileMenu = $this->dom()->new_('div', array('data-role' => 'controlgroup', 'data-mini' => 'true', 'data-type' => 'horizontal', 'style' => 'text-align: left'));
        $profileMenu->append($this->dom()->new_('a',
            array(
                'id' => 'badge-show-requirements',
                'class' => (!$show_members) ? 'badge-tab-button ui-btn-active' : 'badge-tab-button',
                'data-role' => 'button',
                'data-icon' => 'wrench',
                'data-shadow' => true), $this->view->translate('Requirements')))
            ->append($this->dom()->new_('a',
            array(
                'id' => 'badge-show-learn',
                'class' => 'badge-tab-button',
                'data-role' => 'button',
                'data-icon' => 'info',
                'data-shadow' => true), $this->view->translate('Learn')));
        if ($members)
            $profileMenu->append($this->dom()->new_('a',
                array(
                    'id' => 'badge-show-members',
                    'class' => ($show_members) ? 'badge-tab-button ui-btn-active' : 'badge-tab-button',
                    'data-role' => 'button',
                    'data-icon' => 'person',
                    'data-shadow' => true), $this->view->translate('Members')));
        return '<br />' . $profileMenu . '';
    }

    private function _getBadgesMenu()
    {
        $editMenu = $this->dom()->new_('div', array('data-role' => 'controlgroup', 'data-mini' => 'false', 'data-type' => 'horizontal', 'style' => 'text-align: center'));
        $editMenu->append($this->dom()->new_('a',
            array(
                'id' => 'badges-show-popular',
                'class' => 'badges-tab-button ui-btn-active',
                'data-role' => 'button',
                'data-icon' => 'fire',
                'data-shadow' => true), $this->view->translate('Popular')))
            ->append($this->dom()->new_('a',
            array(
                'id' => 'badges-show-friends',
                'class' => 'badges-tab-button',
                'data-role' => 'button',
                'data-icon' => 'person',
                'data-shadow' => true), $this->view->translate('Friends')))
            ->append($this->dom()->new_('a',
            array(
                'id' => 'badges-show-recent',
                'class' => 'badges-tab-button',
                'data-role' => 'button',
                'data-icon' => 'search',
                'data-shadow' => true), $this->view->translate('Recent')));
        return '<br /><br />' . $editMenu . '<br />';
    }

    public function indexManageAction()
    {
        $this->setFormat('browse');
        $table = Engine_Api::_()->getDbTable('badges', 'hebadge');
        $viewer = Engine_Api::_()->user()->getViewer();

        if (!$viewer->getIdentity()) {
            return $this->view->url(array(), 'hebadge_general');
        }

        $request = Zend_Controller_Front::getInstance()->getRequest();

        $params = array('text' => $request->getParam('text'));

        $paginator = $table->getMemberPaginator($viewer, $params, false);

        $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 12));
        $paginator->setCurrentPageNumber($request->getParam('page'));

        $ids = array();
        foreach ($paginator->getCurrentItems() as $item) {
            $ids[] = $item->badge_id;
        }

        $members = $table->getOwnerMembersByBadgeIds($ids, $viewer);

        $this->add($this->component()->manageBadgesList($paginator, null, array('members' => $members)));
        $this->add($this->component()->paginator($paginator));

        $this->renderContent();
    }

    public function indexApprovedAction()
    {
        $badge = Engine_Api::_()->getItem('hebadge_badge', $this->_getParam('badge_id'));
        if (!$badge) {
            $this->view->status = false;
            return;
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        $member = $badge->getMember($viewer);
        if (!$member) {
            $this->view->status = false;
            return;
        }
        $approved = $this->_getParam('approved', 0);
        $member->setApproved($approved);
        $this->view->status = true;
        $this->view->lang = $this->view->translate('Disabled');
        if ($approved)
            $this->view->lang = $this->view->translate('Enabled');
    }

  public function creditIndexAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer->getIdentity()) {
      return $this->view->url(array(), 'default');
    }

    $this->setFormat('browse');

//credit-loader widget
    $viewer = Engine_Api::_()->user()->getViewer();
    $request = Zend_Controller_Front::getInstance()->getRequest();


    $owner_rank = Engine_Api::_()->getDbTable('creditbadges', 'hebadge')->getOwnerRank($viewer);
    $owner_next_rank = Engine_Api::_()->getDbTable('creditbadges', 'hebadge')->getOwnerNextRank($viewer);
    $credit = Engine_Api::_()->getDbTable('creditbadges', 'hebadge')->getOwnerCredit($viewer);

    $current = 0;
    if ($credit) {
      $current = $credit->earned_credit;
      if ($owner_rank) {
        $current -= $owner_rank->credit;
      }
    }

    $total = $current;
    if ($owner_next_rank) {
      $total = $owner_next_rank->credit;
      if ($owner_rank) {
        $total -= $owner_rank->credit;
      }
    }

    $complete = 0;
    if ($current && $total) {
      $complete = floor($current / $total * 100);
    }

    $loader_html = $this->dom()->new_('div', array('class' => 'hebadges-user-rank-loader-wrapper', 'style' => 'margin-bottom: 10px'));

    $current = ($credit) ? $credit->earned_credit : 0;
    $total = ($owner_next_rank) ? $owner_next_rank->credit : (($credit) ? $credit->earned_credit : 0);

    $loader_html_current = $this->dom()->new_('div', array('class' => 'hebadges-user-rank-loader'),
      $this->dom()->new_('span', array('style' => 'font-weight: bold;'), $this->view->translate('HEBADGE_CURRENT_RANK')) . $this->dom()->new_('a', array('class' => 'hebadges-user-rank-loader-href', 'style' => 'margin-left: 10px;', 'href' => $owner_rank->getHref()),
        $owner_rank->getTitle()
      )
    );

    $loader_html_percents = $this->dom()->new_('div', array(),
      $this->dom()->new_('div', array('style' => 'float:left;'),
        $this->dom()->new_('div', array('class' => 'hebadge_credit_loader_total', 'style' => 'float:left; border-radius:4px; height:24px; margin-top: 10px; margin-bottom: 10px; width:150px; background-color: #eee'),
          $this->dom()->new_('div', array('class' => 'hebadge_credit_loader_progress', 'style' => "line-height:24px; text-align: center; height:24px; border-radius:4px;width:{$complete}%; background-color: #555"),
            $this->dom()->new_('span', array('style' => 'font-weight: bold;'), $current)
          )
        )
      ) . $this->dom()->new_('div', array('class' => 'hebadge_credit_loader_progress', 'style' => "line-height:42px; margin-left: 10px; float: left"),
        $this->dom()->new_('span', array('style' => 'font-weight: bold;'), $total)
      )
    );
//

    $loader_html_next = $this->dom()->new_('div', array('class' => 'hebadges-user-rank-loader', 'style' => 'clear: both;'),
      $this->dom()->new_('span', array('style' => 'font-weight: bold;'), $this->view->translate('HEBADGE_NEXT_RANK')) . $this->dom()->new_('a', array('class' => 'hebadges-user-rank-loader-href', 'style' => 'margin-left: 10px;', 'href' => $owner_next_rank->getHref()),
        $owner_next_rank->getTitle())
    );

    $loader_html->append($loader_html_current);
    $loader_html->append($loader_html_percents);
    $loader_html->append($loader_html_next);

    $this->add($this->component()->html($loader_html));

//credit-badges(ranks) widget

    $owner_rank = Engine_Api::_()->getDbTable('creditbadges', 'hebadge')->getOwnerRank($viewer);

    $paginator = Engine_Api::_()->getDbTable('creditbadges', 'hebadge')->getPaginator();
    $paginator->setItemCountPerPage(100);
    $paginator->setCurrentPageNumber($this->_getParam('page'));

    $badges = $this->dom()->new_('div', array('class' => 'member_list ui-grid-b'));
    $blocks = array('ui-block-a', 'ui-block-b', 'ui-block-c');
    $counter = 0;

    foreach ($paginator as $badge) {
      if($owner_rank && $badge->getIdentity() <= $owner_rank->getIdentity()) {
        $active = "background: #eee;";
        $active_dom = $this->dom()->new_('div', array('style'=>'margin:5px 0; text-align: center !important; background: url(/application/modules/Hebadge/externals/images/complete.png) no-repeat scroll left center transparent;'),
          $this->dom()->new_('span', array('style'=>'padding-left: 21px;'), $this->view->translate('HEBADGE_CREDIT_COMPLETE'))
        );
      } else {
        $active = "";
        $active_dom = "";
      }
      $memberEl = $this->dom()->new_('div', array('class' => $blocks[$counter % 3]), null, array(
        $this->dom()->new_('div', array('class' => 'member_item'), null, array(
          $this->dom()->new_('div', array('style'=>"border-radius: 5px; height:115px; $active}"), null, array(
            $this->dom()->new_('a', array('class' => 'profile_img', 'href' => $badge->getHref(), 'style' => $badge->getPhotoUrl('thumb.profile') ? 'background-image: url(' . $badge->getPhotoUrl('thumb.profile') . ')' : '')),
            $this->dom()->new_('span', array(), $badge->getTitle()),
            $active_dom
          ))
        ))
      ));

      $counter++;
      $badges->append($memberEl);
    }


    $this->add($this->component()->html($badges));
    $this->add($this->component()->paginator($paginator));

    $this->renderContent();
  }

}
