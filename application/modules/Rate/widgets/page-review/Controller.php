<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2010-07-02 19:53 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Rate_Widget_PageReviewController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;

  public function indexAction()
  {

    if (!Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('page')) {
      return $this->setNoRender();
    }

    /**
     * @var $subject Page_Model_Page
     */
    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('page');
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

    if (!($subject instanceof Page_Model_Page)) {
      return $this->setNoRender();
    }

    if (!in_array('rate', (array)$subject->getAllowedFeatures())) {
      return $this->setNoRender();
    }

    if (!$subject->authorization()->isAllowed($viewer, 'view')) {
      return $this->setNoRender();
    }

    $path = Zend_Controller_Front::getInstance()->getControllerDirectory('rate');
    $path = dirname($path) . '/views/scripts/review-ajax';
    $this->view->addScriptPath($path);

    $path = Zend_Controller_Front::getInstance()->getControllerDirectory('rate');
    $path = dirname($path) . '/views/scripts/';
    $this->view->addScriptPath($path);

    $this->view->headTranslate(array('RATE_REVIEW_DELETE', 'RATE_REVIEW_DELETEDESC'));

    $this->view->pageId = $page_id = $subject->getIdentity();

    $page = Engine_Api::_()->getDbTable('pages', 'page')
      ->findRow($page_id);

    if ($viewer->getIdentity()
      && Engine_Api::_()->getDbtable('permissions', 'authorization')
        ->getAllowed('rate', $viewer->level_id, 'reviewteamremove')
    ) {
      $this->view->isTeamMember = $page->isTeamMember($viewer);
    }

    $this->view->types = $types = Engine_Api::_()->getApi('core', 'rate')->getPageTypes($page_id);
    $this->view->countOptions = count($types);

    $form = new Rate_Form_Review_Create;
    $this->view->js = $form->addVotes($types);
    $this->view->form = $form;

    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('pagereview');

    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

    $p = $this->_getParam('page', 1);
    $this->view->content_info = $content_info = $subject->getContentInfo();

    if (!empty($content_info['content'])) {
      if ($content_info['content'] == 'review') {
        if ($review = Engine_Api::_()->getDbTable('pagereviews', 'rate')
          ->findRow($content_info['content_id'])
        ) {
          $this->view->init_js = "Review.initView(" . $review->getIdentity() . ");";
          if ($subject->is_timeline) {
            /**
             * @var $tbl Page_Model_DbTable_Content
             */
            $tbl = Engine_Api::_()->getDbTable('content', 'page');
            $id = $tbl->select()->from($tbl->info('name'), array('content_id'))
              ->where('page_id = ?', $subject->getIdentity())
              ->where("name = 'rate.page-review'")
              ->where('is_timeline = 1')
              ->query()
              ->fetch();
            $this->view->init_js = "tl_manager.fireTab('{$id['content_id']}');";
          }
        }
      } else if ($content_info['content'] == 'review_page') {
        $p = $content_info['content_id'];
      }
    }

    $tbl = Engine_Api::_()->getDbTable('pagereviews', 'rate');
    $this->view->paginator = $paginator = $tbl->getPaginator($page_id, $viewer->getIdentity(), $p);
    $this->view->isAllowedPost = $tbl->isAllowedPost($page_id, $viewer);

    // is allowed remove
    $this->view->isAllowedRemove = Engine_Api::_()->getApi('core', 'rate')
      ->isAllowRemoveReview($page_id, $viewer);

    if ($this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0) {
      $this->_childCount = $this->view->paginator->getTotalItemCount();
    }
  }

  public function getChildCount()
  {
    return $this->_childCount;
  }
}