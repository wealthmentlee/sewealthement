<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2012-12-12 17:08 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Rate_Widget_BrowseReviewsController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;

  public function indexAction()
  {
    /**
     * @var $pageReviewsTbl Rate_Model_DbTable_Pagereviews
     */
    if (!Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('page')) {
      return $this->setNoRender();
    }
    $fc = Zend_Controller_Front::getInstance();
    $request = $fc->getRequest();
    $params = $request->getParams();
    if(isset($params['keyword']) && $params['keyword'] == 'Search')
      unset($params['keyword']);
    if(isset($params['profile_type'])) {
      $params['category'] = $params['profile_type'];
      unset($params['profile_type']);
      unset($params['submit']);
    }

    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $isEnabledBrowseReviews = $settings->getSetting('rate.browse.reviews.enable', 0);
    if ($isEnabledBrowseReviews) {
      $settings = Engine_Api::_()->getApi('settings', 'core');
      $itemsCountPerPage = $settings->getSetting('rate.browse.reviews.count', 5);
      $params['itemsCountPerPage'] = $itemsCountPerPage;
      $pageReviewsTbl = Engine_Api::_()->getDbTable('pagereviews', 'rate');

      if (!array_key_exists('page', $params)) {
        $params['page'] = 1;
      }

      $votesTbl = Engine_Api::_()->getDbTable('votes', 'rate');
      $this->view->allVotes = $votesTbl->getAllVotes();
      $this->view->sort = $params['sort'] = (isset($params['sort'])) ? $params['sort'] : 'recent';

      $paginator = $pageReviewsTbl->getReviewsPaginator($params);
      if (isset($params['category']) && $params['category']) {
        $page_category = Engine_Api::_()->getDbTable('fieldsOptions','page')->getOption($params['category']);
        $this->view->page_category_name = $page_category['label'];
      }

      $this->view->paginator = $paginator;
    }
    else {
      $view = Zend_Registry::get('Zend_View');
      header('Location: http://' . $_SERVER['HTTP_HOST'] . $view->baseUrl() . '/');
      exit;
    }
  }

  public function getChildCount()
  {
    return $this->_childCount;
  }
}